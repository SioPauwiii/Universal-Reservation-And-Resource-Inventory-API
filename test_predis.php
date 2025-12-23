<?php
require __DIR__ . '/vendor/autoload.php';

function tryPing($params) {
    try {
        $client = new Predis\Client($params);
        $resp = $client->ping();
        echo "OK:" . $resp . PHP_EOL;
    } catch (Exception $e) {
        echo "ERR:" . $e->getMessage() . PHP_EOL;
    }
}

echo "Trying without password...\n";
tryPing(['scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => 6379]);

echo "Trying with password from .env (if any)...\n";
$envPass = null;
if (file_exists(__DIR__ . '/.env')) {
    $env = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/^REDIS_PASSWORD=(.*)$/m', $env, $m)) {
        $envPass = trim($m[1], " \"'\r\n");
    }
}
if ($envPass) {
    tryPing(['scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => 6379, 'password' => $envPass]);
} else {
    echo "No REDIS_PASSWORD found in .env\n";
}
