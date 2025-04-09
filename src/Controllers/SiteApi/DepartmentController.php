<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Lsshu\Site\Api\Models\SystemDepartment;

class DepartmentController extends SiteApiController
{
    protected string $model = SystemDepartment::class;
}
