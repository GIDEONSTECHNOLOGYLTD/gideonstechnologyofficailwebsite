<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Table;

class CreateReferralCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_codes', function (Table $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code', 12)->unique();
            $table->integer('uses')->default(0);
            $table->integer('max_uses')->default(0)->comment('0 for unlimited');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_codes');
    }
}
