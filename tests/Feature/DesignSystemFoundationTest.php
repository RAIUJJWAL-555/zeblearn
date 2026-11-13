<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

test('1. ui button renders variants correctly', function () {
    // Primary
    $htmlPrimary = Blade::render('<x-ui.button variant="primary">Click Me</x-ui.button>');
    expect($htmlPrimary)->toContain('Click Me');
    expect($htmlPrimary)->toContain('bg-[#FB6221]');
    expect($htmlPrimary)->toContain('<button');

    // Secondary
    $htmlSecondary = Blade::render('<x-ui.button variant="secondary">Cancel</x-ui.button>');
    expect($htmlSecondary)->toContain('Cancel');
    expect($htmlSecondary)->toContain('border-slate-300');

    // Outline
    $htmlOutline = Blade::render('<x-ui.button variant="outline">Learn More</x-ui.button>');
    expect($htmlOutline)->toContain('Learn More');
    expect($htmlOutline)->toContain('border-[#FB6221]');

    // Danger
    $htmlDanger = Blade::render('<x-ui.button variant="danger">Delete</x-ui.button>');
    expect($htmlDanger)->toContain('Delete');
    expect($htmlDanger)->toContain('bg-rose-600');

    // Link anchor when href is passed
    $htmlLink = Blade::render('<x-ui.button href="https://example.com" variant="primary">Go</x-ui.button>');
    expect($htmlLink)->toContain('<a');
    expect($htmlLink)->toContain('href="https://example.com"');

    // Disabled state
    $htmlDisabled = Blade::render('<x-ui.button :disabled="true">Disabled</x-ui.button>');
    expect($htmlDisabled)->toContain('disabled');

    // Loading state
    $htmlLoading = Blade::render('<x-ui.button :loading="true" loadingText="Saving...">Save</x-ui.button>');
    expect($htmlLoading)->toContain('Saving...');
    expect($htmlLoading)->toContain('animate-spin');
    expect($htmlLoading)->toContain('disabled');
});

test('2. ui badge renders variants and dots', function () {
    $htmlPrimary = Blade::render('<x-ui.badge variant="primary">New Course</x-ui.badge>');
    expect($htmlPrimary)->toContain('New Course');
    expect($htmlPrimary)->toContain('bg-orange-50');

    $htmlSuccess = Blade::render('<x-ui.badge variant="success" :dot="true">Active</x-ui.badge>');
    expect($htmlSuccess)->toContain('Active');
    expect($htmlSuccess)->toContain('bg-emerald-50');
    expect($htmlSuccess)->toContain('bg-emerald-500'); // dot color

    $htmlWarning = Blade::render('<x-ui.badge variant="warning">Pending</x-ui.badge>');
    expect($htmlWarning)->toContain('Pending');
    expect($htmlWarning)->toContain('bg-amber-50');

    $htmlDanger = Blade::render('<x-ui.badge variant="danger">Failed</x-ui.badge>');
    expect($htmlDanger)->toContain('Failed');
    expect($htmlDanger)->toContain('bg-rose-50');

    // Backward compatibility with 'default'
    $htmlDefault = Blade::render('<x-ui.badge variant="default">Legacy</x-ui.badge>');
    expect($htmlDefault)->toContain('Legacy');
    expect($htmlDefault)->toContain('bg-slate-100');
});

test('3. ui icon renders standardized svg icons', function () {
    $htmlCheck = Blade::render('<x-ui.icon name="check" size="md" />');
    expect($htmlCheck)->toContain('<svg');
    expect($htmlCheck)->toContain('viewBox="0 0 24 24"');
    expect($htmlCheck)->toContain('w-5 h-5');

    $htmlWarning = Blade::render('<x-ui.icon name="warning" size="sm" />');
    expect($htmlWarning)->toContain('<svg');
    expect($htmlWarning)->toContain('w-4 h-4');

    $htmlSpinner = Blade::render('<x-ui.icon name="spinner" size="lg" />');
    expect($htmlSpinner)->toContain('animate-spin');
    expect($htmlSpinner)->toContain('w-6 h-6');
});

test('4. ui input renders labels, icons and validation errors', function () {
    // Normal input
    $htmlInput = Blade::render('<x-ui.input name="full_name" label="Full Name" placeholder="Enter name" :required="true" icon="user" />');
    expect($htmlInput)->toContain('Full Name');
    expect($htmlInput)->toContain('name="full_name"');
    expect($htmlInput)->toContain('id="full_name"');
    expect($htmlInput)->toContain('placeholder="Enter name"');
    expect($htmlInput)->toContain('required');
    expect($htmlInput)->toContain('pl-10'); // Icon padding

    // Input with validation error
    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['email' => ['Please enter a valid email address.']]));
    View::share('errors', $errors);

    $htmlError = Blade::render('<x-ui.input name="email" label="Email Address" />');
    expect($htmlError)->toContain('Please enter a valid email address.');
    expect($htmlError)->toContain('aria-invalid="true"');
    expect($htmlError)->toContain('border-rose-400');
});

