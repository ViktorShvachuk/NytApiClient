<?php

namespace App\Services\Nyt\DataObjects;

use Illuminate\Support\Collection;

readonly class NytList
{
    /**
     * @param Collection<int, BestSellerResult> $books
     */
    public function __construct(
        public int $listId,
        public string $listName,
        public string $listNameEncoded,
        public string $displayName,
        public string $updated,
        public Collection $books,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            listId: $data['list_id'] ?? 0,
            listName: $data['list_name'] ?? '',
            listNameEncoded: $data['list_name_encoded'] ?? '',
            displayName: $data['display_name'] ?? '',
            updated: $data['updated'] ?? '',
            books: collect($data['books'] ?? [])->map(fn (array $book) => BestSellerResult::fromArray($book)),
        );
    }
}
