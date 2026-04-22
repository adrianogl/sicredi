<?php

use App\Models\Motion;

it('creates a motion with 201', function (): void {
    $response = $this->postJson('/api/v1/motions', [
        'title' => 'Sample Motion',
        'description' => 'Description',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Sample Motion')
        ->assertJsonPath('data.description', 'Description');

    $this->assertDatabaseHas('motions', ['title' => 'Sample Motion']);
});

it('returns 422 when title is missing', function (): void {
    $this->postJson('/api/v1/motions', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

it('lists motions as a resource collection', function (): void {
    Motion::factory()->count(3)->create();

    $this->getJson('/api/v1/motions')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'title', 'description', 'created_at', 'updated_at']],
        ]);
});