test('5. ui select renders options, arrows, and errors', function () {
    $htmlSelect = Blade::render('
        <x-ui.select name="role" label="Account Role" placeholder="Choose a role">
            <option value="student">Student</option>
            <option value="instructor">Instructor</option>
        </x-ui.select>
    ');
    expect($htmlSelect)->toContain('Account Role');
    expect($htmlSelect)->toContain('name="role"');
    expect($htmlSelect)->toContain('Choose a role');
    expect($htmlSelect)->toContain('Student');
    expect($htmlSelect)->toContain('Instructor');
    expect($htmlSelect)->toContain('pointer-events-none');
    expect($htmlSelect)->toContain('M19 9l-7 7-7-7'); // Chevron down SVG icon path
});

test('6. ui textarea renders rows, labels and values', function () {
    $htmlTextarea = Blade::render('<x-ui.textarea name="bio" label="Biography" :rows="6" placeholder="Tell us about yourself">Initial bio</x-ui.textarea>');
    expect($htmlTextarea)->toContain('Biography');
    expect($htmlTextarea)->toContain('name="bio"');
    expect($htmlTextarea)->toContain('rows="6"');
    expect($htmlTextarea)->toContain('Initial bio');
});

test('7. ui card renders headers, footers and variants', function () {
    $htmlCard = Blade::render('
        <x-ui.card title="Analytics Overview" subtitle="Last 30 days">
            <x-slot:actions>
                <button type="button">Refresh</button>
            </x-slot:actions>
            <p>Main Card Body Content</p>
            <x-slot:footer>
                <span>Updated just now</span>
            </x-slot:footer>
        </x-ui.card>
    ');
    expect($htmlCard)->toContain('Analytics Overview');
    expect($htmlCard)->toContain('Last 30 days');
    expect($htmlCard)->toContain('Refresh');
    expect($htmlCard)->toContain('Main Card Body Content');
    expect($htmlCard)->toContain('Updated just now');
});

test('8. ui alert renders semantic feedback and dismissibility', function () {
    $htmlSuccess = Blade::render('<x-ui.alert type="success" title="Success!">Your changes have been saved.</x-ui.alert>');
    expect($htmlSuccess)->toContain('Success!');
    expect($htmlSuccess)->toContain('Your changes have been saved.');
    expect($htmlSuccess)->toContain('bg-emerald-50/80');

    $htmlDismiss = Blade::render('<x-ui.alert type="danger" title="Error" :dismissible="true">Something went wrong.</x-ui.alert>');
    expect($htmlDismiss)->toContain('x-data="{ show: true }"');
    expect($htmlDismiss)->toContain('Dismiss alert');
});

test('9. ui empty state renders icon, title, description, and action CTA', function () {
    $htmlEmpty = Blade::render('
        <x-ui.empty-state title="No courses found" description="Try refining your search terms." icon="search">
            <x-slot:action>
                <x-ui.button variant="primary">Create Course</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    ');
    expect($htmlEmpty)->toContain('No courses found');
    expect($htmlEmpty)->toContain('Try refining your search terms.');
    expect($htmlEmpty)->toContain('Create Course');
});

test('10. ui loading state renders accessible spinner', function () {
    $htmlLoading = Blade::render('<x-ui.loading-state text="Fetching curriculum..." size="md" />');
    expect($htmlLoading)->toContain('Fetching curriculum...');
    expect($htmlLoading)->toContain('role="status"');
    expect($htmlLoading)->toContain('Loading content, please wait.');
});

test('11. ui modal renders accessible alpine dialog with backdrop', function () {
    $htmlModal = Blade::render('
        <x-ui.modal name="confirm-delete" title="Confirm Course Deletion">
            <p>Are you sure you want to delete this course?</p>
            <x-slot:footer>
                <x-ui.button variant="secondary">Cancel</x-ui.button>
                <x-ui.button variant="danger">Delete</x-ui.button>
            </x-slot:footer>
        </x-ui.modal>
    ');
    expect($htmlModal)->toContain('Confirm Course Deletion');
    expect($htmlModal)->toContain('Are you sure you want to delete this course?');
    expect($htmlModal)->toContain('Cancel');
    expect($htmlModal)->toContain('Delete');
    expect($htmlModal)->toContain('role="dialog"');
    expect($htmlModal)->toContain('aria-modal="true"');
    expect($htmlModal)->toContain('x-on:keydown.escape.window');
});
