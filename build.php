<?php

/**
 * This script is for making a standalone "app.phar" script from the whole app;
 * And to be ready for the use of package (phpacker) later
 */

$script_name = 'app.php';
$output_name = 'app.phar';

$phar = new Phar($output_name);

// Include all your source .php files
$phar->buildFromDirectory(__DIR__, '/\.(php)$/');

// Include the full vendor directory (autoloader needs JSON files too)
$phar->buildFromDirectory(__DIR__ . '/vendor', '/./');

$stub = "#!/usr/bin/env php\n" . Phar::createDefaultStub($script_name);
$phar->setStub($stub);

echo $output_name . " created\n";
