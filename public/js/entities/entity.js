/*
 * @class     Entity class
 * @classdesc Creates and managed the entity
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
   * @param {object} [params] - An arguments for entity
   *
   * @return {Entity}  - An instance of entity
   */
  var Entity = function(params){

    // game is very important and MUST be provided
    if(!params || !params.game || !(params.game instanceof Phaser.Game)){
      throw new Error('Game is not defined or wrong type. Stopped');
    }

    /**
     * @property {Phaser.Game} game - A reference to the currently running Game.
     */
    this.game = params.game;

    return this;
  };

  /**
   * @constructor
   */
  Entity.prototype.constructor = Entity;

  /**
   * @property {Collection[]} collections - An array of collection, wich this entity consist in
   */
  Entity.prototype.collections = [];

  /**
   * Render entity to the game
   *
   * @method Entity#render
   * @memberof Entity
   * @chainable 
   * 
   * @return {Entity} - Instance of entity
   */
  Entity.prototype.render = function(){};

  /**
   * Bind an event to a `callback` function. 
   * Passing `"all"` will bind the callback to all events fired.
   *
   * @method Entity#on
   * @memberof Entity
   * @chainable 
   *
   * @param {string}   name       - A name to identify event
   * @param {function} callback   - A function, that will invoke when event fired
   * @param {object}   [context]  - A context of the callback
   * 
   * @return {Entity} - Instance of entity
   */
  Entity.prototype.on = Events.on;

  /**
   * Bind an event to only be triggered a single time. 
   * After the first time the callback is invoked, it will be removed.
   *
   * @method Entity#once
   * @memberof Entity
   * @chainable 
   *
   * @param {string}   name       - A name to identify event
   * @param {function} callback   - A function, that will invoke when event fired
   * @param {object}   [context]  - A context of the callback
   * 
   * @return {Entity} - Instance of entity
   */
  Entity.prototype.once = Events.once;

  /**
   * Remove one or many callbacks. 
   * If `context` is null, removes all callbacks with that function. 
   * If `callback` is null, removes all callbacks for the event. 
   * If `name` is null, removes all bound callbacks for all events.
   *
   * @method Entity#off
   * @memberof Entity
   * @chainable 
   *
   * @param {string}   [name]       - An event that should be removed
   * @param {function} [callback]   - A callback that should be removed
   * @param {object}   [context]    - A context for wich event should be removed
   * 
   * @return {Entity} - Instance of entity
   */
  Entity.prototype.off = Events.off;

  /**
   * Trigger one or many events, firing all bound callbacks. 
   * Callbacks are passed the same arguments as `trigger` is, apart from the event name 
   * (unless you're listening on `"all"`, which will cause your callback to
   * receive the true name of the event as the first argument).
   *
   * @method Entity#trigger
   * @memberof Entity
   * @chainable 
   *
   * @param {string}   name     - An event that should be triggered
   * @param {function} [*args]  - An arguments for callback
   * 
   * @return {Entity} - Instance of entity
   */
  Entity.prototype.trigger = Events.trigger;

  return Entity;
});