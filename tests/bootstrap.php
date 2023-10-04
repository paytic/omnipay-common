<?php

declare(strict_types=1);

define('PROJECT_BASE_PATH', __DIR__ . '/..');
define('TEST_BASE_PATH', __DIR__);
define('TEST_FIXTURE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(TEST_BASE_PATH . DIRECTORY_SEPARATOR . '.env')) {
    $dotenv = new Dotenv();
    $dotenv->bootEnv(TEST_BASE_PATH . '/.env');
}

function envVar($key)
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    return getenv($key);
}

$_ENV['MOBILPAY_PUBLIC_CER'] = gzinflate(base64_decode(envVar('MOBILPAY_PUBLIC_CER')));
$_ENV['MOBILPAY_PRIVATE_KEY'] = gzinflate(base64_decode(envVar('MOBILPAY_PRIVATE_KEY')));
