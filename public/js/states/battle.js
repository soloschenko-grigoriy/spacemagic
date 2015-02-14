/*
 * Battle state
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define(['config/resources', 'collections/drons', 'collections/nodes'], function(Resources, Drons, Nodes){

  /**
   * @constructor
   * 
   * @param {Phaser.Game} game  - A reference to the currently running game.
   *
   * @return {Battle}           - An instance of state
   */
  var Battle = function(game){};

  /**
   * Initialization
   *
   * @method Battle#init
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.init = function(){

    this.game.DAO = {};

    return this;
  };

  /**
   * Preload is called first. Normally you'd use this to load your game assets (or those needed for the current State)
   * You shouldn't create any objects in this method that require assets that you're also loading in this method, as
   * they won't yet be available.
   *
   * @method Battle#preload
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.preload = function(){

    _.each(Resources.images, function(image){
      this.load.image(image.key, image.image);
    }, this);
    
    _.each(Resources.atlases, function(atlas){
      this.load.atlasJSONHash(atlas.key, atlas.image, atlas.data);
    }, this);

    return this;
  };

  /**
   * LoadUpdate is called during the Loader process. This only happens if you've set one or more assets to load in the preload method.
   *
   * @method Battle#loadUpdate
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.loadUpdate = function(){};

  /**
   * LoadRender is called during the Loader process. This only happens if you've set one or more assets to load in the preload method.
   * The difference between loadRender and render is that any objects you render in this method you must be sure their assets exist.
   *
   * @method Battle#loadRender
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.loadRender = function(){};

  /**
   * Create is called once preload has completed, this includes the loading of any assets from the Loader.
   * If you don't have a preload method then create is the first method called in your State.
   *
   * @method Battle#create
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.create = function(){

    this.add.tileSprite(0, 0, 2035, 1272, Resources.images.background2.key);
    this.add.sprite(0, 500, Resources.images.planet.key);
    this.physics.startSystem(Phaser.Physics.ARCADE);

    this.game.DAO.nodes = new Nodes({ game: this.game });
    this.game.DAO.drons = new Drons({ game: this.game });

    this.game.DAO.drons.createForBattle(this.game.DAO.nodes);
    this.game.DAO.drons.first().makeCurrent();
    

    return this;
  };

  /**
   * The update method is left empty for your own use.
   * It is called during the core game loop AFTER debug, physics, plugins and the Stage have had their preUpdate methods called.
   * If is called BEFORE Stage, Tweens, Sounds, Input, Physics, Particles and Plugins have had their postUpdate methods called.
   *
   * @method Battle#update
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.update = function(){};

  /**
   * Nearly all display objects in Phaser render automatically, you don't need to tell them to render.
   * However the render method is called AFTER the game renderer and plugins have rendered, so you're able to do any
   * final post-processing style effects here. Note that this happens before plugins postRender takes place.
   *
   * @method Battle#render
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.render = function(){};

  /**
   * This method will be called if the core game loop is paused.
   *
   * @method Battle#paused
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.paused = function(){};

  /**
   * PauseUpdate is called while the game is paused instead of preUpdate, update and postUpdate.
   *
   * @method Battle#pauseUpdate
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.pauseUpdate = function(){};

  /**
   * This method will be called when the State is shutdown (i.e. you switch to another state from this one).
   *
   * @method Battle#shutdown
   * @chainable 
   * 
   * @return {Battle}
   */
  Battle.prototype.shutdown = function(){};

  return Battle;
});