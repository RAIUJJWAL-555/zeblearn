<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\User;

test('admin can access course categories management page', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->get(route('admin.categories.index'));

    $response->assertOk();
    $response->assertSee('Course Categories');
    $response->assertSee('Add New Category');
    $response->assertSee('Hierarchical URL Structure');
});

test('non-admin cannot access course categories management', function () {
    $student = User::factory()->student()->active()->create();

    $response = $this->actingAs($student)->get(route('admin.categories.index'));

    $response->assertForbidden();
});

test('admin can create a new course category', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'SAP Cloud Solutions',
        'slug' => 'sap-cloud-solutions',
        'description' => 'Ariba, SuccessFactors, and Analytics Cloud training tracks.',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'SAP Cloud Solutions',
        'slug' => 'sap-cloud-solutions',
    ]);
});

test('admin category creation auto-generates slug if omitted', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'SAP Industry Solutions',
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'name' => 'SAP Industry Solutions',
        'slug' => 'sap-industry-solutions',
    ]);
});

test('admin can view edit category page with prefilled values', function () {
    $admin = User::factory()->admin()->active()->create();

    $category = Category::create([
        'name' => 'SAP Supply Chain Track',
        'slug' => 'sap-supply-chain-track',
        'description' => 'EWM, TM, and Ariba training modules.',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.categories.edit', $category));

    $response->assertOk();
    $response->assertSee('Edit Category');
    $response->assertSee('SAP Supply Chain Track');
    $response->assertSee('sap-supply-chain-track');
    $response->assertSee('EWM, TM, and Ariba training modules.');
});

test('admin can update an existing category and courses sync slug', function () {
    $admin = User::factory()->admin()->active()->create();

    $category = Category::create([
        'name' => 'Legacy SAP Tech',
        'slug' => 'legacy-sap-tech',
    ]);

    $course = Course::factory()->published()->create([
        'category' => 'legacy-sap-tech',
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
        'name' => 'Modern SAP Technical',
        'slug' => 'modern-sap-technical',
        'description' => 'Updated technical track',
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Modern SAP Technical',
        'slug' => 'modern-sap-technical',
    ]);

    $course->refresh();
    expect($course->category)->toBe('modern-sap-technical');
});

test('admin can delete a category and associated courses are safely unlinked', function () {
    $admin = User::factory()->admin()->active()->create();

    $category = Category::create([
        'name' => 'Temporary Category',
        'slug' => 'temp-cat-for-delete',
    ]);

    $course = Course::factory()->published()->create([
        'category' => 'temp-cat-for-delete',
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);

    $course->refresh();
    expect($course->category_id)->not->toBe($category->id);
});

test('course landing page is accessible via category-based url', function () {
    $category = Category::firstOrCreate([
        'slug' => 'sap-functional',
    ], [
        'name' => 'SAP Functional',
    ]);

    $course = Course::factory()->published()->create([
        'name' => 'SAP FICO Financial Accounting',
        'slug' => 'sap-fico-test-category-route',
        'category' => 'sap-functional',
        'category_id' => $category->id,
    ]);

    // Test category-based route /{category}/{course}
    $response = $this->get('/sap-functional/sap-fico-test-category-route');
    $response->assertOk();
    $response->assertSee('SAP FICO Financial Accounting');

    // Test course url generation
    expect($course->url)->toBe(url('/sap-functional/sap-fico-test-category-route'));
});

test('visiting a category slug directly redirects to filtered catalog', function () {
    Category::firstOrCreate([
        'slug' => 'sap-functional',
    ], [
        'name' => 'SAP Functional',
    ]);

    $response = $this->get('/sap-functional');
    $response->assertRedirect(route('courses.index', ['category' => 'sap-functional']));
});

test('course with trailing slash does not cause infinite redirect loop', function () {
    $category = Category::firstOrCreate([
        'slug' => 'sap-functional',
    ], [
        'name' => 'SAP Functional',
    ]);

    $course = Course::factory()->published()->create([
        'name' => 'SAP FICO on S/4 HANA',
        'slug' => 'sap-fico-on-s-4-hana',
        'category' => 'sap-functional',
        'category_id' => $category->id,
    ]);

    // Requesting without trailing slash
    $response = $this->get('/sap-functional/sap-fico-on-s-4-hana');
    $response->assertOk();

    // Requesting with trailing slash
    $responseWithSlash = $this->get('/sap-functional/sap-fico-on-s-4-hana/');
    $responseWithSlash->assertOk();
});

test('course model trims slashes from slug automatically', function () {
    $course = new Course;
    $course->slug = 'custom-slug-with-slashes/';
    expect($course->slug)->toBe('custom-slug-with-slashes');

    $course->slug = '/leading-and-trailing/';
    expect($course->slug)->toBe('leading-and-trailing');
});
