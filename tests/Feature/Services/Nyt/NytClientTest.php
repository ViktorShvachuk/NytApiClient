<?php

namespace Tests\Feature\Services\Nyt;

use App\Exceptions\NytApiException;
use App\Services\Nyt\DataObjects\BestSellerResult;
use App\Services\Nyt\DataObjects\NytListDetail;
use App\Services\Nyt\DataObjects\NytOverview;
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

    public function test_it_fetches_overview_successfully(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'published_date' => '2026-03-23',
                    'lists' => [
                        [
                            'display_name' => 'Combined Print & E-Book Fiction',
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
        $overview = $client->getOverview('2026-03-23');

        $this->assertInstanceOf(NytOverview::class, $overview);
        $this->assertEquals('2026-03-23', $overview->publishedDate);
        $this->assertCount(1, $overview->lists);
        $this->assertEquals('Combined Print & E-Book Fiction', $overview->lists->first()->displayName);
        $this->assertCount(1, $overview->lists->first()->books);
        $this->assertEquals('TEST BOOK', $overview->lists->first()->books->first()->title);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.nytimes.com/svc/books/v3/lists/overview.json?published_date=2026-03-23&api-key=test-api-key';
        });
    }

    public function test_it_fetches_list_detail_successfully(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'list_name' => 'Hardcover Fiction',
                    'books' => [
                        [
                            'title' => 'LIST BOOK',
                            'description' => 'A very good book',
                            'author' => 'Junie',
                            'publisher' => 'JetBrains',
                            'primary_isbn13' => '1234567890123',
                        ]
                    ]
                ]
            ], 200)
        ]);

        $client = app(NytClient::class);
        $listDetail = $client->getList('hardcover-fiction', '2026-03-23');

        $this->assertInstanceOf(NytListDetail::class, $listDetail);
        $this->assertEquals('Hardcover Fiction', $listDetail->listName);
        $this->assertCount(1, $listDetail->books);
        $this->assertEquals('LIST BOOK', $listDetail->books->first()->title);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.nytimes.com/svc/books/v3/lists/2026-03-23/hardcover-fiction.json?api-key=test-api-key';
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
        $client->getOverview();
    }

    public function test_it_handles_empty_results(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'lists' => []
                ]
            ], 200)
        ]);

        $client = app(NytClient::class);
        $overview = $client->getOverview();

        $this->assertTrue($overview->lists->isEmpty());
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
