<?php

// NexSchool - Web Routes
// Web authentication (session-based) + Dashboard routes
// ગુજરાતી: વેબ રૂટ્સ - લોગિન, રજિસ્ટર, ડેશબોર્ડ

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassTeacherController;
use App\Http\Controllers\SubjectAssignmentController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicHolidayController;
use App\Http\Controllers\ActivityPlanController;
use App\Http\Controllers\RollNumberSortController;
use App\Http\Controllers\AttendanceRegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyStatsController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\FeeHeadController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\FeeCollectionController;
use App\Http\Controllers\FeeReportController;
use App\Http\Controllers\FeeRegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomepageSectionController;
use App\Http\Controllers\FrontsiteController;
use App\Http\Controllers\FrontsiteAdmissionController;
use App\Http\Controllers\AdmissionInquiryController;
use App\Http\Controllers\DropdownOptionController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\FrontsiteNoticeController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\StudentRouteController;
use App\Http\Controllers\BusAttendanceController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\UpdateController;

// ─── Installer (no auth, no guest, accessible before .env/DB exist) ─
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'welcome'])->name('welcome');
    Route::post('/language', [InstallController::class, 'setLanguage'])->name('language');
    Route::get('/requirements', [InstallController::class, 'requirements'])->name('requirements');
    Route::post('/requirements', [InstallController::class, 'requirementsNext'])->name('requirements.next');
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/database/test', [InstallController::class, 'testDatabase'])->name('database.test');
    Route::get('/license', [InstallController::class, 'license'])->name('license');
    Route::post('/license/activate', [InstallController::class, 'activateLicense'])->name('license.activate');
    Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'saveAdmin'])->name('admin.save');
    Route::get('/school', [InstallController::class, 'school'])->name('school');
    Route::post('/school', [InstallController::class, 'saveSchool'])->name('school.save');
    Route::get('/run', [InstallController::class, 'run'])->name('run');
    Route::post('/process', [InstallController::class, 'process'])->name('process');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});

// Guest routes - only for non-logged-in users
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    // Registration disabled
});

