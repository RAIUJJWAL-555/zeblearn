# ZebLearn Laravel Blade Refactor

This archive contains the updated source files from the supplied `Archive.zip` project.

## What changed
- Fixed/verified Blade URL expressions in the migrated views; no escaped `url()->current()` spellings remain in source Blade templates.
- Added shared Blade layout components:
  - `resources/views/components/layout/header.blade.php`
  - `resources/views/components/layout/footer.blade.php`
- Updated `resources/views/layouts/app.blade.php` to use `<x-layout.header />` and `<x-layout.footer />`.
- Added UI components:
  - `resources/views/components/ui/button.blade.php`
  - `resources/views/components/ui/badge.blade.php`
  - `resources/views/components/ui/container.blade.php`
  - `resources/views/components/ui/section-heading.blade.php`
- Migrated the homepage, About, Contact, Book Demo, Thank You, Blog, EWM article and SAP FICO course views into `resources/views/pages/`.
- Updated `routes/web.php` to use the new `pages.*` view names.
- Preserved original legacy view files in their old locations as rollback references.

## Integration
1. Make a backup of your current project.
2. Extract this archive into the Laravel project root, allowing files to merge/overwrite.
3. Do not delete your `.env`, `vendor/`, or `node_modules/` directories.
4. Run:
   - `php artisan route:list`
   - `php artisan view:clear`
   - `php artisan view:cache`
   - `vendor/bin/pint --format agent`
   - `npm run build`

## Validation performed here
- `php -l routes/web.php`: passed.
- Laravel route registration: passed; all 8 expected routes are registered.
- Blade compiler: passed; all 34 Blade templates compiled.
- Full artisan view/cache commands could not complete in this runtime because PHP DOM/XML and SQLite PDO extensions are missing.
- Pint could not run because PHP `mbstring` and `xml` extensions are missing.
- Vite build could not run because the uploaded `node_modules` did not contain the Linux Rolldown native binding (the archive was prepared on a different platform). Run `npm install` on your Mac, then `npm run build`.

## Notes
- `vendor/`, `node_modules/`, `.env`, SQLite database files, generated framework caches and `.git/` are intentionally excluded.
- The original `resources/views/partials/` and original page files are retained; the new routes use `resources/views/pages/`.
