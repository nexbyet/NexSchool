<?php

namespace App\Http\Controllers;

use App\Models\AdmissionInquiry;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AdmissionInquiryController extends Controller
{
    public function index()
    {
        $inquiries = AdmissionInquiry::with('academicYear')->orderBy('created_at', 'desc')->get();
        return view('admission-inquiries.index', compact('inquiries'));
    }

    public function show(AdmissionInquiry $admissionInquiry)
    {
        $admissionInquiry->load('academicYear');
        return response()->json($admissionInquiry);
    }

    public function approve(Request $request, AdmissionInquiry $admissionInquiry)
    {
        $data = $request->validate([
            'gr_number' => 'nullable|string|max:50|unique:admission_inquiries,gr_number,'.$admissionInquiry->id,
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $admissionInquiry->update([
            'status' => 'approved',
            'gr_number' => $data['gr_number'],
            'academic_year_id' => $data['academic_year_id'],
            'admin_notes' => $data['admin_notes'],
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'પ્રવેશ મંજૂર કર્યો.']);
    }

    public function reject(Request $request, AdmissionInquiry $admissionInquiry)
    {
        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $admissionInquiry->update([
            'status' => 'rejected',
            'admin_notes' => $data['admin_notes'],
        ]);

        return response()->json(['success' => true, 'message' => 'અરજી નામંજૂર કરી.']);
    }

    public function destroy(AdmissionInquiry $admissionInquiry)
    {
        $admissionInquiry->delete();
        return response()->json(['success' => true, 'message' => 'અરજી કાઢી નાખી.']);
    }
}
