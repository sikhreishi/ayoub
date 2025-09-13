<?php

namespace App\Http\Controllers\Admin\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{

    public function switchLang(Request $request)
    {
        $request->validate([
            'lang' => 'required|in:en,ar', 
        ]);

        session(['lang' => $request->lang]);
        session(['dir' => $request->lang == 'ar' ? 'rtl' : 'ltr']);

        App::setLocale($request->lang);

        return response()->json(['status' => 'ok']);
    }
}
