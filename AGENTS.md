# AGENTS.md — noerd/study

Contributor notes for humans and AI agents working on the Study module. The rules for
building WITH noerd (lists, details, pages, modals, modules, tests) come from the `noerd/noerd`
Boost guideline and skills; the module-specific rules are in
`resources/boost/guidelines/core.blade.php`. Both are rendered into the host project's agent files
by `php artisan boost:update` (add `noerd/study` to the `packages` array in `boost.json`).

## What this module is

A tenant app for learning: study materials (books, scripts), summaries per material and
flashcards per material (optionally per summary), plus a print page that renders up to eight
selected flashcards as an A4 PDF through dompdf. The PHP namespace is `Nywerk\Study`, the tenant
app name `STUDY`.

## Layout

- `app-configs/study/` — YAML templates (lists/, details/, navigation.yml); the installed copy
  lives in the host's `app-configs/study/` — change both
- `app-configs/stubs/add_study_tenant_app.php.stub` — the idempotent tenant-app migration
  published by `noerd:install-study`
- `resources/views/components/` — Livewire single-file components (`dashboard.blade.php`,
  `*-list.blade.php`, `*-detail.blade.php`, `flashcard-print-page.blade.php`), flat, Livewire
  namespace `study::`; `icons/app.blade.php` is the tenant-app icon
- `resources/views/pdf/flashcards.blade.php` — the dompdf template
- `src/Models/` (`StudyMaterial`, `Summary`, `Flashcard`), `src/Http/Controllers/FlashcardPrintController.php`,
  `src/Traits/StudyMaterialFilterTrait.php`, `src/Commands/`,
  `src/Providers/StudyServiceProvider.php` (relation field types `studyMaterialRelation`,
  `summaryRelation`)
- `database/migrations|factories|seeders/`, `tests/` (Pest), `resources/lang/de.json`
- Tables are `study_`-prefixed

## Commands

- `php artisan noerd:install-study` — first installation (asks for the tenant assignment)
- `php artisan noerd:update-study` — idempotent YAML update, discovered by `noerd:update-all`

## Working on the module

- Tests bind `Tests\TestCase` + `RefreshDatabase` (host-bound, MySQL). From the host project:
  `php artisan test --compact app-modules/study/tests`. `tests/Traits/CreatesStudyUser.php` sets
  up tenant, user and the `STUDY` app. Tests prove mechanics, never the current YAML
  configuration.
- `Nywerk\Study\Tests\` stays in the production `autoload` of `composer.json`: a path-repository's
  `autoload-dev` is not loaded by the host, and host-root runs need the test trait.
- Format from the host project root with an explicit path: `vendor/bin/pint app-modules/study`
  (a plain `--dirty` run silently skips submodule files)
- Keep queries portable (no MySQL-only SQL in application code); keep the module independent of
  other optional modules; project-specific fields go into `custom_attributes`, never into module
  code or module YAML
- When a feature changes: update the YAML in both places, `resources/lang/de.json`, the tests,
  `resources/boost/guidelines/core.blade.php` and `README.md`
- Releasing: bump `"version"` in `composer.json` to the tag in the tagged commit
