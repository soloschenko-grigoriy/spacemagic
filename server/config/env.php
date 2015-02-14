<?php

// Only invoked if mode is "production"
$slim->configureMode('production', function () use ($slim) {
  $slim->config(array(
    'debug'           => false,
    'log.enable'      => true,
    'templates.path'  => __DIR__.'/../'
  ));

});

// Only invoked if mode is "development"
$slim->configureMode('development', function () use ($slim) {

  ini_set('display_errors', 1);
  ini_set('max_execution_time', 0);
  error_reporting(E_ALL);
  $slim->config(array(
    'debug'           => true,
    'log.enable'      => false,  
    'templates.path'  => __DIR__.'/../'
  ));

  $slim->console->start();
});
