<?php
/**
 * Projects bean class
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
    \Application\Models\User,
    \Application\Models\Turn,
    \R as DB;

class Battle extends RedBean_SimpleModel{

  /**
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /**
   * @var  string   $_table  
   */
  private $_table = 'battle';

  /**
   * @var int MAX_BLUNTED
   */
  CONST MAX_BLUNTED = 5;

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
   * @return  \Application\Models\Battle
   */ 
  public function create($user1, $user2)
  {
    $this->user1   = $user1->id;
    $this->user2   = $user2->id;

    if($user1->isBot()){
      $this->current = $user2->id;
    }else if($user2->isBot()){
      $this->current = $user1->id;
    }else{
      $this->current = (rand(1, 2) == 1 ? $user1->id : $user2->id);
    }

    $this->time    = time();

    \R::store($this);

    $this->_slim->drons->prepareBeforeBattle($user1, $user2, $this);
    
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
   * Prepare item before output
   * 
   * @param   void
   * @return  int
   */ 
  public function getEmemyId($userId)
  {
    if($this->user1 == $userId){
      return $this->user2;
    }else{
      return $this->user1;
    }
  }

  /**
   * When user forgotted to make turn
   * 
   * @param   \Application\Models\User $user
   * @return  \Application\Models\Battle
   */
  public function bluntedByUser(User $user)
  {
    if($user->id == $this->user1){
      $this->user1_blunted = $this->user1_blunted + 1;
    }else{
      $this->user2_blunted = $this->user2_blunted + 1;
    }
  }

  /**
   * When user again make a valid turn - reset blune counter
   * 
   * @param   \Application\Models\User $user
   * @return  \Application\Models\Battle
   */
  public function unbluntedByUser(User $user)
  {
    if($user->id == $this->user1){
      $this->user1_blunted = 0;
    }else{
      $this->user2_blunted = 0;
    }
  }

  /**
   * Update turn
   * 
   * @param   void
   * @return  \Application\Models\Battle
   */
  public function updateTurn(Turn $turn, User $user, $giveUp = false)
  {        
    $enemyId  = $this->getEmemyId($user->id);

    $this->turn    = $turn->id; // update turn number and last dron id
    $this->current = $enemyId; // update current player
    
    if($user->id == $this->user1 && $this->user1_blunted >= self::MAX_BLUNTED){ // if current user blunted maximum allowed times
      $this->finish($enemyId);
    }else if($user->id == $this->user2 && $this->user2_blunted >= self::MAX_BLUNTED){
      $this->finish($enemyId);
    }else if(!$this->_slim->drons->getMany('alive', $enemyId)){ // or if enemy has no alive drons
      $this->finish($user->id);
    }else if($giveUp === true){
      $this->finish($enemyId);
    }

    \R::store($this);

    return $this;
  }

  /**
   * Finish this battle
   * 
   * @param   void
   * @return  \Application\Models\Battle
   */
  public function finish($winnerId)
  {
    $this->winner = $winnerId;
    
    return $this;
  }

  /**
   * If last turn (or battle creation) been made to muck time ago - 
   * this battle is not actual any more
   * 
   * @return bool
   */
  public function checkActuality(User $user)
  { 
    return true;
    $time = $this->time;
    $lastTurn = $this->_slim->turns->get('lastInBattle', $this->id);
    if($lastTurn){
      $time = $lastTurn->time;
    }

    if(time() - $time < 120){      
      return true;
    }else{
      $this->finish($lastTurn ? $lastTurn->current_user_id : $user->id);
      DB::store($this);

      return false;
    }
  }

  /**
   * Convert bean to array and prepare all value type
   * 
   * @return  array 
   */
  public function prepare()
  {
    return array(
      'id'      => (int) $this->id,
      'user1'   => (int) $this->user1,
      'user2'   => (int) $this->user2,
      'turn'    => (int) $this->turn,
      'current' => (int) $this->current,
      'winner'  => (int) $this->winner,

      'user1_blunted' => (int) $this->user1_blunted,
      'user2_blunted' => (int) $this->user2_blunted,
    );
  }
}