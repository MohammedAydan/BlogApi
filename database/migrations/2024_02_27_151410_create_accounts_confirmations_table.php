<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("owner_id")->constrained("users");
            $table->text("full_name_in_arabic");
            $table->text("full_name_in_english");
            $table->text("id_card_front");
            $table->text("id_card_back");
            $table->boolean("status")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_confirmations');
    }
};
