<?php
/**
 * AI model class
 *  
 * @package     Application
 * @subpackage  Models
 * 
 * @author      Soloschenko G. soloschenko@gmail.com
 * @copyright   Soloschenko G. soloschenko@gmail.com
 * 
 * @version     1.0
 * @deprecated  Now AI is on client side
 */ 
namespace Application\Models;

use \Application\Models\User   as User,
    \Application\Models\Battle as Battle,
    \R                         as DB;

class AI{

  /**
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /**
   * @var \Application\Models\Dron[] $_enemyDrons 
   */
  private $_enemyDrons;

  /**
   * @var \Application\Models\Dron[] $_myDrons 
   */
  private $_myDrons;

  /**
   * @var \Application\Models\Dron $_currentDron 
   */
  private $_currentDron;  

  /**
   * @var \Application\Models\Modification $_currentMods 
   */
  private $_currentMods;

  /**
   * @var \Application\Models\User $_bot 
   */
  private $_bot;

  /**
   * Application dependency injection.
   * 
   * @param   \Slim\Slim    $slim
   * @return  \Application\Models\Battle
   */ 
  public function __construct(\Slim\Slim $slim)
  {
    $this->_slim = $slim;

    return $this;
  }

  /**
   * So this is it...
   * Here we try to make an artificial turn
   * 
   * @param  \Application\Models\User  $user   
   * @param  \Application\Models\User  $enemy
   * @return \Application\Models\AI         
   */
  public function makeTurn(User $user, User $enemy)
  {
    // sleep(rand(1,3));
    
    $this->_bot         = $user;
    $this->_enemy       = $enemy;
    $this->_myDrons     = $this->_slim->drons->getMany('alive', $this->_bot->id);
    $this->_enemyDrons  = $this->_slim->drons->getMany('alive', $this->_enemy->id);
    $this->_currentDron = $this->_slim->drons->get('currentAndUid', $this->_bot->id);
    
    $this->_currentMods = $this->prepareCurrentMods();

    $this->_attackMods  = array_filter($this->_currentMods, function($mod){ return $mod->type == 'damage'   && $mod->subtype == 'active'; });
    $this->_defenseMods = array_filter($this->_currentMods, function($mod){ return $mod->type == 'defense'  && $mod->subtype == 'active'; });
    $this->_recoverMods = array_filter($this->_currentMods, function($mod){ return $mod->type == 'recovery' && $mod->subtype == 'active'; });

    $potentialMove    = $this->findPotentialMoveTargets();
    $potentialAttacks = $this->findPotentialAttackTargets($this->_attackMods, $potentialMove);
    $potentialDefense = $this->findPotentialDefenseTargets($this->_defenseMods);
    $potentialRecover = $this->findPotentialRecoverTargets($this->_recoverMods);
    

    $needToAttack  = $this->calcAttackNeeds($potentialAttacks);
    $needToDefense = $this->calcDefenseNeeds($potentialDefense);
    $needToRecover = $this->calcRecoverNeeds($potentialRecover);
    $needToMove    = $this->calcMoveNeeds($potentialMove);
    
    $result = $this->analyze(
      array(
        'move'    => $needToMove,
        'attack'  => $needToAttack, 
        'defense' => $needToDefense, 
        'recover' => $needToRecover,    
      ),
      array(
        'move'    => $potentialMove,
        'attack'  => $potentialAttacks, 
        'defense' => $potentialDefense, 
        'recover' => $potentialRecover,    
      )
    );

    $turn = DB::dispense($this->_slim->turns->_table)->create($this->prepareTurn($result), $this->_bot);
  }

  /**
   * Find all possible targets and mods
   *
   * @param  \Application\Models\Modification[] $mods
   * 
   * @return array
   */
  public function prepareCurrentMods()
  {
    $mods = array();
    if($this->_currentDron->modification1){
      $mods[] = $this->_currentDron->modification1;
    }
    if($this->_currentDron->modification2){
      $mods[] = $this->_currentDron->modification2;
    }
    if($this->_currentDron->modification3){
      $mods[] = $this->_currentDron->modification3;
    }
    if($this->_currentDron->modification4){
      $mods[] = $this->_currentDron->modification4;
    }
    if($this->_currentDron->modification5){
      $mods[] = $this->_currentDron->modification5;
    }
    if($this->_currentDron->modification6){
      $mods[] = $this->_currentDron->modification6;
    }
      
    return $this->_slim->modifications->getMany('idIn', array_unique($mods));
  }

