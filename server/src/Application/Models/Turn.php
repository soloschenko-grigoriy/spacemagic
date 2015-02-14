<?php
/**
 * Turn bean class
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
    \RuntimeException,
    \Application\Models\User,
    \Application\Models\Battle,
    \Application\Models\Dron,
    \R as DB;

class Turn extends RedBean_SimpleModel{

  /**
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /**
   * @var  string   $_table  
   */
  private $_table = 'turn';

  /**
   * Application dependency injection.
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Models\Battle
   */ 
  public function setSlim(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * General battle preparations after creating
   * 
   * @param   \Application\Models\User   $user1 
   * @param   \Application\Models\User   $user2
   * @return  \Application\Models\Turn
   */ 
  public function create(array $model, User $user)
  { 
    $this->current_user_id = $user->id;
    $this->time            = time();

    $currentBattle = $this->setCurrentBattle($user);

    // if this ise bot's turn - change current userId to bot's ud
    if(isset($model['bot']) && $model['bot'] == true){
      $this->current_user_id = $currentBattle->getEmemyId($user->id);
      $user = $this->_slim->users->get('uid', $this->current_user_id);
    }

    $currentDron   = $this->setCurrentDron($user);
    
    if($this->isRetreated($model)){
      DB::store($this);
      $currentBattle->updateTurn($this, $user, true);

      return $this;
    }

    $dronMoved         = $this->moveDronIfNeccessary($model, $currentDron);
    $dronExecutedSkill = $this->executeModificationIfNeccessary($model, $currentDron);
    
    if($model['blunt'] == true){
      $currentBattle->bluntedByUser($user);
    }else{
      $currentBattle->unbluntedByUser($user);
    }
      
    $this->skip = $model['skip'] == true ? 'yes' : 'no';
    $this->blunt = $model['blunt'] == true ? 'yes' : 'no';
    DB::store($this);


    $currentBattle->updateTurn($this, $user);
    $this->_slim->drons->changePrevious($currentDron);

    return $this;
  }

  /**
   * Change this bean by params
   * 
   * @param   array   $model 
   * @return  \Application\Interfaces\IBean 
   */
  public function edit(array $model){}

  /**
   * Delete this bean
   * 
   * @return  bool 
   */
  public function remove(){}

  /**
   * Checks if user retreated from battle
   * 
   * @param  array   $model  
   * @param  Battle  $battle 
   * @return boolean         
   */
  public function isRetreated(array $model)
  {
    if($model['retreat'] === true){
      return true;
    }else{
      return false;
    }
  }

  /**
   * Find current battle and set it
   *
   * @return \Application\Models\Battle
   */
  public function setCurrentBattle()
  {
    $currentBattle = $this->_slim->battles->get('current', $this->current_user_id);
    if(!$currentBattle){
      throw new RuntimeException("This user is not on battle now", 1);
    }

    $this->battle_id = $currentBattle->id;

    return $currentBattle;
  }

  /**
   * Finds and set current dron if correct dron's information provided
   * 
   * @return \Application\Models\Dron
   */
  public function setCurrentDron()
  {
    $currentDron = $this->_slim->drons->get('currentAndUid', $this->current_user_id);
    if(!$currentDron){ // if provided dron exists 
      throw new RuntimeException("Current dron does not exists", 1);
    } 

    $this->current_dron_id = $currentDron->id;

    return $currentDron;
  }

  /**
   * Checks if provided dron been moved and if it is - trys to move it
   *  
   * @param  array                    $model
   * @param  \Application\Models\Dron  $dron
   * @return bool                    
   */
  public function moveDronIfNeccessary(array $model, Dron $dron)
  {
    if(!isset($model['currentDronLine']) || !isset($model['currentDronNum']) || !isset($model['currentDronAngle'])){
      return false; 
    }

    // if($model['skip'] == false && $dron->line == $model['currentDronLine'] && $dron->num == $model['currentDronNum']){
    //   return false;
    // }

    $dron->move($model['currentDronLine'], $model['currentDronNum'], $model['currentDronAngle']);    
    $this->current_dron_line  = $dron->line;
    $this->current_dron_num   = $dron->num;
    $this->current_dron_angle = $dron->angle;

    return true;
  }

  /**
   * Checks if current dron executed modification and if it is - try to execute it
   * 
   * @param  array                   $model       
   * @param  \Application\Models\Dron $currentDron 
   * @throws RuntimeException If modification does't exists
   * @throws RuntimeException If modification does't belongs to current dron
   * @throws RuntimeException If target dron does't exeists
   * @return bool                      
   */
  public function executeModificationIfNeccessary(array $model, Dron $currentDron)
  {
    // ignore if target dron or active modification absent
    if(!isset($model['targetDronId']) || !isset($model['activeModificationId'])){
      return false;
    }

    // ignore  if target dron or active modification incorrect
    if((int) $model['targetDronId'] < 1 || (int) $model['activeModificationId'] < 1){
      return false;
    }

    $activeModification = $this->_slim->modifications->get(null, $model['activeModificationId']);
    if(!$activeModification){
      throw new RuntimeException("Active modification does not exists", 1);
    }

    if(!$currentDron->hasModification($activeModification)){
      throw new RuntimeException("Target dron ($currentDron->id) can't use active modification ($activeModification->id)", 504);
    }

    $targetDron = $this->_slim->drons->get(null, $model['targetDronId']);
    if(!$targetDron){
      throw new RuntimeException("Target dron does not exists", 1);
    }

    $activeModification->execute($model, $currentDron, $targetDron);

    $this->target_dron_id = $targetDron->id;
    $this->activeModificationId  = $activeModification->id;

    if(isset($model['damage'])){
      $this->damage = $model['damage'];
    }

    return true;
  } 

  /**
   * Convert bean to array and prepare all value type
   * 
   * @return  array 
   */
  public function prepare($extra = false)
  {
    $battle   = array();
    $drons    = array();
    if($extra === true){
      $battle = $this->_slim->battles->get(null, $this->battle_id); 
      if($battle){
        $drons = array_merge(
          $this->_slim->drons->prepareAll($this->_slim->drons->getMany('uid', $this->current_user_id)),
          $this->_slim->drons->prepareAll($this->_slim->drons->getMany('uid', $battle->getEmemyId($this->current_user_id)))
        );
        $battle = $battle->prepare();
      }
    }

    return array(
      'id'                    => (int) $this->id,
      'skip'                  => $this->skip == 'yes' ? true : false,
      'blunt'                 => $this->blunt == 'yes' ? true : false,
      'drons'                 => $drons,
      'battle'                => $battle,
      'damage'                => (float) $this->damage,
      'battleId'              => (int) $this->battle_id,
      'timeLeft'              => (int) time() - $this->time,
      'targetDronId'          => (int) $this->target_dron_id,
      'currentUserId'         => (int) $this->current_user_id,
      'currentDronId'         => (int) $this->current_dron_id,
      'currentDronLine'       => (int) $this->current_dron_line,
      'currentDronNum'        => (int) $this->current_dron_num,
      'activeModificationId'  => (int) $this->active_modification_id,

    );
  }
}