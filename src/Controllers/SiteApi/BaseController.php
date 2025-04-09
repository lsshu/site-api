<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as _BaseController;

class BaseController extends _BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
