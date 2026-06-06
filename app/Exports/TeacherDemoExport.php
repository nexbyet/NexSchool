<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeacherDemoExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'whatsapp_number',
            'address',
            'date_of_birth',
            'gender',
            'joining_date',
            'joining_number',
            'experience_in_years',
            'blood_group',
            'basic_pay',
            'max_lwp',
            'max_cl',
            'ratings',
            'basic_salary',
            'other_salary',
            'status',
            'reason_inactive',
            'date_inactive',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Rameshbhai Patel',
                'ramesh@school.com',
                '9876543210',
                '9876543210',
                'Ahmedabad',
                '15/08/1985',
                'male',
                '01/06/2020',
                'JN001',
                '10',
                'B+',
                '35000',
                '12',
                '15',
                'A',
                '30000',
                '5000',
                'active',
                '',
                '',
            ],
            [
                'Geetaben Shah',
                'geeta@school.com',
                '9876543211',
                '',
                'Vadodara',
                '20/03/1990',
                'female',
                '15/06/2021',
                'JN002',
                '8',
                'A+',
                '32000',
                '12',
                '15',
                'B+',
                '28000',
                '4000',
                'active',
                '',
                '',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('A1:T1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFF3CD');
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return [];
    }
}
