<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Table;

class CreateReferralRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_rewards', function (Table $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Referrer user ID');
            $table->unsignedBigInteger('referred_user_id')->comment('Referred user ID');
            $table->string('action_type')->comment('signup, first_purchase, service_booking');
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_rewards');
    }
}
