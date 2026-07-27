# Changelog

## v1.1.2 (July 27, 2026)

### New
- **Custom Report — Excel & PDF Export** — Two new buttons "Excel ડાઉનલોડ" and "PDF ડાઉનલોડ" appear after preview. Excel via `maatwebsite/excel` (.xlsx, auto-sized columns, borders), PDF via `barryvdh/laravel-dompdf` (A4 landscape). Same form data reused; shared `buildReportData()` method.
- **Dashboard — Unregistered Students Table** — New amber-themed table below the standard×category matrix showing unregistered student counts by standard & class (boys/girls/total). Main matrix now filters `is_registered = true`.

### Fixed
- **Dashboard Birthday Range** — Birthdays on start and end dates were excluded due to time-of-day comparison. Changed `now()` to `now()->startOfDay()` / `now()->addDays(10)->endOfDay()` so full boundary dates are included.
- **Bus Students Due/Route List Pages** — Routes `bus-students/due-list` and `bus-students/print-route-list` were not opening because the wildcard `{busOnlyStudent}` was defined before specific GET routes. Reordered: all specific GET routes (`data`, `due-list`, `print-due-list`, `print-route-list`, `routes`) now come before `{busOnlyStudent}`. Also fixed `fetchData` route from POST to GET (JS sends GET).
- **Fee Register — Carry Forward Payments Missing** — Fee register filtered `FeePayment` by `semester` (e.g., `WHERE semester = 1`), but carry forward payments have `semester = null` (no fee structure). Added separate query to load carry forward payments and merge them into the main payments collection, so the balance calculation now includes carry forward payments.

### Technical
- Composer: Added `barryvdh/laravel-dompdf` v3.1.2 (+ `dompdf/dompdf` v3.1.6) for PDF generation.
- New file: `app/Exports/CustomReportExport.php` — implements `FromCollection`, `WithHeadings`, `WithMapping`, `ShouldAutoSize`, `WithStyles`.
- New view: `resources/views/custom-report/pdf.blade.php` — DomPDF-compatible A4 landscape layout using `DejaVu Sans` font, logo via `public_path()`.
- Two new routes: `POST /reports/custom/export-excel` → `custom-report.export-excel`, `POST /reports/custom/download-pdf` → `custom-report.download-pdf`.

## v1.1.1 (July 11, 2026)

### Fixed
- **Custom Report Generator** — Fixed unbalanced braces in standard→class cascade `.then()` callback that broke the entire DOMContentLoaded handler (standard/class cascade, drag-drop, SortableJS, report type toggle all stopped working).

## v1.1.0 (July 11, 2026)

### Fixed
- **Custom Report Generator** — Fixed extra `});` that broke all JavaScript (students not loading, columns not adding, preview broken).

## v1.0.9 (July 11, 2026)

### Fixed
- **Fee Receipt ₹ Words** — Fixed `Undefined array key 15` when amount ≥ ₹1,00,000. Lakh/crore/hajjar positions now use `$w()` helper instead of `$guDigits[]` (which only goes 0-9).
- **Bus Students Route Names** — Fixed `bus-students.due-list` and `bus-students.print-route-list` route names missing `transport.` prefix. Fixed all hardcoded JS URLs (`/bus-students/` → `/transport/bus-students/`).
- **Custom Report Student Selection** — Fixed `loadStudentList()` not being callable from inline `onchange` (was scoped inside `DOMContentLoaded` closure). Moved to global scope.

## v1.0.8 (July 11, 2026)

### New
- **Bus-Only Students** — New module for students from other schools who use only bus service. Separate `bus_only_students` table with fee tracking (sem1/sem2), bus attendance integration, due list, route-wise print. 11 new routes + sidebar link.
- **Unregistered (અનબોર્ડ) Students** — `is_registered` boolean on students table. Auto-generated UR-NNNN GR numbers. Excluded from attendance/daily-stats. Optional inclusion in custom reports. Stats card on dashboard.
- **ગામ Field** — New `gaam`/`gaam_en` column added to students table via migration. Admission form now has dedicated "ગામ" field. Fee register address shows ગામ first. Custom reports have ગામ fields too.

### Fixed
- **Bus Attendance Print** — Now shows 3 student type badges (શાળા/અનબોર્ડ/બસ) with proper merged data structure.
- **BusAttendance Model** — `bus_only_student_id` and `student_type` added to fillable + busOnlyStudent() relation added.

### Technical
- 5 new migrations: `add_gaam_to_students`, `add_is_registered_to_students`, `create_bus_only_students`, `create_bus_only_fee_payments`, `add_bus_only_student_id_to_bus_attendances`.
- `BusAttendanceController@index`/`@mark`/`@print` — Merged 3 student types (regular, unregistered, bus_only) into unified collection.
- `BusOnlyStudent` model with computed `total_fee`, `total_paid`, `due_fee` accessors.
- Sidebar — "બીજી શાળાના બસ વિદ્યાર્થીઓ" link added under transport section.

## v1.0.7 (June 27, 2026)

### New
- **Custom Reports** — Manual student selection (checkbox list by standard/class), column-based sorting (asc/desc), 3 fee fields (કુલ ફી, ભરેલ ફી, બાકી ફી).

### Fixed
- **Roll Number Sort** — Fixed drag crash (`InvalidCharacterError` on `dragClass`) and save button error (`Cannot set properties of null`). `dragClass` changed to array; buttons now use `getElementById`.
- **Fee Statement Search** — Replaced `<select>` dropdown with autocomplete text input (GR/name search).
- **Student Table Overflow** — Removed `overflow-hidden` on parent; added `overflow-x-auto rounded-xl` on table wrapper with `whitespace-nowrap`.
- **Pagination Padding** — Added `px-4 py-3` to pagination wrapper.

### Technical
- `dragClass` in SortableJS config changed from string to array (`['shadow-xl', 'scale-105']`).
- Save/Reset buttons use `document.getElementById()` instead of `document.querySelector('button[onclick="..."]')`.

## v1.0.6 (June 27, 2026)

### New
- **Teacher Timetable View**: Teachers can now view the timetable (read-only) via a new sidebar link under "સમયપત્રક" section. All write operations restricted to admin.

### Fixed
- **બાકી ફી રિપોર્ટ (Due List)** — Now groups fee data per student in a single row with dynamic columns per semester × fee type, plus a total pending column. Eliminates duplicate rows for same student.
- **Fee Report Print Views** — Added school name header (`$school->school_name_gu`) to all 4 print templates (summary, due list, collection, statement).
- **Font sizes increased** across all fee report print views for better readability.

### Technical
- `FeeReportController@dueList` — Rewritten to group by student with `entries` keyed as `sem_{semester}_{type}`, sorted by total_due descending.
- `FeeReportController@printDueList` — Same grouping logic for print output.
- `TimetableController` — All write methods return 403 for non-admin. View hides edit/delete UI when `$readOnly` is true.
- Sidebar — Added timetable link for teacher role.
