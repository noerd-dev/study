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
- Livewire components (flat, `resources/views/components/`, namespace `study::` — the provider
  extends `NoerdModuleServiceProvider`, no bare component names; always reference
  `study::{name}`): `dashboard` (a `NoerdPage` with count cards; the route's `app-access:study`
  selects the app, the dashboard never does),
  `study-materials-list` + `study-material-detail` (slim; the detail calls
  `setPreselect('study_material_id', $modelId)` so summaries/flashcards created from it are
  prefilled), `summaries-list` + `summary-detail`, `flashcards-list` + `flashcard-detail`,
  `flashcard-print-page` (checkbox selection of at most 8 flashcards, then a redirect to the PDF
  route). Detail components keep the hard-coded `validate()` of their `store()` (question /
  title + `exists:study_materials,id`) — assert those fields explicitly in tests
- Narrowed lists: `summaries-list` and `flashcards-list` accept `studyMaterialId` and narrow
  through `$listScopes = ['study_material_id' => 'studyMaterialId']` (eager loads in
  `$listWith`); both override
  `listAction()` with `Noerd::modalFor('{route}', '{component}', ['modelId' => …, 'relations' => ['study_material_id' => …]])`
  so a record created from the narrowed list belongs to that material. The details read
  `$this->relations['study_material_id']` in `mount()` and call `preselect('study_material_id')`.
  The relation fields keep `detailData` and their titles in sync themselves — the details have
  no `*Selected` handlers; the default `created_date` of a flashcard is YAML (`default: today`)
- Relation field types registered in `StudyServiceProvider`: `studyMaterialRelation` (event
  `studyMaterialSelected`) and `summaryRelation` (event `summarySelected`), both with
  `titleResolver: 'title'`; `flashcards-list` adds the `StudyMaterialFilterTrait` header filter
  (`getStudyMaterialsListFilter()`, a `ListFilter::model()` on `study_material_id`)
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
  row is registered by the stub `app-configs/stubs/add_study_tenant_app.php.stub`, published into the host by `noerd:install-study` (and by `noerd:update-study` when the host has none yet), so a deployment that only runs `php artisan migrate` registers the app. The module ships no registering migration of its own
- App icon: Blade icon `resources/views/components/icons/app.blade.php` (`study::icons.app`)
- Translations: `resources/lang/de.json` (English keys)
- `database/seeders/StudyTestDataSeeder.php` seeds demo materials, summaries and flashcards for
  the selected tenant (falls back to the first tenant)

### Commands
- `php artisan noerd:install-study` — installs YAML configs, registers the tenant app, offers the
  migrations (the plain `runModuleInstallation()` flow, nothing else)
- `php artisan noerd:update-study` — idempotent update of the YAML configs (picked up by
  `noerd:update-all`)

### Tests
- Pest tests in `tests/Feature` and `tests/Components`, bound to `Tests\TestCase` +
  `RefreshDatabase` (host-bound, MySQL); run with `php artisan test --compact app-modules/study/tests`
- The user fixture is the core's `createNoerdUserForApp('STUDY')` / `actingAsNoerdUserForApp('STUDY')`
  (tenant with the `STUDY` app assigned, user, `TenantHelper` selection) — the routes sit behind
  `app-access:study`, so the app assignment is mandatory. The module has no test trait of its own
- Prove mechanics, never the current YAML configuration (see the `noerd-testing` skill)

### Reference implementations
- `flashcards-list.blade.php` — a narrowed list (`studyMaterialId`) with a filter trait and a
  `listAction()` override that hands `relations` to the detail
- `flashcard-detail.blade.php` — two relation fields, `relations` preselection and the session
  preselect (`preselect('study_material_id')`) without a `*Selected` handler
- `src/Http/Controllers/FlashcardPrintController.php` — tenant-scoped PDF generation
@endverbatim
