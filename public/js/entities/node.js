/*
 * @class Node
 * @classdesc A grid's node class
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define(['entities/entity', 'config/resources'], function(Entity, Resources){
  
  /**
   * @constructor
   * @extends {Entity}
   * @chainable 
   * 
   * @param {object} [params] - An arguments for entity
   *
   * @return {Node} - An instance of node
   */
  var Node = function(params){

    if(!params || !params.line){
      throw new Error('Line is not defined. Stopped');
    }

    if(!params || !params.num){
      throw new Error('Num is not defined. Stopped');
    }

    if(!params || !params.position || !params.position.x || !params.position.y){
      throw new Error('Position is not defined. Stopped');
    }

    /**
     * @property {number} line - 
     */
    this.line = parseInt(params.line, 10);

    /**
     * @property {number} line - 
     */
    this.num = parseInt(params.num, 10);

    /**
     * @property {Hash} position
     */
    this.position = params.position;
    

    return Entity.apply(this, arguments);
  };

  /**
   * @extends {Entity}
   */
  Node.prototype = Object.create(Entity.prototype);
  
  /**
   * @constructor
   */
  Node.prototype.constructor = Node;

  /**
   * @property {Nodes[]} collections - An array of collection, wich this node consist in
   */
  Node.prototype.collections = [];

  /**
   * @property {Phaser.Sprite} sprite - A reference to the dron's sprite
   */
  Node.prototype.sprite = null;

  /**
   * @property {Dron} dron - A reference to dron, that stands this node on
   */
  Node.prototype.dron = null;
  
  /**
   * @property {boolean} isCurrent - Is this node highlighted as current
   * @protected
   */
  Node.prototype._isCurrent = false;

  /**
   * @property {boolean} isAvaibleForMove - Is this node highlighted as avaible for move
   * @protected
   */
  Node.prototype._isAvaibleForMove = false;

  /**
   * Render node to the game
   *
   * @method Node#render
   * @memberof Node
   * @chainable 
   * 
   * @return {Node} - Instance of grid
   */
  Node.prototype.render = function(){
      
    this.sprite = this.game.add.sprite(this.position.x, this.position.y, Resources.atlases.grid.key, this.line+'-'+this.num+'.png');

    this.sprite.inputEnabled = true;
    this.sprite.anchor.setTo(0.5, 0.5);
    this.sprite.alpha = 0.2;
    this.sprite.events.onInputDown.add(this.onClick, this);
    // this.sprite.events.onInputOver.add(this.onOver, this);
    // this.sprite.events.onInputOut.add(this.onOut, this);

    
    return this;
  };

  /**
   * Make this node current
   *
   * @method Node#makeCurrent
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.makeCurrent = function(){
    
    _.each(this.collections, function(collection){
      collection.makeUnCurrent();
    });

    this._isCurrent = true;

    this.highlightCurrent();

    return this;
  };

  /**
   * Make this node uncurrent
   *
   * @method Node#makeUnCurrent
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.makeUnCurrent = function(){

    if(this._isCurrent === true){
      this._isCurrent = false;
      this.unhighlight();
    }

    return this;
  };

  /**
   * Make this node avaible for move dron on it
   *
   * @method Node#makeAvaibleForMove
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.makeAvaibleForMove = function(){

    this._isAvaibleForMove = true;
    
    this.highlightAvaibleForMove();

    return this;
  };

  /**
   * Make this node avaible for move dron on it
   *
   * @method Node#makeUnAvaibleForMove
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.makeUnAvaibleForMove = function(){

    if(this._isAvaibleForMove === true){
      this._isAvaibleForMove = false;

      this.unhighlight();
    }
    
    return this;
  };

  /**
   * Make this node avaible for move dron on it
   *
   * @method Node#highlightCurrent
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.highlightCurrent = function(){

    this.sprite.alpha = 0.5;

    return this;
  };

  /**
   * Make this node avaible for move dron on it
   *
   * @method Node#highlightAvaibleForMove
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.highlightAvaibleForMove = function(){

    this.sprite.alpha = 1;

    return this;
  };

  /**
   * Remove all highlights from this node
   *
   * @method Node#highlight
   * @memberof Node
   * @chainable 
   * 
   * @return {Node}
   */
  Node.prototype.unhighlight = function(){

    this.sprite.alpha = 0.2;

    return this;
  };

  /**
   * OnClick event handler
   *
   * @method Node#onClick
   * @memberof Node
   * @chainable 
   * 
   * @return {Node} - Instance of node
   */
  Node.prototype.onClick = function(){

    var current = this.game.DAO.drons.getCurrent();
    if(!current || this._isAvaibleForMove !== true){
      return;
    }
    
    current.move(this);

    return this;
  };
  
  /**
   * onOver event handler
   *
   * @method Node#onOver
   * @memberof Node
   * @chainable 
   * 
   * @return {Node} - Instance of node
   */
  Node.prototype.onOver = function(){

    this.sprite.alpha = 1;

    return this;
  };

  /**
   * onOut event handler
   *
   * @method Node#onOut
   * @memberof Node
   * @chainable 
   * 
   * @return {Node} - Instance of node
   */
  Node.prototype.onOut = function(){

    this.sprite.alpha = 0.5;

    return this;
  };


  return Node;
});