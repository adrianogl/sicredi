<?php

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
