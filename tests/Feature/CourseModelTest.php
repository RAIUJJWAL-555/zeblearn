<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('course seeder seeds the verified academy taxonomy', function () {
    expect(Course::count())->toBeGreaterThanOrEqual(11);

    // Confirmed live available courses (including FICO flagship and category templates)
    expect(Course::available()->count())->toBeGreaterThanOrEqual(1);
    $fico = Course::where('slug', 'sap-fico-training')->first();
    expect($fico)->not->toBeNull();
    expect($fico->name)->toBe('SAP FICO Training');
    expect($fico->fee)->toBe(25000);
    expect($fico->formatted_fee)->toBe('₹25,000 + GST');
    expect($fico->featured)->toBeTrue();
    expect($fico->url)->toBe(route('courses.fico'));

    // Upcoming batches
    expect(Course::upcoming()->count())->toBeGreaterThanOrEqual(5);
    $upcomingSlugs = Course::upcoming()->pluck('slug')->all();
    expect($upcomingSlugs)->toContain('sap-mm-training', 'sap-sd-training', 'sap-pp-training', 'sap-abap-training', 'sap-basis-training');

    // Planned roadmap candidates
    expect(Course::planned()->count())->toBeGreaterThanOrEqual(5);
    $plannedSlugs = Course::planned()->pluck('slug')->all();
    expect($plannedSlugs)->toContain('sap-ewm-training', 'sap-btp-training', 'sap-successfactors-training', 'sap-ariba-training', 'sap-pm-qm-training');
});

test('course model taxonomy categories and status scopes work properly', function () {
    expect(Course::byCategory('functional')->count())->toBe(6); // FICO, MM, SD, PP, SuccessFactors, PM/QM
    expect(Course::byCategory('technical')->count())->toBe(3); // ABAP, Basis, BTP
    expect(Course::byCategory('cloud_specialized')->count())->toBe(2); // EWM, Ariba

    $fico = Course::where('slug', 'sap-fico-training')->first();
    expect($fico->isAvailable())->toBeTrue();
    expect($fico->isUpcoming())->toBeFalse();
    expect($fico->isPlanned())->toBeFalse();
    expect($fico->category_label)->toBe('SAP Functional');
    expect($fico->status_label)->toBe('Available Now');
});

test('course factory creates valid model instances with custom states', function () {
    $available = Course::factory()->available()->create(['name' => 'Custom SAP Test']);
    expect($available->isAvailable())->toBeTrue();
    expect($available->fee)->toBe(25000);

    $upcoming = Course::factory()->upcoming()->create();
    expect($upcoming->isUpcoming())->toBeTrue();
    expect($upcoming->fee)->toBeNull();

    $planned = Course::factory()->planned()->create();
    expect($planned->isPlanned())->toBeTrue();
    expect($planned->fee)->toBeNull();
});
