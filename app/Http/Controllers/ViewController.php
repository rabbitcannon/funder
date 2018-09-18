<?php

namespace App\Http\Controllers;

use Eos\Common\Setting;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index() {
        $url = Setting::get('eos.services.interactivecore.connections.outbound.url');
        return view('index', ['url' => $url]);
    }
}
