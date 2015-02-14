<?php
/**
 * User bean class
 *  
 * @package     Application
 * @subpackage  Models
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0
 */ 
namespace Application\Models;

use RedBean_SimpleModel;

class User extends RedBean_SimpleModel{

  /*
   * @property  $_slim  \Slim\Slim
   */
  private $_slim;

  /**
   * Application dependency injection.
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Models\User
   */  
  public function setSlim(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * Change this bean by params
   * 
   * @param   array   $model 
   * @return  \Application\Interfaces\IBean 
   */
  public function edit(array $model)
  {
    return $this;
  }

  /**
   * Delete this bean
   * 
   * @return  bool 
   */
  public function remove(){}

  /**
   * Set user as ready for game
   * 
   * @param   void
   * @return  \Application\Models\User
   */
  public function makeReady()
  {
    $this->ready = 'yes';

    \R::store($this);

    return $this;
  }

  /**
   * Set user as ready for game
   * 
   * @param   void
   * @return  \Application\Models\User
   */
  public function makeNotReady()
  {
    $this->ready = 'no';

    \R::store($this);

    return $this;
  }

  /**
   * Is this user is ready or not
   * 
   * @param   void
   * @return  bool
   */
  public function isReady()
  {
    if($this->ready == 'yes' || $this->ready === true){
      return true;
    }else{
      return false;
    }
  }

  /**
   * Checks if this user is a bot or not
   * 
   * @return boolean
   */
  public function isBot()
  {
    if($this->email == ''){
      return true;
    }

    return false;
  }

  /**
   * Convert user to array and prepare all value type
   * 
   * @return  array 
   */
  public function prepare()
  {
    return array(
      'id'    => (int) $this->id,
      'name'  => $this->name,
      'ava'   => $this->ava,
      'bot'   => $this->isBot(),
      'ready' => $this->ready == 'yes' ? true : false
    );
  }
}