<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'grade_level',
        'learning_mode',
        'shift',
        'gender',
        'ms_team_id',
        'ms_team_url',
    ];

    public function subjects()
    {
        return $this->hasMany(SectionSubject::class);
    }

    public function students()
    {
        return $this->hasMany(StudentSection::class);
    }

    public function getOfficialNameAttribute(): ?string
    {
        if (! str_contains((string) $this->learning_mode, 'Flexible')) {
            return null;
        }

        $key = implode('|', [
            $this->grade_level,
            $this->shift,
            $this->gender,
        ]);

        $names = [
            'Kinder 2|1st Shift|male' => 'ABU BAKR AS-SIDDEEQ',
            'Kinder 2|1st Shift|female' => 'UTHMAN IBN AFFAN',
            'Grade 1|1st Shift|female' => 'ALI IBN ABI TALIB',
            'Grade 1|1st Shift|male' => 'HUDHAYFAH IBN AL-YAMAN',
            'Grade 2|1st Shift|female' => 'TALHAH IBN UBAYDULLAH',
            'Grade 2|1st Shift|male' => 'AMR IBN AL-JAMUH',
            'Grade 3|1st Shift|male' => 'AMMAR IBN YASIR',
            'Grade 3|1st Shift|female' => 'HABIB IBN ZAYD AL-ANSARI',
            'Grade 4|1st Shift|male' => 'ABDUR RAHMAN IBN AWF',
            'Grade 4|1st Shift|female' => 'HAKIM IBN HIZAM',
            'Grade 5|1st Shift|male' => 'MUHAMMAD IBN MASLAMAH',
            'Grade 5|1st Shift|female' => 'HAMZA IBN ABDUL-MUTTALIB',
            'Grade 6|1st Shift|female' => 'ABDULLAH IBN SALAM',
            'Grade 6|1st Shift|male' => 'ABBAS IBN ABD AL-MUTTALIB',
            'Grade 7|1st Shift|female' => 'USAMA IBN ZAYD',
            'Grade 7|1st Shift|male' => 'ABU SUFYAN IBN AL-HARITH',
            'Grade 8|1st Shift|female' => "SA'AD IBN MU'ADH",
            'Grade 9|1st Shift|female' => 'ABU HURAYRAH',
            'Grade 10|1st Shift|female' => 'UTBAH IBN GHAZWAN',
            'Grade 11|1st Shift|female' => 'ABU UBAYDAH IBN AL-JARRAH',
            'Grade 12|1st Shift|female' => "ABU MUSA AL-ASH'ARI",
            'Kinder 1|2nd Shift|male' => 'HUSAYN IBN ALI',
            'Kinder 2|2nd Shift|female' => "ABDULLAH IBN MAS'UD",
            'Kinder 2|2nd Shift|male' => 'UMAR IBN AL-KHATTAB',
            'Grade 1|2nd Shift|male' => 'SUHAYB AR-RUMI',
            'Grade 1|2nd Shift|female' => "SA'D IBN ABI WAQQAS",
            'Grade 2|2nd Shift|male' => 'SAEED IBN ZAYD',
            'Grade 2|2nd Shift|female' => 'ASIM IBN THABIT',
            'Grade 3|2nd Shift|female' => 'ZAYD IBN HARITHA',
            'Grade 3|2nd Shift|male' => 'THABIT IBN QAYS',
            'Grade 4|2nd Shift|male' => 'IKRIMAH IBN ABI JAHL',
            'Grade 4|2nd Shift|female' => 'AZ-ZUBAIR IBN AL AWWAM',
            'Grade 5|2nd Shift|male' => "MUS'AB IBN UMAIR",
            'Grade 6|2nd Shift|male' => 'KHALID IBN WALID',
            'Grade 7|2nd Shift|male' => 'ANAS IBN MALIK',
            'Grade 8|2nd Shift|male' => "MU'ADH IBN JABAL",
            'Grade 8|2nd Shift|female' => "NU'AYM IBN MAS'UD",
            'Grade 9|2nd Shift|male' => 'ABU DHARR AL-GHIFARI',
            'Grade 10|2nd Shift|male' => 'ABU AYYUB AL-ANSARI',
            'Grade 11|2nd Shift|male' => 'ABU UBAY IBN HATIM',
            'Grade 12|2nd Shift|male' => 'SUHAYB AR-RUMI',
        ];

        return $names[$key] ?? null;
    }

    public function getSectionTitleAttribute(): string
    {
        $name = $this->official_name ?: ($this->name ?? 'Unnamed');

        return "{$this->grade_level} - {$name}";
    }

    /** Human-readable label */
    public function getDisplayNameAttribute(): string
    {
        $grade  = $this->grade_level;
        $name   = $this->official_name ?: ($this->name ?? 'Unnamed');
        $shift  = $this->shift ?? 'F2F';
        $gender = ucfirst($this->gender === 'male' ? 'Boys' : 'Girls');
        $year   = $this->school_year;
        return "{$grade} - {$name} {$shift} {$gender} {$year}";
    }
}
