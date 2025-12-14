<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('expense_logs', function (Blueprint $table) {
$table->id();
$table->date('date');
$table->string('description');
$table->decimal('amount', 12, 2);
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('expense_logs');
}
};