<?php
/**
 * Modification bean class
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
    \RuntimeException,
    \OutOfRangeException,
    \R as DB;

class Modification extends RedBean_SimpleModel{

  /*
   * @property  $_slim  \Slim\Slim
   */
  private $_slim;

  /**
   * Application dependency injection.
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Models\Modification
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

  /**
   * Check if this mod can be used
   * 
   * @return boolean
   */
  public function isAvaible(\Application\Models\Dron $current)
  {
    // if this modification is magic check has current dron neccessary mana
    if($this->magic === 'yes' && $current->mana < $this->cost){
      return false;
    }

    return true;
  }

  /**
   * Detects can this modification be executed on provided dron
   * 
   * @param  \Application\Models\Dron $dron
   * @return boolean                      
   */
  public function isAvaibleOnDron(\Application\Models\Dron $current, \Application\Models\Dron $target)
  {
    if(!$this->isAvaible($current)){
      return false;
    }
    
    // echo $current->id, '<br>', $target->id, '<br>', $current->mana, '<br/>', $this->cost;
    if($this->type == 'damage'){
      return $this->isDamageAvaible($current, $target);
    }else if($this->type === 'defense'){
      return $this->isDefenseingAvaible($current, $target);
    }else if($this->type === 'recovery'){
      return $this->isRecoveringAvaible($current, $target);
    }else{
      return false;
    }

    return true;
  }

  /**
   * Check can curent dron make damage to target dron by this modification
   * 
   * @param  \Application\Models\Dron $current
   * @param  \Application\Models\Dron $target 
   * @return boolean                       
   */
  public function isDamageAvaible(\Application\Models\Dron $current, \Application\Models\Dron $target)
  {
    // check if target and current belongs to one user
    if($target->uid == $current->uid){ 
      return false;
    }

    // check if target is in avaible range from current (acording to 'line' param)
    if(abs($current->line - $target->line) > $this->range){
      return false;
    }

    // the same but acording to 'num' param
    if(abs($current->num - $target->num) > $this->range){
      return false;
    }
    
    // if($current->line % 2 != 0 && abs($current->line - $target->line) == $this->range && abs($current->num - $target->num) == $this->range){
    //   return false;
    // }

    return true;
  }

  /**
   * Check can curent dron make defense to target dron by this modification
   * 
   * @param  \Application\Models\Dron $current
   * @param  \Application\Models\Dron $target 
   * @return boolean                       
   */
  public function isDefenseingAvaible(\Application\Models\Dron $current, \Application\Models\Dron $target)
  {
    // check if target and current belongs to different users
    if($target->uid !== $current->uid){
      return false;
    }

    return true;
  }

  /**
   * Check can curent dron make recovery to target dron by this modification
   * 
   * @param  \Application\Models\Dron $current
   * @param  \Application\Models\Dron $target 
   * @return boolean                       
   */
  public function isRecoveringAvaible(\Application\Models\Dron $current, \Application\Models\Dron $target)
  {
    // check if target and current belongs to different users
    if($target->uid !== $current->uid){
      return false;
    }

    // check if target dron is full of HP
    if($target->hp/$target->hp_max == 1){
      // echo $target->hp, ' ', $target->hp_max, ' ', $target->id, ' ';
      return false;
    }

    return true;
  }

  /**
   * Execute this modification on target dron by current one
   * 
   * @param  \Application\Models\Dron $current 
   * @param  \Application\Models\Dron $target  
   * @return \Application\Models\Modification  
   * @throws RuntimeException If this modification is not avaible on target by current
   * @throws RuntimeException If this modification has unsupported type                    
   */
  public function execute(array $turnModel, \Application\Models\Dron $current, \Application\Models\Dron $target)
  {
    $allEnemyDrons = $this->_slim->drons->getMany('alive', $target->uid);
    if(!$this->isAvaibleOnDron($current, $target)){
      throw new RuntimeException("This modification can't be executed. curent: ".$current->id." target: ".$target->id." mod: ".$this->id, 1);
    }

    if($this->magic == 'yes'){
        $mana = $current->mana - $this->cost;
        $current->mana = $mana > 0 ? $mana : 0;
        $current->save();
      }

    if($this->type == 'damage'){
      $this->executeDamage($turnModel, $target);
    }else if($this->type == 'defense'){
      $this->executeDefense($target);
    }else if($this->type == 'recovery'){
      $this->executeRecover($target);
    }else{
      throw new RuntimeException("Unsupported type of modification", 1);
    }

    $target->save();

    return $this;
  }

  /**
   * Execute this DAMAGE modification on target dron by current one
   * 
   * @param  \Application\Models\Dron $current 
   * @param  \Application\Models\Dron $target  
   * @return \Application\Models\Modification                      
   */
  public function executeDamage(array $turnModel, \Application\Models\Dron $target)
  { 
    $targetDronTotalDefense = $target->getTotalDefense();
    // echo $turnModel['damage'], '<br/>', $targetDronTotalDefense, '<br/>', $this->damage_max, '<br/>', $this->damage_min;
    if($turnModel['damage'] + $targetDronTotalDefense > $this->damage_max){
      throw new OutOfRangeException("Provided damage is too large ".$turnModel['damage'].' against '.$this->damage_max.' DEFENSE: '.$targetDronTotalDefense, 1);
    }

    if($turnModel['damage'] + $targetDronTotalDefense < $this->damage_min){
      throw new OutOfRangeException("Provided damage is too small ".$turnModel['damage'].' against '.$this->damage_min.' ID:'.$this->id, 1);
    }

    $target->hp = $target->hp - $turnModel['damage'];

    return $this;
  }

  /**
   * Execute this ARMOR modification on target dron by current one
   * 
   * @param  \Application\Models\Dron $current 
   * @param  \Application\Models\Dron $target  
   * @return \Application\Models\Modification                      
   */
  public function executeDefense(\Application\Models\Dron $target)
  {
    $defense_additional = json_decode($target->defense_additional);
    if(!is_array($defense_additional)){
      $defense_additional = array();
    }

    $defense_additional[] = array(
      'defense'   => $this->defense,
      'duration'  => $this->duration
    );
    
    $target->defense_additional = json_encode($defense_additional);

    return $this;
  }

  /**
   * Execute this RECOVERY modification on target dron by current one
   * 
   * @param  \Application\Models\Dron $current 
   * @param  \Application\Models\Dron $target  
   * @return \Application\Models\Modification                      
   */
  public function executeRecover(\Application\Models\Dron $target)
  {
    $newHP = $target->hp + $this->hp;
    if($newHP > $target->hp_max){
      $newHP = $target->hp_max;
    }
    // echo $target->id, '<br/>', $newHP, '<br/>', $target->hp, '<br/>', $this->hp;
    $target->hp = $newHP;

    return $this;
  }

  /**
   * Convert modification to array and prepare all value type
   * 
   * @return  array 
   */
  public function prepare()
  {
    return array(
      'id'          => (int) $this->id,
      'name'        => $this->name,
      'type'        => $this->type,
      'subtype'     => $this->subtype,
      'magic'       => $this->magic == 'yes' ? true : false,
      'cost'        => (int) $this->cost,
      'damage_min'  => (int) $this->damage_min,
      'damage_max'  => (int) $this->damage_max,
      'hp'          => (int) $this->hp,
      'mana'        => (int) $this->mana,
      'range'       => (int) $this->range,
      'defense'     => (int) $this->defense,
      'recovery'    => (int) $this->recovery,
      'duration'    => (int) $this->duration,
      'selfdamage'  => $this->selfdamage == 'yes' ? true : false,
      'description' => $this->description
    );
  }
}