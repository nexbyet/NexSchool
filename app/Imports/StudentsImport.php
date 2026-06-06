<?php

namespace App\Imports;

use App\Helpers\DateTextHelper;
use App\Models\SchoolClass;
use App\Models\Standard;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        $this->errors = [];
        $this->imported = 0;
        $this->skipped = 0;

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            $row = $row->map(function ($value) {
                return $value !== null ? trim((string) $value) : '';
            });

            if ($row->filter(fn($v) => $v !== '')->isEmpty()) {
                continue;
            }

            if (empty($row['gr_number'])) {
                $this->errors[] = "હરોળ $rowNum: GR નંબર ખાલી છે — અવગણ્યો";
                $this->skipped++;
                continue;
            }

            $validator = Validator::make($row->toArray(), [
                'gr_number' => 'required|numeric|unique:students,gr_number',
                'admission_standard' => 'required|string|max:255',
                'current_standard' => 'required|string|max:255',
                'date_of_admission' => 'required|string',
                'student_name_gu' => 'required|string|max:255',
                'student_name_en' => 'required|string|max:255',
                'father_name_gu' => 'required|string|max:255',
                'father_name_en' => 'required|string|max:255',
                'surname_gu' => 'required|string|max:255',
                'surname_en' => 'required|string|max:255',
                'date_of_birth' => 'required|string',
            ]);

            if ($validator->fails()) {
                $msg = $validator->errors()->first();
                $this->errors[] = "હરોળ $rowNum: $msg — અવગણ્યો";
                $this->skipped++;
                continue;
            }

            $standardNames = Standard::pluck('id', 'name')->toArray();

            $admStdId = $standardNames[$row['admission_standard']] ?? null;
            if (!$admStdId) {
                $this->errors[] = "હરોળ $rowNum: પ્રવેશ ધોરણ '{$row['admission_standard']}' મળ્યું નહીં — અવગણ્યો";
                $this->skipped++;
                continue;
            }

            $curStdId = $standardNames[$row['current_standard']] ?? null;
            if (!$curStdId) {
                $this->errors[] = "હરોળ $rowNum: હાલનું ધોરણ '{$row['current_standard']}' મળ્યું નહીં — અવગણ્યો";
                $this->skipped++;
                continue;
            }

            $admClassId = null;
            if (!empty($row['admission_class'])) {
                $class = SchoolClass::where('standard_id', $admStdId)
                    ->where('name', $row['admission_class'])
                    ->first();
                if (!$class) {
                    $this->errors[] = "હરોળ $rowNum: પ્રવેશ વર્ગ '{$row['admission_class']}' (ધોરણ: {$row['admission_standard']}) મળ્યો નહીં — class ખાલી રહેશે";
                } else {
                    $admClassId = $class->id;
                }
            }

            $curClassId = null;
            if (!empty($row['current_class'])) {
                $class = SchoolClass::where('standard_id', $curStdId)
                    ->where('name', $row['current_class'])
                    ->first();
                if (!$class) {
                    $this->errors[] = "હરોળ $rowNum: હાલનો વર્ગ '{$row['current_class']}' (ધોરણ: {$row['current_standard']}) મળ્યો નહીં — class ખાલી રહેશે";
                } else {
                    $curClassId = $class->id;
                }
            }

            $dateOfAdmission = $this->parseDate($row['date_of_admission'], "હરોળ $rowNum: પ્રવેશ તારીખ");
            if (!$dateOfAdmission) {
                $this->skipped++;
                continue;
            }

            $dob = $this->parseDate($row['date_of_birth'], "હરોળ $rowNum: જન્મ તારીખ");
            if (!$dob) {
                $this->skipped++;
                continue;
            }

            $fullNameGu = $row['full_name_gu'] ?? ($row['student_name_gu'] . ' ' . $row['father_name_gu'] . ' ' . $row['surname_gu']);
            $fullNameEn = $row['full_name_en'] ?? ($row['student_name_en'] . ' ' . $row['father_name_en'] . ' ' . $row['surname_en']);

            $data = [
                'gr_number' => $row['gr_number'],
                'admission_standard_id' => $admStdId,
                'admission_class_id' => $admClassId,
                'current_standard_id' => $curStdId,
                'current_class_id' => $curClassId,
                'date_of_admission' => $dateOfAdmission,
                'student_name_gu' => $row['student_name_gu'],
                'student_name_en' => $row['student_name_en'],
                'father_name_gu' => $row['father_name_gu'],
                'father_name_en' => $row['father_name_en'],
                'surname_gu' => $row['surname_gu'],
                'surname_en' => $row['surname_en'],
                'full_name_gu' => $fullNameGu,
                'full_name_en' => $fullNameEn,
                'mother_name_gu' => $row['mother_name_gu'] ?? null,
                'mother_name_en' => $row['mother_name_en'] ?? null,
                'date_of_birth' => $dob,
                'dob_in_text_gu' => DateTextHelper::gujaratiDateText(Carbon::parse($dob)->day, Carbon::parse($dob)->month, Carbon::parse($dob)->year),
                'dob_in_text_en' => DateTextHelper::englishDateText(Carbon::parse($dob)->day, Carbon::parse($dob)->month, Carbon::parse($dob)->year),
                'birth_place_gu' => $row['birth_place_gu'] ?? null,
                'birth_place_en' => $row['birth_place_en'] ?? null,
                'native_place_gu' => $row['native_place_gu'] ?? null,
                'native_place_en' => $row['native_place_en'] ?? null,
                'religion_gu' => $row['religion_gu'] ?? null,
                'religion_en' => $row['religion_en'] ?? null,
                'cast_gu' => $row['cast_gu'] ?? null,
                'cast_en' => $row['cast_en'] ?? null,
                'category_gu' => $row['category_gu'] ?? null,
                'category_en' => $row['category_en'] ?? null,
                'is_minority' => !empty($row['is_minority']) && in_array(strtoupper($row['is_minority']), ['YES', 'HA', 'હા', '1', 'TRUE']) ? 1 : 0,
                'sharirik_jaati' => in_array($row['sharirik_jaati'] ?? '', ['kumar', 'kumari']) ? $row['sharirik_jaati'] : null,
                'last_school_gu' => $row['last_school_gu'] ?? null,
                'last_school_en' => $row['last_school_en'] ?? null,
                'admission_under_rte' => !empty($row['admission_under_rte']) && in_array(strtoupper($row['admission_under_rte']), ['YES', 'HA', 'હા', '1', 'TRUE']) ? 1 : 0,
                'mobile' => $row['mobile'] ?? null,
                'whatsapp' => $row['whatsapp'] ?? null,
                'apaar_id' => $row['apaar_id'] ?? null,
                'uid_no' => $row['uid_no'] ?? null,
                'pen_no' => $row['pen_no'] ?? null,
                'aadhar_no' => $row['aadhar_no'] ?? null,
                'name_as_per_aadhar' => $row['name_as_per_aadhar'] ?? null,
                'ration_card_no' => $row['ration_card_no'] ?? null,
                'bank_name' => $row['bank_name'] ?? null,
                'bank_branch' => $row['bank_branch'] ?? null,
                'bank_ifsc' => $row['bank_ifsc'] ?? null,
                'bank_account_no' => $row['bank_account_no'] ?? null,
                'name_as_per_bank' => $row['name_as_per_bank'] ?? null,
            ];

            try {
                $student = Student::create($data);

                User::create([
                    'name' => $fullNameEn,
                    'username' => $data['gr_number'],
                    'password' => Carbon::parse($dob)->format('d/m/Y'),
                    'role' => 'student',
                    'student_id' => $student->id,
                ]);

                if (!empty($data['mobile']) && !empty($data['father_name_en'])) {
                    User::firstOrCreate(
                        ['username' => $data['mobile'], 'role' => 'parent'],
                        [
                            'name' => $data['father_name_en'],
                            'password' => 'Parent@123',
                            'parent_mobile' => $data['mobile'],
                        ]
                    );
                }

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "હરોળ $rowNum: ડેટા સાચવવામાં ભૂલ: " . $e->getMessage() . " — અવગણ્યો";
                $this->skipped++;
            }
        }
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function parseDate($value, $label)
    {
        // Try d/m/Y format (string like 15/10/2014)
        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            // Try Excel serial number
            if (is_numeric($value)) {
                try {
                    return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $value))->format('Y-m-d');
                } catch (\Exception $e2) {}
            }
            // Try other common formats
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e3) {
                $this->errors[] = "$label: અમાન્ય તારીખ '$value' — d/m/y ફોર્મેટ વાપરો (દા.ત. 15/10/2014)";
                return null;
            }
        }
    }
}
