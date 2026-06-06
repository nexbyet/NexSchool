<?php

namespace App\Http\Controllers;

use App\Models\Standard;
use App\Models\SchoolClass;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = null;
        $teacherSubjects = collect();
        $teacherClasses = collect();
        $studentStandard = null;
        $studentClass = null;

        if ($user->role === 'teacher' && $user->teacher) {
            $profile = $user->teacher;
            $teacherSubjects = $profile->subjects;
            $teacherClasses = SchoolClass::where('teacher_id', $profile->id)->get();
        } elseif (in_array($user->role, ['student', 'staff']) && $user->student) {
            $profile = $user->student;
            $profile->load(['currentStandard', 'currentClass']);
            $studentStandard = $profile->currentStandard;
            $studentClass = $profile->currentClass;
        } elseif ($user->role === 'parent') {
            if ($user->student) {
                $profile = $user->student;
                $profile->load(['currentStandard', 'currentClass']);
                $studentStandard = $profile->currentStandard;
                $studentClass = $profile->currentClass;
            }
        }

        $isBirthday = $profile && $profile->date_of_birth
            && now()->format('m-d') === \Carbon\Carbon::parse($profile->date_of_birth)->format('m-d');

        return view('profile.index', compact(
            'user', 'profile', 'teacherSubjects', 'teacherClasses',
            'studentStandard', 'studentClass', 'isBirthday'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ];

        if ($user->role === 'teacher' && $user->teacher) {
            $rules['phone'] = 'nullable|string|max:20';
            $rules['address'] = 'nullable|string|max:500';
        }

        $validated = $request->validate($rules);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        $user->update($userData);

        if ($user->role === 'teacher' && $user->teacher) {
            $user->teacher->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $user->teacher->phone,
                'address' => $validated['address'] ?? $user->teacher->address,
            ]);
        }

        if ($user->role === 'student' && $user->student) {
            $user->student->update([
                'mobile' => $validated['phone'] ?? $user->student->mobile,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'પ્રોફાઇલ અપડેટ થઈ.']);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        auth()->user()->update(['password' => Hash::make($validated['password'])]);

        return response()->json(['success' => true, 'message' => 'પાસવર્ડ સફળતાપૂર્વક બદલાયો.']);
    }
}
