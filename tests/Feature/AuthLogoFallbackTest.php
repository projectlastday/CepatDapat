<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('login page shows custom logo when setting exists', function () {
    if (!Schema::hasTable('setting_website')) {
        $this->markTestSkipped('setting_website table not available');
    }

    DB::table('setting_website')->updateOrInsert(
        ['key' => 'logo'],
        ['value' => 'img_website/custom_logo.png']
    );

    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('img_website/custom_logo.png');
});

test('login page shows fallback logo when setting is empty', function () {
    if (!Schema::hasTable('setting_website')) {
        $this->markTestSkipped('setting_website table not available');
    }

    DB::table('setting_website')->updateOrInsert(
        ['key' => 'logo'],
        ['value' => '']
    );

    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('assets/images/CepatDapat.png');
});

test('register page shows custom logo when setting exists', function () {
    if (!Schema::hasTable('setting_website')) {
        $this->markTestSkipped('setting_website table not available');
    }

    DB::table('setting_website')->updateOrInsert(
        ['key' => 'logo'],
        ['value' => 'img_website/custom_logo.png']
    );

    $response = $this->get('/register');
    $response->assertStatus(200);
    $response->assertSee('img_website/custom_logo.png');
});

test('register page shows fallback logo when setting is null', function () {
    if (!Schema::hasTable('setting_website')) {
        $this->markTestSkipped('setting_website table not available');
    }

    DB::table('setting_website')->updateOrInsert(
        ['key' => 'logo'],
        ['value' => null]
    );

    $response = $this->get('/register');
    $response->assertStatus(200);
    $response->assertSee('assets/images/CepatDapat.png');
});
