<?php

namespace Nywerk\Study\Tests\Traits;

use Noerd\Helpers\TenantHelper;
use Noerd\Models\NoerdUser;
use Noerd\Models\Tenant;
use Noerd\Models\TenantApp;

trait CreatesStudyUser
{
    protected function withStudyModule(): NoerdUser
    {
        $user = NoerdUser::factory()->create();
        $tenant = Tenant::factory()->create();
        $user->tenants()->attach($tenant->id);

        // The study routes run behind `app-access:study`, which only lets the
        // request through when the selected tenant has the app assigned.
        $studyApp = TenantApp::query()->firstOrCreate(
            ['name' => 'STUDY'],
            [
                'title' => 'Study',
                'icon' => 'study::icons.app',
                'route' => 'study.dashboard',
                'is_active' => true,
            ],
        );

        $tenant->tenantApps()->attach($studyApp->id);

        TenantHelper::setSelectedTenantId($tenant->id);
        TenantHelper::setSelectedApp('STUDY');

        return $user;
    }
}
