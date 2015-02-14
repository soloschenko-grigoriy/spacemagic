<?php
$slim->container->singleton('battleController', function () use($slim){
  return new \Application\Controllers\Battle($slim);
});

$slim->container->singleton('dronController', function () use($slim){
  return new \Application\Controllers\Dron($slim);
});

$slim->container->singleton('indexController', function () use($slim){
  return new \Application\Controllers\Index($slim);
});

$slim->container->singleton('loginController', function () use($slim){
  return new \Application\Controllers\Login($slim);
});

$slim->container->singleton('userController', function () use($slim){
  return new \Application\Controllers\User($slim);
});

$slim->container->singleton('challengeController', function () use($slim){
  return new \Application\Controllers\Challenge($slim);
});

$slim->container->singleton('turnController', function () use($slim){
  return new \Application\Controllers\Turn($slim);
});