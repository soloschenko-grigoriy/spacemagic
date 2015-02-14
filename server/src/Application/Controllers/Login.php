<?php
/**
 * Login controller class 
 * Contains all handling login routes
 *  
 * @package     Application
 * @subpackage  Controllers
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0
 */ 
namespace Application\Controllers;

use \InvalidArgumentException;

class Login{

  /**
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /**
   * Constructor, depends on Slim class instance
   * 
   * @param   \Slim\Slim $slim
   * @return  \Application\Collections\Battles
   */ 
  public function __construct(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * Index route
   * 
   * @return array
   */ 
  public function index()
  {
    if(!isset($_POST['email']) || !isset($_POST['password'])){
      return array('result' => false);
    }

    if($this->_slim->auth->login($_POST['email'], $_POST['password'])){
      return array('result' => true);
    }else{
      return array('result' => false);
    }
  }

  /**
   * Quit route
   * 
   * @return void
   */
  public function logout()
  {
    $result = $this->_slim->auth->logout();

    return array('result' => $result);
  }

}