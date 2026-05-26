<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject as SubjectModel;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminAcademicController extends Controller
{
    public function dashboard()
    {
        $subjects = SubjectModel::orderBy('grade_level')->get();
        $sections = Section::withCount('students')->get();

        $elementaryGrades = ['Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $highSchoolGrades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $allGrades = array_merge($elementaryGrades, $highSchoolGrades);

        $academicStats = [
            'subjects' => $subjects->count(),
            'sections' => $sections->count(),
            'students' => $sections->sum('students_count'),
            'school_year' => '2026-2027',
        ];

        $academicCharts = [
            'subjectDivision' => [
                'labels' => ['Elementary', 'High School'],
                'data' => [
                    $subjects->whereIn('grade_level', $elementaryGrades)->count(),
                    $subjects->whereIn('grade_level', $highSchoolGrades)->count(),
                ],
            ],
            'sectionMode' => [
                'labels' => ['Face to Face', 'Flexible - 1st Shift', 'Flexible - 2nd Shift'],
                'data' => [
                    $sections->filter(fn ($section) => str_contains(strtolower((string) $section->learning_mode), 'face') || strtoupper((string) $section->shift) === 'F2F')->count(),
                    $sections->filter(fn ($section) => str_contains(strtolower((string) $section->learning_mode), 'flexible') && str_contains(strtolower((string) $section->shift), '1'))->count(),
                    $sections->filter(fn ($section) => str_contains(strtolower((string) $section->learning_mode), 'flexible') && str_contains(strtolower((string) $section->shift), '2'))->count(),
                ],
            ],
            'gradeSubjects' => [
                'labels' => $allGrades,
                'data' => collect($allGrades)->map(fn ($grade) => $subjects->where('grade_level', $grade)->count())->values(),
            ],
            'gradeSections' => [
                'labels' => $allGrades,
                'data' => collect($allGrades)->map(fn ($grade) => $sections->where('grade_level', $grade)->count())->values(),
            ],
        ];

        return view('admin.academic.dashboard', compact('academicStats', 'academicCharts'));
    }

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
        $sections = Section::withCount('students')->get();
        $schoolYears = [
            ['year' => '2024-2025', 'semester' => '2nd Semester', 'status' => 'Completed', 'enrolled' => 84],
            ['year' => '2025-2026', 'semester' => '1st Semester', 'status' => 'Active', 'enrolled' => 99],
            ['year' => '2026-2027', 'semester' => '1st Semester', 'status' => 'Upcoming', 'enrolled' => 0],
        ];
        $events = [
            ['date' => '2026-06-01', 'title' => 'Start of Regular Enrollment', 'type' => 'Enrollment'],
            ['date' => '2026-06-15', 'title' => 'First Day of Classes (SY 2026-2027)', 'type' => 'Academic'],
            ['date' => '2026-07-20', 'title' => 'Islamic New Year (1 Muharram) - Holiday', 'type' => 'Holiday'],
            ['date' => '2026-08-10', 'title' => 'Preliminary Examinations', 'type' => 'Exam'],
            ['date' => '2026-09-18', 'title' => 'Maulidur Rasul - Holiday', 'type' => 'Holiday'],
            ['date' => '2026-10-12', 'title' => 'Midterm Examinations', 'type' => 'Exam'],
        ];
        return view('admin.academic.curriculum', compact('subjects', 'sections', 'schoolYears', 'events'));
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
        $teachers = [
            ['name' => 'Ust. Raffy Lingasa', 'email' => 'tr.rlingasa@amis.edu.ph', 'dept' => 'Arabic & Islamic Studies', 'sections' => 'Grade 1 - HUDHAYFAH', 'status' => 'Active'],
            ['name' => 'Tchr. Wendy Monlingasa', 'email' => 'tr.wmonlingasa@amis.edu.ph', 'dept' => 'Elementary Academics', 'sections' => 'Grade 2 - TALHAH', 'status' => 'Active'],
            ['name' => 'Ust. Ahmad Al-Jamil', 'email' => 'tr.ajamil@amis.edu.ph', 'dept' => 'Arabic & Islamic Studies', 'sections' => 'Grade 3 - AMMAR', 'status' => 'Active'],
            ['name' => 'Tchr. Sarah Balabagan', 'email' => 'tr.sbalabagan@amis.edu.ph', 'dept' => 'Elementary Academics', 'sections' => 'Grade 4 - ABDUR RAHMAN', 'status' => 'Active'],
            ['name' => 'Ust. Omar Mukhtar', 'email' => 'tr.omukhtar@amis.edu.ph', 'dept' => 'Arabic & Islamic Studies', 'sections' => 'Grade 5 - MUHAMMAD', 'status' => 'Inactive'],
        ];
        return view('admin.academic.schedules', compact('sections', 'teachers'));
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

    public function operations()
    {
        $attendance = [
            'rate' => 96.4,
            'present' => 95,
            'absent' => 3,
            'excused' => 1,
            'by_grade' => [
                'Kinder 1 & 2' => 98.2, 'Grade 1' => 96.8, 'Grade 2' => 95.9,
                'Grade 3' => 97.1, 'Grade 4' => 96.2, 'Grade 5' => 94.8
            ]
        ];

        $grades = [
            'submitted' => 8,
            'pending' => 4,
            'total' => 12,
            'sections' => [
                ['name' => 'Grade 1 - HUDHAYFAH', 'status' => 'Submitted', 'date' => 'May 22, 2026'],
                ['name' => 'Grade 2 - TALHAH', 'status' => 'Submitted', 'date' => 'May 23, 2026'],
                ['name' => 'Grade 3 - AMMAR', 'status' => 'Pending', 'date' => '—'],
                ['name' => 'Grade 4 - ABDUR RAHMAN', 'status' => 'Pending', 'date' => '—'],
            ]
        ];

        $reports = [
            ['name' => 'Registrar Master List', 'format' => 'PDF', 'size' => '2.4 MB', 'date' => 'May 25, 2026'],
            ['name' => 'MS Teams Sync Performance Audit', 'format' => 'EXCEL', 'size' => '450 KB', 'date' => 'May 24, 2026'],
            ['name' => 'Grade Distribution Summary Report', 'format' => 'PDF', 'size' => '1.2 MB', 'date' => 'May 21, 2026'],
        ];

        return view('admin.academic.operations', compact('attendance', 'grades', 'reports'));
    }
}
