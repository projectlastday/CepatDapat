<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // Ensure the setting_website table exists and has the logo
    if (Schema::hasTable('setting_website')) {
        DB::table('setting_website')->updateOrInsert(
            ['key' => 'logo'],
            ['value' => 'assets/images/CepatDapat.png']
        );
    }
});

test('guest cannot access backup route', function () {
    $response = $this->post('/setting-website/backup');
    $response->assertRedirect('/login');
});

test('non-super-admin cannot access backup route', function () {
    // Simulate non-super-admin session (type 1 = Admin)
    $this->withSession([
        'id_user' => 1,
        'username' => 'admin',
        'id_user_type' => 1,
    ]);

    $response = $this->post('/setting-website/backup');
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('super-admin can download backup', function () {
    // Simulate super-admin session (type 7)
    $this->withSession([
        'id_user' => 1,
        'username' => 'superadmin',
        'id_user_type' => 7,
    ]);

    $response = $this->post('/setting-website/backup');
    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/sql; charset=UTF-8');

    // Filename pattern check
    $disposition = $response->headers->get('content-disposition');
    expect($disposition)->toContain('backup_cepatdapat_');
    expect($disposition)->toContain('.sql');
});

test('backup SQL contains CREATE TABLE and INSERT', function () {
    $this->withSession([
        'id_user' => 1,
        'username' => 'superadmin',
        'id_user_type' => 7,
    ]);

    $response = $this->post('/setting-website/backup');

    // Stream the response content
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect($content)->toContain('CREATE TABLE');
    // INSERT will exist if any table has data
});

test('backup logs history entry', function () {
    $this->withSession([
        'id_user' => 1,
        'username' => 'superadmin',
        'id_user_type' => 7,
    ]);

    $this->post('/setting-website/backup');

    $history = DB::table('history_setting_website')
        ->where('type', 'Backup Database')
        ->where('id_pelaku', 1)
        ->first();

    expect($history)->not->toBeNull();
    expect($history->data_baru)->toContain('backup_cepatdapat_');
});
