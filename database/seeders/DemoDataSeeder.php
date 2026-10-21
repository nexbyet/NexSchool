<?php

namespace Database\Seeders;

use App\Helpers\DateTextHelper;
use App\Models\AcademicYear;
use App\Models\ActivityPlan;
use App\Models\Attendance;
use App\Models\BusAttendance;
use App\Models\BusOnlyFeePayment;
use App\Models\BusOnlyStudent;
use App\Models\ClassTeacher;
use App\Models\ExamMark;
use App\Models\ExamPattern;
use App\Models\ExamPatternApply;
use App\Models\ExamPatternHead;
use App\Models\FeeCarryForward;
use App\Models\FeeHead;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\GradeScale;
use App\Models\GradeSlab;
use App\Models\PublicHoliday;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Setting;
use App\Models\Standard;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentRoute;
use App\Models\Subject;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\TimetableSlot;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Testing / demo data seeder.
 *
 * Run manually:  php artisan db:seed --class=DemoDataSeeder
 *
 * It is safe to re-run: if demo students already exist it simply stops.
 * Never run migrate:fresh — this seeder only inserts.
 */
class DemoDataSeeder extends Seeder
{
    private AcademicYear $currentYear;

    private AcademicYear $previousYear;

    /** @var array<int, Standard> keyed by sort order (1..8) */
    private array $standards = [];

    /** @var array<string, SchoolClass> keyed by "std-class" e.g. "1-A" */
    private array $classes = [];

    /** @var array<int, Subject> keyed by array index (0..6) */
    private array $subjects = [];

    /** @var array<int, Teacher> */
    private array $teachers = [];

    /** @var array<string, array<int, float>> head amounts for ["1-1"] => [headId => amount] */
    private array $structureTotals = [];

    public function run(): void
    {
        if (Student::exists() || Standard::exists()) {
            $this->command->warn('Demo data already present — DemoDataSeeder skipped (no duplicates created).');

            return;
        }

        DB::transaction(function () {
            $this->seedSchoolSetting();
            $this->seedDropdowns();
            $this->seedAcademicYears();
            $this->seedStandardsAndClasses();
            $this->seedSubjects();
            $this->seedTeachers();
            $this->seedClassTeachers();
            $this->seedSubjectAssignments();
            $this->seedStudents();
            $this->seedAttendance();
            $this->seedTimetable();
            $this->seedFees();
            $this->seedGrades();
            $this->seedExams();
            $this->seedTransport();
            $this->seedBusOnlyStudents();
            $this->seedCalendar();
        });

        $this->command->info('Demo data seeded successfully.');
        $this->command->line('  Admin    : admin@nexschool.com / password');
        $this->command->line('  Teacher  : ramesh.patel@nexschool.com / Teacher@123');
        $this->command->line('  Student  : username = GR number, password = DOB (d/m/Y)');
        $this->command->line('  Parent   : username = mobile, password = Parent@123');
        $this->command->line('  Bus only : no login (admin > bus only students)');
    }

    // ------------------------------------------------------------------
    // School / year / classes
    // ------------------------------------------------------------------

    private function seedSchoolSetting(): void
    {
        $secret = config('license.hmac_secret');
        $payload = '*|2099-12-31|all';
        $licenseKey = base64_encode($payload . '||' . hash_hmac('sha256', $payload, $secret));

        SchoolSetting::create([
            'school_name_gu' => 'શ્રી સરસ્વતી વિદ્યાલય',
            'school_name_en' => 'Shri Saraswati Vidyalaya',
            'management_name_gu' => 'શ્રી સરસ્વતી શિક્ષણ ટ્રસ્ટ',
            'management_name_en' => 'Shri Saraswati Education Trust',
            'address' => 'સ્ટેશન રોડ, આનંદ - ૩૮૮૦૦૧, ગુજરાત',
            'grant_number' => 'GR-AN-2026/1842',
            'grant_date' => '2026-04-01',
            'uid_number' => '24123456789',
            'email' => 'info@shrisaraswati.edu.in',
            'mobile' => '9825012345',
            'whatsapp' => '9825012345',
            'website' => 'https://shrisaraswati.example.com',
            'student_default_sort' => ['student_name_en', 'father_name_en', 'surname_en'],
            'footer_credits' => 'Powered by NexSchool',
            'copyright_text' => '© 2026 શ્રી સરસ્વતી વિદ્યાલય',
            'license_key' => $licenseKey,
            'licensee_name' => 'Shri Saraswati Vidyalaya (Demo)',
            'licensed_until' => '2099-12-31',
            'license_status' => 'active',
            'app_version' => '1.1.7',
        ]);
    }

    private function seedDropdowns(): void
    {
        Setting::create([
            'key' => 'gender_options',
            'value' => json_encode([
                ['label' => 'કુમાર', 'value' => 'kumar'],
                ['label' => 'કુમારી', 'value' => 'kumari'],
            ], JSON_UNESCAPED_UNICODE),
            'group' => 'admission',
        ]);

        Setting::create([
            'key' => 'blood_group_options',
            'value' => json_encode([
                ['label' => 'A+', 'value' => 'A+'],
                ['label' => 'A-', 'value' => 'A-'],
                ['label' => 'B+', 'value' => 'B+'],
                ['label' => 'B-', 'value' => 'B-'],
                ['label' => 'O+', 'value' => 'O+'],
                ['label' => 'O-', 'value' => 'O-'],
                ['label' => 'AB+', 'value' => 'AB+'],
                ['label' => 'AB-', 'value' => 'AB-'],
            ], JSON_UNESCAPED_UNICODE),
            'group' => 'admission',
        ]);
    }

