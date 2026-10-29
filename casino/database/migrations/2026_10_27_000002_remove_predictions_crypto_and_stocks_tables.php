<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove modules no longer exposed by the Promex player interface.
     */
    public function up(): void
    {
        Schema::dropIfExists('prediction_votes');
        Schema::dropIfExists('prediction_markets');

        Schema::dropIfExists('crypto_positions');
        Schema::dropIfExists('crypto_price_snapshots');
        Schema::dropIfExists('crypto_rounds');
        Schema::dropIfExists('crypto_assets');

        Schema::dropIfExists('stock_positions');
        Schema::dropIfExists('stock_price_snapshots');
        Schema::dropIfExists('stock_rounds');
        Schema::dropIfExists('stock_assets');
    }

    /**
     * The prior migrations remain in the repository as the restoration source.
     */
    public function down(): void
    {
        // Intentionally left empty: rebuilding these retired modules requires their original migrations.
    }
};
