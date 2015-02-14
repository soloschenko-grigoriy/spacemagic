<?php
/**
 * User controller class 
 * Contains all handling user routes
 *  
 * @package     Application
 * @subpackage  Controllers
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0
 */ 
namespace Application\Controllers;

use \InvalidArgumentException;

class User{

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
      $user = $this->_slim->users->get(null, $id);
    }else{
      $user = $this->_slim->users->get(
        $this->_slim->request->params('filter'), 
        $this->_slim->request->params('value')
      );
    }
   

    if($user){
      return $user->prepare();
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
  public function create(){}

  /**
   * PUT route handler. 
   * Check the input and call  model's update/put method
   * 
   * @throws InvalidArgumentException   If no current user
   * @throws InvalidArgumentException   If neccessary information is absent
   * @return array
   */ 
  public function update($id)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    $model = json_decode($this->_slim->request()->getBody());
    if(!isset($model->id) || $user->id != $model->id){
      throw new InvalidArgumentException("User is not provided", 1);
    }

    if($model->ready == true){
      $user->makeReady();
    }else{
      $user->makeNotReady();
    }

    return $user->prepare();
  }

  /**
   * DELETE route handler. 
   * Check the input and call model's delete method
   * 
   * @return bool
   */
  public function delete($id){}

  /**
   * Collection GET route handler. 
   * Check the input and call model's getMany method
   * 
   * @return array
   */
  public function getMany()
  {
    return $this->_slim->users->prepareAll(
      $this->_slim->users->getMany(
        $this->_slim->request->params('filter'), 
        $this->_slim->request->params('value')
      )
    );
  }

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