<?php

namespace App\Http\Controllers\Api;

use App\Exports\TeacherDemoExport;
use App\Http\Controllers\Controller;
use App\Imports\TeachersImport;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function index()
    {
        return response()->json(Teacher::orderBy('teacher_id')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date_format:d/m/Y',
            'gender' => 'nullable|in:male,female',
            'joining_date' => 'nullable|date_format:d/m/Y',
            'joining_number' => 'nullable|string|max:50',
            'experience_in_years' => 'nullable|integer|min:0|max:70',
            'blood_group' => 'nullable|string|max:10',
            'basic_pay' => 'nullable|numeric|min:0',
            'max_lwp' => 'nullable|integer|min:0|max:365',
            'max_cl' => 'nullable|integer|min:0|max:365',
            'ratings' => 'nullable|string',
            'basic_salary' => 'nullable|numeric|min:0',
            'other_salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'reason_inactive' => 'nullable|string|required_if:status,inactive',
            'date_inactive' => 'nullable|date_format:d/m/Y|required_if:status,inactive',
        ]);

        foreach (['date_of_birth', 'joining_date', 'date_inactive'] as $dateField) {
            if (!empty($validated[$dateField])) {
                $d = \DateTime::createFromFormat('d/m/Y', $validated[$dateField]);
                $validated[$dateField] = $d ? $d->format('Y-m-d') : null;
            }
        }

        if (empty($validated['teacher_id'])) {
            $last = Teacher::orderBy('id', 'desc')->first();
            $num = $last ? intval(substr($last->teacher_id, 3)) + 1 : 1;
            $validated['teacher_id'] = 'TEA' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        $teacher = Teacher::create($validated);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => 'Teacher@123',
            'role' => 'teacher',
            'teacher_id' => $teacher->id,
        ]);

        return response()->json($teacher, 201);
    }

    public function show(Teacher $teacher)
    {
        $data = $teacher->toArray();
        foreach (['date_of_birth', 'joining_date', 'date_inactive'] as $dateField) {
            if (!empty($data[$dateField])) {
                $data[$dateField] = \Carbon\Carbon::parse($data[$dateField])->format('d/m/Y');
            }
        }
        return response()->json($data);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date_format:d/m/Y',
            'gender' => 'nullable|in:male,female',
            'joining_date' => 'nullable|date_format:d/m/Y',
            'joining_number' => 'nullable|string|max:50',
            'experience_in_years' => 'nullable|integer|min:0|max:70',
            'blood_group' => 'nullable|string|max:10',
            'basic_pay' => 'nullable|numeric|min:0',
            'max_lwp' => 'nullable|integer|min:0|max:365',
            'max_cl' => 'nullable|integer|min:0|max:365',
            'ratings' => 'nullable|string',
            'basic_salary' => 'nullable|numeric|min:0',
            'other_salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'reason_inactive' => 'nullable|string|required_if:status,inactive',
            'date_inactive' => 'nullable|date_format:d/m/Y|required_if:status,inactive',
        ]);

        foreach (['date_of_birth', 'joining_date', 'date_inactive'] as $dateField) {
            if (!empty($validated[$dateField])) {
                $d = \DateTime::createFromFormat('d/m/Y', $validated[$dateField]);
                $validated[$dateField] = $d ? $d->format('Y-m-d') : null;
            }
        }

        $oldEmail = $teacher->email;
        $teacher->update($validated);
        $teacher = $teacher->fresh();

        if ($user = $teacher->user) {
            $user->update([
                'name' => $validated['name'] ?? $teacher->name,
                'email' => $validated['email'] ?? $teacher->email,
            ]);
        }

        $data = $teacher->toArray();
        foreach (['date_of_birth', 'joining_date', 'date_inactive'] as $dateField) {
            if (!empty($data[$dateField])) {
                $data[$dateField] = \Carbon\Carbon::parse($data[$dateField])->format('d/m/Y');
            }
        }
        return response()->json($data);
    }

    public function destroy(Teacher $teacher)
    {
        if ($user = $teacher->user) {
            $user->delete();
        }
        $teacher->delete();
        return response()->json(null, 204);
    }

    public function resetPassword(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        if (!$user = $teacher->user) {
            return response()->json(['message' => 'આ શિક્ષકનું યુઝર એકાઉન્ટ નથી.'], 404);
        }

        $user->update(['password' => $validated['password']]);
        return response()->json(['message' => $teacher->name . ' નો પાસવર્ડ બદલાયો.']);
    }

    public function importDemo()
    {
        return Excel::download(new TeacherDemoExport, 'nexschool_teacher_demo.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $import = new TeachersImport();
        Excel::import($import, $request->file('file'));

        return response()->json([
            'success' => true,
            'message' => $import->getImportedCount() . ' શિક્ષક ઉમેરાયા' . ($import->getSkippedCount() ? ', ' . $import->getSkippedCount() . ' અવગણાયા' : ''),
            'imported' => $import->getImportedCount(),
            'skipped' => $import->getSkippedCount(),
            'errors' => $import->getErrors(),
        ]);
    }
}
