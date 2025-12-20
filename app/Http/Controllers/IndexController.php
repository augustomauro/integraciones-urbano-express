<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $htmlContent = file_get_contents(public_path('frontend/index.html'));
        return response($htmlContent, 200)->header('Content-Type', 'text/html');
    }
}