  /**
   * Find all possible targets and mods
   *
   * @param  \Application\Models\Modification[] $mods
   * 
   * @return array
   */
  public function findPotentialAttackTargets(array $mods, array $potentialMoves)
  {
    $potentialAttacks = array();

    // check can I attack someone?
    foreach($this->_enemyDrons as $enemy){
      foreach($mods as $mod){
        if($mod->isAvaibleOnDron($this->_currentDron, $enemy->box())){
          $potentialAttacks[] = array(
            'mod'     => $mod,
            'target'  => $enemy,
            'weight'  => $mod->damage_min * $enemy->hp/$enemy->hp_max
          );
        }
      } 
    }
    
    // if I will move to some avaible postition - can I attack then?
    $line = $this->_currentDron->line;
    $num  = $this->_currentDron->num;
    foreach ($potentialMoves as $one){
      $this->_currentDron->num  = $one['node']['num'];
      $this->_currentDron->line = $one['node']['line'];
      foreach($this->_enemyDrons as $enemy){
        foreach($mods as $mod){
          if($mod->isAvaibleOnDron($this->_currentDron, $enemy->box())){
            $potentialAttacks[] = array(
              'moving'  => $one['node'],
              'mod'     => $mod,
              'target'  => $enemy,
              'weight'  => $mod->damage_min * $enemy->hp/$enemy->hp_max
            );
          }
        } 
      }
    }

    $this->_currentDron->num  = $num;
    $this->_currentDron->line = $line;
  
    return $potentialAttacks;
  }

  /**
   * Find all possible targets and mods
   *
   * @param  \Application\Models\Modification[] $mods
   * 
   * @return array
   */
  public function findPotentialDefenseTargets(array $mods)
  {
    $potentialDefense = array();

    foreach ($this->_myDrons as $dron) {
      foreach ($mods as $mod) {
        if($mod->isAvaibleOnDron($this->_currentDron, $dron->box())){
          $potentialDefense[] = array(
            'mod'     => $mod,
            'target'  => $dron,
            'weight'  => 1
          );
        }
      } 
    }

    return $potentialDefense;
  }

  /**
   * Find all possible targets and mods
   *
   * @param  \Application\Models\Modification[] $mods
   * 
   * @return array
   */
  public function findPotentialRecoverTargets(array $mods)
  {
    $potentialRecover = array();

    foreach ($this->_myDrons as $dron) {
      foreach ($mods as $mod) {
        if($mod->isAvaibleOnDron($this->_currentDron, $dron->box())){
          $potentialRecover[] = array(
            'mod'     => $mod,
            'target'  => $dron,
            'weight'  => 1
          );
        }
      } 
    }

    return $potentialRecover;
  }

  /**
   * Find all possible targets and mods
   * 
   * @return array
   */
  public function findPotentialMoveTargets()
  {
    $avaibleNodes = $this->_currentDron->getAvaibleNodesForMove(array_merge($this->_enemyDrons, $this->_myDrons));

    $potentialMove = array();
    foreach ($avaibleNodes as $node) {
      $potentialMove[] = array(
        'node'   => $node,
        'weight' => 1
      );
    }

    return $potentialMove;
  }

  /**
   * Checks count of points to make attack deccesion
   *
   * @param  array $potentialAttacks
   * @return int
   */
  public function calcAttackNeeds(array &$potentialAttacks)
  {
    $needToAttack = 0;

    // if any posiiblity to attack - set counter as zero
    if(empty($potentialAttacks)){
      return $needToAttack;
    }

    $needToAttack++;

    // if potential targets has small amount of HP (less then 10%)
    foreach($potentialAttacks as $key => $potential){
      if(($potential['target']->hp/$potential['target']->hp_max) * 100 < 10){
        $needToAttack++;
        $potentialAttacks[$key]['weight']++;
        break;
      }
    }

    // if I can kill some potential enemy in this or next turn
    foreach($potentialAttacks as $key => $potential){
      $avgDamage = ($potential['mod']->damage_min + $potential['mod']->damage_max)/2;

      if($potential['target']->hp - $avgDamage < 1 || $potential['target']->hp - 2 * $avgDamage < 1){
        $needToAttack++;
        $potentialAttacks[$key]['weight']++;
        break;
      }
    }

    // if I have less count of drons
    if(count($this->_myDrons) < count($this->_enemyDrons)){
      $needToAttack++;
    }

    return $needToAttack;
  }

  /**
   * Checks count of points to make defense deccesion
   *
   * @param  array $potentialDefense
   * @return int
   */
  public function calcDefenseNeeds(array &$potentialDefense)
  {
    $needToDefense = 0;

    // if any posiiblity to defense - set counter as zero
    if(empty($potentialDefense)){
      return $needToDefense;
    }

    $needToDefense++;

    // if potential targets has small amount of HP (less then 10%)
    foreach ($potentialDefense as $key => $potential){
      if( ($potential['target']->hp/$potential['target']->hp_max) * 100 < 10){
        $needToDefense++;
        $potentialDefense[$key]['weight']++;
        break;
      }
    }

    return $needToDefense;
  }

