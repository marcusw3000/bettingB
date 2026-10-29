<?php

declare(strict_types=1);

require dirname(__DIR__) . '/casino/vendor/autoload.php';

$app = require dirname(__DIR__) . '/casino/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'users=' . VanguardLTE\User::count() . PHP_EOL;
echo 'games=' . VanguardLTE\Game::count() . PHP_EOL;
echo 'settings=' . Illuminate\Support\Facades\DB::table('settings')->count() . PHP_EOL;
