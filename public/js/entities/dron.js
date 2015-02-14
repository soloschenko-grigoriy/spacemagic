/*
 * @class Dron
 * @classdesc Creates and managed the dron
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
   * @return {Dron} - An instance of Dron
   */
  var Dron = function(params){
    
    /**
     * @property {number} node - A ref to node, on wich this dron stands
     */
    this.node = params.node;

    /**
     * @property {number} f - A default value of frame
     */
    this.frame = params.frame || 1;

    return Entity.apply(this, arguments);
  };

  /**
   * @extends {Entity}
   */
  Dron.prototype = Object.create(Entity.prototype);

  /**
   * @constructor
   */
  Dron.prototype.constructor = Dron;

  /**
   * @property {Phaser.Sprite} sprite - A reference to the dron's sprite
   */
  Dron.prototype.sprite = null;

  /**
   * @property {boolean} isCurrent - Is this dron currently in use or not
   */
  Dron.prototype.isCurrent = false;

  /**
   * @property {Drones[]} collections - An array of collection, wich this dron consist in
   */
  Node.prototype.collections = [];

  /**
   * @property {Hash} angles - A list of angles
   */
  Dron.prototype.sectors = [
    { min: 0,   max: 9   },
    { min: 9,   max: 18  },
    { min: 18,  max: 27  },
    { min: 27,  max: 36  },
    { min: 36,  max: 45  },
    { min: 45,  max: 54  },
    { min: 54,  max: 63  },
    { min: 63,  max: 72  },
    { min: 72,  max: 81  },
    { min: 81,  max: 90  },
    { min: 90,  max: 99  },
    { min: 99,  max: 108 },
    { min: 108, max: 117 },
    { min: 117, max: 126 },
    { min: 126, max: 135 },
    { min: 135, max: 144 },
    { min: 144, max: 153 },
    { min: 153, max: 162 },
    { min: 162, max: 171 },
    { min: 171, max: 180 },
    { min: 180, max: 189 },
    { min: 189, max: 198 },
    { min: 198, max: 207 },
    { min: 207, max: 216 },
    { min: 216, max: 225 },
    { min: 225, max: 234 },
    { min: 234, max: 243 },
    { min: 243, max: 252 },
    { min: 252, max: 261 },
    { min: 261, max: 270 },
    { min: 270, max: 279 },
    { min: 279, max: 288 },
    { min: 288, max: 297 },
    { min: 297, max: 306 },
    { min: 306, max: 315 },
    { min: 315, max: 324 },
    { min: 324, max: 333 },
    { min: 333, max: 342 },
    { min: 342, max: 351 },
    { min: 351, max: 360 },
  ];

  /**
   * @property {Hash} frames - A list of frames per angle
   */
  Dron.prototype.frames = {
    '0'    : 5,
    '9'    : 4,
    '18'   : 3,
    '27'   : 2,
    '36'   : 1,
    '45'   : 0,
    '54'   : 39,
    '63'   : 38,
    '72'   : 37,
    '81'   : 36,
    '90'   : 35,
    '99'   : 34,
    '108'  : 33,
    '117'  : 32,
    '126'  : 31,
    '135'  : 30,
    '144'  : 29,
    '153'  : 28,
    '162'  : 27,
    '171'  : 26,
    '180'  : 25,
    '189'  : 24,
    '198'  : 23,
    '207'  : 22,
    '216'  : 21,
    '225'  : 20,
    '234'  : 19,
    '243'  : 18,
    '252'  : 17,
    '261'  : 16,
    '270'  : 15,
    '279'  : 14,
    '288'  : 13,
    '297'  : 12,
    '306'  : 11,
    '315'  : 10,
    '324'  : 9,
    '333'  : 8,
    '342'  : 7,
    '351'  : 6,
    '360'  : 5,
  };

  /**
   * Render grid to the game
   *
   * @method Dron#render
   * @memberof Dron
   * @chainable 
   * 
   * @return {Dron} - Instance of dron
   */
  Dron.prototype.render = function(){
    
    this.emptyCircle  = this.game.add.sprite(this.node.sprite.x, this.node.sprite.y, Resources.images.circle.key);
    this.manaCircle   = this.game.add.sprite(this.node.sprite.x, this.node.sprite.y, Resources.atlases.mana.key);
    this.healthCircle = this.game.add.sprite(this.node.sprite.x, this.node.sprite.y, Resources.atlases.health.key);
    
    this.sprite = this.game.add.sprite(this.node.sprite.x, this.node.sprite.y, Resources.atlases.dron.key, this.frame);
    
    this.sprite.anchor.setTo(0.5, 0.5);
    this.sprite.scale.setTo(this.calcScale(this.node.sprite.y), this.calcScale(this.node.sprite.y));

    this.sprite.inputEnabled = true;
    this.sprite.input.pixelPerfectAlpha = 1;
    this.sprite.input.pixelPerfectClick = 1;
    
    this.emptyCircle.anchor.setTo(0.5, 0.5);
    this.emptyCircle.scale.setTo(this.sprite.scale.x, this.sprite.scale.y);

    this.healthCircle.anchor.setTo(0.5, 0.5);
    this.healthCircle.scale.setTo(this.sprite.scale.x, this.sprite.scale.y);

    this.manaCircle.anchor.setTo(0.5, 0.5);
    this.manaCircle.scale.setTo(this.sprite.scale.x, this.sprite.scale.y);

    this.node.dron = this;
    // TweenMax.to(this.sprite, 0.4, {
    //   x: '+='+_.random(2,4), y: '+='+_.random(2,4),
    //   repeat          : 50,
    //   yoyo            : true,
    //   ease            : Power0.easeInOut,
    //  });

    return this;
  };

  /**
   * Move this dron to neccessary point
   *
   * @method Dron#move
   * @memberof Dron
   * @chainable 
   *
   * @param {Node} node - A ref to node, wich dron will stand on

   * @return {Dron} - Instance of dron
   */
  Dron.prototype.move = function(node){
    
    this.makeUnCurrent();
    this.rotate(node.sprite.x, node.sprite.y, this.doMove);
    this.node = node;

    return this;
  };

  /**
   * Rotate dron to front on provided point
   *
   * @method Dron#rotate
   * @memberof Dron
   * @chainable 
   *
   * @param {number} x - A x coordinate of point, to wich dron will be turned on front
   * @param {number} y - A y coordinate of point, to wich dron will be turned on front
   * @param {function} [onComplete] - A callback, fired when rotation will complete
   * 
   * @return {Dron} - Instance of dron
   */
  Dron.prototype.rotate = function(x, y, onComplete, onCompleteScope){

    var x0          = this.sprite.x,
        y0          = this.sprite.y,
        angle       = this.game.physics.arcade.angleToPointer(this.sprite),
        angleSector = 0,
        realAngle   = 0,
        startFrame  = this.sprite.animations.currentFrame.index,
        endFrame    = 0,
        framesArr1  = [],
        framesArr2  = [],
        framesArr   = [],
        frames      = [];
   
    angle += Math.PI;
    angle *= 180 / Math.PI;
    
    // detect sector of angle
    _.each(this.sectors, function(sector){
      if(angle >= sector.min && angle <= sector.max){
        angleSector = sector;
        return;
      }
    });

    // find the diff between min sector's angle and our angle AND
    // find the diff between max sector's angle and our angle
    // the min diff - shows our real angle
    if(Math.abs(angle - angleSector.min) > Math.abs(angle - angleSector.max)){
      realAngle = angleSector.max;
    }else{
      realAngle = angleSector.min;
    }

    // find the frame for real angle
    endFrame = this.frames[realAngle];

    // make 2 arrays for animation - in first case anim dron will rotate cww and vice versa
    framesArr1 = _.range(startFrame, endFrame + 1);
    framesArr2 = _.range(startFrame, endFrame +1, -1);

    if(startFrame <= endFrame){
      framesArr = _.range(startFrame, endFrame + 1);
    }else{
      framesArr = _.range(startFrame, endFrame - 1, -1);
    }

    // and finaly - rotate!
    var anim = this.sprite.animations.add('rotate', framesArr);

    if(onComplete){
      anim.onComplete.add(_.bind(onComplete, onCompleteScope || this, x, y));
    }
    
    anim.play('rotate', 40, false);

    return this;
  };

  /**
   * Animate moving and scaling of dron
   *
   * @method Dron#doMove
   * @memberof Dron
   * @chainable 
   * 
   * @param {number} x - A x coordinate of destination point
   * @param {number} y - A y coordinate of destination point
   * 
   * @return {Dron} - Instance of dron
   */
  Dron.prototype.doMove = function(x, y){
    
    var scale = this.calcScale(y);

    this.sprite.bringToTop();

    new TweenMax.to(this.sprite,        2, { x: x, y: y, ease:Power2.easeOut });
    new TweenMax.to(this.manaCircle,    2, { x: x, y: y, ease:Power2.easeOut });
    new TweenMax.to(this.emptyCircle,   2, { x: x, y: y, ease:Power2.easeOut });
    new TweenMax.to(this.healthCircle,  2, { x: x, y: y, ease:Power2.easeOut });

    new TweenMax.to(this.sprite.scale,        2.0, { x: scale, y: scale });
    new TweenMax.to(this.manaCircle.scale,    1.8, { x: scale, y: scale });
    new TweenMax.to(this.emptyCircle.scale,   1.8, { x: scale, y: scale });
    new TweenMax.to(this.healthCircle.scale,  1.8, { x: scale, y: scale });
  
    return this;
  };

  /**
   * Calculate neccessery dron scale for the position
   *
   * @method Dron#calcScale
   * @memberof Dron
   *
   * @param {number} y - A y coordinate of destination point
   * 
   * @return {number} - a scale value
   */
  Dron.prototype.calcScale = function(y){

    var scale = 1;
    if(y < 160){
      scale = 0.6;
    }else if(y < 195){
      scale = 0.65;
    }else if(y < 230){
      scale = 0.7;
    }else if(y < 270){
      scale = 0.75;
    }else if(y < 315){
      scale = 0.8;
    }else if(y < 360){
      scale = 0.85;
    }else if(y < 415){
      scale = 0.9;
    }else if(y < 470){
      scale = 0.95;
    }

    return scale;
  };

  /**
   * Make this dron current
   *
   * @method Dron#makeCurrent
   * @memberof Dron
   * @chainable 
   * 
   * @return {Dron} - a scale value
   */
  Dron.prototype.makeCurrent = function(){

    this.isCurrent = true;

    this.node.makeCurrent();
    this.game.DAO.nodes.makeAvaibleForMove(this.node);
    

    return this;
  };

  /**
   * Make this dron not current
   *
   * @method Dron#makeCurrent
   * @memberof Dron
   * @chainable 
   * 
   * @return {Dron} - a scale value
   */
  Dron.prototype.makeUnCurrent = function(){

    if(this.isCurrent === true){
      this.isCurrent = false;

      this.node.makeUnCurrent();
      this.game.DAO.nodes.makeUnAvaibleForMove();
    }
    
    return this;
  };

  return Dron;
});