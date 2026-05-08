<?php

if (! defined('L5_SWAGGER_CONST_HOST')) {
    $appUrl = getenv('APP_URL');

    if (! is_string($appUrl) || $appUrl === '') {
        $envFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'.env';

        if (is_file($envFile)) {
            $contents = file_get_contents($envFile);

            if ($contents !== false && preg_match('/^APP_URL=(.+)$/m', $contents, $matches)) {
                $appUrl = trim($matches[1], "\"'");
            }
        }
    }

    if (! is_string($appUrl) || $appUrl === '') {
        $appUrl = 'http://localhost';
    }

    define('L5_SWAGGER_CONST_HOST', rtrim($appUrl, '/'));
}
