<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function addQuery(): Builder;

    public function all(): ?Collection;

    public function allWithRelations(string|array $relations): ?Collection;

    public function find(int|string $id): ?Model;

    public function findWithRelations(int|string $id, string|array $relations);

    public function select(array|string $columns): Builder;

    public function store(array $data): Model;
}