    private function seedAcademicYears(): void
    {
        $this->previousYear = AcademicYear::create([
            'year' => '2025-26',
            'start_date' => '2025-06-01',
            'end_date' => '2026-04-30',
            'session_start_date' => '2025-06-16',
            'is_active' => false,
        ]);

        $this->currentYear = AcademicYear::create([
            'year' => '2026-27',
            'start_date' => '2026-06-01',
            'end_date' => '2027-04-30',
            'session_start_date' => '2026-06-15',
            'is_active' => true,
        ]);
    }

    private function seedStandardsAndClasses(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            $standard = Standard::create([
                'name' => 'ધોરણ ' . $i,
                'sort_order' => $i,
            ]);
            $this->standards[$i] = $standard;

            foreach (['A', 'B'] as $sectionIndex => $section) {
                $class = SchoolClass::create([
                    'name' => $section,
                    'section' => $section,
                    'standard_id' => $standard->id,
                    'academic_year' => $this->currentYear->year,
                    'room_number' => 'R' . ($i * 10 + $sectionIndex + 1),
                    'capacity' => 40,
                    'sort_order' => $sectionIndex + 1,
                    'status' => 'active',
                ]);
                $this->classes[$i . '-' . $section] = $class;
            }
        }
    }

    private function seedSubjects(): void
    {
        $definitions = [
            ['ગુજરાતી', 'GUJ', 100],
            ['અંગ્રેજી', 'ENG', 100],
            ['ગણિત', 'MAT', 100],
            ['વિજ્ઞાન', 'SCI', 100],
            ['સામાજિક વિજ્ઞાન', 'SOC', 100],
            ['હિન્દી', 'HIN', 100],
            ['કોમ્પ્યુટર', 'COM', 50],
        ];

        foreach ($definitions as $index => [$name, $code, $total]) {
            $subject = Subject::create([
                'name' => $name,
                'code' => $code,
                'description' => $name . ' વિષય',
                'credit_hours' => $total === 50 ? 2 : 4,
                'pass_mark' => (int) round($total * 0.33),
                'total_mark' => $total,
                'status' => 'active',
            ]);
            $this->subjects[$index] = $subject;

            foreach ($this->standards as $standard) {
                $standard->subjects()->attach($subject->id, ['sort_order' => $index + 1]);
            }
        }
    }

    // ------------------------------------------------------------------
    // Teachers
    // ------------------------------------------------------------------

    private function seedTeachers(): void
    {
        $definitions = [
            ['પટેલ રમેશભાઈ', 'Rameshbhai Patel', 'ramesh.patel@nexschool.com', 'male', 'બી.એ., બી.એડ.', 'ગુજરાતી', '1985-04-12', '2015-06-01', 11, 32000],
            ['દેસાઈ નીતાબેન', 'Nitaben Desai', 'nita.desai@nexschool.com', 'female', 'એમ.એ., બી.એડ.', 'અંગ્રેજી', '1988-09-23', '2016-06-10', 10, 31000],
            ['શાહ જીગ્નેશભાઈ', 'Jigneshbhai Shah', 'jignesh.shah@nexschool.com', 'male', 'બી.એસસી., બી.એડ.', 'ગણિત', '1984-01-15', '2014-06-15', 12, 34000],
            ['જોષી હેતલબેન', 'Hetalben Joshi', 'hetal.joshi@nexschool.com', 'female', 'એમ.એસસી., બી.એડ.', 'વિજ્ઞાન', '1990-07-08', '2018-06-18', 8, 30000],
            ['મહેતા અશોકભાઈ', 'Ashokbhai Mehta', 'ashok.mehta@nexschool.com', 'male', 'બી.એ., બી.એડ.', 'સામાજિક વિજ્ઞાન', '1982-11-30', '2012-06-01', 14, 36000],
            ['રાવલ પ્રિયંકાબેન', 'Priyankaben Raval', 'priyanka.raval@nexschool.com', 'female', 'એમ.એ., બી.એડ.', 'હિન્દી', '1991-03-19', '2019-06-17', 7, 29000],
            ['ચૌહાણ વિક્રમસિંહ', 'Vikramsinh Chauhan', 'vikram.chauhan@nexschool.com', 'male', 'બી.સી.એ., એમ.સી.એ.', 'કોમ્પ્યુટર', '1987-06-05', '2017-06-12', 9, 33000],
            ['સોલંકી ભાવનાબેન', 'Bhavnaben Solanki', 'bhavna.solanki@nexschool.com', 'female', 'બી.એ., પ્રાથમિક શિક્ષણ', 'ગુજરાતી', '1993-12-01', '2020-06-15', 6, 28000],
        ];

        $bloodGroups = ['A+', 'B+', 'O+', 'AB+', 'A-', 'O-'];

        foreach ($definitions as $index => $t) {
            [$nameGu, $nameEn, $email, $gender, $qualification, $specialization, $dob, $joining, $experience, $salary] = $t;
            $number = $index + 1;

            $teacher = Teacher::create([
                'teacher_id' => 'TEA' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'name' => $nameEn,
                'email' => $email,
                'phone' => '98250' . str_pad((string) (11000 + $number), 5, '0', STR_PAD_LEFT),
                'whatsapp_number' => '98250' . str_pad((string) (11000 + $number), 5, '0', STR_PAD_LEFT),
                'address' => 'આનંદ, ગુજરાત',
                'date_of_birth' => $dob,
                'gender' => $gender,
                'joining_date' => $joining,
                'joining_number' => 'JOIN-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'experience_in_years' => $experience,
                'blood_group' => $bloodGroups[$index % count($bloodGroups)],
                'qualification' => $qualification,
                'specialization' => $specialization,
                'basic_pay' => $salary,
                'basic_salary' => $salary,
                'other_salary' => 2500,
                'max_lwp' => 10,
                'max_cl' => 12,
                'ratings' => $index % 2 === 0 ? 4 : 5,
                'status' => 'active',
            ]);

            $teacher->qualification = $qualification;
            $teacher->specialization = $specialization;
            $teacher->save();

            $this->teachers[] = $teacher;

            User::create([
                'name' => $nameEn,
                'email' => $email,
                'password' => 'Teacher@123',
                'role' => 'teacher',
                'teacher_id' => $teacher->id,
            ]);
        }
    }

    private function seedClassTeachers(): void
    {
        $index = 0;
        foreach ($this->classes as $class) {
            ClassTeacher::create([
                'school_class_id' => $class->id,
                'teacher_id' => $this->teachers[$index % count($this->teachers)]->id,
                'academic_year_id' => $this->currentYear->id,
            ]);
            $index++;
        }
    }

    private function seedSubjectAssignments(): void
    {
        $classIndex = 0;
        foreach ($this->classes as $class) {
            foreach ($this->subjects as $subjectIndex => $subject) {
                SubjectTeacherAssignment::create([
                    'subject_id' => $subject->id,
                    'standard_id' => $class->standard_id,
                    'class_id' => $class->id,
                    'teacher_id' => $this->teachers[($classIndex + $subjectIndex) % count($this->teachers)]->id,
                    'academic_year_id' => $this->currentYear->id,
                ]);
            }
            $classIndex++;
        }
    }

    // ------------------------------------------------------------------
    // Students
    // ------------------------------------------------------------------

    private function seedStudents(): void
    {
        $maleGu = ['રાજ', 'મીત', 'આરવ', 'ધ્રુવ', 'જય', 'કૃષ', 'હાર્દિક', 'સાહિલ', 'રુદ્ર', 'વીર', 'પાર્થ', 'આદિત્ય', 'યશ', 'નિશાંત', 'મનન', 'કેયુર', 'ભાવેશ', 'જીતેન્દ્ર'];
        $maleEn = ['Raj', 'Meet', 'Aarav', 'Dhruv', 'Jay', 'Krush', 'Hardik', 'Sahil', 'Rudra', 'Veer', 'Parth', 'Aditya', 'Yash', 'Nishant', 'Manan', 'Keyur', 'Bhavesh', 'Jitendra'];
        $femaleGu = ['રિયા', 'ધ્વનિ', 'પ્રિયા', 'કૃપા', 'મીરા', 'સાન્વી', 'અનન્યા', 'જાન્વી', 'નિધિ', 'રિદ્ધિ', 'હેત્વી', 'ખુશી', 'ઈશા', 'ફ્રીના', 'તૃષા', 'દીપિકા', 'માનસી', 'શ્રદ્ધા'];
        $femaleEn = ['Riya', 'Dhvani', 'Priya', 'Krupa', 'Meera', 'Saanvi', 'Ananya', 'Janvi', 'Nidhi', 'Riddhi', 'Hetvi', 'Khushi', 'Isha', 'Frina', 'Trusha', 'Dipika', 'Mansi', 'Shraddha'];
        $fatherGu = ['રમેશભાઈ', 'મહેશભાઈ', 'દિનેશભાઈ', 'પ્રકાશભાઈ', 'જગદીશભાઈ', 'અશોકભાઈ', 'વિનોદભાઈ', 'રાજેશભાઈ', 'સુરેશભાઈ', 'નરેશભાઈ'];
        $fatherEnList = ['Rameshbhai', 'Maheshbhai', 'Dineshbhai', 'Prakashbhai', 'Jagdishbhai', 'Ashokbhai', 'Vinodbhai', 'Rajeshbhai', 'Sureshbhai', 'Nareshbhai'];
        $motherGu = ['નીતાબેન', 'હેતલબેન', 'પ્રિયાબેન', 'કિરણબેન', 'ભાવનાબેન', 'રેખાબેન', 'સંગીતાબેન', 'મનીષાબેન'];
        $motherEnList = ['Nitaben', 'Hetalben', 'Priyaben', 'Kiranben', 'Bhavnaben', 'Rekhaben', 'Sangitaben', 'Manishaben'];
        $surnameGu = ['પટેલ', 'દેસાઈ', 'શાહ', 'જોષી', 'મહેતા', 'રાવલ', 'ચૌહાણ', 'સોલંકી', 'પરમાર', 'વાઘેલા'];
        $surnameEnList = ['Patel', 'Desai', 'Shah', 'Joshi', 'Mehta', 'Raval', 'Chauhan', 'Solanki', 'Parmar', 'Vaghela'];
        $categories = [
            ['સામાન્ય', 'General'],
            ['બક્ષીપંચ', 'OBC'],
            ['અનુસુચિત જાતિ', 'SC'],
            ['અનુસુચિત જનજાતિ', 'ST'],
        ];
        $villages = [
            ['આનંદ', 'Anand'],
            ['નડિયાદ', 'Nadiad'],
            ['ખેડા', 'Kheda'],
            ['વડોદરા', 'Vadodara'],
            ['અમદાવાદ', 'Ahmedabad'],
            ['બોરસદ', 'Borsad'],
            ['પેટલાદ', 'Petlad'],
            ['ઉમરેઠ', 'Umreth'],
        ];
        $banks = [
            ['બેંક ઓફ બરોડા', 'BOB0001234'],
            ['સ્ટેટ બેંક ઓફ ઇન્ડિયા', 'SBIN0005678'],
            ['યુનિયન બેંક ઓફ ઇન્ડિયા', 'UBIN0009101'],
            ['એચ.ડી.એફ.સી. બેંક', 'HDFC0001122'],
        ];

        $gr = 1001;
        $globalIndex = 0;

        foreach ($this->classes as $classKey => $class) {
            [$standardOrder] = explode('-', $classKey);
            $standardOrder = (int) $standardOrder;
            $standard = $this->standards[$standardOrder];
            $birthYear = 2020 - $standardOrder;

            for ($i = 0; $i < 5; $i++) {
                $gender = $globalIndex % 2 === 0 ? 'kumar' : 'kumari';
                $firstGu = $gender === 'kumar' ? $maleGu[$globalIndex % count($maleGu)] : $femaleGu[$globalIndex % count($femaleGu)];
                $firstEn = $gender === 'kumar' ? $maleEn[$globalIndex % count($maleEn)] : $femaleEn[$globalIndex % count($femaleEn)];
                $father = $fatherGu[$globalIndex % count($fatherGu)];
                $fatherEn = $fatherEnList[$globalIndex % count($fatherEnList)];
                $mother = $motherGu[$globalIndex % count($motherGu)];
                $motherEn = $motherEnList[$globalIndex % count($motherEnList)];
                $surname = $surnameGu[$globalIndex % count($surnameGu)];
                $surnameEn = $surnameEnList[$globalIndex % count($surnameEnList)];
                [$categoryGu, $categoryEn] = $categories[$globalIndex % count($categories)];
                [$villageGu, $villageEn] = $villages[$globalIndex % count($villages)];
                [$bankGu, $ifsc] = $banks[$globalIndex % count($banks)];

                $day = ($globalIndex % 27) + 1;
                $month = ($globalIndex % 12) + 1;
                $dob = sprintf('%04d-%02d-%02d', $birthYear, $month, $day);

                $religionGu = 'હિન્દુ';
                $religionEn = 'Hindu';
                if ($globalIndex % 13 === 0) {
                    $religionGu = 'મુસ્લિમ';
                    $religionEn = 'Muslim';
                } elseif ($globalIndex % 17 === 0) {
                    $religionGu = 'ઈસાઈ';
                    $religionEn = 'Christian';
                }

                $student = Student::create([
                    'gr_number' => (string) $gr,
                    'admission_standard_id' => $standard->id,
                    'admission_class_id' => $class->id,
                    'current_standard_id' => $standard->id,
                    'current_class_id' => $class->id,
                    'date_of_admission' => '2026-06-15',
                    'previous_attendance_days' => 0,
                    'student_name_gu' => $firstGu,
                    'student_name_en' => $firstEn,
                    'father_name_gu' => $father,
                    'father_name_en' => $fatherEn,
                    'surname_gu' => $surname,
                    'surname_en' => $surnameEn,
                    'full_name_gu' => trim($firstGu . ' ' . $father . ' ' . $surname),
                    'full_name_en' => trim($firstEn . ' ' . $fatherEn . ' ' . $surnameEn),
                    'mother_name_gu' => $mother,
                    'mother_name_en' => $motherEn,
                    'date_of_birth' => $dob,
                    'dob_in_text_gu' => DateTextHelper::gujaratiDateText($day, $month, $birthYear),
                    'dob_in_text_en' => DateTextHelper::englishDateText($day, $month, $birthYear),
                    'birth_place_gu' => $villageGu,
                    'birth_place_en' => $villageEn,
                    'native_place_gu' => $villageGu,
                    'native_place_en' => $villageEn,
                    'gaam' => $villageGu,
                    'gaam_en' => $villageEn,
                    'religion_gu' => $religionGu,
                    'religion_en' => $religionEn,
                    'cast_gu' => $surname,
                    'cast_en' => $surnameEn,
                    'category_gu' => $categoryGu,
                    'category_en' => $categoryEn,
                    'is_minority' => $categoryEn === 'OBC' && $globalIndex % 3 === 0,
                    'sharirik_jaati' => $gender,
                    'last_school_gu' => '—',
                    'last_school_en' => '—',
                    'admission_under_rte' => $globalIndex % 19 === 0,
                    'mobile' => '98' . str_pad((string) (10000000 + $globalIndex), 8, '0', STR_PAD_LEFT),
                    'whatsapp' => '98' . str_pad((string) (10000000 + $globalIndex), 8, '0', STR_PAD_LEFT),
                    'apaar_id' => str_pad((string) (500000000000 + $globalIndex), 12, '0', STR_PAD_LEFT),
                    'uid_no' => str_pad((string) (600000000000 + $globalIndex), 12, '0', STR_PAD_LEFT),
                    'pen_no' => str_pad((string) (70000000000 + $globalIndex), 11, '0', STR_PAD_LEFT),
                    'aadhar_no' => str_pad((string) (200000000000 + $globalIndex), 12, '0', STR_PAD_LEFT),
                    'name_as_per_aadhar' => trim($firstEn . ' ' . $fatherEn . ' ' . $surnameEn),
                    'ration_card_no' => 'RC' . str_pad((string) (100000 + $globalIndex), 6, '0', STR_PAD_LEFT),
                    'bank_name' => $bankGu,
                    'bank_branch' => $villageGu . ' શાખા',
                    'bank_ifsc' => $ifsc,
                    'bank_account_no' => str_pad((string) (30000000000 + $globalIndex), 11, '0', STR_PAD_LEFT),
                    'name_as_per_bank' => trim($firstEn . ' ' . $fatherEn . ' ' . $surnameEn),
                    'status' => 'active',
                    'is_registered' => true,
                    'attendance_days' => 0,
                ]);

                User::create([
                    'name' => $student->full_name_en,
                    'username' => $student->gr_number,
                    'password' => Carbon::parse($dob)->format('d/m/Y'),
                    'role' => 'student',
                    'student_id' => $student->id,
                ]);

                User::create([
                    'name' => $fatherEn . ' ' . $surnameEn,
                    'username' => $student->mobile,
                    'password' => 'Parent@123',
                    'role' => 'parent',
                    'student_id' => $student->id,
                    'parent_mobile' => $student->mobile,
                ]);

                $gr++;
                $globalIndex++;
            }
        }

        // Two unregistered students (અનબોર્ડ) — must not appear in exam marks/reports.
        $unregisteredClass = $this->classes['1-A'];
        foreach (['UR-0001' => ['કિરણ', 'Kiran', 'kumar'], 'UR-0002' => ['પૂજા', 'Pooja', 'kumari']] as $urGr => [$first, $firstEn, $gender]) {
            Student::create([
                'gr_number' => $urGr,
                'admission_standard_id' => $this->standards[1]->id,
                'admission_class_id' => $unregisteredClass->id,
                'current_standard_id' => $this->standards[1]->id,
                'current_class_id' => $unregisteredClass->id,
                'date_of_admission' => '2026-06-20',
                'student_name_gu' => $first,
                'student_name_en' => $firstEn,
                'father_name_gu' => 'અજ્ઞાત',
                'father_name_en' => 'Unknown',
                'surname_gu' => '—',
                'surname_en' => '—',
                'full_name_gu' => $first . ' અજ્ઞાત',
                'full_name_en' => $firstEn . ' Unknown',
                'date_of_birth' => '2019-05-10',
                'dob_in_text_gu' => DateTextHelper::gujaratiDateText(10, 5, 2019),
                'dob_in_text_en' => DateTextHelper::englishDateText(10, 5, 2019),
                'sharirik_jaati' => $gender,
                'status' => 'active',
                'is_registered' => false,
            ]);
        }

        // Two alumni (LC issued) in standard 8-A.
        $alumni = Student::where('current_class_id', $this->classes['8-A']->id)->orderBy('id')->take(2)->get();
        foreach ($alumni as $index => $student) {
            $student->update([
                'status' => 'alumni',
                'leaving_date' => '2026-09-30',
                'lc_issue_date' => '2026-10-01',
                'lc_number' => 'LC/2026/' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'leaving_standard_id' => $this->standards[8]->id,
                'leaving_reason_gu' => 'અભ્યાસ પૂર્ણ',
                'leaving_reason_en' => 'Completed studies',
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Attendance
    // ------------------------------------------------------------------

    private function seedAttendance(): void
    {
        $holidayDates = ['2026-10-02', '2026-10-31', '2026-11-09'];
        $dates = [];
        $cursor = Carbon::today();
        while (count($dates) < 10) {
            if ($cursor->dayOfWeek !== Carbon::SUNDAY && !in_array($cursor->format('Y-m-d'), $holidayDates, true)) {
                $dates[] = $cursor->format('Y-m-d');
            }
            $cursor->subDay();
        }

        $classIds = [$this->classes['1-A']->id, $this->classes['5-A']->id];
        $students = Student::whereIn('current_class_id', $classIds)->where('is_registered', true)->orderBy('id')->get();

        $statuses = ['present', 'present', 'present', 'present', 'present', 'absent', 'absent_with_leave', 'present', 'medical_leave', 'present'];
        $index = 0;

        foreach ($students as $student) {
            foreach ($dates as $date) {
                Attendance::create([
                    'student_id' => $student->id,
                    'date' => $date,
                    'status' => $statuses[$index % count($statuses)],
                    'marked_by' => 1,
                    'remarks' => null,
                ]);
                $index++;
            }

            $presentDays = count($dates);
            $student->update(['attendance_days' => $presentDays]);
        }
    }

    // ------------------------------------------------------------------
    // Timetable
    // ------------------------------------------------------------------

    private function seedTimetable(): void
    {
        $slotDefinitions = [
            ['પ્રથમ પીરિયડ', 'First Period', '08:00:00', '08:45:00', false, 1],
            ['બીજો પીરિયડ', 'Second Period', '08:45:00', '09:30:00', false, 2],
            ['ત્રીજો પીરિયડ', 'Third Period', '09:30:00', '10:15:00', false, 3],
            ['નાસ્તો', 'Break', '10:15:00', '10:45:00', true, 4],
            ['ચોથો પીરિયડ', 'Fourth Period', '10:45:00', '11:30:00', false, 5],
            ['પાંચમો પીરિયડ', 'Fifth Period', '11:30:00', '12:15:00', false, 6],
            ['છઠ્ઠો પીરિયડ', 'Sixth Period', '12:15:00', '13:00:00', false, 7],
        ];

        $slots = [];
        foreach ($slotDefinitions as [$nameGu, $nameEn, $start, $end, $isBreak, $sort]) {
            $slot = TimetableSlot::create([
                'academic_year_id' => $this->currentYear->id,
                'name_gu' => $nameGu,
                'name_en' => $nameEn,
                'start_time' => $start,
                'end_time' => $end,
                'is_break' => $isBreak,
                'sort_order' => $sort,
            ]);
            if (!$isBreak) {
                $slots[] = $slot;
            }
        }

        $targetClasses = ['1-A', '1-B', '5-A', '8-A'];

        foreach ($targetClasses as $className) {
            $class = $this->classes[$className];
            $assignments = SubjectTeacherAssignment::where('class_id', $class->id)->get()->keyBy('subject_id');

            foreach (range(1, 6) as $day) {
                foreach ($slots as $slotIndex => $slot) {
                    $subject = $this->subjects[($day + $slotIndex) % count($this->subjects)];
                    $assignment = $assignments->get($subject->id);

                    TimetableEntry::create([
                        'academic_year_id' => $this->currentYear->id,
                        'timetable_slot_id' => $slot->id,
                        'standard_id' => $class->standard_id,
                        'school_class_id' => $class->id,
                        'day_of_week' => $day,
                        'subject_id' => $subject->id,
                        'teacher_id' => $assignment?->teacher_id,
                    ]);
                }
            }
        }
    }

    // ------------------------------------------------------------------
    // Fees
    // ------------------------------------------------------------------

    private function seedFees(): void
    {
        $heads = [
            ['શાળા ફી', 'Tuition Fee'],
            ['પરીક્ષા ફી', 'Exam Fee'],
            ['વિકાસ ફી', 'Development Fee'],
            ['વાહન ફી', 'Transport Fee'],
            ['યુનિફોર્મ ફી', 'Uniform Fee'],
        ];

        $createdHeads = [];
        foreach ($heads as [$gu, $en]) {
            $createdHeads[] = FeeHead::create(['name_gu' => $gu, 'name_en' => $en]);
        }

        // Tuition structure per standard, semester 1 and 2.
        foreach ($this->standards as $order => $standard) {
            foreach ([1, 2] as $semester) {
                $structure = FeeStructure::create([
                    'academic_year_id' => $this->currentYear->id,
                    'semester' => $semester,
                    'type' => 'tuition',
                    'frequency' => 'semester',
                    'late_fee_type' => 'none',
                    'late_fee_amount' => 0,
                    'late_fee_after_days' => 0,
                    'is_active' => true,
                ]);
                $structure->standards()->attach($standard->id);

                $amounts = [
                    $createdHeads[0]->id => 1500 + (100 * $order),
                    $createdHeads[1]->id => 200,
                    $createdHeads[2]->id => 300,
                ];

                $total = 0;
                foreach ($amounts as $headId => $amount) {
                    DB::table('fee_structure_details')->insert([
                        'fee_structure_id' => $structure->id,
                        'fee_head_id' => $headId,
                        'amount' => $amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $total += $amount;
                }

                $this->structureTotals[$order . '-' . $semester] = [$structure->id => $total];
            }
        }

        // Transport structures (single fee head) for semester 1 and 2.
        $transportStructures = [];
        foreach ([1, 2] as $semester) {
            $structure = FeeStructure::create([
                'academic_year_id' => $this->currentYear->id,
                'semester' => $semester,
                'type' => 'transport',
                'frequency' => 'semester',
                'late_fee_type' => 'none',
                'late_fee_amount' => 0,
                'late_fee_after_days' => 0,
                'is_active' => true,
            ]);
            $structure->standards()->attach(array_map(fn ($s) => $s->id, $this->standards));

            DB::table('fee_structure_details')->insert([
                'fee_structure_id' => $structure->id,
                'fee_head_id' => $createdHeads[3]->id,
                'amount' => 1200,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $transportStructures[$semester] = $structure;
        }

        // Assign tuition (semester 1) to every registered active student.
        $students = Student::where('is_registered', true)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $studentFees = [];
        $index = 0;

        foreach ($students as $student) {
            $order = (int) $student->currentStandard->sort_order;
            $structureTotal = $this->structureTotals[$order . '-1'];
            $structureId = array_key_first($structureTotal);
            $total = $structureTotal[$structureId];

            $concession = $index % 7 === 0 ? 200 : 0;

            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'academic_year_id' => $this->currentYear->id,
                'semester' => 1,
                'fee_structure_id' => $structureId,
                'total_amount' => $total,
                'concession_amount' => $concession,
                'net_amount' => $total - $concession,
                'is_active' => true,
            ]);

            $studentFees[] = $studentFee;
            $index++;
        }

        // Payments: 60% full, 25% half, 15% unpaid.
        $receipt = 1;
        $methods = ['cash', 'online', 'bank', 'cheque'];
        $index = 0;

        foreach ($studentFees as $studentFee) {
            $mode = $index % 20;
            $amount = 0;
            if ($mode < 12) {
                $amount = (float) $studentFee->net_amount;
            } elseif ($mode < 17) {
                $amount = floor(((float) $studentFee->net_amount) / 2);
            }

            if ($amount > 0) {
                FeePayment::create([
                    'student_fee_id' => $studentFee->id,
                    'student_id' => $studentFee->student_id,
                    'academic_year_id' => $this->currentYear->id,
                    'semester' => 1,
                    'receipt_number' => 'SCH' . str_pad((string) $receipt, 5, '0', STR_PAD_LEFT),
                    'amount_paid' => $amount,
                    'payment_date' => sprintf('2026-09-%02d', ($index % 20) + 1),
                    'payment_method' => $methods[$index % count($methods)],
                    'received_by' => 1,
                ]);
                $receipt++;
            }

            $index++;
        }

        // Bus fee for first 9 students (they are also assigned routes below).
        $busStudents = $students->take(9);
        $busReceipt = 1;
        foreach ($busStudents as $index => $student) {
            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'academic_year_id' => $this->currentYear->id,
                'semester' => 1,
                'fee_structure_id' => $transportStructures[1]->id,
                'total_amount' => 1200,
                'concession_amount' => 0,
                'net_amount' => 1200,
                'is_active' => true,
            ]);

            if ($index % 3 !== 2) {
                FeePayment::create([
                    'student_fee_id' => $studentFee->id,
                    'student_id' => $student->id,
                    'academic_year_id' => $this->currentYear->id,
                    'semester' => 1,
                    'receipt_number' => 'BUS' . str_pad((string) $busReceipt, 5, '0', STR_PAD_LEFT),
                    'amount_paid' => 600,
                    'payment_date' => '2026-09-15',
                    'payment_method' => 'cash',
                    'received_by' => 1,
                ]);
                $busReceipt++;
            }
        }

        // Carry forward from previous year for two students.
        foreach ($students->take(2) as $index => $student) {
            FeeCarryForward::create([
                'student_id' => $student->id,
                'from_academic_year_id' => $this->previousYear->id,
                'to_academic_year_id' => $this->currentYear->id,
                'amount' => 500 + ($index * 250),
            ]);

            StudentFee::create([
                'student_id' => $student->id,
                'academic_year_id' => $this->currentYear->id,
                'semester' => null,
                'fee_structure_id' => null,
                'total_amount' => 500 + ($index * 250),
                'concession_amount' => 0,
                'net_amount' => 500 + ($index * 250),
                'is_active' => true,
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Grades / exams
    // ------------------------------------------------------------------

    private function seedGrades(): void
    {
        $scale = GradeScale::create([
            'academic_year_id' => $this->currentYear->id,
            'name' => 'જી.એસ.ઈ.બી. ગ્રેડ સ્કેલ',
            'is_active' => true,
        ]);

        $slabs = [
            ['A1', 90, 100, true, 1],
            ['A2', 80, 89.99, true, 2],
            ['B1', 70, 79.99, true, 3],
            ['B2', 60, 69.99, true, 4],
            ['C1', 50, 59.99, true, 5],
            ['C2', 40, 49.99, true, 6],
            ['D', 33, 39.99, true, 7],
            ['E1', 20, 32.99, false, 8],
            ['E2', 0, 19.99, false, 9],
        ];

        foreach ($slabs as [$grade, $min, $max, $isPass, $sort]) {
            GradeSlab::create([
                'grade_scale_id' => $scale->id,
                'grade' => $grade,
                'min_percent' => $min,
                'max_percent' => $max,
                'is_pass' => $isPass,
                'sort_order' => $sort,
            ]);
        }
    }

    private function seedExams(): void
    {
        $pattern = ExamPattern::create([
            'academic_year_id' => $this->currentYear->id,
            'name' => 'પ્રથમ સત્ર પરીક્ષા',
            'description' => 'સત્ર 1 અને સત્ર 2 માટે લેખિત + મૌખિક પરીક્ષા.',
            'is_active' => true,
        ]);

        $heads = [];
        foreach ([1, 2] as $semester) {
            $heads[$semester] = [
                ExamPatternHead::create([
                    'exam_pattern_id' => $pattern->id,
                    'semester' => $semester,
                    'name' => 'લેખિત',
                    'total_marks' => 80,
                    'converted_marks' => 80,
                    'sort_order' => 1,
                ]),
                ExamPatternHead::create([
                    'exam_pattern_id' => $pattern->id,
                    'semester' => $semester,
                    'name' => 'મૌખિક',
                    'total_marks' => 20,
                    'converted_marks' => 20,
                    'sort_order' => 2,
                ]),
            ];
        }

        foreach ($this->classes as $class) {
            foreach ($this->subjects as $subject) {
                ExamPatternApply::create([
                    'exam_pattern_id' => $pattern->id,
                    'standard_id' => $class->standard_id,
                    'school_class_id' => $class->id,
                    'subject_id' => $subject->id,
                ]);
            }
        }

        // Marks for 8-A (all subjects) and 5-A (first four subjects).
        $markTargets = [
            '8-A' => array_keys($this->subjects),
            '5-A' => [0, 1, 2, 3],
        ];

        foreach ($markTargets as $className => $subjectIndexes) {
            $class = $this->classes[$className];
            $students = Student::where('current_class_id', $class->id)
                ->where('is_registered', true)
                ->where('status', 'active')
                ->orderBy('id')
                ->get();

            foreach ($subjectIndexes as $subjectIndex) {
                $subject = $this->subjects[$subjectIndex];

                foreach ($students as $studentIndex => $student) {
                    foreach ($heads[1] as $headIndex => $head) {
                        if ($headIndex === 0) {
                            $marks = min(80, 38 + (($studentIndex * 7 + $subjectIndex * 5) % 42));
                        } else {
                            $marks = min(20, 9 + (($studentIndex * 3 + $subjectIndex) % 11));
                        }

                        ExamMark::create([
                            'exam_pattern_id' => $pattern->id,
                            'academic_year_id' => $this->currentYear->id,
                            'standard_id' => $class->standard_id,
                            'school_class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'semester' => 1,
                            'head_id' => $head->id,
                            'student_id' => $student->id,
                            'marks_obtained' => $marks,
                        ]);
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------
    // Transport
    // ------------------------------------------------------------------

    private function seedTransport(): void
    {
        $vehicle1 = Vehicle::create([
            'vehicle_no' => 'GJ-01-AB-1234',
            'vehicle_type' => 'bus',
            'capacity' => 40,
            'driver_name' => 'ભરતભાઈ પટેલ',
            'driver_mobile' => '9876543210',
            'is_active' => true,
        ]);

        $vehicle2 = Vehicle::create([
            'vehicle_no' => 'GJ-01-CD-5678',
            'vehicle_type' => 'van',
            'capacity' => 20,
            'driver_name' => 'મનોજભાઈ રાવલ',
            'driver_mobile' => '9876543211',
            'is_active' => true,
        ]);

        $route1 = Route::create([
            'route_name' => 'રૂટ 1 — મુખ્ય બજાર',
            'vehicle_id' => $vehicle1->id,
            'description' => 'આનંદ મુખ્ય બજારથી શાળા સુધી',
            'is_active' => true,
        ]);

        $route2 = Route::create([
            'route_name' => 'રૂટ 2 — સ્ટેશન રોડ',
            'vehicle_id' => $vehicle2->id,
            'description' => 'સ્ટેશન રોડથી શાળા સુધી',
            'is_active' => true,
        ]);

        $stops1 = [];
        foreach ([['મુખ્ય બજાર', '07:15', '13:15'], ['જુના ગામ', '07:25', '13:25'], ['બસ સ્ટેન્ડ', '07:35', '13:35'], ['શાળા', '07:50', '13:50']] as $i => [$name, $pickup, $drop]) {
            $stops1[] = RouteStop::create([
                'route_id' => $route1->id,
                'stop_name' => $name,
                'stop_order' => $i + 1,
                'pickup_time' => $pickup,
                'drop_time' => $drop,
            ]);
        }

        $stops2 = [];
        foreach ([['સ્ટેશન રોડ', '07:20', '13:20'], ['ગણેશ ચોક', '07:30', '13:30'], ['શાળા', '07:50', '13:50']] as $i => [$name, $pickup, $drop]) {
            $stops2[] = RouteStop::create([
                'route_id' => $route2->id,
                'stop_name' => $name,
                'stop_order' => $i + 1,
                'pickup_time' => $pickup,
                'drop_time' => $drop,
            ]);
        }

        $students = Student::where('is_registered', true)->where('status', 'active')->orderBy('id')->take(9)->get();

        foreach ($students as $index => $student) {
            StudentRoute::create([
                'student_id' => $student->id,
                'route_id' => $index < 6 ? $route1->id : $route2->id,
                'stop_id' => $index < 6 ? $stops1[$index % 4]->id : $stops2[$index % 3]->id,
                'pickup' => true,
                'drop' => true,
                'is_active' => true,
            ]);
        }

        // Bus attendance for the last two school days on route 1.
        $dates = [];
        $cursor = Carbon::today();
        while (count($dates) < 2) {
            if ($cursor->dayOfWeek !== Carbon::SUNDAY) {
                $dates[] = $cursor->format('Y-m-d');
            }
            $cursor->subDay();
        }

        $route1Students = $students->take(6);
        foreach ($dates as $date) {
            foreach ($route1Students as $index => $student) {
                BusAttendance::create([
                    'student_id' => $student->id,
                    'student_type' => 'school',
                    'route_id' => $route1->id,
                    'date' => $date,
                    'morning_status' => $index % 7 === 0 ? 'absent' : 'present',
                    'evening_status' => $index % 6 === 0 ? 'leave' : 'present',
                ]);
            }
        }
    }

    private function seedBusOnlyStudents(): void
    {
        $route = Route::orderBy('id')->first();

        $definitions = [
            ['જયેશભાઈ ઠાકોર (બસ ઓન્લી)', 'ધોરણ 9', 'આનંદ', '9999988801'],
            ['મીનાબેન વ્યાસ (બસ ઓન્લી)', 'ધોરણ 10', 'નડિયાદ', '9999988802'],
            ['રાજુભાઈ દવે (બસ ઓન્લી)', 'ધોરણ 11', 'ખેડા', '9999988803'],
        ];

        foreach ($definitions as $index => [$name, $standard, $gaam, $mobile]) {
            $busOnly = BusOnlyStudent::create([
                'full_name_gu' => $name,
                'standard_label' => $standard,
                'gaam' => $gaam,
                'mobile' => $mobile,
                'route_id' => $route->id,
                'fee_sem1' => 4000,
                'fee_sem2' => 4000,
                'status' => 'active',
            ]);

            if ($index !== 2) {
                BusOnlyFeePayment::create([
                    'bus_only_student_id' => $busOnly->id,
                    'semester' => 1,
                    'amount' => 4000,
                    'payment_date' => '2026-09-20',
                    'payment_method' => 'cash',
                ]);
            }
        }
    }

    // ------------------------------------------------------------------
    // Calendar
    // ------------------------------------------------------------------

    private function seedCalendar(): void
    {
        PublicHoliday::create([
            'academic_year_id' => $this->currentYear->id,
            'name' => 'ગાંધી જયંતિ',
            'type' => 'jaher',
            'date' => '2026-10-02',
        ]);

        PublicHoliday::create([
            'academic_year_id' => $this->currentYear->id,
            'name' => 'સરદાર પટેલ જયંતિ',
            'type' => 'jaher',
            'date' => '2026-10-31',
        ]);

        PublicHoliday::create([
            'academic_year_id' => $this->currentYear->id,
            'name' => 'દિવાળી વેકેશન',
            'type' => 'sthanik',
            'date' => '2026-11-09',
        ]);

        $activities = [
            ['વાર્ષિક ક્રીડા મહોત્સવ', '2026-11-14', 'શાળા મેદાન'],
            ['બાળક દિવસ ઉજવણી', '2026-11-20', 'સાંસ્કૃતિક કાર્યક્રમ'],
            ['વિજ્ઞાન પ્રદર્શન', '2026-12-05', 'ધોરણ 6 થી 8'],
        ];

        foreach ($activities as $index => [$name, $date, $remarks]) {
            ActivityPlan::create([
                'academic_year_id' => $this->currentYear->id,
                'sort_order' => $index + 1,
                'activity_name' => $name,
                'date' => $date,
                'remarks' => $remarks,
            ]);
        }
    }
}
