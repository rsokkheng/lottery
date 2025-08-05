<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class LanguageController extends BaseController
{
    session(['locale' => $lang]);
    App::setLocale($lang);
    return redirect()->back();
    
}
