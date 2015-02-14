<?php
/**
 * Challenge controller class 
 * Contains all handling battle routes
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

use \InvalidArgumentException;

class Challenge{

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
  public function index(){}

  /**
   * GET route handler. 
   * Check the input and call model's get method
   * 
   * @throws InvalidArgumentException   If no current user
   * @return array
   */
  public function get($id = null)
  {
    if($id){
      $challenge = $this->_slim->challenges->get(null, $id);
    }else{
      $challenge = $this->_slim->challenges->get(
        $this->_slim->request->params('filter'), 
        $this->_slim->request->params('value')
      );
    }
    
    
    if($challenge){
      return $challenge->prepare();
    }

    return null;
  }

  /**
   * POST route handler. 
   * Check the input and call model's create/insert method
   * 
   * @throws InvalidArgumentException   If no current user
   * @throws InvalidArgumentException   If neccessary information is absent
   * @return array
   */
  public function create()
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    $model = json_decode($this->_slim->request()->getBody(), true);
    if(!isset($model['target'])){
      throw new InvalidArgumentException("Some data is absent", 1);
    }

    return $this->_slim->challenges->create($model);
  }

  /**
   * PUT route handler. 
   * Check the input and call  model's update/put method
   * 
   * @throws InvalidArgumentException   If no current user
   * @throws InvalidArgumentException   If neccessary information is absent
   * @throws InvalidArgumentException   If provided battle is absent
   * @return array
   */ 
  public function update($id)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    $model = json_decode($this->_slim->request()->getBody(), true);

    $challenge = $this->_slim->challenges->get(null, $id);
    if(!$challenge){
      throw new InvalidArgumentException("Challenge not found", 1);
    }

    return $challenge->edit($model);
  }

  /**
   * DELETE route handler. 
   * Check the input and call model's delete method
   * 
   * @throws InvalidArgumentException   If no current user
   * @throws InvalidArgumentException   If provided battle is absent
   * @return bool
   */
  public function delete($id)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    $challenge = $this->_slim->challenges->get(null, $id);
    if(!$challenge || $challenge->initiator != $user->id){
      throw new InvalidArgumentException("Challenge not found", 1);
    }

    return $challenge->remove();
  }

  /**
   * Collection GET route handler. 
   * Check the input and call model's getMany method
   * 
   * @return array
   */
  public function getMany(){}

  /**
   * Collection POST route handler. 
   * Check the input and call model's createMany method
   * 
   * @return array
   */
  public function createMany(){}

  /**
   * Collection PUT route handler. 
   * Check the input and call model's updateMany method
   * 
   * @return array
   */
  public function updateMany(){}

  /**
   * Collection DELETE route handler. 
   * Check the input and call model's updateMany method
   * 
   * @return bool
   */
  public function deleteMany(){}

}