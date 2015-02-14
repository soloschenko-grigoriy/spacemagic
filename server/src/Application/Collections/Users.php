<?php
/**
 * Users model class contains all about user model
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

class Users{
  
  /*
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /*
   * @var string  $_name  
   */
  private $_table = 'user';

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
   * Find and return user
   * 
   * @param   string  $filter   What kind of beans shoud be found
   * @param   mixed   $value    Value for search
   * @return  \Application\Models\User
   */
  public function get($filter = '', $value = null)
  {
    if($filter == 'current'){
      if(!isset($_COOKIE['user'])){
        $result = null;
      }else{  
        $result = DB::findOne($this->_table, 'keycode = ?', array($_COOKIE['user']));
      }
    }else if($filter == 'randomReady'){
      $result = DB::findOne($this->_table, 'ready = "yes" AND id <> :id ORDER BY RAND()', array('id' => $value));
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
   * @return  \Application\Models\User[]
   */
  public function getMany($filter = '', $value = null, $lastId = 0)
  {
    if($filter == 'ready'){ // find users, that ready for battle
      return $this->getReady($lastId);
    }else{ // find all users
      return DB::findAll($this->_table, 'id > :lastId', array('lastId' => (int) $lastId));
    }
  }

  public function getReady($lastId)
  {
    $result = DB::findAll($this->_table, 'ready = "yes" AND id > :lastId AND id <> :myId AND email <> "" LIMIT 7', array(
      'lastId' => (int) $lastId, 
      'myId'   => $this->get('current')->id
    ));

    if(count($result) < 7){
      $bots = DB::findAll($this->_table, 'ready = "yes" AND id > :lastId AND email = "" LIMIT :limit', array(
        'lastId' => (int) $lastId,
        'limit'  => 7 - count($result)
      ));
      $result = array_merge($result, $bots);
    }

    return $result;    
  }

  /**
   * Create a new bean by params
   * 
   * @param   arary   $params   New bean will be created by this params
   * @return  \Application\Models\User
   */
  public function create(array $params){}

  /**
   * Iterativly export each user in array
   * 
   * @param   \Application\Models\User[]
   * @return  array
   */
  public function prepareAll(array $users)
  {
    $result = array();
    foreach ($users as $user) {
      $result[] = $user->prepare();
    }

    return $result;
  }

  /*
  ini_set('max_execution_time', 0);
    $bots = DB::findAll('user', 'id > 4132 AND email = " " LIMIT :limit', array(
      'limit'  => 10000
    ));

    $uids = array_map(function($one){ return $one['id']; }, DB::exportAll($bots));
    $app = $this->_slim;
    
    DB::transaction( function() use($uids, $app){
      foreach ($uids as $key => $uid) {
        $app->drons->create(array('uid' => $uid));
      }
    });
   */
}