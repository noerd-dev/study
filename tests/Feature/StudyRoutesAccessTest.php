<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\NoerdUser;
use Noerd\Models\Tenant;
use Noerd\Models\TenantApp;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class, CreatesStudyUser::class);

it('redirects guests to the login', function (): void {
    $this->get('/study/study-materials')->assertRedirect(route('noerd.login'));
});

it('rejects a tenant without the STUDY app', function (): void {
    $user = NoerdUser::factory()->create();
    $tenant = Tenant::factory()->create();
    $user->tenants()->attach($tenant->id);
    $user->update(['selected_tenant_id' => $tenant->id]);

    // The STUDY app exists but is NOT assigned to this tenant.
    TenantApp::firstOrCreate(
        ['name' => 'STUDY'],
        ['title' => 'Study', 'icon' => 'study::icons.app', 'route' => 'study.dashboard', 'is_active' => true],
    );

    $this->actingAs($user, 'noerd');

    $this->get('/study/study-materials')->assertStatus(403);
});

it('lets a tenant with the STUDY app open the study materials list', function (): void {
    $user = $this->withStudyModule();
    $tenant = $user->tenants()->firstOrFail();
    $user->update(['selected_tenant_id' => $tenant->id]);

    $this->actingAs($user, 'noerd');

    $this->get('/study/study-materials')
        ->assertOk()
        ->assertSeeLivewire('study::study-materials-list');
});