// Authenticated routes - require login session
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Academic Years
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('/academic-years/{academicYear}', [AcademicYearController::class, 'show'])->name('academic-years.show');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
    Route::post('/academic-years/{academicYear}/active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Students — IMPORT routes must come BEFORE {student} wildcard
    Route::get('/students/import/demo', [StudentController::class, 'importDemo'])->name('students.import.demo');
    Route::match(['get', 'post'], '/students/import', [StudentController::class, 'importView'])->name('students.import.view');
    Route::post('/students/import/upload', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/data/fetch', [StudentController::class, 'fetchData'])->name('students.data');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/{student}/leaving', [StudentController::class, 'updateLeaving'])->name('students.leaving');

    // Teachers
    Route::get('/teachers/import/demo', [TeacherController::class, 'importDemo'])->name('teachers.import.demo');
    Route::get('/teachers/import', [TeacherController::class, 'importView'])->name('teachers.import.view');
    Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::post('/teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');

    // Standards & Classes Management
    Route::get('/standards', [StandardController::class, 'index'])->name('standards.index');
    Route::post('/standards', [StandardController::class, 'store'])->name('standards.store');
    Route::get('/standards/{standard}', [StandardController::class, 'show'])->name('standards.show');
    Route::put('/standards/{standard}', [StandardController::class, 'update'])->name('standards.update');
    Route::delete('/standards/{standard}', [StandardController::class, 'destroy'])->name('standards.destroy');
    Route::post('/standards/reorder', [StandardController::class, 'reorder'])->name('standards.reorder');
    Route::post('/standards/{standard}/class', [StandardController::class, 'storeClass'])->name('standards.class.store');
    Route::get('/standards/class/{classId}/edit', [StandardController::class, 'showClass'])->name('standards.class.show');
    Route::put('/standards/class/{classId}', [StandardController::class, 'updateClass'])->name('standards.class.update');
    Route::delete('/standards/class/{classId}', [StandardController::class, 'destroyClass'])->name('standards.class.destroy');
    Route::post('/standards/classes/reorder', [StandardController::class, 'reorderClasses'])->name('standards.classes.reorder');

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::post('/subjects/{subject}/standards', [SubjectController::class, 'assignStandards'])->name('subjects.assign-standards');
    Route::post('/subjects/reorder', [SubjectController::class, 'reorder'])->name('subjects.reorder');

    // Class Teacher Assignment
    Route::get('/class-teachers', [ClassTeacherController::class, 'index'])->name('class-teachers.index');
    Route::post('/class-teachers/assign', [ClassTeacherController::class, 'assign'])->name('class-teachers.assign');

    // Subject-Teacher Assignment
    Route::get('/subject-assignments', [SubjectAssignmentController::class, 'index'])->name('subject-assignments.index');
    Route::post('/subject-assignments/assign', [SubjectAssignmentController::class, 'assign'])->name('subject-assignments.assign');

    // Time Table Module
    Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    Route::post('/timetable/classes', [TimetableController::class, 'getClasses'])->name('timetable.classes');
    Route::post('/timetable/slots', [TimetableController::class, 'storeSlot'])->name('timetable.slots.store');
    Route::get('/timetable/slots/{slot}', [TimetableController::class, 'showSlot'])->name('timetable.slots.show');
    Route::put('/timetable/slots/{slot}', [TimetableController::class, 'updateSlot'])->name('timetable.slots.update');
    Route::delete('/timetable/slots/{slot}', [TimetableController::class, 'deleteSlot'])->name('timetable.slots.destroy');
    Route::post('/timetable/slots/reorder', [TimetableController::class, 'reorderSlots'])->name('timetable.slots.reorder');
    Route::post('/timetable/entries/update', [TimetableController::class, 'updateEntry'])->name('timetable.entries.update');
    Route::post('/timetable/entries/copy-all', [TimetableController::class, 'copyToAllDays'])->name('timetable.entries.copy-all');

    Route::post('/timetable/entries/clear', [TimetableController::class, 'clearEntries'])->name('timetable.entries.clear');
    Route::get('/timetable/entries/fetch', [TimetableController::class, 'getEntries'])->name('timetable.entries.fetch');

    // Public Holidays (by-year MUST be before {publicHoliday} to avoid route conflict)
    Route::get('/public-holidays', [PublicHolidayController::class, 'index'])->name('public-holidays.index');
    Route::get('/public-holidays/by-year', [PublicHolidayController::class, 'byYear'])->name('public-holidays.by-year');
    Route::get('/public-holidays/print', [PublicHolidayController::class, 'print'])->name('public-holidays.print');
    Route::post('/public-holidays', [PublicHolidayController::class, 'store'])->name('public-holidays.store');
    Route::get('/public-holidays/{publicHoliday}', [PublicHolidayController::class, 'show'])->name('public-holidays.show');
    Route::put('/public-holidays/{publicHoliday}', [PublicHolidayController::class, 'update'])->name('public-holidays.update');
    Route::delete('/public-holidays/{publicHoliday}', [PublicHolidayController::class, 'destroy'])->name('public-holidays.destroy');

    // Activity Plans (by-year MUST be before {activityPlan} to avoid route conflict)
    Route::get('/activity-plans', [ActivityPlanController::class, 'index'])->name('activity-plans.index');
    Route::get('/activity-plans/by-year', [ActivityPlanController::class, 'byYear'])->name('activity-plans.by-year');
    Route::get('/activity-plans/print', [ActivityPlanController::class, 'print'])->name('activity-plans.print');
    Route::post('/activity-plans', [ActivityPlanController::class, 'store'])->name('activity-plans.store');
    Route::get('/activity-plans/{activityPlan}', [ActivityPlanController::class, 'show'])->name('activity-plans.show');
    Route::put('/activity-plans/{activityPlan}', [ActivityPlanController::class, 'update'])->name('activity-plans.update');
    Route::delete('/activity-plans/{activityPlan}', [ActivityPlanController::class, 'destroy'])->name('activity-plans.destroy');

    // Roll Number Sort (Attendance)
    Route::get('/attendance/roll-number-sort', [RollNumberSortController::class, 'index'])->name('roll-number-sort.index');
    Route::post('/attendance/roll-number-sort', [RollNumberSortController::class, 'update'])->name('roll-number-sort.update');

    // Digital Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/students', [AttendanceController::class, 'students'])->name('attendance.students');
    Route::post('/attendance/save', [AttendanceController::class, 'save'])->name('attendance.save');

    // Attendance Register Print
    Route::get('/attendance/register', [AttendanceRegisterController::class, 'index'])->name('attendance-register.index');
    Route::post('/attendance/register/print', [AttendanceRegisterController::class, 'print'])->name('attendance-register.print');
    Route::match(['GET', 'POST'], '/attendance/register/summary', [AttendanceRegisterController::class, 'printSummary'])->name('attendance-register.summary');
    Route::get('/attendance/register/classes/{standard}', [AttendanceRegisterController::class, 'getClasses'])->name('attendance-register.classes');

    // Daily Stats Book (દૈનિક આંકડાબુક)
    Route::get('/daily-stats', [DailyStatsController::class, 'index'])->name('daily-stats.index');
    Route::post('/daily-stats/show', [DailyStatsController::class, 'show'])->name('daily-stats.show');
    Route::get('/daily-stats/print', [DailyStatsController::class, 'print'])->name('daily-stats.print');

    // Certificates (બોનાફાઈડ પ્રમાણપત્ર)
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/search-by-gr', [CertificateController::class, 'searchByGr'])->name('certificates.search-by-gr');
    Route::post('/certificates/search-by-class', [CertificateController::class, 'searchByClass'])->name('certificates.search-by-class');
    Route::get('/certificates/bonafied/preview/{student}/{lang}', [CertificateController::class, 'preview'])->name('certificates.preview');
    Route::get('/certificates/bonafied/print/{student}/{lang}', [CertificateController::class, 'print'])->name('certificates.print');

    // School Settings (Admin only)
    Route::middleware(['role:admin', 'license'])->group(function () {
        Route::get('/settings/school-info', [SchoolSettingController::class, 'edit'])->name('settings.school-info');
        Route::post('/settings/school-info', [SchoolSettingController::class, 'update'])->name('settings.school-info.update');
        Route::post('/settings/school-info/logo', [SchoolSettingController::class, 'uploadLogo'])->name('settings.school-info.logo');
        Route::get('/settings/theme', [ThemeController::class, 'index'])->name('settings.theme.index');
        Route::post('/settings/theme', [ThemeController::class, 'update'])->name('settings.theme.update');
        Route::get('/settings/dropdowns', [DropdownOptionController::class, 'index'])->name('settings.dropdowns.index');
        Route::post('/settings/dropdowns', [DropdownOptionController::class, 'update'])->name('settings.dropdowns.update');
        Route::get('/settings/site', [SiteSettingController::class, 'edit'])->name('settings.site');
        Route::post('/settings/site', [SiteSettingController::class, 'update'])->name('settings.site.update');
        Route::post('/settings/site/favicon', [SiteSettingController::class, 'uploadFavicon'])->name('settings.site.favicon');
        Route::post('/settings/site/favicon/delete', [SiteSettingController::class, 'deleteFavicon'])->name('settings.site.favicon.delete');
        Route::get('/settings/updates', [UpdateController::class, 'index'])->name('settings.updates.index');
        Route::post('/settings/updates/check', [UpdateController::class, 'check'])->name('settings.updates.check');
        Route::post('/settings/updates/run', [UpdateController::class, 'run'])->name('settings.updates.run');
    });

            // Reinstall (admin only, password-protected)
        Route::get('/settings/reinstall', [InstallController::class, 'reinstallForm'])->name('settings.reinstall');
        Route::post('/settings/reinstall', [InstallController::class, 'reinstallConfirm'])->name('settings.reinstall.confirm');

    // License Management (outside role:admin so unlicensed users can reach it)
        Route::get('/settings/license', [LicenseController::class, 'index'])->name('settings.license');
        Route::post('/settings/license/activate', [LicenseController::class, 'activate'])->name('settings.license.activate');
        Route::post('/settings/license/deactivate', [LicenseController::class, 'deactivate'])->name('settings.license.deactivate');
        Route::get('/settings/license/status', [LicenseController::class, 'status'])->name('settings.license.status');

    // Fee Heads
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::resource('heads', FeeHeadController::class)->except(['create', 'edit']);

        // Fee Structures
        Route::get('structures', [FeeStructureController::class, 'index'])->name('structures.index');
        Route::get('structures/by-year/{academicYearId}', [FeeStructureController::class, 'getByYear'])->name('structures.by-year');
        Route::get('structures/{id}', [FeeStructureController::class, 'show'])->name('structures.show');
        Route::post('structures', [FeeStructureController::class, 'store'])->name('structures.store');
        Route::post('structures/update/{id}', [FeeStructureController::class, 'update'])->name('structures.update');
        Route::post('structures/delete/{id}', [FeeStructureController::class, 'destroy'])->name('structures.destroy');
        Route::post('structures/copy', [FeeStructureController::class, 'copyFromPreviousYear'])->name('structures.copy');

        // Student Fee Assignments
        Route::get('assignments', [StudentFeeController::class, 'index'])->name('assignments.index');
        Route::post('assignments/students', [StudentFeeController::class, 'getStudents'])->name('assignments.students');
        Route::post('assignments/bulk', [StudentFeeController::class, 'bulkAssign'])->name('assignments.bulk');
        Route::post('assignments/remove/{id}', [StudentFeeController::class, 'destroy'])->name('assignments.remove');
        Route::post('assignments/update/{id}', [StudentFeeController::class, 'update'])->name('assignments.update');
        Route::post('assignments/unassigned', [StudentFeeController::class, 'getUnassignedStudents'])->name('assignments.unassigned');

        // Fee Collection
        Route::get('collection', [FeeCollectionController::class, 'index'])->name('collection.index');
        Route::post('collection/students', [FeeCollectionController::class, 'getStudents'])->name('collection.students');
        Route::post('collection/pay', [FeeCollectionController::class, 'collect'])->name('collection.pay');
        Route::post('collection/pay-multi', [FeeCollectionController::class, 'collectMulti'])->name('collection.pay-multi');
        Route::get('collection/receipt/{studentId}/{academicYearId}', [FeeCollectionController::class, 'receipt'])->name('collection.receipt');
        Route::post('collection/history', [FeeCollectionController::class, 'studentHistory'])->name('collection.history');

        // Fee Reports
        Route::get('reports', [FeeReportController::class, 'index'])->name('reports.index');
        Route::post('reports/summary', [FeeReportController::class, 'summary'])->name('reports.summary');
        Route::post('reports/due-list', [FeeReportController::class, 'dueList'])->name('reports.due-list');
        Route::post('reports/collection', [FeeReportController::class, 'collectionReport'])->name('reports.collection');
        Route::post('reports/statement', [FeeReportController::class, 'studentStatement'])->name('reports.statement');
        Route::post('reports/search-students', [FeeReportController::class, 'searchStudents'])->name('reports.search-students');
        Route::get('reports/print-summary', [FeeReportController::class, 'printSummary'])->name('reports.print-summary');
        Route::get('reports/print-due-list', [FeeReportController::class, 'printDueList'])->name('reports.print-due-list');
        Route::get('reports/print-collection', [FeeReportController::class, 'printCollectionReport'])->name('reports.print-collection');
        Route::get('reports/print-statement', [FeeReportController::class, 'printStudentStatement'])->name('reports.print-statement');

        // Fee Register
        Route::get('register', [FeeRegisterController::class, 'index'])->name('register.index');
        Route::get('register/print', [FeeRegisterController::class, 'print'])->name('register.print');
    });

    // Transport Module
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::resource('vehicles', VehicleController::class)->except(['create', 'edit']);
        Route::resource('routes', RouteController::class)->except(['create', 'edit']);
        Route::post('routes/{route}/stops', [RouteController::class, 'storeStop'])->name('routes.stops.store');
        Route::put('stops/{stop}', [RouteController::class, 'updateStop'])->name('stops.update');
        Route::delete('stops/{stop}', [RouteController::class, 'destroyStop'])->name('stops.destroy');
        Route::get('assignments', [StudentRouteController::class, 'index'])->name('student-route.index');
        Route::post('assignments', [StudentRouteController::class, 'assign'])->name('student-route.assign');
        Route::post('assignments/bulk', [StudentRouteController::class, 'bulkAssign'])->name('student-route.bulk');
        Route::delete('assignments/{id}', [StudentRouteController::class, 'destroy'])->name('student-route.destroy');
        Route::get('stops/{routeId}', [StudentRouteController::class, 'getStops'])->name('stops.by-route');
        Route::get('student-routes/{studentId}', [StudentRouteController::class, 'getRoutes'])->name('student-routes');
        Route::get('attendance', [BusAttendanceController::class, 'index'])->name('bus-attendance.index');
        Route::post('attendance/mark', [BusAttendanceController::class, 'mark'])->name('bus-attendance.mark');
        Route::get('attendance/print', [BusAttendanceController::class, 'print'])->name('bus-attendance.print');
        Route::get('timetable', [RouteController::class, 'showTimetable'])->name('routes.timetable');
        Route::get('timetable/print', [RouteController::class, 'printTimetable'])->name('routes.timetable.print');
    });

    // Frontsite — Admin Pages
    Route::middleware(['role:admin', 'license'])->group(function () {
        Route::resource('pages', PageController::class);

        // Frontsite — Menu
        Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
        Route::post('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        Route::get('menus/{menu}/items', [MenuController::class, 'getItems'])->name('menus.items');
        Route::post('menu-items', [MenuController::class, 'storeItem'])->name('menu-items.store');
        Route::post('menu-items/{menuItem}', [MenuController::class, 'updateItem'])->name('menu-items.update');
        Route::delete('menu-items/{menuItem}', [MenuController::class, 'destroyItem'])->name('menu-items.destroy');
        Route::post('menu-items/reorder', [MenuController::class, 'reorderItems'])->name('menu-items.reorder');

        // Frontsite — Notice Board
        Route::resource('notice-board', NoticeController::class);

        // Frontsite — Slider
        Route::get('sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::post('sliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::post('sliders/{sliderItem}', [SliderController::class, 'update'])->name('sliders.update');
        Route::delete('sliders/{sliderItem}', [SliderController::class, 'destroy'])->name('sliders.destroy');
        Route::post('sliders/reorder', [SliderController::class, 'reorder'])->name('sliders.reorder');

        // Frontsite — Galleries
        Route::resource('galleries', GalleryController::class);
        Route::post('galleries/{gallery}/images', [GalleryController::class, 'storeImage'])->name('galleries.images.store');
        Route::delete('gallery-images/{galleryImage}', [GalleryController::class, 'destroyImage'])->name('gallery-images.destroy');
        Route::post('galleries/{gallery}/images/reorder', [GalleryController::class, 'reorderImages'])->name('galleries.images.reorder');

        // Frontsite — Homepage Sections
        Route::get('homepage-sections', [HomepageSectionController::class, 'index'])->name('homepage-sections.index');
        Route::post('homepage-sections', [HomepageSectionController::class, 'store'])->name('homepage-sections.store');
        Route::post('homepage-sections/{homepageSection}', [HomepageSectionController::class, 'update'])->name('homepage-sections.update');
        Route::post('homepage-sections/{homepageSection}/content', [HomepageSectionController::class, 'updateContent'])->name('homepage-sections.content');
        Route::delete('homepage-sections/{homepageSection}', [HomepageSectionController::class, 'destroy'])->name('homepage-sections.destroy');
        Route::post('homepage-sections/reorder', [HomepageSectionController::class, 'reorder'])->name('homepage-sections.reorder');

        // Admission Inquiries
        Route::get('admission-inquiries', [AdmissionInquiryController::class, 'index'])->name('admission-inquiries.index');
        Route::get('admission-inquiries/{admissionInquiry}', [AdmissionInquiryController::class, 'show'])->name('admission-inquiries.show');
        Route::post('admission-inquiries/{admissionInquiry}/approve', [AdmissionInquiryController::class, 'approve'])->name('admission-inquiries.approve');
        Route::post('admission-inquiries/{admissionInquiry}/reject', [AdmissionInquiryController::class, 'reject'])->name('admission-inquiries.reject');
        Route::delete('admission-inquiries/{admissionInquiry}', [AdmissionInquiryController::class, 'destroy'])->name('admission-inquiries.destroy');
    });
});

// Frontsite — Public Routes
Route::get('/', [FrontsiteController::class, 'home'])->name('frontsite.home');
Route::get('/admission', [FrontsiteAdmissionController::class, 'form'])->name('frontsite.admission.form');
Route::post('/admission/submit', [FrontsiteAdmissionController::class, 'submit'])->name('frontsite.admission.submit');
Route::get('/notices', [FrontsiteNoticeController::class, 'index'])->name('frontsite.notices.index');
Route::get('/notice/{id}', [FrontsiteNoticeController::class, 'show'])->name('frontsite.notices.show');
Route::get('/{slug}', [FrontsiteController::class, 'page'])->name('frontsite.page')->where('slug', '[a-z0-9\-]+');
