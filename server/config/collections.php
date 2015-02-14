<?php
$slim->container->singleton('auth', function () use($slim){
  return new \Application\Collections\Auth($slim);
});

$slim->container->singleton('users', function () use($slim){
  return new \Application\Collections\Users($slim);
});

$slim->container->singleton('drons', function () use($slim){
  return new \Application\Collections\Drons($slim);
});

$slim->container->singleton('modifications', function () use($slim){
  return new \Application\Collections\Modifications($slim);
});

$slim->container->singleton('battles', function () use($slim){
  return new \Application\Collections\Battles($slim);
});

$slim->container->singleton('challenges', function () use($slim){
  return new \Application\Collections\Challenges($slim);
});

$slim->container->singleton('turns', function () use($slim){
  return new \Application\Collections\Turns($slim);
});

$slim->container->singleton('AI', function () use($slim){
  return new \Application\Models\AI($slim);
});