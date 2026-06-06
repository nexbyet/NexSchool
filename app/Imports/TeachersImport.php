<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToCollection, WithHeadingRow
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

            if (empty($row['email'])) {
                $this->errors[] = "હરોળ $rowNum: ઇમેઇલ ખાલી છે — અવગણ્યો";
                $this->skipped++;
                continue;
            }

            $validator = Validator::make($row->toArray(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:teachers,email',
                'phone' => 'nullable|string|max:20',
                'whatsapp_number' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'gender' => 'nullable|in:male,female',
                'joining_number' => 'nullable|string|max:50',
                'experience_in_years' => 'nullable|integer|min:0|max:70',
                'blood_group' => 'nullable|string|max:10',
                'basic_pay' => 'nullable|numeric|min:0',
                'max_lwp' => 'nullable|integer|min:0|max:365',
                'max_cl' => 'nullable|integer|min:0|max:365',
                'basic_salary' => 'nullable|numeric|min:0',
                'other_salary' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:active,inactive',
            ]);

            if ($validator->fails()) {
                $msg = $validator->errors()->first();
                $this->errors[] = "હરોળ $rowNum: $msg — અવગણ્યો";
                $this->skipped++;
                continue;
            }

            $dateOfBirth = !empty($row['date_of_birth']) ? $this->parseDate($row['date_of_birth'], "હરોળ $rowNum: જન્મ તારીખ") : null;
            $joiningDate = !empty($row['joining_date']) ? $this->parseDate($row['joining_date'], "હરોળ $rowNum: જોડાણ તારીખ") : null;
            $dateInactive = !empty($row['date_inactive']) ? $this->parseDate($row['date_inactive'], "હરોળ $rowNum: નિષ્ક્રિય તારીખ") : null;

            if (!empty($row['date_of_birth']) && !$dateOfBirth) {
                $this->skipped++;
                continue;
            }
            if (!empty($row['joining_date']) && !$joiningDate) {
                $this->skipped++;
                continue;
            }
            if (!empty($row['date_inactive']) && !$dateInactive) {
                $this->skipped++;
                continue;
            }

            $status = in_array($row['status'] ?? '', ['active', 'inactive']) ? $row['status'] : 'active';

            $data = [
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'] ?? null,
                'whatsapp_number' => $row['whatsapp_number'] ?? null,
                'address' => $row['address'] ?? null,
                'date_of_birth' => $dateOfBirth,
                'gender' => $row['gender'] ?? null,
                'joining_date' => $joiningDate,
                'joining_number' => $row['joining_number'] ?? null,
                'experience_in_years' => $row['experience_in_years'] ?? null,
                'blood_group' => $row['blood_group'] ?? null,
                'basic_pay' => $row['basic_pay'] ?? null,
                'max_lwp' => $row['max_lwp'] ?? null,
                'max_cl' => $row['max_cl'] ?? null,
                'ratings' => $row['ratings'] ?? null,
                'basic_salary' => $row['basic_salary'] ?? null,
                'other_salary' => $row['other_salary'] ?? null,
                'status' => $status,
                'reason_inactive' => ($status === 'inactive' && !empty($row['reason_inactive'])) ? $row['reason_inactive'] : null,
                'date_inactive' => ($status === 'inactive') ? $dateInactive : null,
            ];

            try {
                $last = Teacher::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->teacher_id, 3)) + 1 : 1;
                $data['teacher_id'] = 'TEA' . str_pad($num, 3, '0', STR_PAD_LEFT);

                $teacher = Teacher::create($data);

                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => 'Teacher@123',
                    'role' => 'teacher',
                    'teacher_id' => $teacher->id,
                ]);

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
        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            if (is_numeric($value)) {
                try {
                    return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $value))->format('Y-m-d');
                } catch (\Exception $e2) {}
            }
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e3) {
                $this->errors[] = "$label: અમાન્ય તારીખ '$value' — d/m/y ફોર્મેટ વાપરો (દા.ત. 15/10/2014)";
                return null;
            }
        }
    }
}
