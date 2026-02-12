<?php

use App\Modules\Ships\Domain\Enums\ShipStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('imo', 7)->unique();
            $table->dateTime('eta');
            $table->dateTime('etd');
            $table->string('status')->default(ShipStatus::Scheduled->value);
            $table->string('berth_code', 20)->nullable();
            $table->boolean('has_pending_operations')->default(false);
            $table->timestamps();

            $table->index(['status', 'eta']);
            $table->index('berth_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
