<?php

namespace App\Services\Nyt;

use App\Exceptions\NytApiException;
use App\Services\Nyt\DataObjects\BestSellerResult;
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
     * Get Best Sellers History.
     *
     * @param array $params
     * @return Collection<int, BestSellerResult>
     * @throws NytApiException|ConnectionException
     */
    public function getBestSellers(array $params = []): Collection
    {
        $response = Http::baseUrl($this->baseUrl)
            ->withQueryParameters(array_merge($params, ['api-key' => $this->apiKey]))
            ->get('lists/overview.json');

        if ($response->failed()) {
            $this->handleError($response);
        }

        $results = $response->json('results', []);
        $lists = $results['lists'] ?? [];

        return collect($lists)
            ->flatMap(fn (array $list) => $list['books'] ?? [])
            ->map(fn (array $data) => BestSellerResult::fromArray($data));
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
