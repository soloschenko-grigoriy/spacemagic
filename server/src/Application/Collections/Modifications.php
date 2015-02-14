<?php
/**
 * Modifications class
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

class Modifications{
  
  /*
   * @var \Slim\Slim  $_slim
   */
  private $_slim;

  /*
   * @var string  $_table  
   */
  private $_table = 'modification';

  /**
   * Constructor, depends on Slim class instance
   * 
   * @param   \Slim\Slim $slim
   * @return  \Application\Collections\Modifications
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
   * @return  \Application\Models\Modification
   */
  public function get($filter = '', $value = null)
  {
    $result = \R::load($this->_table, $value);

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
   * @return  \Application\Models\Modification[]
   */
  public function getMany($filter = '', $value = null, $lastId = 0)
  {
    if($filter == 'idIn'){
      return \R::findAll($this->_table, 'id IN ('.implode(',', $value).')');
    }else{
      return \R::findAll($this->_table);
    }
    
  }

  /**
   * Create a new bean by params
   * 
   * @param   arary   $params   New bean will be created by this params
   * @return  \Application\Models\Modification
   */
  public function create(array $params){}

  /**
   * Iterativly export each modification in array
   * 
   * @param   \Application\Models\Modification[]   $modifications
   * @return  array
   */
  public function prepareAll(array $modifications)
  {
    $result = array();
    foreach ($modifications as $modification) {
      $result[] = $modification->prepare();
    }

    return $result;
  }
}