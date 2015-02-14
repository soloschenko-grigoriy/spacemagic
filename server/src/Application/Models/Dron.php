<?php
/**
 * Dron bean class
 *  
 * @package     Application
 * @subpackage  Models
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0
 */ 
namespace Application\Models;

use RedBean_SimpleModel,
    \R as DB;

class Dron extends RedBean_SimpleModel{

  /*
   * @var \Slim\Slim $_slim 
   */
  private $_slim;

  /*
   * @var int DEFAULT_HP_MAX
   */
  CONST DEFAULT_HP_MAX = 500;

  /*
   * @var int DEFAULT_MANA_MAX
   */
  CONST DEFAULT_MANA_MAX = 0;

  /*
   * @var int DEFAULT_DEFENSE
   */
  CONST DEFAULT_DEFENSE = 10;

  /*
   * @var int DEFAULT_RECOVERY
   */
  CONST DEFAULT_RECOVERY = 1;

  /**
   * Application dependency injection.
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Models\Dron
   */ 
  public function setSlim(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * Change this bean by params
   * 
   * @param   array   $model 
   * @return  \Application\Interfaces\IBean 
   */
  public function edit(array $model)
  {
    return $this;
  }

  /**
   * Delete this bean
   * 
   * @return  bool 
   */
  public function remove(){}

  public function save()
  {
    DB::store($this);

    return $this;
  }
  
  /**
   * Update dron's position if possible
   * 
   * @param   int    line
   * @param   int    num
   * @return  \Application\Models\Dron
   */
  public function move($line, $num, $angle)
  {
    $line   = (int) $line;
    $num    = (int) $num;
    $angle  = (float) $angle;

    if(!$this->canMoveToNode($line, $num)){
      return $this;
    }

    $this->line   = $line;
    $this->num    = $num;
    $this->angle  = $angle;
      
    DB::store($this);

    return $this; 
  }

  /**
   * Detects has this dron installed provided modification or not
   * 
   * @param  \Application\Models\Modification $modification
   * @return boolean                      
   */
  public function hasModification(\Application\Models\Modification $modification)
  {
    if($this->modification1 == $modification->id){
      return true;
    }

    if($this->modification1 == $modification->id){
      return true;
    }

    if($this->modification2 == $modification->id){
      return true;
    }

    if($this->modification3 == $modification->id){
      return true;
    }

    if($this->modification4 == $modification->id){
      return true;
    }

    if($this->modification5 == $modification->id){
      return true;
    }

    if($this->modification6 == $modification->id){
      return true;
    }

    return false;
  }

  /**
   * Calculate and return amror of this dron including all additonal itemes 
   * 
   * @return integer
   */
  public function getTotalDefense()
  {
    $defense = $this->defense;
    
    $defense_additional = json_decode($this->defense_additional);
    if(is_array($defense_additional)){
      foreach ($defense_additional as $item){
        $defense += $item->defense;
      }
    }

    return $defense;
  }
  /**
   * Change modifications on this dron
   * 
   * @param   array $modifications 
   * @return  array 
   */
  public function changeModifications(array $modificationIds)
  {
    $this->modification1 = $modificationIds['modification1'] > 0 ? $modificationIds['modification1'] : null;
    $this->modification2 = $modificationIds['modification2'] > 0 ? $modificationIds['modification2'] : null;
    $this->modification3 = $modificationIds['modification3'] > 0 ? $modificationIds['modification3'] : null;
    $this->modification4 = $modificationIds['modification4'] > 0 ? $modificationIds['modification4'] : null;
    $this->modification5 = $modificationIds['modification5'] > 0 ? $modificationIds['modification5'] : null;
    $this->modification6 = $modificationIds['modification6'] > 0 ? $modificationIds['modification6'] : null;

    $this->hp_max     = self::DEFAULT_HP_MAX;
    $this->mana_max   = self::DEFAULT_MANA_MAX;
    $this->defense    = self::DEFAULT_DEFENSE;
    $this->recovery   = self::DEFAULT_RECOVERY;
    $this->best_wapon = null;

    $modifications = $this->_slim->modifications->getMany('idIn', $modificationIds);
    $modificationsByIds = array();

    foreach ($modifications as $modification) {
      $modificationsByIds[$modification->id] = $modification;
    }

    $maxDamage = 0;
    foreach($modificationIds as $modificationId){
      if($modificationId < 1 || !isset($modificationsByIds[$modificationId])){
        continue;
      }

      $modification = $modificationsByIds[$modificationId];
      if($modification->subtype == 'last_current' && $modification->type != 'damage'){
        continue;
      }

      if($modification->type == 'defense'){
        $this->defense += $modification->defense;
      }else if($modification->type == 'recovery'){
        $this->hp_max   += $modification->hp;
        $this->mana_max += $modification->mana;
        $this->recovery += $modification->recovery;
      }else if($modification->type == 'damage' && $modification->damage_max > $maxDamage){
        $maxDamage = $modification->damage_max;
        $this->best_wapon = $modification->id;        
      }
    }

    // in case that user taked off some modification that increasing hp. 
    if($this->hp_max < $this->hp){
      $this->hp = $this->hp_max;
    }

    // the same for mana
    if($this->mana_max < $this->mana){
      $this->mana = $this->mana_max;
    }

    DB::store($this);

    return $this->prepare();
  }

  /**
   * Checks if this dron stands on node provided by line and num
   * 
   * @param  int  $line 
   * @param  num  $num 
   * @return boolean      
   */
  public function isStandsOnNode($line, $num)
  {
    if($this->line == $line && $this->num == $num){
      return true;
    }

    return false;
  }

  /**
   * Can this dron be moved to provided node?
   * 
   * @param  int $line 
   * @param  int $num  
   * 
   * @return boolean     
   */
  public function canMoveToNode($line, $num)
  { 
    if(abs($this->line - $line) > 2 || abs($this->num - $num) > 2){
      return false;
    }

    return true;
  }

  /**
   * Find and return array of all possible nodes to move 
   * 
   * @param  \Application\Models\Dron[] $drons 
   * @return array                        
   */
  public function getAvaibleNodesForMove(array $drons, $radius = 2)
  {
    $nodes = array(
      array('line' => 0, 'num' => 0),
      array('line' => 0, 'num' => 1),
      array('line' => 0, 'num' => 2),
      array('line' => 0, 'num' => 3),
      array('line' => 0, 'num' => 4),
      array('line' => 0, 'num' => 5),
      array('line' => 0, 'num' => 6),

      array('line' => 1, 'num' => 0),
      array('line' => 1, 'num' => 1),
      array('line' => 1, 'num' => 2),
      array('line' => 1, 'num' => 3),
      array('line' => 1, 'num' => 4),
      array('line' => 1, 'num' => 5),
      array('line' => 1, 'num' => 6),
      array('line' => 1, 'num' => 7),

      array('line' => 2, 'num' => 0),
      array('line' => 2, 'num' => 1),
      array('line' => 2, 'num' => 2),
      array('line' => 2, 'num' => 3),
      array('line' => 2, 'num' => 4),
      array('line' => 2, 'num' => 5),
      array('line' => 2, 'num' => 6),

      array('line' => 3, 'num' => 0),
      array('line' => 3, 'num' => 1),
      array('line' => 3, 'num' => 2),
      array('line' => 3, 'num' => 3),
      array('line' => 3, 'num' => 4),
      array('line' => 3, 'num' => 5),
      array('line' => 3, 'num' => 6),
      array('line' => 3, 'num' => 7),

      array('line' => 4, 'num' => 0),
      array('line' => 4, 'num' => 1),
      array('line' => 4, 'num' => 2),
      array('line' => 4, 'num' => 3),
      array('line' => 4, 'num' => 4),
      array('line' => 4, 'num' => 5),
      array('line' => 4, 'num' => 6),
    );

    $result = array();
    
    $k   = $radius;
    $k1  = ($this->line % 2 === 0) ? $k    : $k+1;
    $k2  = ($this->line % 2 === 0) ? $k+1  : $k;

    foreach ($nodes as $node){
      if(!$this->_slim->drons->anyStandsOnNode($drons, $node['line'], $node['num'])){
        continue;
      }

      for($i = 1; $i <= $k; $i++){
        if($node['line'] == $this->line){
          if($node['num'] > $this->num-($k+1) && $node['num'] < $this->num+($k+1) && $this->num != $node['num']){
            $result[] = $node;
          }
        }else if($node['line'] == $this->line+$i || $node['line'] == $this->line-$i){
          if(($node['line']%2 !== 0 && $node['num'] > $this->num-$k && $node['num'] < $this->num+$k2) ||
            ($node['line']%2 === 0 && $node['num'] > $this->num-$k1 && $node['num'] < $this->num+$k)){
            $result[] = $node;
          }
        }
      }      


    }

    // print_r($result);
    return $result;
  }

  
  /**
   * Convert dron to array and prepare all value type
   * 
   * @return  array 
   */
  public function prepare()
  {
    $defense_additional = json_decode($this->defense_additional);
    if(is_array($defense_additional)){
      $defense_additional = array_map(function($item){
        return array(
          'defense'   => (int) $item->defense,
          'duration'  => (int) $item->duration
        );
      }, $defense_additional);
    }else{
      $defense_additional = array();
    }

    return array(
      'id'    => (int) $this->id,
      'uid'   => (int) $this->uid,
      
      'queue'    => (int)   $this->queue,
      'angle'    => (float) $this->angle,
      
      'position' => array(
        'line'   => (int) $this->line,
        'num'    => (int) $this->num,
      ),

      'modification1' => (int) $this->modification1,
      'modification2' => (int) $this->modification2,
      'modification3' => (int) $this->modification3,
      'modification4' => (int) $this->modification4,
      'modification5' => (int) $this->modification5,
      'modification6' => (int) $this->modification6,

      'hp'          => (int) $this->hp,
      'hp_max'      => (int) $this->hp_max,
      'mana'        => (int) $this->mana,
      'mana_max'    => (int) $this->mana_max,
      'defense'     => (int) $this->defense,
      'recovery'    => (int) $this->recovery,
      'best_wapon'  => (int) $this->best_wapon,
      'previous'    => $this->previous == 'yes' ? true : false,

      'defense_additional' => $defense_additional

    );
  }
}