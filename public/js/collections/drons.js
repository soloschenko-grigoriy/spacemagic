/*
 * @class Drons collection
 * @classdesc Contains collection of drons and used for managing all of them
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define(['entities/dron', 'collections/collection', 'collections/nodes'], function(Dron, Collection, Nodes){
  
  /**
   * @constructor
   * @extends {Collection}
   * 
   * @param {object} [params]  - An arguments for collection
   * 
   * @return {Drons} - An instance of collection
   */
  var Drons = function(params){

    return Collection.apply(this, arguments);
  };

  /**
   * @extends {Collection}
   */
  Drons.prototype = Object.create(Collection.prototype);

  /**
   * @constructor
   */
  Drons.prototype.constructor = Drons;

  /**
   * @property {function} type - A reference to constructor of entity
   */
  Drons.prototype.type = Dron;

  /**
   * Find and return current dron
   *
   * @method Drons#getCurrent
   * @memberof Drons
   * 
   * @return {Dron} - founded Dron 
   */
  Drons.prototype.getCurrent = function(){

    return _.find(this.entities, function(dron){ return dron.isCurrent === true; });
  };

  /**
   * Creates all drons, neccessary for battle
   *
   * @method Drons#createForBattle
   * @memberof Drons
   *
   * @param {Nodes} nodes - A ref to nodes, on wich drons will stand
   * 
   * @return {Drons}
   */
  Drons.prototype.createForBattle = function(nodes){
    
    if(!nodes || !(nodes instanceof Nodes)){
      throw new Error('Nodes is not defined or wrong type. Stopped');
    }

    var node;
    for(var i = 1; i <= 9; i++){
      node = nodes.find(i,1);
      this.create({ node: node, frame: 25 }).render();
    }

    for(var k = 1; k <= 9; k++){
      node = nodes.lastInLine(k);
      this.create({ node: node, y: node.sprite.y, frame: 5 }).render();
    }

    return this;
  };

  return Drons;
});