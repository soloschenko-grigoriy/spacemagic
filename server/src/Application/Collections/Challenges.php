<?php
/**
 * Challenges model class
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
    \R as DB;

class Challenges{
  
  /*
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /*
   * @var  string   $_table  
   */
  private $_table = 'challenge';

  /**
   * Constructor, depends on Slim class instance
   * 
   * @param   \Slim\Slim $slim
   * @return  \Application\Collections\Challenges
   */ 
  public function __construct(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * Find and return user
   * 
   * @param   string  $filter   What kind of beans shoud be found
   * @param   mixed   $value    Value for search
   * @return  \Application\Models\Challenge
   */
  public function get($filter = '', $value = null)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    if($filter == 'target'){
      $result = DB::findOne($this->_table, 'target = ?', array($value));
    }else if($filter == 'targetAndStatus'){
      $result = DB::findOne($this->_table, 'target = :target AND status = :status', 
        array('target' => $value['target'], 'status' => $value['status'])
      );
    }else if($filter == 'initiator'){
      $result = DB::findOne($this->_table, 'initiator = ?', array($value));
    }else if($filter == 'targetOrInitiatorAndStatus'){
      $result = DB::findOne(
        $this->_table, 
        '(initiator = :uid OR target = :uid) AND status = :status ORDER BY id DESC LIMIT 1', 
        array('uid' => $value['uid'], 'status' => $value['status'])
      );
    }else{
      $result = DB::load($this->_table, $value);
    }

    if($result){
      return $result->box();
    }

    return null;
  }

  /**
   * Find and return part of users by filter
   * 
   * @param   string  $filter what kind of users shoud be found
   * @param   mixed   $value    Value for search
   * @param   int     $lastId used for pagination - ID of last loaded user
   * @return  \Application\Models\Challenge[]
   */
  public function getMany($filter = '', $value = null, $lastId = 0)
  {
    return DB::findAll($this->_table);
  }

  /**
   * Create a new bean by params
   * 
   * @param   arary   $params   New bean will be created by this params
   * @return  \Application\Models\Challenge
   */
  public function create(array $model)
  {
    $target = $this->_slim->users->get(null, $model['target']);
    if(!$target){
      return null;
    }

    return DB::dispense($this->_table)->create($target)->prepare();
  }

  public function clear(User $user)
  {
    $current = $this->get('targetOrInitiatorAndStatus', array('uid' => $user->id, 'status' => 'wait'));
    if(!$current){
      return $this;
    }

    if($current->initiator == $user->id){
      $current->status = 'reseted';
    }else{
      $current->status = 'declined';
    }

    DB::store($current);
  }

  /**
   * Iterativly export each user in array
   * 
   * @param   \Application\Models\Challenge[]
   * @return  array
   */
  public function prepareAll(array $challenges)
  {
    $result = array();
    foreach ($challenges as $challenge) {
      $result[] = $challenge->prepare();
    }

    return $result;
  }
}