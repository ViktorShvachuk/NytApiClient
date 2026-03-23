<?php

namespace App\Services\Nyt\DataObjects;

use Illuminate\Support\Collection;

readonly class NytListDetail
{
    /**
     * @param Collection<int, BestSellerResult> $books
     */
    public function __construct(
        public string $listName,
        public string $listNameEncoded,
        public string $displayName,
        public string $bestsellersDate,
        public string $publishedDate,
        public string $previousPublishedDate,
        public string $nextPublishedDate,
        public string $updated,
        public Collection $books,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            listName: $data['list_name'] ?? '',
            listNameEncoded: $data['list_name_encoded'] ?? '',
            displayName: $data['display_name'] ?? '',
            bestsellersDate: $data['bestsellers_date'] ?? '',
            publishedDate: $data['published_date'] ?? '',
            previousPublishedDate: $data['previous_published_date'] ?? '',
            nextPublishedDate: $data['next_published_date'] ?? '',
            updated: $data['updated'] ?? '',
            books: collect($data['books'] ?? [])->map(fn (array $book) => BestSellerResult::fromArray($book)),
        );
    }
}
