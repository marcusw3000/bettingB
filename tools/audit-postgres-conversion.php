<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$games = $root . '/casino/app/Games';
$patterns = [
    'mysql connection' => '/connections[^\n]*mysql|connection\([\'\"]mysql|\[[\'\"]mysql[\'\"]\]/i',
    'mysql syntax' => '/\b(auto_increment|insert\s+ignore|replace\s+into|ifnull|find_in_set|group_concat|unix_timestamp)\b/i',
    'outbound cURL' => '/\bcurl_(init|exec)\s*\(|\bCURLOPT_(POST|POSTFIELDS)\b/i',
];
$counts = array_fill_keys(array_keys($patterns), 0);
$affected = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($games));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $tokens = token_get_all(file_get_contents($file->getPathname()));
    $contents = '';
    foreach ($tokens as $token) {
        if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
            continue;
        }
        $contents .= is_array($token) ? $token[1] : $token;
    }
    foreach ($patterns as $name => $pattern) {
        if (preg_match($pattern, $contents)) {
            $counts[$name]++;
            $affected[$file->getPathname()] = true;
        }
    }
}

echo 'Affected game files: ' . count($affected) . PHP_EOL;
foreach ($counts as $name => $count) {
    echo $name . ': ' . $count . PHP_EOL;
}
