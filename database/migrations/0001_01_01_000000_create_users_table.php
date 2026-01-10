<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Gender;
use App\Constants\UserStatus;
use App\Constants\UserEmailVerificationStatus;
use App\Constants\UserType;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('gender')
                ->default(Gender::MALE)
                ->comment('1=Male, 2=Female')
                ->index();
            $table->string('user_type')
                ->default(UserType::EXECUTIVE)
                ->comment('1=Male, 2=Female')
                ->index();
            $table->unsignedTinyInteger('email_verification_status')
                ->default(UserEmailVerificationStatus::VERIFIED)
                ->comment('1=Verified, 0=Unverified')
                ->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedTinyInteger('user_status')
                ->default(UserStatus::ACTIVE)
                ->comment('0=Inactive, 1=Active')
                ->index();
            $table->rememberToken();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | PASSWORD RESET TOKENS
        |--------------------------------------------------------------------------
        */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | SESSIONS
        |--------------------------------------------------------------------------
        */
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');

            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
