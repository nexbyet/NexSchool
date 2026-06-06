<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('religion_gu')->nullable()->after('religion');
            $table->string('religion_en')->nullable()->after('religion_gu');
            $table->string('category_gu')->nullable()->after('category');
            $table->string('category_en')->nullable()->after('category_gu');
        });

        // Backfill existing data: copy religion → religion_gu, category → category_gu
        DB::table('students')->whereNotNull('religion')->update([
            'religion_gu' => DB::raw('religion'),
        ]);
        DB::table('students')->whereNotNull('category')->update([
            'category_gu' => DB::raw('category'),
        ]);

        // Infer category_en from category_gu using fixed mapping
        $catMap = [
            'સામાન્ય' => 'General',
            'અનુસુચિત જાતિ' => 'SC',
            'અનુસુચિત જન જાતિ' => 'ST',
            'બક્ષીપંચ' => 'OBC',
            'આર્થિક પછાત' => 'EWS',
        ];
        foreach ($catMap as $gu => $en) {
            DB::table('students')->where('category_gu', $gu)->update(['category_en' => $en]);
        }

        // Infer religion_en from religion_gu
        $relMap = [
            'હિન્દુ' => 'Hindu',
            'મુસ્લિમ' => 'Muslim',
            'શીખ' => 'Sikh',
            'બૌદ્ધ' => 'Buddhist',
            'ઈસાઈ' => 'Christian',
            'પારસી' => 'Parsi',
        ];
        foreach ($relMap as $gu => $en) {
            DB::table('students')->where('religion_gu', $gu)->update(['religion_en' => $en]);
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('religion');
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('religion')->nullable()->after('id');
            $table->string('category')->nullable()->after('religion');
        });

        DB::table('students')->whereNotNull('religion_gu')->update([
            'religion' => DB::raw('religion_gu'),
        ]);
        DB::table('students')->whereNotNull('category_gu')->update([
            'category' => DB::raw('category_gu'),
        ]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('religion_gu');
            $table->dropColumn('religion_en');
            $table->dropColumn('category_gu');
            $table->dropColumn('category_en');
        });
    }
};
