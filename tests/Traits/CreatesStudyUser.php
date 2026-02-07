<?php

namespace Nywerk\Study\Tests\Traits;

use Noerd\Helpers\TenantHelper;
use Noerd\Models\Tenant;
use Noerd\Models\User;

trait CreatesStudyUser
{
    protected function withStudyModule(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();
        $user->tenants()->attach($tenant->id);

        TenantHelper::setSelectedTenantId($tenant->id);
        TenantHelper::setSelectedApp('STUDY');

        return $user;
    }
}
