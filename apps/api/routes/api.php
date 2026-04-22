<?php

use App\Http\Controllers\Api\Motion\ListMotionsController;
use App\Http\Controllers\Api\Motion\ShowMotionsSelectionController;
use App\Http\Controllers\Api\Motion\StoreMotionController;
use App\Http\Controllers\Api\Result\ShowMotionResultController;
use App\Http\Controllers\Api\Vote\ShowVoteFormController;
use App\Http\Controllers\Api\Vote\StoreVoteController;
use App\Http\Controllers\Api\VotingSession\OpenVotingSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('motions')->name('motions.')->group(function (): void {
    Route::get('/', ListMotionsController::class)->name('index');
    Route::post('/', StoreMotionController::class)->name('store');
    Route::get('{motion}/result', ShowMotionResultController::class)->name('result');
    Route::post('{motion}/sessions', OpenVotingSessionController::class)->name('sessions.store');
});

Route::post('sessions/{session}/votes', StoreVoteController::class)->name('votes.store');

Route::prefix('ui')->name('ui.')->group(function (): void {
    Route::get('motions', ShowMotionsSelectionController::class)->name('motions');
    Route::get('sessions/{session}/vote', ShowVoteFormController::class)->name('vote');
});
