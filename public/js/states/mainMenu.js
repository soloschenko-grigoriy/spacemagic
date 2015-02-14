/*
 * Battle state
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define(function(){

  /**
   * Contains all about battle lifecircle - create, update, render etc
   * 
   * @param  {Phaser.Game} game - A reference to the currently running game.
   */
  return function(game){

    /**
     * Preload is called first. Normally you'd use this to load your game assets (or those needed for the current State)
     * You shouldn't create any objects in this method that require assets that you're also loading in this method, as
     * they won't yet be available.
     *
     * @method Battle#preload
     */
    this.preload = function()
    {
      this.load.image('background', '/assets/images/gui/bg.png');
      this.load.spritesheet('menu', '/assets/images/gui/menu.png', 200, 42);
    };

    /**
     * LoadUpdate is called during the Loader process. This only happens if you've set one or more assets to load in the preload method.
     *
     * @method Battle#loadUpdate
     */
    this.loadUpdate = function()
    {
    };

    /**
     * LoadRender is called during the Loader process. This only happens if you've set one or more assets to load in the preload method.
     * The difference between loadRender and render is that any objects you render in this method you must be sure their assets exist.
     *
     * @method Battle#loadRender
     */
    this.loadRender = function()
    {
    };

    /**
     * Create is called once preload has completed, this includes the loading of any assets from the Loader.
     * If you don't have a preload method then create is the first method called in your State.
     *
     * @method Battle#create
     */
    this.create = function()
    {
      this.add.sprite(0, 0, 'background');

      for(var i = 0; i <= 3; i++){
        var item = this.add.button(300, 100 + 45 * i, 'menu', this.actionOnClick, this, 1, 0, 0),
            style = { font: "20px Arial", fill: "#fff", align: "center" };
        
        this.add.text(item.x + 70, item.y + 10, "Some", style);
      }
    };

    /**
     * 
     * 
     * @return {[type]} [description]
     */
    this.actionOnClick = function()
    {
      this.state.start('Battle');
    };

    /**
     * The update method is left empty for your own use.
     * It is called during the core game loop AFTER debug, physics, plugins and the Stage have had their preUpdate methods called.
     * If is called BEFORE Stage, Tweens, Sounds, Input, Physics, Particles and Plugins have had their postUpdate methods called.
     *
     * @method Battle#update
     */
    this.update = function()
    {
    };

    /**
     * Nearly all display objects in Phaser render automatically, you don't need to tell them to render.
     * However the render method is called AFTER the game renderer and plugins have rendered, so you're able to do any
     * final post-processing style effects here. Note that this happens before plugins postRender takes place.
     *
     * @method Battle#render
     */
    this.render = function()
    {
    };

    /**
     * This method will be called if the core game loop is paused.
     *
     * @method Battle#paused
     */
    this.paused = function()
    {
    };

    /**
     * PauseUpdate is called while the game is paused instead of preUpdate, update and postUpdate.
     *
     * @method Battle#pauseUpdate
     */
    this.pauseUpdate = function()
    {
    };

    /**
     * This method will be called when the State is shutdown (i.e. you switch to another state from this one).
     *
     * @method Battle#shutdown
     */
    this.shutdown = function()
    {
    };
  };
});