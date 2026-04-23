<?php

namespace App\Repositories\Contracts;

use App\Models\Motion;
use Illuminate\Database\Eloquent\Collection;

interface MotionRepositoryInterface
{
    /**
     * @param  array{title: string, description?: ?string}  $data
     */
    public function create(array $data): Motion;

    /**
     * @return Collection<int, Motion>
     */
    public function latest(int $limit): Collection;
}
