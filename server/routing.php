<?php
// sleep(3);
// ----------------- INDEX ----------------- //
$slim->get('/', function () use($slim){
  $slim->render('layout.phtml', $slim->indexController->index());
});

// ----------------- LOGIN ----------------- //
$slim->post('/login', function () use($slim){
  echo json_encode($slim->loginController->index());
});

$slim->get('/logout', function () use($slim){
  echo json_encode($slim->loginController->logout());
});

// ----------------- USER ----------------- //
$slim->get('/users', function () use($slim){
  echo json_encode($slim->userController->getMany());
});

$slim->get('/user', function () use($slim){
  echo json_encode($slim->userController->get());
});

$slim->get('/user/:id', function ($id) use($slim){
  echo json_encode($slim->userController->get($id));
});


$slim->put('/user/:id', function ($id) use($slim){
  echo json_encode($slim->userController->update($id));
});

// ----------------- CHALLENGE ----------------- //
$slim->get('/challenge', function() use ($slim){
  echo json_encode($slim->challengeController->get());
});
$slim->get('/challenge/:id', function($id) use ($slim){
  echo json_encode($slim->challengeController->get($id));
});
$slim->post('/challenge', function() use ($slim){
  echo json_encode($slim->challengeController->create());
});

$slim->patch('/challenge/:id', function($id) use($slim){
  echo json_encode($slim->challengeController->update($id));
});

$slim->put('/challenge/:id', function($id) use($slim){
  echo json_encode($slim->challengeController->update($id));
});

$slim->delete('/challenge/:id', function($id) use($slim){
  echo json_encode($slim->challengeController->delete($id));
});

// ----------------- BATTLE ----------------- //
$slim->get('/battle', function() use ($slim){
  echo json_encode($slim->battleController->get());
});
$slim->get('/battle/:id', function($id) use ($slim){
  echo json_encode($slim->battleController->get($id));
});
$slim->post('/battle', function() use ($slim){
  echo json_encode($slim->battleController->create());
});

$slim->put('/battle/:id', function($id) use($slim){
  echo json_encode($slim->battleController->update($id));
});

// ----------------- DRON ----------------- //
$slim->get('/drons', function() use ($slim){
  echo json_encode($slim->dronController->getMany());
});

$slim->put('/dron/:id', function ($id) use($slim){
  echo json_encode($slim->dronController->update($id));
});

// ----------------- TURN ----------------- //
$slim->get('/turn', function() use ($slim){
  echo json_encode($slim->turnController->get());
});

$slim->post('/turn', function() use ($slim){
  echo json_encode($slim->turnController->create());
});

$slim->get('/turns', function() use ($slim){
  echo json_encode($slim->turnController->getMany());
});

