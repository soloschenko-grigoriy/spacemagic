requirejs.config({
    baseUrl: '/js/',
    paths: {
      // ------------------- Libs -------------------//
      'underscore'    : '/js/vendor/underscore/underscore.min',
      'phaser'        : '/js/vendor/phaser/phaser.min',
      'TweenMax'      : '/js/vendor/gsap/TweenMax.min',
      'TimelineMax'   : '/js/vendor/gsap/TimelineMax.min',
      'events'        : '/js/vendor/backbone/events'
    },
    shim: {
      'phaser': {
        exports: 'Phaser'
      },
      'TimelineMax': {
        exports: 'TimelineMax'
      },
      'TweenMax': {
        exports: 'TweenMax'
      },
    }

});

require(['underscore', 'phaser', 'states/battle', 'states/mainMenu', 'TimelineMax', 'TweenMax'], function(_, Phaser, BattleState, MainMenuState){
  var game = new Phaser.Game(1000, 660, Phaser.AUTO, '');

  game.state.add('Battle', BattleState);
  game.state.add('MainMenu', MainMenuState);

  game.state.start('Battle');
});

