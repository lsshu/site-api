<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Lsshu\Site\Api\Models\SystemTenants;

class TenantsController extends SiteApiController
{
    protected string $model = SystemTenants::class;
}
