<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $pendingEnrollments = Enrollment::with(['user', 'course'])->where('payment_status', 'pending')->get();
        $activeEnrollments = Enrollment::with(['user', 'course'])->where('payment_status', 'paid')->get();

        return view('admin.dashboard', compact('totalStudents', 'totalCourses', 'totalEnrollments', 'pendingEnrollments', 'activeEnrollments'));
    }

    public function students()
    {
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        return view('admin.students', compact('students'));
    }

    public function approveEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update(['payment_status' => 'paid']);
        return redirect()->back()->with('success', 'Pendaftaran kelas berhasil disetujui!');
    }

    public function rejectEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update(['payment_status' => 'cancelled']);
        return redirect()->back()->with('success', 'Pendaftaran kelas ditolak/dibatalkan.');
    }
}
