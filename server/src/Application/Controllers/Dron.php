<?php
/**
 * Dron controller class
 * Contains all handling dron routes
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

class Dron{

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
  public function get($id = null){}

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
   * @throws InvalidArgumentException   If provided battle is absent
   * @return array
   */ 
  public function update($id)
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    $model = json_decode($this->_slim->request()->getBody());
    if(!isset($model->id) || !isset($model->modification1) || !isset($model->modification2) || !isset($model->modification3) || !isset($model->modification4) || !isset($model->modification5) || !isset($model->modification6)){
      throw new InvalidArgumentException("Some modification is not provided", 1);
    }

    $dron = $this->_slim->drons->get(null, $model->id);
    if(!$dron || $dron->uid != $user->id){
      throw new InvalidArgumentException("No dron founded", 1);
    }

    return $dron->changeModifications(array(
      'modification1' => (int) $model->modification1, 
      'modification2' => (int) $model->modification2, 
      'modification3' => (int) $model->modification3, 
      'modification4' => (int) $model->modification4, 
      'modification5' => (int) $model->modification5, 
      'modification6' => (int) $model->modification6, 
    ));
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
   * @throws InvalidArgumentException   If no current user
   * @return array
   */
  public function getMany()
  {
    $user = $this->_slim->users->get('current');
    if(!$user){
      throw new InvalidArgumentException("This action is not allowed to unauthorised person", 1);
    }

    return $this->_slim->drons->prepareAll(
      $this->_slim->drons->getMany(
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