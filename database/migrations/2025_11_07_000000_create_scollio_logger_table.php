<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scollio_logger', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('method', 10)->nullable();
            $table->text('url')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('headers')->nullable();
            $table->json('body')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->integer('status_code')->nullable();
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->text('exception_message')->nullable();
            $table->text('exception_file')->nullable();
            $table->integer('exception_line')->nullable();
            $table->longText('exception_trace')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->uuid('request_id')->nullable();
            $table->string('route_action')->nullable();
            $table->timestamps();

            $table->index(['status_code','requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scollio_logger');
    }
};
