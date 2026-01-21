<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function classes()
    {
        return view('admin.classes');
    }

    public function syllabus()
    {
        return view('admin.syllabus');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function activity()
    {
        return view('admin.activity');
    }
}
