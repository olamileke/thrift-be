<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditMonthlyIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_income', function (Blueprint $table) {
            
            $table->renameColumn('amount', 'original_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_income', function (Blueprint $table) {
            
            $table->renameColumn('original_amount', 'amount');
        });
    }
}
