<?php

namespace App\Helpers;

class DateTextHelper
{
    private static $guOrdinalDays = [
        1 => 'પહેલી', 'બીજી', 'ત્રીજી', 'ચોથી', 'પાંચમી', 'છઠ્ઠી', 'સાતમી', 'આઠમી', 'નવમી', 'દસમી',
        11 => 'અગિયારમી', 'બારમી', 'તેરમી', 'ચૌદમી', 'પંદરમી', 'સોળમી', 'સત્તરમી', 'અઢારમી', 'ઓગણીસમી', 'વીસમી',
        21 => 'એકવીસમી', 'બાવીસમી', 'ત્રેવીસમી', 'ચોવીસમી', 'પચ્ચીસમી', 'છવ્વીસમી', 'સત્તાવીસમી', 'અઠ્ઠાવીસમી', 'ઓગણત્રીસમી', 'ત્રીસમી',
        31 => 'એકત્રીસમી',
    ];

    private static $guMonths = [
        'જાન્યુઆરી', 'ફેબ્રુઆરી', 'માર્ચ', 'એપ્રિલ', 'મે', 'જૂન',
        'જુલાઈ', 'ઓગસ્ટ', 'સપ્ટેમ્બર', 'ઓક્ટોબર', 'નવેમ્બર', 'ડિસેમ્બર',
    ];

    private static $guNums = [
        0 => '', 1 => 'એક', 2 => 'બે', 3 => 'ત્રણ', 4 => 'ચાર', 5 => 'પાંચ',
        6 => 'છ', 7 => 'સાત', 8 => 'આઠ', 9 => 'નવ', 10 => 'દસ',
        11 => 'અગિયાર', 12 => 'બાર', 13 => 'તેર', 14 => 'ચૌદ', 15 => 'પંદર',
        16 => 'સોળ', 17 => 'સત્તર', 18 => 'અઢાર', 19 => 'ઓગણીસ', 20 => 'વીસ',
        21 => 'એકવીસ', 22 => 'બાવીસ', 23 => 'ત્રેવીસ', 24 => 'ચોવીસ', 25 => 'પચ્ચીસ',
        26 => 'છવ્વીસ', 27 => 'સત્તાવીસ', 28 => 'અઠ્ઠાવીસ', 29 => 'ઓગણત્રીસ', 30 => 'ત્રીસ',
        31 => 'એકત્રીસ', 32 => 'બત્રીસ', 33 => 'તેત્રીસ', 34 => 'ચોત્રીસ', 35 => 'પાંત્રીસ',
        36 => 'છત્રીસ', 37 => 'સાડત્રીસ', 38 => 'અડત્રીસ', 39 => 'ઓગણચાળીસ', 40 => 'ચાળીસ',
        41 => 'એકતાળીસ', 42 => 'બેતાળીસ', 43 => 'તેતાળીસ', 44 => 'ચુંમાળીસ', 45 => 'પિસ્તાળીસ',
        46 => 'છેતાળીસ', 47 => 'સુડતાળીસ', 48 => 'અડતાળીસ', 49 => 'ઓગણપચાસ', 50 => 'પચાસ',
        51 => 'એકાવન', 52 => 'બાવન', 53 => 'ત્રેપન', 54 => 'ચોપન', 55 => 'પંચાવન',
        56 => 'છપ્પન', 57 => 'સત્તાવન', 58 => 'અઠ્ઠાવન', 59 => 'ઓગણસાઠ', 60 => 'સાઠ',
        61 => 'એકસઠ', 62 => 'બાસઠ', 63 => 'ત્રેસઠ', 64 => 'ચોસઠ', 65 => 'પાંસઠ',
        66 => 'છસઠ', 67 => 'સડસઠ', 68 => 'અડસઠ', 69 => 'ઓગણોસિત્તેર', 70 => 'સિત્તેર',
        71 => 'એકોતેર', 72 => 'બોતેર', 73 => 'તેોતેર', 74 => 'ચુમોતેર', 75 => 'પંચોતેર',
        76 => 'છોતેર', 77 => 'સિત્તોતેર', 78 => 'ઈઠોતેર', 79 => 'ઓગણાએંસી', 80 => 'એંસી',
        81 => 'એક્યાસી', 82 => 'બ્યાસી', 83 => 'તેરાસી', 84 => 'ચોરાસી', 85 => 'પંચાસી',
        86 => 'છ્યાસી', 87 => 'સત્તાસી', 88 => 'અઠ્ઠાસી', 89 => 'નેવાસી', 90 => 'નેવું',
        91 => 'એકાણું', 92 => 'બાણું', 93 => 'ત્રાણું', 94 => 'ચોરાણું', 95 => 'પંચાણું',
        96 => 'છાણું', 97 => 'સત્તાણું', 98 => 'અઠ્ઠાણું', 99 => 'નવ્વાણું',
    ];

    private static $enOrdinalDays = [
        1 => 'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'FIFTH', 'SIXTH', 'SEVENTH', 'EIGHTH', 'NINTH', 'TENTH',
        11 => 'ELEVENTH', 'TWELFTH', 'THIRTEENTH', 'FOURTEENTH', 'FIFTEENTH', 'SIXTEENTH', 'SEVENTEENTH', 'EIGHTEENTH', 'NINETEENTH', 'TWENTIETH',
        21 => 'TWENTY-FIRST', 'TWENTY-SECOND', 'TWENTY-THIRD', 'TWENTY-FOURTH', 'TWENTY-FIFTH', 'TWENTY-SIXTH', 'TWENTY-SEVENTH', 'TWENTY-EIGHTH', 'TWENTY-NINTH', 'THIRTIETH',
        31 => 'THIRTY-FIRST',
    ];

