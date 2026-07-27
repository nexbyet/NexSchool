<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $rows;
    protected $columns;
    protected $hasSrNo;
    protected $columnWidths;

    public function __construct(array $rows, array $columns, bool $hasSrNo, array $columnWidths = [])
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->hasSrNo = $hasSrNo;
        $this->columnWidths = $columnWidths;
    }

    public function collection()
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        $heads = [];
        if ($this->hasSrNo) {
            $heads[] = 'ક્રમ';
        }
        $labels = [
            'gr_number' => 'GR નંબર',
            'full_name_gu' => 'પૂરું નામ (ગુ.)',
            'full_name_en' => 'Full Name (En)',
            'student_name_gu' => 'નામ (ગુ.)',
            'student_name_en' => 'Name (En)',
            'father_name_gu' => 'પિતાનું નામ (ગુ.)',
            'father_name_en' => "Father's Name (En)",
            'surname_gu' => 'અટક (ગુ.)',
            'surname_en' => 'Surname (En)',
            'mother_name_gu' => 'માતાનું નામ (ગુ.)',
            'mother_name_en' => "Mother's Name (En)",
            'date_of_birth' => 'જન્મ તારીખ',
            'age' => 'ઉંમર',
            'sharirik_jaati' => 'કુમાર/કુમારી',
            'category_gu' => 'શ્રેણી (ગુ.)',
            'category_en' => 'Category (En)',
            'religion_gu' => 'ધર્મ (ગુ.)',
            'religion_en' => 'Religion (En)',
            'cast_gu' => 'જ્ઞાતિ (ગુ.)',
            'cast_en' => 'Cast (En)',
            'mobile' => 'મોબાઇલ',
            'whatsapp' => 'WhatsApp',
            'aadhar_no' => 'આધાર નંબર',
            'apaar_id' => 'APAAR ID',
            'uid_no' => 'UID નંબર',
            'pen_no' => 'PEN નંબર',
            'current_standard' => 'હાલનું ધોરણ',
            'current_class' => 'હાલનો વર્ગ',
            'date_of_admission' => 'પ્રવેશ તારીખ',
            'admission_standard' => 'પ્રવેશ ધોરણ',
            'last_school_gu' => 'છેલ્લી શાળા (ગુ.)',
            'last_school_en' => 'Last School (En)',
            'birth_place_gu' => 'જન્મ સ્થળ (ગુ.)',
            'native_place_gu' => 'વતન (ગુ.)',
            'gaam' => 'ગામ (ગુ.)',
            'gaam_en' => 'Village (En)',
            'is_minority' => 'લઘુમતી',
            'admission_under_rte' => 'RTE',
            'is_registered' => 'નોંધાયેલ',
            'total_fee' => 'કુલ ફી',
            'paid_fee' => 'ભરેલ ફી',
            'due_fee' => 'બાકી ફી',
        ];
        foreach ($this->columns as $col) {
            $heads[] = $labels[$col] ?? $col;
        }
        return $heads;
    }

    public function map($row): array
    {
        $data = [];
        $idx = $row['__sr_index'] ?? 0;
        if ($this->hasSrNo) {
            $data[] = $idx;
        }
        foreach ($this->columns as $col) {
            $data[] = $row[$col] ?? '';
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestCol}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A2:{$highestCol}{$highestRow}")->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
