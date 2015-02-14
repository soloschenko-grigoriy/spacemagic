<?php
/**
 * Turn model class
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

use \Application\Models\User,
    \Application\Models\Turn,
    \RuntimeException,
    \R as DB;

class Turns{
  
  /**
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /**
   * @var  string   $_table  
   */
  public $_table = 'turn';

  /**
   * Constructor, depends on Slim class instance
   * 
   * @param   \Slim\Slim $slim
   * @return  \Application\Collections\Turns
   */ 
  public function __construct(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * Find and return instance of bean by ID
   * 
   * @param   string  $filter   What kind of beans shoud be found
   * @param   mixed   $value    Value for search
   * @return  \Application\Models\Turn
   *
   * @throws InvalidArgumentException   If no current user
   * 
   */
  public function get($filter = '', $value = null)
  {
    $currentUser = $this->_slim->users->get('current');
    if(!$currentUser){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    if($filter == 'lastIdAndBattle'){
      $battle = $this->_slim->battles->get('current', $currentUser->id);

      if(!$battle){
        return DB::findOne($this->_table, 'id > :lastId AND battle_id = :battleId', array('lastId' => $value['lastId'], 'battleId' => $value['battleId']));
      }
      $enemy = $this->_slim->users->get(null, $battle->getEmemyId($currentUser->id));

      // if($enemy->isBot()){
      //   $this->_slim->AI->makeTurn($enemy, $currentUser);
      // }
      
      $this->checkLastTurn($value['battleId'], $currentUser);
      $result = DB::findOne($this->_table, 'id > :lastId AND battle_id = :battleId', array('lastId' => $value['lastId'], 'battleId' => $value['battleId']));
    }else if($filter == 'lastInBattle'){
      $result = DB::findOne($this->_table, 'battle_id = ? ORDER BY id DESC LIMIT 1', array($value));     
    }else{
      $result = DB::load($this->_table, $value);
    }

    if($result){
      return $result->box();
    }

    return null;
  }

  /**
   * Find and return part of beans by filter
   * 
   * @param   string  $filter   What kind of beans shoud be found
   * @param   mixed   $value    Value for search
   * @param   int     $lastId   Used for pagination - ID of last loaded bean
   * @return  \Application\Models\Turn[]
   */
  public function getMany($filter = '', $value = null, $lastId = 0)
  {
    if($filter == 'battle'){
      return DB::findAll($this->_table, 'battle_id = ?', array((int) $value));
    }else{
      return DB::findAll($this->_table);
    }
  }

  /**
   * Create a new bean by params
   * 
   * @param   arary   $params   New bean will be created by this params
   * @return  \Application\Models\Turn
   */
  public function create(array $model)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }
    if(!isset($model['currentUserId']) || $user->id != $model['currentUserId']){
      throw new InvalidArgumentException("Some data is absent", 1);
    }

    $turn = DB::dispense($this->_table)->create($model, $user);

    return $turn->prepare(true);
  }

  /**
   * Checks if smt been wrong with opponent's turn
   * If last turn been made to far ago - blunt enemy user and make trun automaticly
   *
   * @param integer $battleId
   * @return \Application\Collections\Turns
   */
  public function checkLastTurn($battleId, User $currentUser)
  {
    return;
    if((int) $battleId < 1){
      throw new RuntimeException("Battle ID should be specified", 1); 
    }

    $lastTurn = $this->get('lastInBattle', $battleId);
    if(!$lastTurn){
      return $this;
    }

    if(time() - $lastTurn->time < 20){
      return $this;
    }

    $battle = $this->_slim->battles->get(null, $battleId);
    if(!$battle){
      return $this;
    }
    
    $enemy = $this->_slim->users->get(null, $battle->getEmemyId($currentUser->id));
    DB::dispense($this->_table)->create(array(), $enemy);

    return $this;
  }

  /**
   * Iterativly export each bean to array
   * 
   * @param   \Application\Models\Turn[]
   * @return  array
   */
  public function prepareAll(array $turns)
  {
    $result = array();
    foreach ($turns as $turn) {
      $result[] = $turn->prepare();
    }

    return $result;
  }
}