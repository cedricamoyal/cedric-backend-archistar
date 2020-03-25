<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Analytic;

class AnalyticController extends Controller
{
    public function index()
    {
        return Analytic::all();
    }
}
