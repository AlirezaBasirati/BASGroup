<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('encrypted_text');
            $table->string('recipient');
            $table->string('iv'); // initialization vector stored as hex
            $table->timestamp('expires_at');
            $table->timestamps(); // creates created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
