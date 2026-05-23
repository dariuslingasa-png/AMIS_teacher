<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class EnrollmentController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.applications.dashboard');
    }
}
