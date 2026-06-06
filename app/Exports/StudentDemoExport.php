<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentDemoExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'gr_number',
            'admission_standard',
            'admission_class',
            'current_standard',
            'current_class',
            'date_of_admission',
            'student_name_gu',
            'student_name_en',
            'father_name_gu',
            'father_name_en',
            'surname_gu',
            'surname_en',
            'mother_name_gu',
            'mother_name_en',
            'date_of_birth',
            'birth_place_gu',
            'birth_place_en',
            'native_place_gu',
            'native_place_en',
            'religion_gu',
            'religion_en',
            'cast_gu',
            'cast_en',
            'category_gu',
            'category_en',
            'is_minority',
            'sharirik_jaati',
            'last_school_gu',
            'last_school_en',
            'admission_under_rte',
            'mobile',
            'whatsapp',
            'apaar_id',
            'uid_no',
            'pen_no',
            'aadhar_no',
            'name_as_per_aadhar',
            'ration_card_no',
            'bank_name',
            'bank_branch',
            'bank_ifsc',
            'bank_account_no',
            'name_as_per_bank',
        ];
    }

    public function array(): array
    {
        return [
            [
                '1001',
                'ધોરણ ૧',
                'A',
                'ધોરણ ૧',
                'A',
                '15/06/2024',
                'રોહન',
                'Rohan',
                'રમેશભાઈ',
                'Rameshbhai',
                'શાહ',
                'Shah',
                'સીતાબેન',
                'Seetaben',
                '15/10/2014',
                'અમદાવાદ',
                'Ahmedabad',
                'અમદાવાદ',
                'Ahmedabad',
                'હિંદુ',
                'Hindu',
                'પટેલ',
                'Patel',
                'બક્ષીપંચ',
                'OBC',
                'NO',
                'kumar',
                '',
                '',
                'NO',
                '9876543210',
                '9876543210',
                '',
                '',
                '',
                '123456789012',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                '1002',
                'ધોરણ ૧',
                'B',
                'ધોરણ ૧',
                'B',
                '16/06/2024',
                'કૃતિ',
                'Kriti',
                'મહેશભાઈ',
                'Maheshbhai',
                'પટેલ',
                'Patel',
                'ગીતાબેન',
                'Geetaben',
                '20/11/2013',
                'વડોદરા',
                'Vadodara',
                'વડોદરા',
                'Vadodara',
                'હિંદુ',
                'Hindu',
                'પટેલ',
                'Patel',
                'બક્ષીપંચ',
                'OBC',
                'NO',
                'kumari',
                '',
                '',
                'NO',
                '9876543211',
                '',
                '',
                '',
                '',
                '234567890123',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('A1:AU1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFF3CD');
        foreach (range('A', 'AU') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return [];
    }
}
