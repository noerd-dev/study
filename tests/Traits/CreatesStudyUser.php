<?php

namespace Nywerk\Study\Tests\Traits;

use Noerd\Helpers\TenantHelper;
use Noerd\Models\NoerdUser;
use Noerd\Models\Tenant;

trait CreatesStudyUser
{
    protected function withStudyModule(): NoerdUser
    {
        $user = NoerdUser::factory()->create();
        $tenant = Tenant::factory()->create();
        $user->tenants()->attach($tenant->id);

        TenantHelper::setSelectedTenantId($tenant->id);
        TenantHelper::setSelectedApp('STUDY');

        return $user;
    }
}