  /**
   * Checks count of points to make recover deccesion
   *
   * @param  array $potentialRecover
   * @return int
   */
  public function calcRecoverNeeds(array &$potentialRecover)
  {
    $needToRecover = 0;

    // if any posiiblity to recover - set counter as zero
    if(empty($potentialRecover)){
      return $needToRecover;
    }

    $needToRecover++;

    // if potential targets has small amount of HP (less then 10%)
    foreach ($potentialRecover as $key => $potential){
      if( ($potential['target']->hp/$potential['target']->hp_max) * 100 < 10){
        $needToRecover++;
        $potentialRecover[$key]['weight']++;
        break;
      }
    }

    // if I have less count of drons
    if(count($this->_myDrons) < count($this->_enemyDrons)){
      $needToRecover++;
    }

    return $needToRecover;
  }

  /**
   * Checks count of points to make recover deccesion
   *
   * @param  array $potentialMove
   * @return int
   */
  public function calcMoveNeeds(array &$potentialMove)
  {
    $needToMove = 0;

    // if any posiiblity to move - set counter as zero
    if(empty($potentialMove)){
      return $needToMove;
    }

    $needToMove++;

    $line = $this->_currentDron->line;
    $num  = $this->_currentDron->num;

    foreach ($potentialMove as $key=>$one){
      $this->_currentDron->num  = $one['node']['num'];
      $this->_currentDron->line = $one['node']['line'];


      // check if we can attack in next turn after moving now
      $futurePotentialAttackTargets = $this->findPotentialAttackTargets($this->_attackMods, $this->findPotentialMoveTargets());
      $attackNeeds = $this->calcAttackNeeds($futurePotentialAttackTargets);
      if($attackNeeds > 0){
        $needToMove++;
        $potentialMove[$key]['weight'] += count($futurePotentialAttackTargets);
      }
    }

    $this->_currentDron->num  = $num;
    $this->_currentDron->line = $line;
    
    // if the move does not necessarily, but I have a useful melee weapon - I must move
    foreach($this->_attackMods as $key => $mod){
      if($mod->isAvaible($this->_currentDron)){
        $needToMove++;
        break;
      }
    }
    
    return $needToMove;
  }

  /**
   * Compare all needs and find more logical turn
   * 
   * @return [type] [description]
   */
  public function analyze(array $needs, array $potential)
  {
    // $export = array();
    // foreach ($potential['attack'] as $attack) {
    //   $export[] = array(
    //     'mod' => $attack['mod']->id,
    //     'target' => $attack['target']->id,
    //     'weight' => $attack['weight'],
    //     'moving' => $attack['moving'] ? $attack['moving'] : null
    //   );
    // }
    // print_r($export);
    if($needs['attack'] > $needs['defense'] && $needs['attack'] > $needs['recover']){
      return array('type' => 'attack', 'potential' => $this->findMaxWeightItem($potential['attack']));
    }

    if($needs['recover'] > $needs['defense']){
      return array('type' => 'recover', 'potential' => $this->findMaxWeightItem($potential['recover']));
    }

    if($needs['defense'] > 1){
      return array('type' => 'defense', 'potential' => $this->findMaxWeightItem($potential['defense']));
    }

    if($needs['move'] > 1){
      return array('type' => 'move', 'potential' => $this->findMaxWeightItem($potential['move']));
    }

    /**
     * @todo check fo retreat
     */
    
    return array('type' => 'skip', 'potential' => null);
  }

  /**
   * Find and return item with biggest weight count
   * 
   * @param  array  $potential
   * @return array            
   */
  public function findMaxWeightItem(array $potential)
  {
    $maxWeightItem = null;
    $maxWeight = 0;
    
    foreach ($potential as $one){
     if($one['weight'] > $maxWeight){
      $maxWeight     = $one['weight'];
      $maxWeightItem = $one;
     }
    }

    return $maxWeightItem;
  }

  /**
   * Use calculated information to prepare all neccessary info for model
   * 
   * @return [type] [description]
   */
  public function prepareTurn(array $analyze)
  {
    $result = array(
      'skip'              => false,
      'blunt'             => false,
      'retreat'           => false,
      'currentUserId'     => $this->_bot->id,
      'currentDronId'     => $this->_currentDron->id,
      'currentDronNum'    => $this->_currentDron->num,
      'currentDronLine'   => $this->_currentDron->line,
      'currentDronAngle'  => $this->_currentDron->angle
    );

    if($analyze['potential']){
      if($analyze['type'] == 'move'){
        $result['currentDronNum']  = $analyze['potential']['node']['num'];
        $result['currentDronLine'] = $analyze['potential']['node']['line'];
      }else{
        $result['targetDronId']         = $analyze['potential']['target']->id;
        $result['activeModificationId'] = $analyze['potential']['mod']->id;

        if($analyze['type'] == 'attack'){
          if(isset($analyze['potential']['moving'])){
            $result['currentDronNum']  = $analyze['potential']['moving']['num'];
            $result['currentDronLine'] = $analyze['potential']['moving']['line'];
          }

          $result['damage'] = (int) rand($analyze['potential']['mod']->damage_min, $analyze['potential']['mod']->damage_max) - $analyze['potential']['target']->getTotalDefense();
        }
      }
    }else{
      $result['skip'] = true;
    }

    return $result;
  }

}