    private static $enNums = [
        0 => '', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR', 5 => 'FIVE',
        6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE', 10 => 'TEN',
        11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN', 14 => 'FOURTEEN', 15 => 'FIFTEEN',
        16 => 'SIXTEEN', 17 => 'SEVENTEEN', 18 => 'EIGHTEEN', 19 => 'NINETEEN', 20 => 'TWENTY',
        21 => 'TWENTY-ONE', 22 => 'TWENTY-TWO', 23 => 'TWENTY-THREE', 24 => 'TWENTY-FOUR', 25 => 'TWENTY-FIVE',
        26 => 'TWENTY-SIX', 27 => 'TWENTY-SEVEN', 28 => 'TWENTY-EIGHT', 29 => 'TWENTY-NINE', 30 => 'THIRTY',
        31 => 'THIRTY-ONE', 32 => 'THIRTY-TWO', 33 => 'THIRTY-THREE', 34 => 'THIRTY-FOUR', 35 => 'THIRTY-FIVE',
        36 => 'THIRTY-SIX', 37 => 'THIRTY-SEVEN', 38 => 'THIRTY-EIGHT', 39 => 'THIRTY-NINE', 40 => 'FORTY',
        41 => 'FORTY-ONE', 42 => 'FORTY-TWO', 43 => 'FORTY-THREE', 44 => 'FORTY-FOUR', 45 => 'FORTY-FIVE',
        46 => 'FORTY-SIX', 47 => 'FORTY-SEVEN', 48 => 'FORTY-EIGHT', 49 => 'FORTY-NINE', 50 => 'FIFTY',
        51 => 'FIFTY-ONE', 52 => 'FIFTY-TWO', 53 => 'FIFTY-THREE', 54 => 'FIFTY-FOUR', 55 => 'FIFTY-FIVE',
        56 => 'FIFTY-SIX', 57 => 'FIFTY-SEVEN', 58 => 'FIFTY-EIGHT', 59 => 'FIFTY-NINE', 60 => 'SIXTY',
        61 => 'SIXTY-ONE', 62 => 'SIXTY-TWO', 63 => 'SIXTY-THREE', 64 => 'SIXTY-FOUR', 65 => 'SIXTY-FIVE',
        66 => 'SIXTY-SIX', 67 => 'SIXTY-SEVEN', 68 => 'SIXTY-EIGHT', 69 => 'SIXTY-NINE', 70 => 'SEVENTY',
        71 => 'SEVENTY-ONE', 72 => 'SEVENTY-TWO', 73 => 'SEVENTY-THREE', 74 => 'SEVENTY-FOUR', 75 => 'SEVENTY-FIVE',
        76 => 'SEVENTY-SIX', 77 => 'SEVENTY-SEVEN', 78 => 'SEVENTY-EIGHT', 79 => 'SEVENTY-NINE', 80 => 'EIGHTY',
        81 => 'EIGHTY-ONE', 82 => 'EIGHTY-TWO', 83 => 'EIGHTY-THREE', 84 => 'EIGHTY-FOUR', 85 => 'EIGHTY-FIVE',
        86 => 'EIGHTY-SIX', 87 => 'EIGHTY-SEVEN', 88 => 'EIGHTY-EIGHT', 89 => 'EIGHTY-NINE', 90 => 'NINETY',
        91 => 'NINETY-ONE', 92 => 'NINETY-TWO', 93 => 'NINETY-THREE', 94 => 'NINETY-FOUR', 95 => 'NINETY-FIVE',
        96 => 'NINETY-SIX', 97 => 'NINETY-SEVEN', 98 => 'NINETY-EIGHT', 99 => 'NINETY-NINE',
    ];

    public static function gujaratiDateText($day, $month, $year)
    {
        $dayText = self::$guOrdinalDays[(int)$day] ?? (string)$day;
        $monthText = self::$guMonths[(int)$month - 1] ?? '';
        $yearText = self::gujaratiYear($year);
        return "$dayText $monthText $yearText";
    }

    public static function englishDateText($day, $month, $year)
    {
        $dayText = self::$enOrdinalDays[(int)$day] ?? strtoupper((string)$day);
        $months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
        $monthText = $months[(int)$month - 1] ?? '';
        $yearText = self::englishYear($year);
        return "$dayText $monthText $yearText";
    }

    private static function gujaratiYear($year)
    {
        $y = (int)$year;
        $lastTwo = $y % 100;
        $suffix = $lastTwo > 0 ? ' ' . (self::$guNums[$lastTwo] ?? (string)$lastTwo) : '';

        if ($y >= 2000) {
            return 'બે હજાર' . $suffix;
        }

        // 1900–1999
        $century = 'ઓગણીસસો';
        return $century . $suffix;
    }

    private static function englishYear($year)
    {
        $y = (int)$year;
        $lastTwo = $y % 100;

        if ($y >= 2000) {
            if ($lastTwo === 0) return 'TWO THOUSAND';
            return 'TWO THOUSAND ' . (self::$enNums[$lastTwo] ?? strtoupper((string)$lastTwo));
        }

        // 1900–1999
        $firstTwo = (int)floor($y / 100); // 19
        $century = self::$enNums[$firstTwo] ?? strtoupper((string)$firstTwo); // NINETEEN

        if ($lastTwo === 0) return $century . ' HUNDRED';
        if ($lastTwo < 10) return $century . ' OH ' . (self::$enNums[$lastTwo] ?? strtoupper((string)$lastTwo));

        return $century . ' ' . (self::$enNums[$lastTwo] ?? strtoupper((string)$lastTwo));
    }
}
