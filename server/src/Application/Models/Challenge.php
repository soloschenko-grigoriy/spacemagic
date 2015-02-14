<?php
/**
 * Challenge bean class
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

use RedBean_SimpleModel,
    \R as DB;

class Challenge extends RedBean_SimpleModel{

  /**
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /**
   * @var  string   $_table  
   */
  private $_table = 'battle';

  /**
   * Application dependency injection.
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Models\Challenge
   */ 
  public function setSlim(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * General preparations after creating
   * 
   * @param   \Application\Models\User   $target 
   * @return  \Application\Models\Challenge
   */ 
  public function create($target)
  {
    $initiator = $this->_slim->users->get('current');

    $this->target     = $target->id;
    $this->initiator  = $initiator->id;
    $this->status     = 'wait';
    $this->timer      = 30;
    $this->time       = time();

    if(!$initiator->isReady() || !$target->isReady()){ // some user is not ready
      $this->status = 'declined';
    }

    $target->makeNotReady();
    $initiator->makeNotReady();

    $id = DB::store($this);

    if($target->isBot()){
      $this->edit(array('id' => $id, 'status' => 'accepted'));
    }

    return $this;
  }

  /**
   * Change this bean by params
   * 
   * @param   array   $model 
   * @return  \Application\Models\Challenge 
   */
  public function edit(array $model)
  {
    if(isset($model['status']) && $this->status != $model['status']){
      $this->status = $model['status'];

      if($this->status != 'wait'){
        $this->_slim->users->get(null, $this->target)->makeReady();
        $this->_slim->users->get(null, $this->initiator)->makeReady();
      }

      if($this->status === 'accepted'){
        $this->_slim->battles->create(array(
          'user1' => $this->initiator,
          'user2' => $this->target,
        ));
      }
    }

    if(isset($model['timer']) && $this->timer != $model['timer']){
      $this->timer = $model['timer'];
    }

    DB::store($this);
    
    return $this->prepare();
  }

  /**
   * Delete this bean
   * 
   * @return  bool 
   */
  public function remove()
  {
    DB::trash($this);

    return true;
  }

  /**
   * Convert bean to array and prepare all value type
   * 
   * @return  array 
   */
  public function prepare()
  {
    return array(
      'id'        => (int) $this->id,
      'time'      => (int) $this->time,
      'timer'     => (int) $this->timer,
      'status'    =>  $this->status,
      'target'    => (int) $this->target,
      'initiator' => (int) $this->initiator
    );
  }
}