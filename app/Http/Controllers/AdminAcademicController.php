<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject as SubjectModel;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminAcademicController extends Controller
{
    public function subjects()
    {
        $subjects = SubjectModel::orderBy('grade_level')->get();
        if ($subjects->isEmpty()) {
            // Seed sample subjects
            $samples = [
                ['name' => 'Makabansa', 'code' => 'MKB1', 'grade_level' => 'Grade 1'],
                ['name' => 'GMRC', 'code' => 'GMRC1', 'grade_level' => 'Grade 1'],
                ['name' => 'Language', 'code' => 'LANG1', 'grade_level' => 'Grade 1'],
                ['name' => 'Reading & Literacy', 'code' => 'READ1', 'grade_level' => 'Grade 1'],
                ['name' => 'Mathematics', 'code' => 'MATH1', 'grade_level' => 'Grade 1'],
                ['name' => 'Qur’an', 'code' => 'QURAN1', 'grade_level' => 'Grade 1'],
                ['name' => 'Arabic', 'code' => 'ARAB1', 'grade_level' => 'Grade 1'],
                ['name' => 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)', 'code' => 'SHAF1', 'grade_level' => 'Grade 1'],
                ['name' => 'Science', 'code' => 'SCI3', 'grade_level' => 'Grade 3'],
                ['name' => 'English', 'code' => 'ENG2', 'grade_level' => 'Grade 2'],
                ['name' => 'Filipino', 'code' => 'FIL2', 'grade_level' => 'Grade 2'],
            ];
            foreach ($samples as $s) {
                SubjectModel::create(array_merge($s, ['school_year' => '2026-2027']));
            }
            $subjects = SubjectModel::orderBy('grade_level')->get();
        }
        return view('admin.academic.subjects', compact('subjects'));
    }

    public function curriculum()
    {
        return view('admin.academic.curriculum');
    }

    public function gradeLevels()
    {
        $sections = Section::withCount('students')->get();
        return view('admin.academic.grade-levels', compact('sections'));
    }

    public function teachers()
    {
        $teachers = [
            ['name' => 'Ust. Raffy Lingasa', 'email' => 'tr.rlingasa@amis.edu.ph', 'dept' => 'Arabic & Islamic Studies', 'sections' => 'Grade 1 - HUDHAYFAH', 'status' => 'Active'],
            ['name' => 'Tchr. Wendy Monlingasa', 'email' => 'tr.wmonlingasa@amis.edu.ph', 'dept' => 'Elementary Academics', 'sections' => 'Grade 2 - TALHAH', 'status' => 'Active'],
            ['name' => 'Ust. Ahmad Al-Jamil', 'email' => 'tr.ajamil@amis.edu.ph', 'dept' => 'Arabic & Islamic Studies', 'sections' => 'Grade 3 - AMMAR', 'status' => 'Active'],
            ['name' => 'Tchr. Sarah Balabagan', 'email' => 'tr.sbalabagan@amis.edu.ph', 'dept' => 'Elementary Academics', 'sections' => 'Grade 4 - ABDUR RAHMAN', 'status' => 'Active'],
            ['name' => 'Ust. Omar Mukhtar', 'email' => 'tr.omukhtar@amis.edu.ph', 'dept' => 'Arabic & Islamic Studies', 'sections' => 'Grade 5 - MUHAMMAD', 'status' => 'Inactive'],
        ];
        return view('admin.academic.teachers', compact('teachers'));
    }

    public function schedules()
    {
        $sections = Section::all();
        return view('admin.academic.schedules', compact('sections'));
    }

    public function schoolYears()
    {
        $schoolYears = [
            ['year' => '2024-2025', 'semester' => '2nd Semester', 'status' => 'Completed', 'enrolled' => 84],
            ['year' => '2025-2026', 'semester' => '1st Semester', 'status' => 'Active', 'enrolled' => 99],
            ['year' => '2026-2027', 'semester' => '1st Semester', 'status' => 'Upcoming', 'enrolled' => 0],
        ];
        return view('admin.academic.school-years', compact('schoolYears'));
    }

    public function calendar()
    {
        $events = [
            ['date' => '2026-06-01', 'title' => 'Start of Regular Enrollment', 'type' => 'Enrollment'],
            ['date' => '2026-06-15', 'title' => 'First Day of Classes (SY 2026-2027)', 'type' => 'Academic'],
            ['date' => '2026-07-20', 'title' => 'Islamic New Year (1 Muharram) - Holiday', 'type' => 'Holiday'],
            ['date' => '2026-08-10', 'title' => 'Preliminary Examinations', 'type' => 'Exam'],
            ['date' => '2026-09-18', 'title' => 'Maulidur Rasul - Holiday', 'type' => 'Holiday'],
            ['date' => '2026-10-12', 'title' => 'Midterm Examinations', 'type' => 'Exam'],
        ];
        return view('admin.academic.calendar', compact('events'));
    }
}
