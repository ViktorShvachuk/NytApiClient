<?php

namespace App\Services\Nyt\DataObjects;

readonly class BestSellerResult
{
    public function __construct(
        public string $title,
        public string $description,
        public string $author,
        public string $publisher,
        public string $primaryIsbn13,
        public string $contributor = '',
        public string $bookImage = '',
        public string $amazonProductUrl = '',
        public int $rank = 0,
        public int $weeksOnList = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            author: $data['author'] ?? '',
            publisher: $data['publisher'] ?? '',
            primaryIsbn13: $data['primary_isbn13'] ?? '',
            contributor: $data['contributor'] ?? '',
            bookImage: $data['book_image'] ?? '',
            amazonProductUrl: $data['amazon_product_url'] ?? '',
            rank: $data['rank'] ?? 0,
            weeksOnList: $data['weeks_on_list'] ?? 0,
        );
    }
}
