<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['nyt.api_key' => 'test-api-key']);
    }

    public function test_home_page_displays_nyt_best_sellers(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'published_date' => '2026-03-23',
                    'lists' => [
                        [
                            'display_name' => 'Fiction List',
                            'list_name_encoded' => 'fiction-list',
                            'books' => [
                                [
                                    'title' => 'FEATURED BOOK',
                                    'description' => 'A featured description',
                                    'author' => 'Author Name',
                                    'publisher' => 'Publisher Name',
                                    'primary_isbn13' => '1234567890123',
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('NYT Best Sellers - 2026-03-23');
        $response->assertSee('Fiction List');
        $response->assertSee('FEATURED BOOK');
        $response->assertSee('Author Name');
        $response->assertSee('A featured description');
    }

    public function test_home_page_handles_empty_results(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'lists' => []
                ]
            ], 200)
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Brak wyników dla wybranej daty.');
    }
}
