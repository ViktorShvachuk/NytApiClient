<?php

namespace App\Services\Nyt;

use App\Exceptions\NytApiException;
use App\Services\Nyt\DataObjects\BestSellerResult;
use App\Services\Nyt\DataObjects\NytListDetail;
use App\Services\Nyt\DataObjects\NytOverview;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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

        throw new NytApiException(
            $message,
            $response->status(),
            $response
        );
    }
}
