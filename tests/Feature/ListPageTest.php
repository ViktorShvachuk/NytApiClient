<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ListPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['nyt.api_key' => 'test-api-key']);
    }

    public function test_list_page_displays_nyt_best_sellers_for_specific_list(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'list_name' => 'Hardcover Fiction',
                    'display_name' => 'Hardcover Fiction',
                    'bestsellers_date' => '2026-03-21',
                    'books' => [
                        [
                            'title' => 'SPECIFIC LIST BOOK',
                            'description' => 'A specific description',
                            'author' => 'Specific Author',
                            'publisher' => 'Specific Publisher',
                            'primary_isbn13' => '9876543210123',
                            'rank' => 1,
                            'weeks_on_list' => 5
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->get('/list/hardcover-fiction');

        $response->assertStatus(200);
        $response->assertSee('Hardcover Fiction');
        $response->assertSee('SPECIFIC LIST BOOK');
        $response->assertSee('Specific Author');
        $response->assertSee('Rank: #1');
        $response->assertSee('(5 weeks on list)');
    }

    public function test_list_page_handles_empty_results(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [
                    'list_name' => 'Empty List',
                    'books' => []
                ]
            ], 200)
        ]);

        $response = $this->get('/list/empty-list');

        $response->assertStatus(200);
        $response->assertSee('Brak książek na tej liście.');
    }
}
