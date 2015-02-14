/*
 * @class Collection collection
 * @classdesc This is the base class for collection of entities
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define(['events'], function(Events){
  
  /**
   * @constructor
   * @chainable 
   * 
   * @param {object} [params]  - An arguments for collection
   *
   * @return {Collection} - An instance of collection
   */
  var Collection = function(params){

    // game is very important and MUST be provided
    if(!params || !params.game || !(params.game instanceof Phaser.Game)){
      throw new Error('Game is not defined or wrong type. Stopped');
    }

    /**
     * @property {Phaser.Game} game
     */
    this.game = params.game;

    /**
     * @property {Entity[]} entities
     */
    this.entities = params.entities || [];

    return this;
  };
  
  /**
   * @constructor
   */
  Collection.prototype.constructor = Collection;

  /**
   * @property {function} type - A reference to constructor of entity
   */
  Collection.prototype.type = function(){};

  /**
   * Add entities to collection
   * 
   * @method Collection#add
   * @memberof Collection
   * @chainable 
   * 
   * @param {Entity[]} entities - An array of entities, that should be added
   *
   * @return {Collection}  - An instance of collection
   */
  Collection.prototype.add = function(entities){

    _.each(entities, function(entity){
      if(entity instanceof this.type){
        this.entities.push(entity);

        if(entity.collections.indexOf(this)){
          entity.collections.push(this);
        }
      }else{
        throw new Error('Tried to add an entity of another type. Stopped');
      }
    }, this);

    return this;
  };

  /**
   * Remove entities from collection
   * 
   * @method Collection#remove
   * @memberof Collection
   * @chainable 
   * 
   * @param {Entity[]} entities - An array of entities, that should be removed
   *
   * @return {Collection}  - An instance of collection
   */
  Collection.prototype.remove = function(entities){

    this.entities = _.reject(this.entities, function(entity){ return ~entities.indexOf(entity); });

    return this;
  };

  /**
   * Creates an entity and adds it to collection
   * 
   * @method Collection#create
   * @memberof Collection
   *
   * @param {object} [params] - An arguments for entity
   * 
   * @return {Entity}  - Created entity
   */
  Collection.prototype.create = function(params){
    

    var arg = params || {};

    arg.game = this.game;

    var entity = new this.type(arg);
    this.add([entity]);

    return entity;
  };

  /**
   * Find and return the first entity
   * 
   * @method Collection#first
   * @memberof Collection
   * 
   * @return {Entity}  - An instance of entity
   */
  Collection.prototype.first = function(){

    return _.first(this.entities);
  };

  /**
   * Find and return the last entity
   * 
   * @method Collection#last
   * @memberof Collection
   * 
   * @return {Entity}  - An instance of entity
   */
  Collection.prototype.last = function(){

    return _.last(this.entities);
  };

  /**
   * Bind an event to a `callback` function. 
   * Passing `"all"` will bind the callback to all events fired.
   *
   * @method Collection#on
   * @memberof Collection
   * @chainable 
   *
   * @param {string}   name       - A name to identify event
   * @param {function} callback   - A function, that will invoke when event fired
   * @param {object}   [context]  - A context of the callback
   * 
   * @return {Collection} - Instance of entity
   */
  Collection.prototype.on = Events.on;

  /**
   * Bind an event to only be triggered a single time. 
   * After the first time the callback is invoked, it will be removed.
   *
   * @method Collection#once
   * @memberof Collection
   * @chainable 
   *
   * @param {string}   name       - A name to identify event
   * @param {function} callback   - A function, that will invoke when event fired
   * @param {object}   [context]  - A context of the callback
   * 
   * @return {Collection} - Instance of entity
   */
  Collection.prototype.once = Events.once;

  /**
   * Remove one or many callbacks. 
   * If `context` is null, removes all callbacks with that function. 
   * If `callback` is null, removes all callbacks for the event. 
   * If `name` is null, removes all bound callbacks for all events.
   *
   * @method Collection#off
   * @memberof Collection
   * @chainable 
   *
   * @param {string}   [name]       - An event that should be removed
   * @param {function} [callback]   - A callback that should be removed
   * @param {object}   [context]    - A context for wich event should be removed
   * 
   * @return {Collection} - Instance of entity
   */
  Collection.prototype.off = Events.off;

  /**
   * Trigger one or many events, firing all bound callbacks. 
   * Callbacks are passed the same arguments as `trigger` is, apart from the event name 
   * (unless you're listening on `"all"`, which will cause your callback to
   * receive the true name of the event as the first argument).
   *
   * @method Collection#trigger
   * @memberof Collection
   * @chainable 
   *
   * @param {string}   name     - An event that should be triggered
   * @param {function} [*args]  - An arguments for callback
   * 
   * @return {Collection} - Instance of entity
   */
  Collection.prototype.trigger = Events.trigger;
    
  return Collection;
});