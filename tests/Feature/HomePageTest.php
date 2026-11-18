<?php

use App\Services\SettingService;

test('homepage returns HTTP 200 on GET request', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('LEARN SAP ONLINE');
    $response->assertSee('GET HIRED');
    $response->assertSee('BOOK YOUR FREE LIVE DEMO');
});

test('homepage returns HTTP 200 on HEAD request', function () {
    $response = $this->head('/');

    $response->assertOk();
});

test('health endpoint /up returns HTTP 200', function () {
    $response = $this->get('/up');

    $response->assertOk();
});

test('setting service provides safe defaults when database is unavailable', function () {
    $service = app(SettingService::class);

    // maintenance_mode_enabled should default to false
    expect((bool) $service->get('maintenance_mode_enabled', false))->toBeFalse();

    // site_name should return default string
    expect($service->get('site_name'))->toBe('ZebLearn LMS');
});
