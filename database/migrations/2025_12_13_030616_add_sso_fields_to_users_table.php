<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('sso_user_id')->nullable()->unique()->after('id');
            $table->string('auth_type')->default('local')->after('email'); // 'local' or 'sso'
            $table->string('password')->nullable()->change(); // Make password nullable for SSO users
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sso_user_id', 'auth_type']);
        });
    }
};