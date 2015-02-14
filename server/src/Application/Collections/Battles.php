<?php
/**
 * Battles model class
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

use \R as DB;

class Battles{
  
  /*
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /*
   * @var  string   $_table  
   */
  public $_table = 'battle';

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
   * Find and return instance of bean by ID
   * 
   * @param   string  $filter   What kind of beans shoud be found
   * @param   mixed   $value    Value for search
   * @return  \Application\Models\Battle
   */
  public function get($filter = '', $value = null)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    if($filter == 'challenge'){
      $result = DB::findOne($this->_table, 'user2 = :uid', array('uid' => (int) $value));
    }else if($filter == 'current'){
      $result = DB::findOne($this->_table, 'winner IS NULL AND (user1 = :uid OR user2 = :uid)', array('uid'=> (int) $value));
    }else if($filter == 'updated'){
      $result = DB::findOne($this->_table, 'id = :id AND turn > :turn', array('id' => $value['id'], 'turn' => $value['turn']));
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
   * @return  \Application\Models\Battle[]
   */
  public function getMany($filter = '', $value = null, $lastId = 0)
  {
    DB::findAll($this->_table);
  }

  /**
   * Create a new bean by params
   * 
   * @param   arary   $params   New bean will be created by this params
   * @return  \Application\Models\Battle
   */
  public function create(array $model)
  {
    $user1 = $this->_slim->users->get(null, $model['user1']);
    $user2 = $this->_slim->users->get(null, $model['user2']);


    if(!$user1 || !$user2){ // user not found
      return null;
    }
   
    if(!$user1->isReady() || !$user2->isReady()){ // some user is not ready
      return null;
    }

    $user1->makeNotReady();
    $user2->makeNotReady();

    $battle = DB::dispense($this->_table)->create($user1, $user2);

    return $battle->prepare();
  }

  /**
   * Iterativly export each bean to array
   * 
   * @param   \Application\Models\Battle[]
   * @return  array
   */
  public function prepareAll(array $battles)
  {
    $result = array();
    foreach ($battles as $battle) {
      $result[] = $battle->prepare();
    }

    return $result;
  }
}