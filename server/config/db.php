<?php

define( 'REDBEAN_MODEL_PREFIX', '\\Application\\Models\\' ); 

\R::setup('mysql:host=localhost;dbname=game','game','game');
\R::freeze(true);

$di = new RedBean_DependencyInjector;
\RedBean_ModelHelper::setDependencyInjector( $di );

$di->addDependency('Slim', $slim);

