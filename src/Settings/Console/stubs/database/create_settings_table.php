<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = $this->getTable();

        [
            'key' => $keyColumn,
            'value' => $valueColumn,
            'autoload' => $autoloadColumn
        ] = $this->getColumns();

        Schema::create(
            $table,
            function (Blueprint $table) use ($keyColumn, $valueColumn, $autoloadColumn) {
                $table->id();
                $table->string($keyColumn)->unique();
                $table->json($valueColumn);
                $table->boolean($autoloadColumn)->default(true)->index();
                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        $table = $this->getTable();

        Schema::dropIfExists($table);
    }

    protected function getTable(): string
    {
        return config('monet.settings.database.table');
    }

    protected function getColumns(): array
    {
        return config('monet.settings.database.columns');
    }
};
