<?php

use App\Models\City;

beforeEach(function () {
    City::insert([
        ['name' => 'Mumbai', 'state_name' => 'Maharashtra', 'country_code' => 'IN', 'latitude' => 19.0760, 'longitude' => 72.8777, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Chennai', 'state_name' => 'Tamil Nadu', 'country_code' => 'IN', 'latitude' => 13.0827, 'longitude' => 80.2707, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Munich', 'state_name' => 'Bavaria', 'country_code' => 'DE', 'latitude' => 48.1351, 'longitude' => 11.5820, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

test('city search requires minimum 2 characters', function () {
    $this->get(route('cities.search', ['q' => 'M']))
        ->assertRedirect();
});

test('city search returns matching cities', function () {
    $response = $this->getJson(route('cities.search', ['q' => 'Mu']))
        ->assertOk()
        ->assertJsonCount(2);

    expect($response->json('0.name'))->toBe('Mumbai');
});

test('city search prioritises Indian cities', function () {
    $response = $this->getJson(route('cities.search', ['q' => 'Mu']))
        ->assertOk();

    expect($response->json('0.country_code'))->toBe('IN');
    expect($response->json('1.country_code'))->toBe('DE');
});

test('city search filters by country', function () {
    $this->getJson(route('cities.search', ['q' => 'Mu', 'country' => 'DE']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Munich']);
});

test('city search returns coordinates', function () {
    $response = $this->getJson(route('cities.search', ['q' => 'Chennai']))
        ->assertOk()
        ->assertJsonCount(1);

    expect($response->json('0.latitude'))->not->toBeNull();
    expect($response->json('0.longitude'))->not->toBeNull();
});
