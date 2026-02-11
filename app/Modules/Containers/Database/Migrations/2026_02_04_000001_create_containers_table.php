<?php

use App\Modules\Containers\Domain\Enums\ContainerStatus;
use App\Modules\Containers\Domain\Enums\ContainerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 11)->unique();
            $table->string('type')->default(ContainerType::TwentyFeet->value);
            $table->string('status')->default(ContainerStatus::AwaitingUnloading->value);
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
