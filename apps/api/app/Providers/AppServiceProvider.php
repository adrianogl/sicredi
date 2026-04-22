<?php

namespace App\Providers;

use App\Repositories\Contracts\MotionRepositoryInterface;
use App\Repositories\Contracts\VoteRepositoryInterface;
use App\Repositories\Contracts\VotingSessionRepositoryInterface;
use App\Repositories\MotionRepository;
use App\Repositories\VoteRepository;
use App\Repositories\VotingSessionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MotionRepositoryInterface::class, MotionRepository::class);
        $this->app->bind(VotingSessionRepositoryInterface::class, VotingSessionRepository::class);
        $this->app->bind(VoteRepositoryInterface::class, VoteRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
