<?php
/**
 * Comments class
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

use \Application\Models\User,
    \Application\Models\Battle,
    \RuntimeException,
    \R as DB;

class Drons{
  
  /*
   * @var  \Slim\Slim   $_slim  
   */
  private $_slim;

  /*
   * @var string  $_name  
   */
  private $_table = 'dron';

  /**
   * Constructor, depends on Slim class instance
   * 
   * @param   \Slim\Slim  $slim
   * @return  \Application\Collections\Drons
   */ 
  public function __construct(\Slim\Slim $slim)
  {
    $this->_slim = $slim;
    $this->count = 0;
    return $this;
  }

  /**
   * Find and return instance of bean by ID
   * 
   * @param   int
   * @return  \Application\Models\Dron
   */
  public function get($filter = '', $value = null)
  {
    if($filter == 'queueAndUid'){
      $result = DB::findOne($this->_table, 'uid = :uid AND queue = :queue', array('uid' => $value['uid'], 'queue' => $value['queue']));
    }else if($filter == 'currentAndUid'){
      $result = $this->getOneCurrentByUid($value); 
    }else{
      $result = DB::findOne($this->_table, 'id = ?', array($value));
    }

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
   * @return  \Application\Models\Dron[]
   */
  public function getMany($filter = '', $value = null, $lastId = 0)
  {
    if($filter == 'uid'){
      return DB::findAll('dron','uid = :uid', array('uid' => (int) $value));
    }else if($filter == 'alive'){
      return DB::findAll('dron', 'uid = :uid AND hp > 0', array('uid' => (int) $value));
    }else{
      return \R::findAll($this->_table);
    }
  }

  /**
   * Create a new bean by params
   * 
   * @param   arary   $params   New bean will be created by this params
   * @return  \Application\Models\Dron
   *
   */
  public function create(array $params)
  {
    $dron1 = DB::dispense($this->_table);
    $dron2 = DB::dispense($this->_table);
    $dron3 = DB::dispense($this->_table);
    $dron4 = DB::dispense($this->_table);
    $dron5 = DB::dispense($this->_table);

    $dron1->uid = $params['uid'];
    $dron2->uid = $params['uid'];
    $dron3->uid = $params['uid'];
    $dron4->uid = $params['uid'];
    $dron5->uid = $params['uid'];

    $dron1->queue = 1;
    $dron2->queue = 2;
    $dron3->queue = 3;
    $dron4->queue = 4;
    $dron5->queue = 5;

    $dron5->previous = 'yes';

    DB::store($dron1);
    DB::store($dron2);
    DB::store($dron3);
    DB::store($dron4);
    DB::store($dron5);

    return array($dron1,$dron2,$dron3,$dron4,$dron5);
  }

  /**
   * Remove all "previous" param and add it to current dron
   * We realy NEED to load a bean, because in case that current dron === target dron
   * current overrides target changes.
   * 
   * @param   \Application\Models\Dron $current
   * @return  \Application\Models\Dron
   */
  public function changePrevious($current)
  {
    DB::exec('UPDATE dron SET previous = "no" WHERE uid = ?', array($current->uid));
    
    $current = $this->get(null, $current->id);
    $current->previous = 'yes';
    DB::store($current);

    return $current;
  }

  /**
   * Preparing drons of users before battle
   * @param   \Application\Models\User     $user1
   * @param   \Application\Models\User     $user2
   * @param   \Application\Models\Battle   $battle
   * @return  void
   */
  public function prepareBeforeBattle(User $user1, User $user2, Battle $battle)
  {
    $drons = DB::findAll('dron', 'uid = :user1Id OR uid = :user2Id', 
      array('user1Id' => $user1->id, 'user2Id' => $user2->id)
    );

    if($battle->user1 == $user1->id){
      $leftDrons   = array_filter($drons, function($dron) use($user1) { return $dron->uid == $user1->id; });
      $rightDrons  = array_filter($drons, function($dron) use($user2) { return $dron->uid == $user2->id; });
    }else{
      $leftDrons   = array_filter($drons, function($dron) use($user2) { return $dron->uid == $user2->id; });
      $rightDrons  = array_filter($drons, function($dron) use($user1) { return $dron->uid == $user1->id; });
    }

    DB::transaction(function() use($leftDrons, $rightDrons){
      array_walk($leftDrons, function($dron){
        $dron->num              = 0;
        $dron->line             = $dron->queue - 1;    
        $dron->angle            = 1.57;
        $dron->previous         = ($dron->queue == 5) ? 'yes' : 'no';
        $dron->hp               = $dron->hp_max;
        $dron->mana             = $dron->mana_max;

        $dron->defense_additional = '';

        DB::store($dron);
      });

      array_walk($rightDrons, function($dron){
        $dron->num              = ($dron->queue % 2 == 0) ? 7 : 6;
        $dron->line             = $dron->queue - 1; 
        $dron->angle            = -1.57;
        $dron->previous         = ($dron->queue == 5) ? 'yes' : 'no';
        $dron->hp               = $dron->hp_max;
        $dron->mana             = $dron->mana_max;

        $dron->defense_additional = '';

        DB::store($dron);
      });
    });
  }

  public function prepareBotDrons()
  {
    ini_set('max_execution_time', 0);
    $this->count = $this->count +1;
    // if($this->count > 5){
    //   die();
    // }
    // $xx = (7+$this->count)*1000 + 344;
    $mods  = DB::exportAll($this->_slim->modifications->getMany());
    $drons = DB::findAll($this->_table,'`modification1` IS NULL 
      AND  `modification2` IS NULL 
      AND  `modification3` IS NULL 
      AND  `modification4` IS NULL 
      AND  `modification5` IS NULL 
      AND  `modification6` IS NULL 
      AND  `best_wapon` IS NULL  limit 2000');

    $activeMods = array_filter($mods, function($mod){ return $mod['subtype'] === 'active'; });
    $magicMods  = array_filter($mods, function($mod){ return $mod['magic'] === 'yes' && $mod['mana'] > 0; });
    $otherMods  = array_filter($mods, function($mod){ return $mod['subtype'] != 'active' && $mod['magic'] != 'yes' && $mod['mana'] == 0; });

    foreach ($drons as $key => $dron){
      $activeModsCount = 0;
      $magicModsCount  = 0;
      $otherModsCount  = 0;

      $currentActiveMods = array();
      $currentMagicMods  = array();
      $currentOtherMods  = array();

      $activeModsCount = rand(1, count($activeMods));
      if($activeModsCount > 6){
        $activeModsCount = 6;
      }

      $rand = array_rand($activeMods, $activeModsCount);
      if(is_array($rand)){
        foreach($rand as $one){
          $currentActiveMods[] = $activeMods[$one];
        }
      }else{
        $currentActiveMods[] = $activeMods[$rand];
      }
      
      
      $activeMagicMods = array_filter($currentActiveMods, function($mod){ return $mod['magic'] === 'yes'; });
      if(count($activeMagicMods) > 0){
        if($activeModsCount == 6){
          $activeModsCount = 5;
          $toDelete = array_rand($currentActiveMods);
          unset($currentActiveMods[$toDelete]);
        }
        
        $magicModsCount = rand(1, 6 - $activeModsCount);
        if($magicModsCount > count($magicMods)){
          $magicModsCount = count($magicMods);
        }

        $rand = array_rand($magicMods, $magicModsCount);
        if(is_array($rand)){
          foreach($rand as $one){
            $currentMagicMods[] = $magicMods[$one];
          }
        }else{
          $currentMagicMods[] = $magicMods[$rand];
        }
      }

      $otherModsCount = 6 - $activeModsCount - $magicModsCount;
      if($otherModsCount > count($otherMods)){
        $otherModsCount = count($otherMods);
      }
      if($otherModsCount > 0){
        $rand = array_rand($otherMods, $otherModsCount);
        if(is_array($rand)){
          foreach($rand as $one){
            $currentOtherMods[] = $otherMods[$one];
          }
        }else{
          $currentOtherMods[] = $otherMods[$rand];
        }
      }
      
      $result = array_values(array_merge($currentActiveMods, $currentMagicMods, $currentOtherMods));

      $dron->changeModifications(array(
        'modification1' => isset($result[0]) ? $result[0]['id'] : 0, 
        'modification2' => isset($result[1]) ? $result[1]['id'] : 0, 
        'modification3' => isset($result[2]) ? $result[2]['id'] : 0, 
        'modification4' => isset($result[3]) ? $result[3]['id'] : 0, 
        'modification5' => isset($result[4]) ? $result[4]['id'] : 0, 
        'modification6' => isset($result[5]) ? $result[5]['id'] : 0, 
      ));

      // $this->prepareBotDrons();
    }
    
  }

  /**
   * Iterativly export each bean to array
   * 
   * @param   \Application\Models\Dron[]
   * @return  array
   */
  public function prepareAll(array $drons)
  {
    $result = array();
    foreach ($drons as $dron) {
      $result[] = $dron->prepare();
    }

    return $result;
  }

  public function anyStandsOnNode(array $drons, $line, $num)
  {
    $nodeIsEmpty = true;
    foreach ($drons as $dron) {
      if($dron->isStandsOnNode($line, $num)){
        $nodeIsEmpty = false;
        break;
      }
    }

    
    return $nodeIsEmpty;
  }

  /**
   * Find and return one current dron for provided UID
   * 
   * @param  int $value
   * @return \Application\Models\Dron     
   */
  protected function getOneCurrentByUid($value = null)
  {
    $result = null;

    $prev = DB::findOne($this->_table, 'uid = ? AND previous = "yes"', array($value));  
    if($prev && $prev->queue != 5){
      $result = DB::findOne($this->_table, 'uid = :uid AND hp > 0 AND queue > :queue ORDER BY id ASC', 
        array('uid' => $value, 'queue' => $prev->queue)
      );
    }

    if(!$result){
      $result = DB::findOne($this->_table, 'uid = ? AND hp > 0 ORDER BY id ASC', array($value));
    }

    return $result;
  }

}