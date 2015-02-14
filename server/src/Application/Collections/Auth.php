<?php
/**
 * Auth class contains all about autorisation, authentification etc
 *  
 * @package     Application
 * @subpackage  Collections
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0
 */ 
namespace Application\Collections;

class Auth{

	/*
   * @var  $_slim  \Slim\Slim
   */
  private $_slim;

  /**
   * Constructor, depends on Slim class instance
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Collections\Users
   */ 
  public function __construct(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * If this user exists - login, otherwise return false
   * 
   * @param   string  email
   * @param   string  password
   * @return  boolean
   */
  public function login($email, $password)
  {
    $user = \R::findOne('user','email = :email AND password = :password', 
      array('email' => $email, 'password' => sha1($password)
    ));
    
    if(!$user){
      return false;
    }else{
      $user->keycode = sha1($email.$user->id.'mysecretsuperwordthatnobodyknows'.time());
      
      \R::store($user);

      setcookie('user', $user->keycode, time()+60*60*24*30, '/');

      return true;
    }
  }

  /**
   * Delete cookie
   * 
   */
  public function logout()
  {
    if( ! isset($_COOKIE['user']))
    {
      return false;
    }

    setcookie('user', "", time() - 3600, '/');

    return true;
  }
}