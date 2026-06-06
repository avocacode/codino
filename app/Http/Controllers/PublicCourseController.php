<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)->get();
        return view('welcome', compact('courses'));
    }

    public function show($slug)
    {
        $course = Course::with(['modules.lessons.sources'])->where('slug', $slug)->firstOrFail();
        return view('course_detail', compact('course'));
    }
}
