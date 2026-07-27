<?php

namespace App\Http\Controllers;

use App\Models\BusOnlyFeePayment;
use App\Models\BusOnlyStudent;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BusOnlyStudentController extends Controller
{
    public function index()
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->get();
        $students = BusOnlyStudent::with('route')->active()->orderBy('full_name_gu')->get();
        return view('transport.bus-students.index', compact('students', 'routes'));
    }

    public function fetchData(Request $request)
    {
        $query = BusOnlyStudent::with('route');
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name_gu', 'LIKE', "%$s%")
                  ->orWhere('mobile', 'LIKE', "%$s%");
            });
        }
        $students = $query->orderBy('full_name_gu')->get();

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name_gu' => 'required|string|max:255',
            'standard_label' => 'nullable|string|max:50',
            'gaam' => 'nullable|string|max:100',
            'mobile' => 'nullable|digits:10',
            'route_id' => 'required|exists:routes,id',
            'fee_sem1' => 'nullable|numeric|min:0',
            'fee_sem2' => 'nullable|numeric|min:0',
        ]);

        $data['fee_sem1'] = (float) ($data['fee_sem1'] ?? 0);
        $data['fee_sem2'] = (float) ($data['fee_sem2'] ?? 0);

        $student = BusOnlyStudent::create($data);

        return response()->json([
            'success' => true,
            'message' => 'બસ વિદ્યાર્થી ઉમેરાયો',
            'student' => $student->load('route'),
        ]);
    }

    public function show(BusOnlyStudent $busOnlyStudent)
    {
        $busOnlyStudent->load('route', 'feePayments');
        return response()->json($busOnlyStudent);
    }

    public function update(Request $request, BusOnlyStudent $busOnlyStudent)
    {
        $data = $request->validate([
            'full_name_gu' => 'required|string|max:255',
            'standard_label' => 'nullable|string|max:50',
            'gaam' => 'nullable|string|max:100',
            'mobile' => 'nullable|digits:10',
            'route_id' => 'required|exists:routes,id',
            'fee_sem1' => 'nullable|numeric|min:0',
            'fee_sem2' => 'nullable|numeric|min:0',
        ]);

        $data['fee_sem1'] = (float) ($data['fee_sem1'] ?? 0);
        $data['fee_sem2'] = (float) ($data['fee_sem2'] ?? 0);

        $busOnlyStudent->update($data);

        return response()->json([
            'success' => true,
            'message' => 'બસ વિદ્યાર્થી માહિતી સુધારાઈ',
            'student' => $busOnlyStudent->fresh()->load('route'),
        ]);
    }

    public function destroy(BusOnlyStudent $busOnlyStudent)
    {
        $busOnlyStudent->delete();
        return response()->json([
            'success' => true,
            'message' => 'બસ વિદ્યાર્થી કાઢી નાખ્યો',
        ]);
    }

    public function payFee(Request $request)
    {
        $data = $request->validate([
            'bus_only_student_id' => 'required|exists:bus_only_students,id',
            'semester' => 'required|in:1,2',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date_format:d/m/Y',
            'payment_method' => 'required|in:cash,bank,cheque,online',
            'reference_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['payment_date'] = Carbon::createFromFormat('d/m/Y', $data['payment_date'])->format('Y-m-d');

        BusOnlyFeePayment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'ફી ચુકવણી સફળતાપૂર્વક સેવ થઈ',
        ]);
    }

    public function dueList(Request $request)
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->get();
        $query = BusOnlyStudent::with('route')->active();

        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        $students = $query->orderBy('full_name_gu')->get()->map(function ($s) {
            $paid = (float) BusOnlyFeePayment::where('bus_only_student_id', $s->id)->sum('amount');
            $total = (float) $s->fee_sem1 + (float) $s->fee_sem2;
            $due = max(0, $total - $paid);
            $s->total_fee = $total;
            $s->paid_fee = $paid;
            $s->due_fee = $due;
            return $s;
        });

        $grandTotal = $students->sum('total_fee');
        $grandPaid = $students->sum('paid_fee');
        $grandDue = $students->sum('due_fee');

        return view('transport.bus-students.due-list', compact(
            'students', 'routes', 'grandTotal', 'grandPaid', 'grandDue'
        ));
    }

    public function printDueList(Request $request)
    {
        $query = BusOnlyStudent::with('route')->active();

        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        $students = $query->orderBy('full_name_gu')->get()->map(function ($s) {
            $paid = (float) BusOnlyFeePayment::where('bus_only_student_id', $s->id)->sum('amount');
            $total = (float) $s->fee_sem1 + (float) $s->fee_sem2;
            $s->total_fee = $total;
            $s->paid_fee = $paid;
            $s->due_fee = max(0, $total - $paid);
            return $s;
        });

        $school = \App\Models\SchoolSetting::find(1);
        $grandTotal = $students->sum('total_fee');
        $grandPaid = $students->sum('paid_fee');
        $grandDue = $students->sum('due_fee');

        return view('transport.bus-students.print-due-list', compact(
            'students', 'school', 'grandTotal', 'grandPaid', 'grandDue', 'request'
        ));
    }

    public function printRouteList(Request $request)
    {
        $routeId = $request->route_id;
        $query = BusOnlyStudent::with('route')->active();

        if ($routeId) {
            $query->where('route_id', $routeId);
        }

        $busStudents = $query->orderBy('full_name_gu')->get();

        $school = \App\Models\SchoolSetting::find(1);
        $routes = $routeId ? collect([Route::find($routeId)]) : Route::where('is_active', true)->orderBy('route_name')->get();

        return view('transport.bus-students.print-route-list', compact(
            'busStudents', 'school', 'routes', 'routeId'
        ));
    }

    // ─── Fee Collection Page ──────────────────────────────────────
    public function feeCollection()
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->get();
        return view('transport.bus-students.fee-collection.index', compact('routes'));
    }

    public function getCollectionData(Request $request)
    {
        $request->validate(['route_id' => 'nullable|exists:routes,id']);

        $query = BusOnlyStudent::with('route')->active();
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }
        $students = $query->orderBy('full_name_gu')->get()->map(function ($s) {
            $paid = (float) BusOnlyFeePayment::where('bus_only_student_id', $s->id)->sum('amount');
            $total = (float) $s->fee_sem1 + (float) $s->fee_sem2;
            return [
                'id'       => $s->id,
                'name'     => $s->full_name_gu,
                'standard' => $s->standard_label,
                'gaam'     => $s->gaam,
                'mobile'   => $s->mobile,
                'route'    => $s->route->route_name ?? '',
                'fee_sem1' => (float) $s->fee_sem1,
                'fee_sem2' => (float) $s->fee_sem2,
                'total'    => $total,
                'paid'     => $paid,
                'due'      => max(0, $total - $paid),
            ];
        });

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function collectFee(Request $request)
    {
        $data = $request->validate([
            'bus_only_student_id' => 'required|exists:bus_only_students,id',
            'semester'            => 'required|in:1,2',
            'amount'              => 'required|numeric|min:1',
            'payment_date'        => 'required|date_format:d/m/Y',
            'payment_method'      => 'required|in:cash,bank,cheque,online',
            'reference_number'    => 'nullable|string|max:50',
            'notes'               => 'nullable|string|max:500',
        ]);

        $data['payment_date'] = Carbon::createFromFormat('d/m/Y', $data['payment_date'])->format('Y-m-d');

        $payment = BusOnlyFeePayment::create($data);

        return response()->json([
            'success'    => true,
            'message'    => 'ફી ચુકવણી સફળ',
            'payment_id' => $payment->id,
        ]);
    }

    public function receipt(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $studentId = $request->query('student_id');

        if (!$paymentId || !$studentId) {
            abort(404);
        }

        $payment = BusOnlyFeePayment::with('student.route')->findOrFail($paymentId);
        $student = BusOnlyStudent::with('route')->findOrFail($studentId);

        $school = \App\Models\SchoolSetting::find(1);

        $guDigits = ['શૂન્ય','એક','બે','ત્રણ','ચાર','પાંચ','છ','સાત','આઠ','નવ'];
        $guTeens = ['દસ','અગિયાર','બાર','તેર','ચૌદ','પંદર','સોળ','સત્તર','અઢાર','ઓગણીસ'];
        $guTens = ['','','વીસ','ત્રીસ','ચાલીસ','પચાસ','સાઠ','સિત્તેર','એંસી','નેવું'];
        $inWords = function($num) use ($guDigits, $guTeens, $guTens) {
            $w = function($n) use ($guDigits, $guTeens, $guTens) {
                $r = '';
                if ($n >= 100) { $r .= $guDigits[floor($n/100)] . ' સો '; $n %= 100; }
                if ($n >= 20) { $r .= $guTens[floor($n/10)] . ' '; $n %= 10; }
                elseif ($n >= 10) { $r .= $guTeens[$n-10] . ' '; $n = 0; }
                if ($n > 0) $r .= $guDigits[$n] . ' ';
                return $r;
            };
            $whole = floor($num); $frac = round(($num - $whole) * 100);
            $words = '';
            if ($whole >= 10000000) { $words .= $w(floor($whole/10000000)) . ' કરોડ '; $whole %= 10000000; }
            if ($whole >= 100000) { $words .= $w(floor($whole/100000)) . ' લાખ '; $whole %= 100000; }
            if ($whole >= 1000) { $words .= $w(floor($whole/1000)) . ' હજાર '; $whole %= 1000; }
            $words .= $w($whole);
            $words = trim($words) . ' રૂપિયા';
            if ($frac > 0) $words .= ' અને ' . $w($frac) . ' પૈસા';
            return $words . ' માત્ર';
        };

        return view('transport.bus-students.fee-collection.receipt', compact(
            'payment', 'student', 'school', 'inWords'
        ));
    }

    public function history(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:bus_only_students,id',
        ]);

        $student = BusOnlyStudent::with('route')->find($data['student_id']);
        $payments = BusOnlyFeePayment::where('bus_only_student_id', $data['student_id'])
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($p) {
                $methodLabels = ['cash' => 'રોકડા', 'bank' => 'બેંક', 'cheque' => 'ચેક', 'online' => 'ઓનલાઇન'];
                return [
                    'id'              => $p->id,
                    'semester'        => $p->semester,
                    'amount'          => (float) $p->amount,
                    'payment_date'    => $p->payment_date ? date('d/m/Y', strtotime($p->payment_date)) : '',
                    'payment_method'  => $methodLabels[$p->payment_method] ?? $p->payment_method,
                    'reference_number' => $p->reference_number,
                    'notes'           => $p->notes,
                ];
            });

        $paid = $payments->sum('amount');
        $total = (float) $student->fee_sem1 + (float) $student->fee_sem2;
        $due = max(0, $total - $paid);

        return response()->json([
            'success'  => true,
            'student'  => [
                'id'       => $student->id,
                'name'     => $student->full_name_gu,
                'standard' => $student->standard_label,
                'route'    => $student->route->route_name ?? '',
                'fee_sem1' => (float) $student->fee_sem1,
                'fee_sem2' => (float) $student->fee_sem2,
                'total'    => $total,
                'paid'     => $paid,
                'due'      => $due,
            ],
            'payments' => $payments,
        ]);
    }

    public function getRoutes()
    {
        $routes = Route::where('is_active', true)->orderBy('route_name')->get(['id', 'route_name']);
        return response()->json($routes);
    }
}
