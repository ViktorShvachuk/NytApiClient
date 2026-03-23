<?php

namespace App\Services\Nyt\DataObjects;

use Illuminate\Support\Collection;

readonly class NytOverview
{
    /**
     * @param Collection<int, NytList> $lists
     */
    public function __construct(
        public string $bestsellersDate,
        public string $publishedDate,
        public string $previousPublishedDate,
        public string $nextPublishedDate,
        public Collection $lists,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            bestsellersDate: $data['bestsellers_date'] ?? '',
            publishedDate: $data['published_date'] ?? '',
            previousPublishedDate: $data['previous_published_date'] ?? '',
            nextPublishedDate: $data['next_published_date'] ?? '',
            lists: collect($data['lists'] ?? [])->map(fn (array $list) => NytList::fromArray($list)),
        );
    }
}
