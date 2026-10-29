<?php

namespace VanguardLTE\LegacyGames\Contracts;

interface GameStorage
{
    public function connectionName(): string;

    public function transaction(callable $callback): mixed;
}
