@verbatim
## Study Module

The Study module is a Noerd tenant app (Composer package `noerd/study`, namespace `Nywerk\Study`
— NOT `Noerd\Study`; `app-configs/study/navigation.yml`) for study materials, their summaries and
flashcards, including a printable flashcard PDF. The framework rules (lists, details, pages,
modals, themes, tests, translations) come from the `noerd/noerd` guideline — this block only adds
what is specific to this module.

### Domain
- All models use `BelongsToTenant`, `$guarded = []`, `HasFactory` and a `tenant()` relation
- `StudyMaterial` (table `study_materials`, renamed from `study_books` by the
  `rename_study_books_to_study_materials` migration) — `title`, `author`, `page_count`,
  `publication_year` (integer casts); has many `summaries()` and `flashcards()`
- `Summary` (table `study_summaries`) — `title`, `content` (longText); belongs to
  `studyMaterial()` via `study_material_id` (renamed from `book_id`)
- `Flashcard` (table `study_flashcards`) — `question`, `answer`, `created_date` (date cast);
  belongs to `studyMaterial()` (required) and optionally to `summary()` (`summary_id`, nullable,
  `nullOnDelete`)
- No `custom_attributes` columns and no partner module: the study module uses neither
  `noerd/customer` nor `noerd/party`

### Structure
- Livewire components (flat, `resources/views/components/`, namespace `study::`):
  `dashboard` (count cards per tenant; sets the selected app to `STUDY` in `mount()`),
  `study-materials-list` + `study-material-detail` (slim; the detail calls
  `setPreselect('study_material_id', $modelId)` so summaries/flashcards created from it are
  prefilled), `summaries-list` + `summary-detail`, `flashcards-list` + `flashcard-detail`,
  `flashcard-print-page` (checkbox selection of at most 8 flashcards, then a redirect to the PDF
  route). Detail components keep the hard-coded `validate()` of their `store()` (question /
  title + `exists:study_materials,id`) — assert those fields explicitly in tests
- Narrowed lists: `summaries-list` and `flashcards-list` accept `studyMaterialId`; both override
  `listAction()` with `Noerd::modalFor('{route}', '{component}', ['modelId' => …, 'relations' => ['study_material_id' => …]])`
  so a record created from the narrowed list belongs to that material. The details read
  `$this->relations['study_material_id']` in `mount()` and call `preselect('study_material_id')`
- Relation field types registered in `StudyServiceProvider`: `studyMaterialRelation` (event
  `studyMaterialSelected`) and `summaryRelation` (event `summarySelected`), both with
  `titleResolver: 'title'`; `flashcards-list` adds the `StudyMaterialFilterTrait` picklist filter
  (`study_material_id`)
- PDF: `FlashcardPrintController::print()` (`barryvdh/laravel-dompdf`, view
  `study::pdf.flashcards`, A4, inline response) takes `flashcard_ids[]`, scopes them to the
  selected tenant and restores the selection order in PHP — never with MySQL `FIELD()`, the suite
  also runs on SQLite. The provider creates `storage/fonts` for dompdf on boot
- YAML: `app-configs/study/{lists,details}/` + `navigation.yml` — keep the module copy and the
  installed project copy (`app-configs/study/…`) in sync
- Routes: `routes/study-routes.php` — middleware `['noerd', 'app-access:study']`, names
  `study.dashboard`, `study.study-materials`, `study.summaries`, `study.flashcards` (lists),
  `study.study-material.detail`, `study.summary.detail`, `study.flashcard.detail` (record routes
  used by route modals), `study.flashcards-print` (page) and `study.flashcards-print.pdf`
- Tenant app name is `STUDY` (uppercase) — gates and test traits compare exactly. The tenant-app
  row is registered twice: by the module migration `add_study_tenant_app` (raw MySQL `INSERT …
  SELECT … NOW()` wrapped in try/catch — it only logs on other drivers) and by the stub
  `app-configs/stubs/add_study_tenant_app.php.stub` published by `noerd:install-study`
- App icon: Blade icon `resources/views/components/icons/app.blade.php` (`study::icons.app`)
- Translations: `resources/lang/de.json` (English keys)
- `database/seeders/StudyTestDataSeeder.php` seeds demo materials, summaries and flashcards for
  the selected tenant (falls back to the first tenant)

### Commands
- `php artisan noerd:install-study` — installs YAML configs, registers the tenant app, runs
  migrations; afterwards `registerModule()` shells out to `composer require noerd/study` /
  `composer dump-autoload` and clears the config/cache/services caches
- `php artisan noerd:update-study` — idempotent update of the YAML configs (picked up by
  `noerd:update-all`)

### Tests
- Pest tests in `tests/Feature` and `tests/Components`, bound to `Tests\TestCase` +
  `RefreshDatabase` (host-bound, MySQL); run with `php artisan test --compact app-modules/study/tests`
- `tests/Traits/CreatesStudyUser.php` provides `withStudyModule()` (tenant with the `STUDY` app
  attached, user, `TenantHelper` selection) — the routes sit behind `app-access:study`, so the app
  assignment is mandatory
- Prove mechanics, never the current YAML configuration (see the `noerd-testing` skill)

### Reference implementations
- `flashcards-list.blade.php` — a narrowed list (`studyMaterialId`) with a filter trait and a
  `listAction()` override that hands `relations` to the detail
- `flashcard-detail.blade.php` — two relation fields with `*Selected` handlers, `relations`
  preselection and a data-dependent default (`created_date`) set in `mount()`
- `src/Http/Controllers/FlashcardPrintController.php` — tenant-scoped PDF generation
@endverbatim
