<?php
/**
 * Index controller class 
 * Contains all handling index routes
 *  
 * @package     Application
 * @subpackage  Controllers
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0.0
 */ 
namespace Application\Controllers;

use \InvalidArgumentException,
    \R as DB;

class Index{

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
    $me         = array();
    $turns      = array();
    $enemy      = array();
    $battle     = array();
    $modifications     = array();
    $myDrons    = array();
    $enemyDrons = array();
  
    $user = $this->_slim->users->get('current');
    
    if($user){
      $modifications     = $this->_slim->modifications->prepareAll($this->_slim->modifications->getMany());
      $myDrons    = $this->_slim->drons->prepareAll($this->_slim->drons->getMany('uid', $user->id));
      $me         = $user->prepare();
      
      $battle     = $this->_slim->battles->get('current', $user->id); 
      if($battle && $battle->checkActuality($user)){
        $enemyId    = $battle->getEmemyId($user->id);

        $turns      = $this->_slim->turns->prepareAll($this->_slim->turns->getMany('battle', $battle->id));
        $enemy      = $this->_slim->users->get(null, $enemyId)->prepare();
        $battle     = $battle->prepare();
        $enemyDrons = $this->_slim->drons->prepareAll($this->_slim->drons->getMany('uid', $enemyId)); 
      }else{
        $battle = array();
      }

      $this->_slim->challenges->clear($user);
    }
     
    return array(
      'me'            => json_encode($me),
      'turns'         => json_encode($turns),
      'enemy'         => json_encode($enemy),
      'battle'        => json_encode($battle),
      'myDrons'       => json_encode($myDrons),
      'enemyDrons'    => json_encode($enemyDrons),
      'modifications' => json_encode($modifications), 
    );
  }

}