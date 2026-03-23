<?php

namespace App\Services\Nyt;

use App\Exceptions\NytApiException;
use App\Services\Nyt\DataObjects\NytListDetail;
use App\Services\Nyt\DataObjects\NytOverview;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class NytClient
{
    public function __construct(
        private string $apiKey,
        private string $baseUrl
    ) {}

    /**
     * Get Best Sellers Overview.
     *
     * @param string|null $publishedDate
     * @return NytOverview
     * @throws NytApiException|ConnectionException
     */
    public function getOverview(?string $publishedDate = null): NytOverview
    {
        $params = [];
        if ($publishedDate) {
            $params['published_date'] = $publishedDate;
        }

        $response = Http::baseUrl($this->baseUrl)
            ->retry(3, 100, function ($exception, $request) {
                return $exception instanceof ConnectionException ||
                    ($exception instanceof RequestException && ($exception->response->status() >= 500 || $exception->response->status() === 429));
            }, throw: false)
            ->withQueryParameters(array_merge($params, ['api-key' => $this->apiKey]))
            ->get('lists/overview.json');

        if ($response->failed()) {
            $this->handleError($response);
        }

        return NytOverview::fromArray($response->json('results', []));
    }

    /**
     * Get Best Sellers List.
     *
     * @param string $list
     * @param string|null $date
     * @return NytListDetail
     * @throws NytApiException|ConnectionException
     */
    public function getList(string $list, ?string $date = 'current'): NytListDetail
    {
        $response = Http::baseUrl($this->baseUrl)
            ->retry(3, 100, function ($exception, $request) {
                return $exception instanceof ConnectionException ||
                    ($exception instanceof RequestException && ($exception->response->status() >= 500 || $exception->response->status() === 429));
            }, throw: false)
            ->withQueryParameters(['api-key' => $this->apiKey])
            ->get("lists/{$date}/{$list}.json");

        if ($response->failed()) {
            $this->handleError($response);
        }

        return NytListDetail::fromArray($response->json('results', []));
    }

    /**
     * Handle API response error.
     *
     * @param Response $response
     * @throws NytApiException
     */
    private function handleError(Response $response): void
    {
        $message = $response->json('fault.faultstring')
            ?? $response->json('message')
            ?? 'NYT API request failed.';

        Log::error('NYT API error', [
            'status' => $response->status(),
            'url' => $response->effectiveUri()?->__toString(),
            'message' => $message,
            'response' => $response->json(),
        ]);

        throw new NytApiException(
            $message,
            $response->status(),
            $response
        );
    }
}
