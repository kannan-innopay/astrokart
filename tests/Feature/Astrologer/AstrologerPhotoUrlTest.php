<?php

use App\Models\Astrologer;

test('the photo_url accessor returns a public storage url', function () {
    $astrologer = Astrologer::factory()->make(['photo' => 'astrologer-photos/sample.webp']);

    expect($astrologer->photo_url)->toContain('/storage/astrologer-photos/sample.webp');
});

test('the photo_url accessor is null when there is no photo', function () {
    $astrologer = Astrologer::factory()->make(['photo' => null]);

    expect($astrologer->photo_url)->toBeNull();
});
