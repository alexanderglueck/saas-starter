<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $accessEntity = null;

    protected function can($right)
    {
        abort_unless(Auth::user()->can($right . ' ' . $this->accessEntity), 403, trans('ui.access_denied'));
    }

    protected function isImpersonating()
    {
        return session()->has('impersonate');
    }
}
