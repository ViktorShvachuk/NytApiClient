<?php

namespace Tests\Feature\Services\Nyt;

use App\Exceptions\NytApiException;
use App\Services\Nyt\DataObjects\BestSellerResult;
use App\Services\Nyt\NytClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NytClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['nyt.api_key' => 'test-api-key']);
        config(['nyt.base_url' => 'https://api.nytimes.com/svc/books/v3/']);
    }

    public function test_it_fetches_best_sellers_successfully(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'lists' => [
                        [
                            'books' => [
                                [
                                    'title' => 'TEST BOOK',
                                    'description' => 'A very good book',
                                    'author' => 'Junie',
                                    'publisher' => 'JetBrains',
                                    'primary_isbn13' => '1234567890123',
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $client = app(NytClient::class);
        $results = $client->getBestSellers();

        $this->assertCount(1, $results);
        $this->assertInstanceOf(BestSellerResult::class, $results->first());
        $this->assertEquals('TEST BOOK', $results->first()->title);
        $this->assertEquals('Junie', $results->first()->author);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.nytimes.com/svc/books/v3/lists/overview.json?api-key=test-api-key';
        });
    }

    public function test_it_throws_exception_on_api_failure(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'fault' => ['faultstring' => 'Invalid API Key']
            ], 401)
        ]);

        $this->expectException(NytApiException::class);
        $this->expectExceptionMessage('Invalid API Key');
        $this->expectExceptionCode(401);

        $client = app(NytClient::class);
        $client->getBestSellers();
    }

    public function test_it_handles_empty_results(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => []
            ], 200)
        ]);

        $client = app(NytClient::class);
        $results = $client->getBestSellers();

        $this->assertTrue($results->isEmpty());
    }

    public function test_it_throws_runtime_exception_if_api_key_is_missing(): void
    {
        config(['nyt.api_key' => null]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NYT_API_KEY is not set in environment.');

        app(NytClient::class);
    }

    public function test_it_throws_runtime_exception_if_api_key_is_empty_string(): void
    {
        config(['nyt.api_key' => '']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NYT_API_KEY is not set in environment.');

        app(NytClient::class);
    }
}
