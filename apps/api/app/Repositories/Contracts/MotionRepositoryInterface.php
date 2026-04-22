<?php

namespace App\Repositories\Contracts;

use App\Models\Motion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MotionRepositoryInterface
{
    /**
     * @param  array{title: string, description?: ?string}  $data
     */
    public function create(array $data): Motion;

    /**
     * @return LengthAwarePaginator<int, Motion>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * @return Collection<int, Motion>
     */
    public function latest(int $limit): Collection;
}
