<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScollioLogsTable extends Migration
{
    public function up()
    {
        $tableName = config('scollio-logger.table', 'scollio_logs');
        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('level', 20)->index();
            $table->text('message');
            $table->string('location')->nullable()->index();
            $table->string('file')->nullable();
            $table->integer('line')->nullable();
            $table->json('context')->nullable();
            $table->string('channel')->default('default')->index();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->index(['level','created_at']);
            $table->index(['channel','created_at']);
        });
    }

    public function down()
    {
        $tableName = config('scollio-logger.table', 'scollio_logs');
        Schema::dropIfExists($tableName);
    }
}