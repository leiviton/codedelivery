<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagSincronizadoVisitaOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('type')->nullable();
            $table->string('product')->nullable();
            $table->string('plano')->nullable();
            $table->string('id_plano')->nullable();
            $table->smallInteger('flag_sincronizado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('phone1');
            $table->dropColumn('phone2');
            $table->dropColumn('type');
            $table->dropColumn('product');
            $table->dropColumn('plano');
            $table->dropColumn('id_plano');
            $table->dropColumn('flag_sincronizado');
        });
    }
}
