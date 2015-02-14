/*
 * @class     Nodes collection
 * @classdesc Contains collection of nodes and used for managing all of them
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define(['entities/node', 'collections/collection'], function(Node, Collection){
  
  /**
   * @constructor
   * @extends {Collection}
   * 
   * @param {object} [params]  - An arguments for collection
   *
   * @return {Nodes} - An instance of collection
   */
  var Nodes = function(params){

    Collection.apply(this, arguments);

    _.each(this.positions, function(line, index){
      _.each(line, function(position, num){
        this.create({
          num      : num,
          line     : index,
          position : position
        }).render();
      }, this);
    }, this);

    return this;
  };

  /**
   * @extends {Collection}
   */
  Nodes.prototype = Object.create(Collection.prototype);

  /**
   * @constructor
   */
  Nodes.prototype.constructor = Nodes;

  /**
   * @property {function} type - A reference to constructor of entity
   */
  Nodes.prototype.type = Node;

  /**
   * @property {Hash} positions - A hash of positions for each node
   */
  Nodes.prototype.positions = {
    1: {
      1:  { x: 179, y: 130 },
      2:  { x: 236, y: 130 },
      3:  { x: 295, y: 130 },
      4:  { x: 354, y: 130 },
      5:  { x: 412, y: 130 },
      6:  { x: 471, y: 130 },
      7:  { x: 529, y: 130 },
      8:  { x: 588, y: 130 },
      9:  { x: 647, y: 130 },
      10: { x: 706, y: 130 },
      11: { x: 763, y: 131 },
      12: { x: 822, y: 130 },
    },
    2: {
      1:  { x: 195.5, y: 160.5 },
      2:  { x: 257,   y: 161.5 },
      3:  { x: 318,   y: 160.5 },
      4:  { x: 378,   y: 160.5 },
      5:  { x: 439,   y: 162.5 },
      6:  { x: 500,   y: 162   },
      7:  { x: 561,   y: 162   },
      8:  { x: 622,   y: 162   },
      9:  { x: 682.5, y: 163   },
      10: { x: 743,   y: 162   },
      11: { x: 804.5, y: 162.5 },
    },
    3: {
      1:  { x: 153.5, y: 195.5 },
      2:  { x: 216.5, y: 195.5 },
      3:  { x: 280,   y: 196.5 },
      4:  { x: 341.5, y: 196   },
      5:  { x: 406,   y: 196   },
      6:  { x: 468,   y: 196.5 },
      7:  { x: 532,   y: 196   },
      8:  { x: 594,   y: 196   },
      9:  { x: 659,   y: 197   },
      10: { x: 721,   y: 196.5 },
      11: { x: 784.5, y: 196   },
      12: { x: 848.5, y: 196   },
    },
    4: {
      1:  { x: 172,   y: 233   },
      2:  { x: 237,   y: 233   },
      3:  { x: 302.5, y: 233   },
      4:  { x: 368.5, y: 234   },
      5:  { x: 434.5, y: 232.5 },
      6:  { x: 501,   y: 233   },
      7:  { x: 565.5, y: 232.5 },
      8:  { x: 632.5, y: 233   },
      9:  { x: 696.5, y: 233.5 },
      10: { x: 762.5, y: 233   },
      11: { x: 830,   y: 233.5 },
    },
    5: {
      1:  { x: 123,   y: 273.5 },
      2:  { x: 192,   y: 274   },
      3:  { x: 260,   y: 272.5 },
      4:  { x: 328,   y: 273.5 },
      5:  { x: 397,   y: 272.5 },
      6:  { x: 465.5, y: 272   },
      7:  { x: 536,   y: 272   },
      8:  { x: 604,   y: 273   },
      9:  { x: 672,   y: 272   },
      10: { x: 740.5, y: 273   },
      11: { x: 808.5, y: 273   },
      12: { x: 877.5, y: 272.5 },
    },
    6: {
      1:  { x: 142,   y: 316.5 },
      2:  { x: 215,   y: 316.5 },
      3:  { x: 286,   y: 316.5 },
      4:  { x: 356.5, y: 316.5 },
      5:  { x: 429,   y: 316.5 },
      6:  { x: 500,   y: 315.5 },
      7:  { x: 571.5, y: 316.5 },
      8:  { x: 643.5, y: 315.5 },
      9:  { x: 715.5, y: 315.5 },
      10: { x: 787,   y: 315.5 },
      11: { x: 858,   y: 315.5 },
    },
    7: {
      1:  { x:88.5,   y: 364   },
      2:  { x: 162.5, y: 364   },
      3:  { x: 238,   y: 363.5 },
      4:  { x: 312.5, y: 364   },
      5:  { x: 387.5, y: 364   },
      6:  { x: 462,   y: 364   },
      7:  { x: 538,   y: 363   },
      8:  { x: 613,   y: 364   },
      9:  { x: 688.5, y: 364   },
      10: { x: 762,   y: 364   },
      11: { x: 837.5, y: 364   },
      12: { x: 911.5, y: 363   },
    },
    8: {
      1:  { x: 107,   y: 415.5 },
      2:  { x: 185.5, y: 415.5 },
      3:  { x: 264,   y: 415.5 },
      4:  { x: 342,   y: 416   },
      5:  { x: 421,   y: 416   },
      6:  { x: 499.5, y: 416.5 },
      7:  { x: 579.5, y: 415.5 },
      8:  { x: 656.5, y: 415.5 },
      9:  { x: 735,   y: 416   },
      10: { x: 815.5, y: 414.5 },
      11: { x: 892.5, y: 415.5 },
    },
    9: {
      1:  { x: 45.5,  y: 473.5 },
      2:  { x: 128.5, y: 473.5 },
      3:  { x: 210.5, y: 474   },
      4:  { x: 293,   y: 473   },
      5:  { x: 374.5, y: 473.5 },
      6:  { x: 457.5, y: 473   },
      7:  { x: 541,   y: 473   },
      8:  { x: 623,   y: 472   },
      9:  { x: 706.5, y: 473   },
      10: { x: 788,   y: 473   },
      11: { x: 871,   y: 472   },
      12: { x: 953.5, y: 471   },
    }
  };

  /**
   * Find and return grid's node by it's line ana number index
   *
   * @method Nodes#find
   * @memberof Nodes
   * 
   * @param {number} line - a line of required node
   * @param {number} num  - a num of required node
   * 
   * @return {Node} - founded node 
   */
  Nodes.prototype.find = function(line, num){

    return _.find(this.entities, function(node) { return node.line === line && node.num === num; });
  };

  /**
   * Find and return grid's node by it's x and y coordinates
   *
   * @method Nodes#findByCoords
   * @memberof Nodes
   * 
   * @param {number} x - a x coord of required node
   * @param {number} y - a y coord of required node
   * 
   * @return {Node} - founded node 
   */
  Nodes.prototype.findByCoords = function(x, y){

    return _.find(this.entities, function(node) { return node.position.x === x && node.position.y === y; });
  };

  /**
   * Find and return grid's node that is lastInLine ion provided line
   *
   * @method Nodes#lastInLine
   * @memberof Nodes
   * 
   * @param {number} line - a num of required node
   * 
   * @return {Node} - founded node 
   */
  Nodes.prototype.lastInLine = function(line){

    return this.find(line, (line % 2 === 0) ? 11 : 12);
  };

  /**
   * Make uncurrent all entities
   *
   * @method Nodes#makeUncurrentAll
   * @memberof Nodes
   * 
   * @return {Node} - founded node 
   */
  Nodes.prototype.makeUnCurrent = function(){

    _.invoke(this.entities, 'makeUnCurrent');

    return this;
  };

  /**
   * Highlight nodes, wich dron can stand on
   *
   * @method Nodes#makeAvaibleForMove
   * @memberof Nodes
   * @chainable
   * 
   * @param  {Node} node - A ref to node, on wich dron currently stands
   * 
   * @return {Nodes}      
   */
  Nodes.prototype.makeAvaibleForMove = function(node){

    _.invoke(this.findInRadius(node, 5, 'ignoreFilled'), 'makeAvaibleForMove');

    return this;
  };

  /**
   * Unighlight nodes, wich dron can stand on
   *
   * @method Nodes#makeAvaibleForMove
   * @memberof Nodes
   * @chainable
   * 
   * @return {Nodes}      
   */
  Nodes.prototype.makeUnAvaibleForMove = function(){

    _.invoke(this.entities, 'makeUnAvaibleForMove');

    return this;
  };

  /**
   * Make unhighlighted all entities
   *
   * @method Nodes#unhighlight
   * @memberof Nodes
   * 
   * @return {Node} - founded node 
   */
  Nodes.prototype.unhighlight = function(){

    _.invoke(this.entities, 'unhighlight');

    return this;
  };

  /**
   * Find and retun all nodes in provided radius around provided one
   *
   * @method Nodes#findInRadius
   * @memberof Nodes
   * 
   * @param {Node}   center   - A ref to node, around wich we should find other nodes
   * @param {number} [radius] - A radius in wich we shal find nodes
   * @param {string} [type]   - A type of searching -  'ignoreEmpty' or 'ignoreFilled' are avaible
   * 
   * @return {Node[]}        
   */
  Nodes.prototype.findInRadius = function(center, radius, type){
    
    var result    = [],
        positions = [];

    _.each(this.entities, function(node){

      if(type === 'ignoreEmpty' && !node.dron){
        return;
      }else if(type === 'ignoreFilled' && node.dron){
        return;
      }

      var diff = Math.abs(node.line - center.line);

      if(node.line === center.line){
        if(node.num >= center.num - radius && node.num <= center.num + radius && node.num !== center.num){
          result.push(node);
        }
      }else if(Math.abs(node.line - center.line) <= radius && (node.line - center.line) % 2 === 0){
        if(node.num >= center.num - radius + diff/2 && node.num <= center.num + radius - diff/2){
          result.push(node);
        }
      }else if(Math.abs(node.line - center.line) <= radius && (node.line - center.line) % 2 !== 0){
        if(node.line %2 === 0){
          if(node.num >= center.num - radius + diff/2-1 && node.num <= center.num + radius - diff/2){
            result.push(node);
          }
        }else{
          if(node.num >= center.num - radius + diff/2 && node.num <= center.num + radius - diff/2+1 ){
            result.push(node);
          }
        }
      }

    });

    return result;
  };

  

  return Nodes;
});

