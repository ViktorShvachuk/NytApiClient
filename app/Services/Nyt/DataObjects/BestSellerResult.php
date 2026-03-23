<?php

namespace App\Services\Nyt\DataObjects;

readonly class BestSellerResult
{
    public function __construct(
        public string $title,
        public string $description,
        public string $contributor,
        public string $author,
        public string $publisher,
        public string $primaryIsbn13,
        public string $primaryIsbn10,
        public array $ranksHistory = [],
        public array $reviews = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            contributor: $data['contributor'] ?? '',
            author: $data['author'] ?? '',
            publisher: $data['publisher'] ?? '',
            primaryIsbn13: $data['primary_isbn13'] ?? '',
            primaryIsbn10: $data['primary_isbn10'] ?? '',
            ranksHistory: $data['ranks_history'] ?? [],
            reviews: $data['reviews'] ?? [],
        );
    }
}
