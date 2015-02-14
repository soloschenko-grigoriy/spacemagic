/*
 * @class Resources DAO
 * @classdesc Contains all resources
 *
 * @author    Soloschenko G. soloschenko@gmail.com
 * @copyright Soloschenko G. soloschenko@gmail.com
 * @version   1.0
 */
define({
  
  /**
   * @property {Hash} images - Contains all image resouces
   */
  images: {
    background: {
      key   : 'background',
      image : '/assets/images/gui/bg.png',
    },
    background2: {
      key   : 'background2',
      image : '/assets/images/gui/bg2.png',
    },
    background3: {
      key   : 'background3',
      image : '/assets/images/gui/bg3.jpg',
    },
    planet: {
      key   : 'planet',
      image : '/assets/images/gui/planet.png',
    },
    circle: {
      key   : 'circle',
      image : '/assets/images/gui/circle.png',
    },
    rec: {
      key   : 'rec',
      image : '/assets/images/gui/rec.png',
    },
  },

  /**
   * @property {Hash} sounds - Contains all sound resouces
   */
  sounds: {

  },

  /**
   * @property {Hash} data - Contains all data resouces
   */
  atlases: {
    dron: {
      key   : 'dron',
      data  : '/assets/data/dron.json',
      image : '/assets/images/dron.png'
    },
    grid: {
      key   : 'grid',
      data  : '/assets/data/grid.json',
      image : '/assets/images/gui/grid.png'
    },
    health: {
      key   : 'health',
      data  : '/assets/data/health.json',
      image : '/assets/images/gui/health.png'
    },
    mana: {
      key   : 'mana',
      data  : '/assets/data/mana.json',
      image : '/assets/images/gui/mana.png'
    }
  },
});