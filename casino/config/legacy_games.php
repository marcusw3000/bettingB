<?php

return [
    /*
     * Legacy games stay off until they have passed the PostgreSQL parity suite.
     * This prevents an unconverted game from silently using the application
     * connection during the migration.
     */
    'enabled' => env('LEGACY_GAMES_ENABLED', false),
    'connection' => env('LEGACY_GAMES_DB_CONNECTION', 'pgsql'),
];
