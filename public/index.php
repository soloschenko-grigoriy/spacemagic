<?php
require '../server/vendor/autoload.php';
require '../server/vendor/redbean/rb.php';

$slim = new \Slim\Slim(array(
  // 'mode' => 'production',
  'mode' => 'development',
));

// Load configs
$configs = glob('../server/config/*.php');
foreach ($configs as $config) {
  require $config;
}

// Automatically load router files
require '../server/routing.php';

$slim->run();