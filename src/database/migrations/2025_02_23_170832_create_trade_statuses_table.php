<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trade_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sold_item_id')->constrained('sold_items', 'item_id');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_statuses');
    }
};
