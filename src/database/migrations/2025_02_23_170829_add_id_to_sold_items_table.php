<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sold_items', function (Blueprint $table) {
            // 既存のテーブルにidカラムを追加
            $table->id()->first();
        });
    }

    public function down()
    {
        Schema::table('sold_items', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
