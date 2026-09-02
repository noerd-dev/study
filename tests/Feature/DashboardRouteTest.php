<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Helpers\TenantHelper;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

beforeEach(function (): void {
    $this->user = $this->withStudyModule();
    $this->actingAs($this->user);
});

it('loads the dashboard via direct route', function (): void {
    $this->get('/study')
        ->assertSuccessful()
        ->assertSeeLivewire('study::dashboard');
});

it('selects the study app when entering through a deep link', function (): void {
    TenantHelper::setSelectedApp(null);

    $this->get('/study')->assertSuccessful();

    expect(TenantHelper::getSelectedApp())->toBe('STUDY');
});
