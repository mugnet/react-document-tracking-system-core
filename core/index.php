<?php

/**
 * Bootstrap the application
 */
require __DIR__ . '/../bootstrap.php';

// parse url
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], 'core')
);

// Get the app instance
$app = require __DIR__ . '/../server.php';

// set current request uri
$app->setUrl($uri);

// start app
$app->start();
