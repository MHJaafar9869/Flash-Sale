<?php

declare(strict_types=1);

namespace App\Repositories\BaseRepository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

readonly class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function addQuery(): Builder
    {
        return $this->model->query();
    }

    public function all(): ?Collection
    {
        return $this->model->all();
    }

    public function allWithRelations(string|array $relations): ?Collection
    {
        return $this->addQuery()->with($relations)->get();
    }

    public function find(int|string $id): ?Model
    {
        return $this->addQuery()->find($id);
    }

    public function findWithRelations(int|string $id, string|array $relations)
    {
        return $this->addQuery()->with($relations)->find($id);
    }

    public function select(array|string $columns): Builder
    {
        return $this->addQuery()->select((array) $columns);
    }

    public function store(array $data): Model
    {
        return $this->addQuery()->create($data);
    }
}
