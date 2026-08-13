<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentFullExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $students;

    public function __construct(Collection $students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'GR નંબર',
            'પ્રવેશ ધોરણ', 'પ્રવેશ વર્ગ',
            'હાલનું ધોરણ', 'હાલનો વર્ગ',
            'પ્રવેશ તારીખ', 'અગાઉની હાજરી (દિવસ)',
            'નામ (ગુ.)', 'નામ (En)', 'પિતાનું નામ (ગુ.)', 'પિતાનું નામ (En)',
            'અટક (ગુ.)', 'અટક (En)', 'પૂરું નામ (ગુ.)', 'પૂરું નામ (En)',
            'માતાનું નામ (ગુ.)', 'માતાનું નામ (En)',
            'જન્મ તારીખ', 'જન્મ તારીખ (અક્ષરો ગુ.)', 'જન્મ તારીખ (અક્ષરો En)',
            'જન્મ સ્થળ (ગુ.)', 'જન્મ સ્થળ (En)', 'વતન (ગુ.)', 'વતન (En)', 'ગામ (ગુ.)', 'ગામ (En)',
            'ધર્મ (ગુ.)', 'ધર્મ (En)', 'જ્ઞાતિ (ગુ.)', 'જ્ઞાતિ (En)', 'શ્રેણી (ગુ.)', 'શ્રેણી (En)',
            'લઘુમતી', 'કુમાર/કુમારી',
            'છેલ્લી શાળા (ગુ.)', 'છેલ્લી શાળા (En)', 'RTE', 'ફોટો',
            'મોબાઇલ', 'WhatsApp', 'APAAR ID', 'UID નંબર', 'PEN નંબર', 'આધાર નંબર',
            'આધાર મુજબ નામ', 'રેશનકાર્ડ નંબર',
            'બેંક નામ', 'બેંક શાખા', 'IFSC', 'બેંક ખાતા નંબર', 'બેંક મુજબ નામ',
            'LC નંબર', 'શાળા છોડવાની તારીખ', 'LC જારી તારીખ', 'શાળા છોડવાનું ધોરણ',
            'શાળા છોડવાનું કારણ (ગુ.)', 'શાળા છોડવાનું કારણ (En)', 'હાજરી (દિવસ)', 'નોંધ',
            'સ્થિતિ', 'નોંધાયેલ',
        ];
    }

    public function map($s): array
    {
        return [
            $s->gr_number,
            $s->admissionStandard?->name ?? '',
            $s->admissionClass?->name ?? '',
            $s->currentStandard?->name ?? '',
            $s->currentClass?->name ?? '',
            $s->date_of_admission ? \Carbon\Carbon::parse($s->date_of_admission)->format('d/m/Y') : '',
            $s->previous_attendance_days,
            $s->student_name_gu, $s->student_name_en,
            $s->father_name_gu, $s->father_name_en,
            $s->surname_gu, $s->surname_en,
            $s->full_name_gu, $s->full_name_en,
            $s->mother_name_gu, $s->mother_name_en,
            $s->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->format('d/m/Y') : '',
            $s->dob_in_text_gu, $s->dob_in_text_en,
            $s->birth_place_gu, $s->birth_place_en,
            $s->native_place_gu, $s->native_place_en,
            $s->gaam, $s->gaam_en,
            $s->religion_gu, $s->religion_en,
            $s->cast_gu, $s->cast_en,
            $s->category_gu, $s->category_en,
            $s->is_minority ? 'હા' : 'ના',
            $s->sharirik_jaati === 'kumar' ? 'કુમાર' : ($s->sharirik_jaati === 'kumari' ? 'કુમારી' : ''),
            $s->last_school_gu, $s->last_school_en,
            $s->admission_under_rte ? 'હા' : 'ના',
            $s->photo,
            $s->mobile, $s->whatsapp,
            $s->apaar_id, $s->uid_no, $s->pen_no, $s->aadhar_no,
            $s->name_as_per_aadhar, $s->ration_card_no,
            $s->bank_name, $s->bank_branch, $s->bank_ifsc, $s->bank_account_no, $s->name_as_per_bank,
            $s->lc_number,
            $s->leaving_date ? \Carbon\Carbon::parse($s->leaving_date)->format('d/m/Y') : '',
            $s->lc_issue_date ? \Carbon\Carbon::parse($s->lc_issue_date)->format('d/m/Y') : '',
            $s->leavingStandard?->name ?? '',
            $s->leaving_reason_gu, $s->leaving_reason_en,
            $s->attendance_days, $s->leaving_remarks,
            $s->status,
            $s->is_registered ? 'હા' : 'ના (અનબોર્ડ)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E7FF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A1:{$highestCol}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
            ],
        ]);

        $sheet->getStyle("A2:{$highestCol}{$highestRow}")->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }
}
