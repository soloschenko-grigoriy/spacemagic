<?php
/**
 * Turn controller class 
 * Contains all handling turn routes
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

class Turn{

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
   * @return array
   */
  public function get($id = null)
  {
    $turn = $this->_slim->turns->get(
      $this->_slim->request->params('filter'), 
      $this->_slim->request->params('value')
    );
    
    if($turn){
      return $turn->prepare(true);
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
    return $this->_slim->turns->create(
      json_decode($this->_slim->request()->getBody(), true)
    );
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
  public function update($id){}

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
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    $turns = $this->_slim->turns->getMany(
      $this->_slim->request->params('filter'), 
      $this->_slim->request->params('value')
    );
    
    if($turns){
      return $this->_slim->turns->prepareAll($turns);
    }

    return null;
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