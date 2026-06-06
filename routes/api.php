<?php

// NexSchool - API Routes
// RESTful API with Sanctum token authentication
// મોબાઇલ એપ/થર્ડ-પાર્ટી માટે API રૂટ્સ

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SchoolSettingController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\StandardController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\ClassTeacherController;
use App\Http\Controllers\Api\SubjectAssignmentController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\PublicHolidayController;
use App\Http\Controllers\Api\ActivityPlanController;
use App\Http\Controllers\Api\RollNumberSortController;
use App\Http\Controllers\Api\AttendanceRegisterController;
use App\Http\Controllers\Api\AttendanceController as ApiAttendanceController;
use App\Http\Controllers\Api\DailyStatsController as ApiDailyStatsController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\TransportController;
use App\Http\Controllers\Api\FrontsiteController;
use App\Http\Controllers\Api\CertificateController;

// Public API routes - no auth needed
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);
Route::get('/school-settings', [SchoolSettingController::class, 'show']);

// Protected API routes - require valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Students — IMPORT routes must be BEFORE apiResource to avoid {student} wildcard match
    Route::get('/students/import/demo', [StudentController::class, 'importDemo'])->name('api.students.import.demo');
    Route::post('/students/import/upload', [StudentController::class, 'import'])->name('api.students.import');
    Route::apiResource('students', StudentController::class)->names([
        'index' => 'api.students.index',
        'store' => 'api.students.store',
        'show' => 'api.students.show',
        'update' => 'api.students.update',
        'destroy' => 'api.students.destroy',
    ]);
    Route::post('/students/{student}/leaving', [StudentController::class, 'updateLeaving'])->name('api.students.leaving');
    Route::get('/teachers/import/demo', [TeacherController::class, 'importDemo'])->name('api.teachers.import.demo');
    Route::post('/teachers/import/upload', [TeacherController::class, 'import'])->name('api.teachers.import');
    Route::apiResource('teachers', TeacherController::class)->names([
        'index' => 'api.teachers.index',
        'store' => 'api.teachers.store',
        'show' => 'api.teachers.show',
        'update' => 'api.teachers.update',
        'destroy' => 'api.teachers.destroy',
    ]);
    Route::post('/teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('api.teachers.reset-password');
    Route::apiResource('classes', SchoolClassController::class);

    // Class Teacher Assignment
    Route::get('/class-teachers', [ClassTeacherController::class, 'index'])->name('api.class-teachers.index');
    Route::post('/class-teachers/assign', [ClassTeacherController::class, 'assign'])->name('api.class-teachers.assign');

    // Subject-Teacher Assignment
    Route::get('/subject-assignments', [SubjectAssignmentController::class, 'index'])->name('api.subject-assignments.index');
    Route::post('/subject-assignments/assign', [SubjectAssignmentController::class, 'assign'])->name('api.subject-assignments.assign');

    // Academic Years (active route must be BEFORE apiResource to avoid conflict)
    Route::get('/academic-years/active', [AcademicYearController::class, 'active'])->name('api.academic-years.active');
    Route::post('/academic-years/{academic_year}/active', [AcademicYearController::class, 'setActive'])->name('api.academic-years.set-active');
    Route::apiResource('academic-years', AcademicYearController::class)->names([
        'index' => 'api.academic-years.index',
        'store' => 'api.academic-years.store',
        'show' => 'api.academic-years.show',
        'update' => 'api.academic-years.update',
        'destroy' => 'api.academic-years.destroy',
    ]);

    // Standards
    Route::get('/standards', [StandardController::class, 'index']);
    Route::post('/standards', [StandardController::class, 'store']);
    Route::get('/standards/{standard}', [StandardController::class, 'show']);
    Route::put('/standards/{standard}', [StandardController::class, 'update']);
    Route::delete('/standards/{standard}', [StandardController::class, 'destroy']);
    Route::post('/standards/reorder', [StandardController::class, 'reorder']);
    Route::post('/standards/{standard}/class', [StandardController::class, 'storeClass']);
    Route::put('/standards/class/{classId}', [StandardController::class, 'updateClass']);
    Route::delete('/standards/class/{classId}', [StandardController::class, 'destroyClass']);
    Route::post('/standards/classes/reorder', [StandardController::class, 'reorderClasses']);

    // Subjects (global + standard assignment)
    Route::apiResource('subjects', SubjectController::class)->names([
        'index' => 'api.subjects.index',
        'store' => 'api.subjects.store',
        'show' => 'api.subjects.show',
        'update' => 'api.subjects.update',
        'destroy' => 'api.subjects.destroy',
    ]);
    Route::post('/subjects/{subject}/standards', [SubjectController::class, 'assignStandards']);
    Route::post('/subjects/reorder', [SubjectController::class, 'reorder']);

    // Time Table
    Route::get('/timetable', [TimetableController::class, 'index'])->name('api.timetable.index');
    Route::post('/timetable/entries/update', [TimetableController::class, 'updateEntry'])->name('api.timetable.entries.update');
    Route::post('/timetable/entries/copy-all', [TimetableController::class, 'copyToAllDays'])->name('api.timetable.entries.copy-all');
    Route::get('/timetable/slots', [TimetableController::class, 'getSlots'])->name('api.timetable.slots');

    // Public Holidays
    Route::get('/public-holidays', [PublicHolidayController::class, 'index'])->name('api.public-holidays.index');
    Route::post('/public-holidays', [PublicHolidayController::class, 'store'])->name('api.public-holidays.store');
    Route::get('/public-holidays/{publicHoliday}', [PublicHolidayController::class, 'show'])->name('api.public-holidays.show');
    Route::put('/public-holidays/{publicHoliday}', [PublicHolidayController::class, 'update'])->name('api.public-holidays.update');
    Route::delete('/public-holidays/{publicHoliday}', [PublicHolidayController::class, 'destroy'])->name('api.public-holidays.destroy');

    // Activity Plans
    Route::get('/activity-plans', [ActivityPlanController::class, 'index'])->name('api.activity-plans.index');
    Route::post('/activity-plans', [ActivityPlanController::class, 'store'])->name('api.activity-plans.store');
    Route::get('/activity-plans/{activityPlan}', [ActivityPlanController::class, 'show'])->name('api.activity-plans.show');
    Route::put('/activity-plans/{activityPlan}', [ActivityPlanController::class, 'update'])->name('api.activity-plans.update');
    Route::delete('/activity-plans/{activityPlan}', [ActivityPlanController::class, 'destroy'])->name('api.activity-plans.destroy');

    // Roll Number Sort
    Route::get('/roll-number-sort', [RollNumberSortController::class, 'index'])->name('api.roll-number-sort.index');
    Route::put('/roll-number-sort', [RollNumberSortController::class, 'update'])->name('api.roll-number-sort.update');

    // Attendance Register
    Route::get('/attendance-register/standards', [AttendanceRegisterController::class, 'standards']);
    Route::get('/attendance-register/classes/{standard}', [AttendanceRegisterController::class, 'classes']);
    Route::post('/attendance-register', [AttendanceRegisterController::class, 'show']);

    // Digital Attendance
    Route::get('/attendance', [ApiAttendanceController::class, 'index']);
    Route::post('/attendance/mark', [ApiAttendanceController::class, 'mark']);

    // Daily Stats Book (દૈનિક આંકડાબુક)
    Route::get('/daily-stats', [ApiDailyStatsController::class, 'show']);

    // School Settings (update/logo: admin only - show is public above)
    Route::put('/school-settings', [SchoolSettingController::class, 'update']);
    Route::post('/school-settings/logo', [SchoolSettingController::class, 'uploadLogo']);

    // Fee API
    Route::prefix('fees')->name('api.fees.')->group(function () {
        Route::apiResource('heads', FeeController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::get('structures', [FeeController::class, 'structures'])->name('structures');
        Route::post('structures', [FeeController::class, 'storeStructure'])->name('structures.store');
        Route::get('structures/{id}', [FeeController::class, 'showStructure'])->name('structures.show');
        Route::delete('structures/{id}', [FeeController::class, 'destroyStructure'])->name('structures.destroy');
        Route::get('student-fees', [FeeController::class, 'studentFees'])->name('student-fees');
        Route::post('student-fees', [FeeController::class, 'storeStudentFee'])->name('student-fees.store');
        Route::post('payments', [FeeController::class, 'storePayment'])->name('payments.store');
        Route::get('payments', [FeeController::class, 'payments'])->name('payments');
        Route::get('reports/summary', [FeeController::class, 'summary'])->name('summary');
        Route::get('reports/due-list', [FeeController::class, 'dueList'])->name('due-list');
        Route::get('reports/collection', [FeeController::class, 'collectionReport'])->name('collection');
    });

    // ─── Transport Module ─────────────────────────────────────
    Route::prefix('transport')->name('api.transport.')->group(function () {
        Route::get('vehicles', [TransportController::class, 'vehicles'])->name('vehicles');
        Route::post('vehicles', [TransportController::class, 'storeVehicle'])->name('vehicles.store');
        Route::get('vehicles/{vehicle}', [TransportController::class, 'showVehicle'])->name('vehicles.show');
        Route::put('vehicles/{vehicle}', [TransportController::class, 'updateVehicle'])->name('vehicles.update');
        Route::delete('vehicles/{vehicle}', [TransportController::class, 'destroyVehicle'])->name('vehicles.destroy');
        Route::get('routes', [TransportController::class, 'routes'])->name('routes');
        Route::post('routes', [TransportController::class, 'storeRoute'])->name('routes.store');
        Route::get('routes/{route}', [TransportController::class, 'showRoute'])->name('routes.show');
        Route::put('routes/{route}', [TransportController::class, 'updateRoute'])->name('routes.update');
        Route::delete('routes/{route}', [TransportController::class, 'destroyRoute'])->name('routes.destroy');
        Route::get('routes/{route}/stops', [TransportController::class, 'stops'])->name('stops');
        Route::post('routes/{route}/stops', [TransportController::class, 'storeStop'])->name('stops.store');
        Route::put('stops/{stop}', [TransportController::class, 'updateStop'])->name('stops.update');
        Route::delete('stops/{stop}', [TransportController::class, 'destroyStop'])->name('stops.destroy');
        Route::get('student-routes', [TransportController::class, 'studentRoutes'])->name('student-routes');
        Route::post('student-routes/assign', [TransportController::class, 'assignStudentRoute'])->name('student-routes.assign');
        Route::post('student-routes/bulk-assign', [TransportController::class, 'bulkAssignStudentRoute'])->name('student-routes.bulk');
        Route::delete('student-routes/{id}', [TransportController::class, 'destroyStudentRoute'])->name('student-routes.destroy');
        Route::get('student-routes/by-student/{studentId}', [TransportController::class, 'studentRouteByStudent'])->name('student-routes.by-student');
        Route::get('bus-attendance', [TransportController::class, 'busAttendance'])->name('bus-attendance');
        Route::post('bus-attendance/mark', [TransportController::class, 'markBusAttendance'])->name('bus-attendance.mark');
        Route::get('bus-attendance/print', [TransportController::class, 'printBusAttendance'])->name('bus-attendance.print');
    });

    // ─── Certificates (બોનાફાઈડ પ્રમાણપત્ર) ──────────────────
    Route::prefix('certificates')->name('api.certificates.')->group(function () {
        Route::get('standards', [CertificateController::class, 'standards'])->name('standards');
        Route::post('search-by-gr', [CertificateController::class, 'searchByGr'])->name('search-by-gr');
        Route::post('search-by-class', [CertificateController::class, 'searchByClass'])->name('search-by-class');
        Route::get('preview/{student}/{lang}', [CertificateController::class, 'preview'])->name('preview');
        Route::get('print/{student}/{lang}', [CertificateController::class, 'print'])->name('print');
    });

    // ─── Frontsite Admin ──────────────────────────────────────
    Route::prefix('frontsite')->name('api.frontsite.')->group(function () {
        // Pages
        Route::get('pages', [FrontsiteController::class, 'pages'])->name('pages');
        Route::post('pages', [FrontsiteController::class, 'storePage'])->name('pages.store');
        Route::get('pages/{page}', [FrontsiteController::class, 'showPage'])->name('pages.show');
        Route::put('pages/{page}', [FrontsiteController::class, 'updatePage'])->name('pages.update');
        Route::delete('pages/{page}', [FrontsiteController::class, 'destroyPage'])->name('pages.destroy');
        // Menus
        Route::get('menus', [FrontsiteController::class, 'menus'])->name('menus');
        Route::post('menus', [FrontsiteController::class, 'storeMenu'])->name('menus.store');
        Route::put('menus/{menu}', [FrontsiteController::class, 'updateMenu'])->name('menus.update');
        Route::delete('menus/{menu}', [FrontsiteController::class, 'destroyMenu'])->name('menus.destroy');
        // Menu Items
        Route::get('menus/{menu}/items', [FrontsiteController::class, 'menuItems'])->name('menu-items');
        Route::post('menu-items', [FrontsiteController::class, 'storeMenuItem'])->name('menu-items.store');
        Route::put('menu-items/{menuItem}', [FrontsiteController::class, 'updateMenuItem'])->name('menu-items.update');
        Route::delete('menu-items/{menuItem}', [FrontsiteController::class, 'destroyMenuItem'])->name('menu-items.destroy');
        Route::post('menu-items/reorder', [FrontsiteController::class, 'reorderMenuItems'])->name('menu-items.reorder');
        // Notices
        Route::get('notices', [FrontsiteController::class, 'notices'])->name('notices');
        Route::post('notices', [FrontsiteController::class, 'storeNotice'])->name('notices.store');
        Route::get('notices/{notice}', [FrontsiteController::class, 'showNotice'])->name('notices.show');
        Route::post('notices/{notice}', [FrontsiteController::class, 'updateNotice'])->name('notices.update');
        Route::delete('notices/{notice}', [FrontsiteController::class, 'destroyNotice'])->name('notices.destroy');
        // Sliders
        Route::get('sliders', [FrontsiteController::class, 'sliders'])->name('sliders');
        Route::post('sliders', [FrontsiteController::class, 'storeSlider'])->name('sliders.store');
        Route::get('sliders/{sliderItem}', [FrontsiteController::class, 'showSlider'])->name('sliders.show');
        Route::post('sliders/{sliderItem}', [FrontsiteController::class, 'updateSlider'])->name('sliders.update');
        Route::delete('sliders/{sliderItem}', [FrontsiteController::class, 'destroySlider'])->name('sliders.destroy');
        Route::post('sliders/reorder', [FrontsiteController::class, 'reorderSliders'])->name('sliders.reorder');
        // Galleries
        Route::get('galleries', [FrontsiteController::class, 'galleries'])->name('galleries');
        Route::post('galleries', [FrontsiteController::class, 'storeGallery'])->name('galleries.store');
        Route::get('galleries/{gallery}', [FrontsiteController::class, 'showGallery'])->name('galleries.show');
        Route::put('galleries/{gallery}', [FrontsiteController::class, 'updateGallery'])->name('galleries.update');
        Route::delete('galleries/{gallery}', [FrontsiteController::class, 'destroyGallery'])->name('galleries.destroy');
        Route::post('galleries/{gallery}/images', [FrontsiteController::class, 'storeGalleryImage'])->name('galleries.images.store');
        Route::delete('gallery-images/{galleryImage}', [FrontsiteController::class, 'destroyGalleryImage'])->name('gallery-images.destroy');
        Route::post('galleries/{gallery}/images/reorder', [FrontsiteController::class, 'reorderGalleryImages'])->name('galleries.images.reorder');
        // Homepage Sections
        Route::get('homepage-sections', [FrontsiteController::class, 'homepageSections'])->name('homepage-sections');
        Route::post('homepage-sections', [FrontsiteController::class, 'storeHomepageSection'])->name('homepage-sections.store');
        Route::get('homepage-sections/{homepageSection}', [FrontsiteController::class, 'showHomepageSection'])->name('homepage-sections.show');
        Route::put('homepage-sections/{homepageSection}', [FrontsiteController::class, 'updateHomepageSection'])->name('homepage-sections.update');
        Route::post('homepage-sections/{homepageSection}/content', [FrontsiteController::class, 'updateHomepageSectionContent'])->name('homepage-sections.content');
        Route::delete('homepage-sections/{homepageSection}', [FrontsiteController::class, 'destroyHomepageSection'])->name('homepage-sections.destroy');
        Route::post('homepage-sections/reorder', [FrontsiteController::class, 'reorderHomepageSections'])->name('homepage-sections.reorder');
        // Admission Inquiries
        Route::get('admission-inquiries', [FrontsiteController::class, 'admissionInquiries'])->name('admission-inquiries');
        Route::get('admission-inquiries/{admissionInquiry}', [FrontsiteController::class, 'showAdmissionInquiry'])->name('admission-inquiries.show');
        Route::post('admission-inquiries/{admissionInquiry}/approve', [FrontsiteController::class, 'approveAdmissionInquiry'])->name('admission-inquiries.approve');
        Route::post('admission-inquiries/{admissionInquiry}/reject', [FrontsiteController::class, 'rejectAdmissionInquiry'])->name('admission-inquiries.reject');
        Route::delete('admission-inquiries/{admissionInquiry}', [FrontsiteController::class, 'destroyAdmissionInquiry'])->name('admission-inquiries.destroy');
    });
});
