<?php

// NexSchool - School Settings Migration
// શાળાની માહિતી: નામ (gu/en), મેનેજમેન્ટ, સરનામું, UID, સંપર્ક, સોશિયલ મીડિયા
// Singleton: only one row (id=1) should exist

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name_gu');                          // શાળાનું નામ (ગુજરાતી)
            $table->string('school_name_en');                          // School Name (English)
            $table->string('management_name_gu')->nullable();           // મેનેજમેન્ટનું નામ (ગુજરાતી)
            $table->string('management_name_en')->nullable();           // Management Name (English)
            $table->text('address')->nullable();                        // શાળાનું સરનામું
            $table->string('grant_number')->nullable();                 // મંજૂરી નંબર
            $table->date('grant_date')->nullable();                     // મંજૂરી તારીખ
            $table->string('uid_number', 11)->nullable();               // UID (11 અંક)
            $table->string('email')->nullable();                        // સંપર્ક ઇમેઇલ
            $table->string('mobile')->nullable();                       // મોબાઇલ નંબર
            $table->string('whatsapp')->nullable();                     // WhatsApp નંબર
            $table->string('facebook')->nullable();                     // Facebook URL
            $table->string('instagram')->nullable();                    // Instagram URL
            $table->string('youtube')->nullable();                      // YouTube URL
            $table->string('website')->nullable();                      // Website URL
            $table->string('logo')->nullable();                         // શાળાનો લોગો (file path)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
