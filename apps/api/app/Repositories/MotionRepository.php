<?php

namespace App\Repositories;

use App\Models\Motion;
use App\Repositories\Contracts\MotionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class MotionRepository implements MotionRepositoryInterface
{
    /**
     * @param  array{title: string, description?: ?string}  $data
     */
    public function create(array $data): Motion
    {
        $motion = Motion::create($data);

        Log::info('motion.created', [
            'motion_id' => $motion->id,
            'title' => $motion->title,
        ]);

        return $motion;
    }

    /**
     * @return Collection<int, Motion>
     */
    public function latest(int $limit): Collection
    {
        return Motion::query()->latest()->limit($limit)->get();
    }
}
