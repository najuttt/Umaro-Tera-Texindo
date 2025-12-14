<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('hpp_logs', function (Blueprint $table) {
$table->id();
$table->date('date');
$table->decimal('hpp_total', 12, 2);
$table->string('note')->nullable();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('hpp_logs');
}
};