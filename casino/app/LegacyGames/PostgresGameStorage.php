<?php

namespace VanguardLTE\LegacyGames;

use Illuminate\Support\Facades\DB;
use VanguardLTE\LegacyGames\Contracts\GameStorage;

final class PostgresGameStorage implements GameStorage
{
    public function connectionName(): string
    {
        return (string) config('legacy_games.connection', 'pgsql');
    }

    public function transaction(callable $callback): mixed
    {
        return DB::connection($this->connectionName())->transaction($callback);
    }
}
