<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('complaint_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->string('status');
            $table->unsignedBigInteger('changed_by')->nullable(); // user who changed status
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_status_histories');
    }
};
