
//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/eroulette/action.php?gid='+gid+'&';

//general vars
var current_bet = 0;
var marker = null;
var wheel = null;
var numeric_wheel = null;
var shadow = null;
var ref_ball = null;
var ball = null;
var show_wheel = false;
var hide_wheel = false;
var shadow_alpha = 0.6;
var spinning = false;
var place_bets = true;
var ball_grp = null;
var spin_angle = 0;
var spin_distance = 0;
var spin_speed = 0;
var spin_interval = null;
var winner_number = null;
var bucket_radius = 0;
var numbers_angles = new Array();
var ball_in_position = false;
var ready_to_stop = false;
var ball_jumping = false;
var chips_values = [0.25,1,5,25,100];
var chips = new Array();
var new_chips = new Array();
var mooving_chips = new Array();
var placed_chips = new Array();
var bet_areas = new Array();
var bet_areas_data = new Array();
var selected_chip = 1;
var unselected_chip_alpha = 0.4;
var chip_speed = 1000;
var test_text = null;
var area_bets_chip_count = new Array();
var area_bets_amount = new Array();
var area_bets_amount_text = new Array();
var clear_chips_to = "";
var winning_amount = 0;
var winning_color = "";
var winning_str_color = "";
var winning_areas = new Array();
var after_spin_balance = 0;
var move_marker = false;
var move_marker_out = false;
var previous_bets = "";
var shift_on = false;
var after_spin = false;
var nhistory = new Array();
var history_black = null;
var history_red = null;
var history_green = null;
var ball_spin_sound = null;
var ball_droping_sound = null;
var ball_final_sound = null;
var no_more_bets_sound = null;
var place_bets_sound = null;
var black_sound = null;
var red_sound = null;
var twenty_sound = null;
var thirty_sound = null;
var win_sound = null;
var nums_sounds = new Array();
var is_mute = false;
var set_mute = false;
var pfdata = null;

//calculate width
var game_width = $(window).width();
if(game_width > 1900){game_width = 1900;}

//calculate height for size 9/16
var game_height = (game_width * 9)/16; 

var config = {
	type: Phaser.AUTO,
	width: game_width,
	height: game_height,
	physics: {
        default: 'arcade',
    },
	scene: {
		preload: preload,
		create: create,
		update: update
	},
	audio: {
        disableWebAudio: true
    }
};

var game = new Phaser.Game(config);

function preload (){
	
	//Loading Bar
	var progressBar = this.add.graphics();
	var progressBox = this.add.graphics();
	progressBox.fillStyle(0x222222, 0.8);
	progressBox.fillRect((game_width/2)-150, game_height/2, 320, 50);
	
	var loadingText = this.make.text({
		x: game_width / 2,
		y: game_height / 2 - 50,
		text: 'Loading...',
		style: {
			font: '20px monospace',
			fill: '#ffffff'
		}
	});
	loadingText.setOrigin(0.5, 0.5);
	
	this.load.on('progress', function (value) {
		progressBar.clear();
		progressBar.fillStyle(0xffffff, 1);
		progressBar.fillRect((game_width/2)-140, (game_height/2)+10, 300 * value, 30);
	});
	
	
	//Load elements
	//Images
	this.load.image('background', 'utilities/games/eroulette/imgs/back.jpg?v3');
	this.load.image('wheel', 'utilities/games/eroulette/imgs/wheel.png');
	this.load.image('numeric_wheel', 'utilities/games/eroulette/imgs/enumeric_wheel.png');
	this.load.image('ball', 'utilities/games/eroulette/imgs/ball.png');
	this.load.image('chip0.25', 'utilities/images/games/chips/chip0.25.png');	
	this.load.image('chip1', 'utilities/images/games/chips/chip1.png');
	this.load.image('chip5', 'utilities/images/games/chips/chip5.png');	
	this.load.image('chip25', 'utilities/images/games/chips/chip25.png');
	this.load.image('chip100', 'utilities/images/games/chips/chip100.png');
	this.load.image('chip500', 'utilities/images/games/chips/chip500.png');
	this.load.image('area', 'utilities/games/eroulette/imgs/blank_area.png');
	this.load.image('marker', 'utilities/games/eroulette/imgs/marker.png?v3');  
	
	//Sounds
	this.load.audio('ball_spin', 'utilities/games/eroulette/sounds/ball_spin.mp3');
	this.load.audio('ball_droping', 'utilities/games/eroulette/sounds/ball_droping.mp3');
	this.load.audio('ball_final', 'utilities/games/eroulette/sounds/ball_final.mp3');
	this.load.audio('no_more_bets', 'utilities/games/eroulette/sounds/f_NoMoreBets.mp3');
	this.load.audio('place_bets', 'utilities/games/eroulette/sounds/f_PlaceYourBets.mp3');
	this.load.audio('black', 'utilities/games/eroulette/sounds/f_Black.mp3');
	this.load.audio('red', 'utilities/games/eroulette/sounds/f_Red.mp3');
	this.load.audio('twenty', 'utilities/games/eroulette/sounds/f_Twenty.mp3');
	this.load.audio('thirty', 'utilities/games/eroulette/sounds/f_Thirty.mp3');
	this.load.audio('win', 'utilities/games/eroulette/sounds/f_YouWin.mp3');
	for(var i=0; i<20; i++){
		this.load.audio('num'+i, 'utilities/games/eroulette/sounds/f_'+i+'.mp3');
	}
	
}

function create (){
	
	//Show limits box
	$("#limit_box").css("top",(game_height*0.01)+"px");
	$("#limit_box").css("left",(game_width*0.01)+"px");
	$("#limit_box").show(500);
	
	//Show balance box
	$("#balance_box").css("top",(game_height-(game_height*0.15))+"px");
	$("#balance_box").css("left",(game_width*0.01)+"px");
	$("#balance_box").show(500);
	
	//position message box
	$(".game_msg").css("top",(game_height/2)+"px");
	
	this.scale.pageAlignHorizontally = true;
	
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");
	
	//sounds
	ball_spin_sound = this.sound.add("ball_spin", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	ball_droping_sound = this.sound.add("ball_droping", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	ball_final_sound = this.sound.add("ball_final", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	no_more_bets_sound = this.sound.add("no_more_bets", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	place_bets_sound = this.sound.add("place_bets", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	black_sound = this.sound.add("black", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	red_sound = this.sound.add("red", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	twenty_sound = this.sound.add("twenty", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	thirty_sound = this.sound.add("thirty", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	win_sound = this.sound.add("win", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});	
	for(var i=0; i<20; i++){
		nums_sounds.push(this.sound.add('num'+i, {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	}
	
	//Set background
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	//Buttons
	$("#btn_box").css("top",(game_height - (game_height*0.06)) + "px");	
	
	//chips
	var chip_separator = game.config.width*0.043;
	for(var i = 0; i < chips_values.length; i++){
		chips[chips_values[i]] = this.add.image(game_width/2+(chip_separator*i), game_height/1.15, 'chip'+chips_values[i]).setInteractive();
		chips[chips_values[i]].displayWidth=game.config.width*0.040; 
		chips[chips_values[i]].scaleY=chips[chips_values[i]].scaleX;
		chips[chips_values[i]].on('pointerup', function (pointer) {	
			var cvalue = this.texture.key.replace("chip",""); // get the chip value based on the texture name (ex: chip1)
			change_chip(cvalue*1, false);
		});		
	}	
	change_chip(1);
	
	//bet areas
	bet_areas_data.push({id: "red", xpos: game_width/2.31, ypos: game_height/1.54, w: game.config.width*0.1, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "black", xpos: game_width/1.86, ypos: game_height/1.54, w: game.config.width*0.1, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "1to18", xpos: game_width/4.5, ypos: game_height/1.54, w: game.config.width*0.1, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "even", xpos: game_width/3.05, ypos: game_height/1.54, w: game.config.width*0.1, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "odd", xpos: game_width/1.55, ypos: game_height/1.54, w: game.config.width*0.1, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "19to36", xpos: game_width/1.33, ypos: game_height/1.54, w: game.config.width*0.1, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "1st12", xpos: game_width/3.45, ypos: game_height/1.74, w: game.config.width*0.205, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "2nd12", xpos: game_width/2.02, ypos: game_height/1.74, w: game.config.width*0.205, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "3rd12", xpos: game_width/1.42, ypos: game_height/1.74, w: game.config.width*0.205, h:game.config.width*0.040, divider:1});
	bet_areas_data.push({id: "1strow", xpos: game_width/1.21, ypos: game_height/2.05, w: game.config.width*0.07, h:game.config.width*0.050, divider:1});
	bet_areas_data.push({id: "2ndrow", xpos: game_width/1.23, ypos: game_height/2.55, w: game.config.width*0.07, h:game.config.width*0.050, divider:1});
	bet_areas_data.push({id: "3rdrow", xpos: game_width/1.235, ypos: game_height/3.35, w: game.config.width*0.07, h:game.config.width*0.050, divider:1});
	
	bet_areas_data.push({id: "0", xpos: game_width/6.7, ypos: game_height/2.55, w: game.config.width*0.05, h:game.config.width*0.050, divider:8});
	bet_areas_data.push({id: "0-2", xpos: game_width/5.8, ypos: game_height/2.55, w: game.config.width*0.02, h:game.config.width*0.02, divider:4});
	
	bet_areas_data.push({id: "1", xpos: game_width/5.05, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "4", xpos: game_width/4, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "7", xpos: game_width/3.35, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "10", xpos: game_width/2.85, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "13", xpos: game_width/2.48, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "16", xpos: game_width/2.21, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "19", xpos: game_width/1.99, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "22", xpos: game_width/1.81, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "25", xpos: game_width/1.655, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "28", xpos: game_width/1.53, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "31", xpos: game_width/1.42, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "34", xpos: game_width/1.32, ypos: game_height/2.05, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	
	bet_areas_data.push({id: "2", xpos: game_width/5, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "5", xpos: game_width/3.95, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "8", xpos: game_width/3.3, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "11", xpos: game_width/2.84, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "14", xpos: game_width/2.48, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "17", xpos: game_width/2.21, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "20", xpos: game_width/1.99, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "23", xpos: game_width/1.81, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "26", xpos: game_width/1.67, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "29", xpos: game_width/1.54, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "32", xpos: game_width/1.430, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "35", xpos: game_width/1.335, ypos: game_height/2.55, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	
	bet_areas_data.push({id: "3", xpos: game_width/4.85, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "6", xpos: game_width/3.86, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "9", xpos: game_width/3.28, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "12", xpos: game_width/2.84, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "15", xpos: game_width/2.48, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "18", xpos: game_width/2.21, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "21", xpos: game_width/1.99, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "24", xpos: game_width/1.82, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "27", xpos: game_width/1.68, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "30", xpos: game_width/1.55, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "33", xpos: game_width/1.44, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	bet_areas_data.push({id: "36", xpos: game_width/1.35, ypos: game_height/3.35, w: game.config.width*0.045, h:game.config.width*0.040, divider:8});
	
	bet_areas_data.push({id: "0-1-2-3", xpos: game_width/5.9, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "1-4-2-5-3-6", xpos: game_width/4.6, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "4-7-5-8-6-9", xpos: game_width/3.7, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "7-10-8-11-9-12", xpos: game_width/3.1, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "10-13-11-14-12-15", xpos: game_width/2.7, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "13-16-14-17-15-18", xpos: game_width/2.36, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "16-19-17-20-18-21", xpos: game_width/2.1, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "19-22-20-23-21-24", xpos: game_width/1.9, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "22-25-23-26-24-27", xpos: game_width/1.73, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "25-28-26-29-27-30", xpos: game_width/1.59, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "28-31-29-32-30-33", xpos: game_width/1.47, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	bet_areas_data.push({id: "31-34-32-35-33-36", xpos: game_width/1.37, ypos: game_height/1.9, w: game.config.width*0.02, h:game.config.width*0.02, divider:1.33});
	
	bet_areas_data.push({id: "1-2-3", xpos: game_width/5.16, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "4-5-6", xpos: game_width/4.12, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "7-8-9", xpos: game_width/3.36, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "10-11-12", xpos: game_width/2.88, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "13-14-15", xpos: game_width/2.52, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "16-17-18", xpos: game_width/2.22, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "19-20-21", xpos: game_width/1.99, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "22-23-24", xpos: game_width/1.81, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "25-26-27", xpos: game_width/1.65, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "28-29-30", xpos: game_width/1.525, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "31-32-33", xpos: game_width/1.42, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "34-35-36", xpos: game_width/1.32, ypos: game_height/1.9, w: game.config.width*0.025, h:game.config.width*0.02, divider:2.66});
	
	bet_areas_data.push({id: "1-2", xpos: game_width/5.05, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "4-5", xpos: game_width/4.04, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "7-8", xpos: game_width/3.35, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "10-11", xpos: game_width/2.88, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "13-14", xpos: game_width/2.51, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "16-17", xpos: game_width/2.23, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "19-20", xpos: game_width/2, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "22-23", xpos: game_width/1.82, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "25-26", xpos: game_width/1.67, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "28-29", xpos: game_width/1.54, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "31-32", xpos: game_width/1.43, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "34-35", xpos: game_width/1.33, ypos: game_height/2.3, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	
	bet_areas_data.push({id: "2-3", xpos: game_width/4.96, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "5-6", xpos: game_width/3.96, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "8-9", xpos: game_width/3.32, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "11-12", xpos: game_width/2.87, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "14-15", xpos: game_width/2.51, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "17-18", xpos: game_width/2.23, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "20-21", xpos: game_width/2.01, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "23-24", xpos: game_width/1.83, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "26-27", xpos: game_width/1.68, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "29-30", xpos: game_width/1.555, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "32-33", xpos: game_width/1.445, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	bet_areas_data.push({id: "35-36", xpos: game_width/1.34, ypos: game_height/2.95, w: game.config.width*0.025, h:game.config.width*0.02, divider:4});
	
	bet_areas_data.push({id: "0-1", xpos: game_width/5.92, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "1-4", xpos: game_width/4.55, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "4-7", xpos: game_width/3.69, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "7-10", xpos: game_width/3.11, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "10-13", xpos: game_width/2.68, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "13-16", xpos: game_width/2.36, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "16-19", xpos: game_width/2.11, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "19-22", xpos: game_width/1.91, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "22-25", xpos: game_width/1.74, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "25-28", xpos: game_width/1.6, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "28-31", xpos: game_width/1.48, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "31-34", xpos: game_width/1.38, ypos: game_height/2.08, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	
	bet_areas_data.push({id: "2-5", xpos: game_width/4.45, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "5-8", xpos: game_width/3.63, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "8-11", xpos: game_width/3.09, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "11-14", xpos: game_width/2.68, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "14-17", xpos: game_width/2.36, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "17-20", xpos: game_width/2.12, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "20-23", xpos: game_width/1.92, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "23-26", xpos: game_width/1.75, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "26-29", xpos: game_width/1.61, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "29-32", xpos: game_width/1.49, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "32-35", xpos: game_width/1.39, ypos: game_height/2.57, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	
	bet_areas_data.push({id: "0-3", xpos: game_width/5.52, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "3-6", xpos: game_width/4.35, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "6-9", xpos: game_width/3.6, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "9-12", xpos: game_width/3.06, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "12-15", xpos: game_width/2.67, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "15-18", xpos: game_width/2.36, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "18-21", xpos: game_width/2.12, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "21-24", xpos: game_width/1.93, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "24-27", xpos: game_width/1.76, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "27-30", xpos: game_width/1.62, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "30-33", xpos: game_width/1.51, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	bet_areas_data.push({id: "33-36", xpos: game_width/1.4, ypos: game_height/3.47, w: game.config.width*0.02, h:game.config.width*0.025, divider:4});
	
	bet_areas_data.push({id: "0-1-2", xpos: game_width/5.8, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "1-4-2-5", xpos: game_width/4.48, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "4-7-5-8", xpos: game_width/3.67, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "7-10-8-11", xpos: game_width/3.1, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "10-13-11-14", xpos: game_width/2.68, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "13-16-14-17", xpos: game_width/2.37, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "16-19-17-20", xpos: game_width/2.11, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "19-22-20-23", xpos: game_width/1.9, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "22-25-23-26", xpos: game_width/1.74, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "25-28-26-29", xpos: game_width/1.6, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "28-31-29-32", xpos: game_width/1.48, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "31-34-32-35", xpos: game_width/1.38, ypos: game_height/2.3, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	
	bet_areas_data.push({id: "2-0-3", xpos: game_width/5.62, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2.66});
	bet_areas_data.push({id: "2-5-3-6", xpos: game_width/4.41, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "5-8-6-9", xpos: game_width/3.61, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "8-11-9-12", xpos: game_width/3.08, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "11-14-12-15", xpos: game_width/2.68, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "14-17-15-18", xpos: game_width/2.36, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "17-20-18-21", xpos: game_width/2.12, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "20-23-21-24", xpos: game_width/1.92, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "23-26-24-27", xpos: game_width/1.75, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "26-29-27-30", xpos: game_width/1.62, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "29-32-30-33", xpos: game_width/1.5, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});
	bet_areas_data.push({id: "32-35-33-36", xpos: game_width/1.395, ypos: game_height/2.95, w: game.config.width*0.02, h:game.config.width*0.02, divider:2});	
	
	
	for(var i = 0; i < bet_areas_data.length; i++){
		bet_areas[bet_areas_data[i].id] = this.add.image(bet_areas_data[i].xpos, bet_areas_data[i].ypos, 'area').setInteractive();
		bet_areas[bet_areas_data[i].id].name = bet_areas_data[i].id;
		bet_areas[bet_areas_data[i].id].displayWidth=bet_areas_data[i].w; 
		bet_areas[bet_areas_data[i].id].displayHeight=bet_areas_data[i].h; 
		bet_areas[bet_areas_data[i].id].alpha = 0.5;
		bet_areas[bet_areas_data[i].id].on('pointerup', function (pointer) { place_bet(this.name); });	
		bet_areas[bet_areas_data[i].id].divider = bet_areas_data[i].divider;	
	}
	
	clear_betareas_amounts();
	
	//Shadow
	shadow = this.add.graphics();
	shadow.fillStyle(0x000000, 1);
	shadow.fillRect(0, 0, game_width, game_height);
	shadow.alpha = 0;
	shadow.depth = 1000000;
	
	//Marker
	marker = this.physics.add.image(game_width/14.94, game_height/3.09, 'marker');
	marker.displayWidth=game.config.width*0.04; 
	marker.scaleY=marker.scaleX;
	marker.depth = 999999;
	
	//Wheel
	wheel = this.add.image(game_width/2, game_height/2, 'wheel');
	wheel.displayWidth=game.config.width*0.4; 
	wheel.scaleY=wheel.scaleX;
	wheel.alpha = 0;
	wheel.depth = 1000000;
	
	numeric_wheel = this.add.image(game_width/2, game_height/2, 'numeric_wheel');
	numeric_wheel.displayWidth=game.config.width*0.2576; 
	numeric_wheel.scaleY=numeric_wheel.scaleX;
	numeric_wheel.alpha = 0;
	numeric_wheel.depth = 1000000;
	
	//ball
	ball = this.add.image(game_width/3.05, game_height/2, 'ball');
	ball.displayWidth=game.config.width*0.01418; 
	ball.scaleY=ball.scaleX;
	ball.alpha = 0;	
	ball.depth = 1000000;
	ball_grp = this.add.group();
	ball_grp.add(ball);
	
	ref_ball = this.add.image(game_width/3.05, game_height/2, 'ball');
	ref_ball.displayWidth=game.config.width*0.01418; 
	ref_ball.scaleY=ref_ball.scaleX;
	ref_ball.alpha = 0;	
	ref_ball.depth = 1000000;
	
	//number angles
	bucket_radius = numeric_wheel.displayWidth*0.32; 
	numbers_angles[0] = (0);
	numbers_angles[1] = (224);
	numbers_angles[2] = (58);
	numbers_angles[3] = (340);
	numbers_angles[4] = (39);
	numbers_angles[5] = (185);
	numbers_angles[6] = (97);
	numbers_angles[7] = (302);
	numbers_angles[8] = (156);
	numbers_angles[9] = (263);
	numbers_angles[10] = (176);
	numbers_angles[11] = (137);
	numbers_angles[12] = (321);
	numbers_angles[13] = (117);
	numbers_angles[14] = (244);
	numbers_angles[15] = (19);
	numbers_angles[16] = (205);
	numbers_angles[17] = (78);
	numbers_angles[18] = (283);
	numbers_angles[19] = (29);
	numbers_angles[20] = (234);
	numbers_angles[21] = (49);
	numbers_angles[22] = (273);
	numbers_angles[23] = (166);
	numbers_angles[24] = (195);
	numbers_angles[25] = (68);
	numbers_angles[26] = (350);
	numbers_angles[27] = (107);
	numbers_angles[28] = (311);
	numbers_angles[29] = (292);
	numbers_angles[30] = (147);
	numbers_angles[31] = (253);
	numbers_angles[32] = (10);
	numbers_angles[33] = (215);
	numbers_angles[34] = (88);
	numbers_angles[35] = (331);
	numbers_angles[36] = (127);
	
	
	//set text settings
	machine_text_style = { font: (game_width*0.01) + "px Arial", fill: "#ffffff", align: "center" };
	test_text = this.add.text(game_width/2.84, game_height/1.341, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
	var black_numbers_style = { font: (game_width*0.015) + "px Arial", fill: "#ffffff", align: "left" };
	history_black = this.add.text(game_width/1.11, game_height/8.22, "", black_numbers_style);
	
	var red_numbers_style = { font: (game_width*0.015) + "px Arial", fill: "#FF0000", align: "right" };
	history_red = this.add.text(game_width/1.063, game_height/8.22, "", red_numbers_style);
	
	var green_numbers_style = { font: (game_width*0.015) + "px Arial", fill: "#00FF00", align: "center" };
	history_green = this.add.text(game_width/1.085, game_height/8.22, "", green_numbers_style);
	
	
	this.input.keyboard.on('keydown_SHIFT', function(){shift_on = true;}, this);
	this.input.keyboard.on('keyup_SHIFT', function(){shift_on = false;}, this);
	
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	//positions tests	
	/*var angle_mod = 0;	
	var R = 170;	
	var num_angle = 350;
	numeric_wheel.angle += angle_mod;	
	ball.x = R*Math.cos((numeric_wheel.angle + num_angle)*Math.PI/180) + game_width/2;
    ball.y = R*Math.sin((numeric_wheel.angle + num_angle)*Math.PI/180) + game_height/2;*/
	
	//setInterval("testmode();",1000);
	
	
}

function update (){
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;
	
	//Show wheel
	if(show_wheel){
		if(shadow_alpha > shadow.alpha){
			shadow.alpha += 0.1;
		}
		if(1 > wheel.alpha){
			wheel.alpha += 0.1;
			numeric_wheel.alpha += 0.1;
			ball.alpha += 0.1;
		}else{
			show_wheel = false;	
		}
	}
	
	//hide wheel
	if(hide_wheel){
		if(0 < shadow.alpha){
			shadow.alpha -= 0.1;
		}
		if(0 < wheel.alpha){
			wheel.alpha -= 0.1;
			numeric_wheel.alpha -= 0.1;
			ball.alpha -= 0.1;
			ref_ball.alpha -= 0.1;
		}else{
			hide_wheel = false;	
		}
	}
	
	//wheel rotatio
	if(wheel.alpha > 0){
		numeric_wheel.angle -= 1;
	}
	
	if(spinning){
		Phaser.Actions.RotateAroundDistance(ball_grp.getChildren(), { x: numeric_wheel.x, y: numeric_wheel.y }, spin_angle, spin_distance);
	}
	
	if(winner_number !== null){		
		ref_ball.x = bucket_radius*Math.cos((numeric_wheel.angle + numbers_angles[winner_number])*Math.PI/180) + game_width/2;
		ref_ball.y = bucket_radius*Math.sin((numeric_wheel.angle + numbers_angles[winner_number])*Math.PI/180) + game_height/2;
	}
	
	//check if ready to stop ball
	if(ready_to_stop){
		
		/*if(ball.x > (ref_ball.x - (ref_ball.displayWidth/2)) && ball.x < (ref_ball.x + (ref_ball.displayWidth/2)) && ball.y > (ref_ball.y - (ref_ball.displayHeight/2)) && ball.y < (ref_ball.y + (ref_ball.displayHeight/2))){
			stop_spinning();
			ready_to_stop = false;
		}*/
		
		var distance = Phaser.Math.Distance.Between(ball.x, ball.y, ref_ball.x, ref_ball.y);
		if(distance < game_width*0.01){
			
			stop_spinning();
			ready_to_stop = false;
			
		}	
		
	}
	
	//move chips to table
	if(new_chips.length > 0){
		for(var i=0; i<new_chips.length; i++){
			var new_chip = this.physics.add.image(chips[new_chips[i].value].x, chips[new_chips[i].value].y, 'chip' + new_chips[i].value).setInteractive();
			new_chip.displayWidth = chips[new_chips[i].value].displayWidth;
			new_chip.displayHeight = chips[new_chips[i].value].displayHeight;
			new_chip.area = new_chips[i].area;
			
			new_chip.on('pointerup', function (pointer) {				
				if(shift_on){
					remove_area_bet(this.area);
				}else{
					place_bet(this.area);
				}
			});	
			
			this.physics.moveTo(new_chip,bet_areas[new_chips[i].area].x,bet_areas[new_chips[i].area].y,chip_speed);
			
			mooving_chips.push({chip:new_chip,area:new_chips[i].area});			
			new_chips.splice(i,1);
		}
	}
	
	if(mooving_chips.length > 0){
		for(var i=0; i<mooving_chips.length; i++){
			var distance = Phaser.Math.Distance.Between(mooving_chips[i].chip.x, mooving_chips[i].chip.y, bet_areas[mooving_chips[i].area].x, bet_areas[mooving_chips[i].area].y);
			if(distance < game_width*0.035){
				
				var distanser = area_bets_chip_count[mooving_chips[i].area] * (game_width*0.001);
				
				mooving_chips[i].chip.body.reset(bet_areas[mooving_chips[i].area].x, bet_areas[mooving_chips[i].area].y - distanser);				
				placed_chips.push(mooving_chips[i]);
				
				if(area_bets_chip_count[mooving_chips[i].area] > 0 && !area_bets_amount_text[mooving_chips[i].area]){ //placing first chip
					var txt_x = mooving_chips[i].chip.x - (mooving_chips[i].chip.displayWidth*0.1);;
					var txt_y = mooving_chips[i].chip.y + (mooving_chips[i].chip.displayHeight*0.40);
					area_bets_amount_text[mooving_chips[i].area] = this.add.text(txt_x, txt_y, currency_symbol+area_bets_amount[mooving_chips[i].area], machine_text_style);
				}else if(area_bets_amount_text[mooving_chips[i].area]){
					area_bets_amount_text[mooving_chips[i].area].text = currency_symbol+area_bets_amount[mooving_chips[i].area];
				}
				
				mooving_chips.splice(i,1);
			}			
		}
	}
	
	//move marker to number
	if(move_marker){
		var distanser = (area_bets_chip_count[winner_number] * (game_width*0.001)) + (marker.displayHeight/2.4);
		var xadj = 0;
		if(area_bets_chip_count[winner_number] == 0){
			xadj = game_width*0.00526;
		}
		this.physics.moveTo(marker,bet_areas[winner_number].x-xadj,bet_areas[winner_number].y - distanser,chip_speed);
		var distance = Phaser.Math.Distance.Between(marker.x-xadj, marker.y, bet_areas[winner_number].x, bet_areas[winner_number].y - distanser);
		if(distance < game_width*0.037){			
			marker.body.reset(bet_areas[winner_number].x-xadj, bet_areas[winner_number].y - distanser);	
			move_marker = false;			
		}
	}
	
	//move marker out
	if(move_marker_out){
		this.physics.moveTo(marker,game_width/14.94,game_height/3.09,chip_speed);
		var distance = Phaser.Math.Distance.Between(marker.x, marker.y, game_width/14.94, game_height/3.09);
		if(distance < game_width*0.037){			
			marker.body.reset(game_width/14.94, game_height/3.09);	
			move_marker_out = false;			
		}
	}
	
	//clear chips from table
	if(clear_chips_to != ""){
		if(clear_chips_to == "player"){
			for(var i=0; i<placed_chips.length; i++){
				placed_chips[i].chip.y += game_width*0.01;
				if(placed_chips[i].chip.y > game_height + (placed_chips[i].chip.displayHeight)*2){
					placed_chips.splice(i,1);
				}
			}
			if(placed_chips.length == 0){clear_chips_to = "";}
		}else if(clear_chips_to == "dealer"){
			for(var i=0; i<placed_chips.length; i++){
				if(winning_areas.indexOf(placed_chips[i].area) == -1){
					placed_chips[i].chip.y -= game_width*0.01;
					if(placed_chips[i].chip.y < (placed_chips[i].chip.displayHeight)*-2){
						placed_chips.splice(i,1);
					}
				}
			}
			if(placed_chips.length == 0){clear_chips_to = "";}
		}
	}
	
}

function update_balance(new_balance){
	current_balance = new_balance;
	$("#balance_field").html(number_format(current_balance));
}

function adjust_balance(amount){
	console.log(current_balance);
	console.log(amount);
	current_balance += amount;
	$("#balance_field").html(number_format(current_balance));
}

function get_bets_details(){
	var bets = new Array();
	var total_bet = 0;
	for(var i=0; i<bet_areas_data.length; i++){
		if(area_bets_amount[bet_areas_data[i].id] > 0){
			bets.push(bet_areas_data[i].id + "|" + area_bets_amount[bet_areas_data[i].id]);
			total_bet += area_bets_amount[bet_areas_data[i].id];
		}
	}
	return {bets:bets.join(","),total:total_bet};
}


function spin(){
	if(!spinning){
		
		var bet_detail = get_bets_details();
		
		winner_number = null;
		winning_amount = "";
		winning_color = "";
		winning_str_color = "";
		winning_areas = new Array();
		spin_angle = 0.06;
		spin_distance = wheel.displayWidth*0.43;
		ball_in_position = false;	
		ready_to_stop = false;		
		spinning = true;
		place_bets = false;
		$(".game_btn").hide(500);
		previous_bets = bet_detail.bets;
		
		//Provably fair
		if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
		
		$.getJSON(core_url + "action=spin&bets=" + bet_detail.bets + "&pnr="+player_number + "&oskr=" + Math.random()  ,function(data){
				
			if(!data.error){ //change the way to check error
				
				no_more_bets_sound.play();
				ball_spin_sound.play();
				//play_spinning_sound = true;	
				ball.alpha = 1;
				ref_ball.alpha = 0;
				
				winner_number = data.winning_number;
				winning_amount = data.win_amount;
				winning_color = data.winning_color;
				winning_str_color = data.winning_str_color;
				winning_areas = data.winning_areas.split(",");
				after_spin_balance = data.balance*1;
				show_wheel = true;
				after_spin = true;
				
				pfdata = data.pf;
				
				setTimeout("start_ball_droping()",2000);
				
			}else{
				alert("There was a problem: " + data.msg);
			}
		});	
		
	}
}

function slow_ball(){
	if(bucket_radius < spin_distance){spin_distance -= wheel.displayWidth*0.015;}
	if(spin_angle > 0.001){spin_angle -= 0.002;}
	ball_droping_sound.setVolume(ball_droping_sound.volume-0.05);
}

function start_ball_droping(){
	setTimeout("ball_droping_sound.play();",4000);
	//setTimeout("ball_final_sound.play();",5000);
	spin_interval = setInterval("slow_ball();",500);
	setInterval("jump_ball();",100);
	setTimeout("ready_to_stop = true;",6500);
}

function jump_ball(){
	if(ball_jumping){
		spin_distance += wheel.displayWidth*0.005;
		ball_jumping = false;
	}else{
		spin_distance -= wheel.displayWidth*0.005;
		ball_jumping = true;
	}
}

function stop_spinning(){
	ball_droping_sound.stop();	
	ball_droping_sound.setVolume(1);
	spinning = false;
	clearInterval(spin_interval);
	
	ball.alpha = 0;
	ref_ball.alpha = 1;
	
	setTimeout("play_winning_number_sound();",500)
	setTimeout("show_spin_result();",2000);
	setTimeout("update_pf_data()",2000);
}

function play_winning_number_sound(){
	var timer = 0;
	if(winner_number == "00"){
		nums_sounds[0].play();
		setTimeout("nums_sounds[0].play();",500);
		timer = 500;
	}else if(winner_number < 20){
		nums_sounds[winner_number].play();
	}else if(winner_number < 30){
		twenty_sound.play();
		var num_dif = winner_number - 20;
		if(num_dif > 0){
			setTimeout("nums_sounds["+num_dif+"].play();",500);
			timer = 500;
		}
	}else{
		thirty_sound.play();
		var num_dif = winner_number - 30;
		if(num_dif > 0){
			setTimeout("nums_sounds["+num_dif+"].play();",500);
			timer = 500;
		}
	}
	if(winning_str_color != ""){
		if(winning_str_color == "red"){
			setTimeout("red_sound.play();",timer+700);
		}else if(winning_str_color == "black"){
			setTimeout("black_sound.play();",timer+700);
		}
	}
}

function show_spin_result(){
	
	if(winning_amount > 0){
		win_sound.play();
	}
	
	move_marker = true;
	hide_wheel = true;
	
	update_balance(after_spin_balance);
	nhistory.push({color:winning_color,number:winner_number});
	draw_history();
	
	var msg = "<h1 style='color:#"+winning_color+"'>"+winner_number+"</h1>";	
	if(winning_amount > 0){
		msg += 'You win ' + currency_symbol + winning_amount;
	}
	
	clear_bets("dealer");
	show_message(msg);
	
	$('#btn_clear').slideDown(500);
	$('#btn_rebet').slideDown(500);
	$('#btn_rebet_spin').slideDown(500);
	
	//setTimeout("hide_message();",3000);
}

function draw_history(){
	history_black.text = "";
	history_red.text = "";
	history_green.text = "";
	
	if(nhistory.length > 19){ //Space can only handle 19 numbers in history
		nhistory.splice(0,1);	
	}
	
	for(var i=0;i<nhistory.length;i++){
		insert_in_history(nhistory[i].color, nhistory[i].number);
	}
}

function insert_in_history(color, number){
	//show only 19 numbers
	switch(color){ 
		case "000":
			history_black.text = number + "\n" + history_black.text;
			history_red.text = "\n" + history_red.text;
			history_green.text = "\n" + history_green.text;
		break;
		case "F00":
			history_black.text = "\n" + history_black.text;
			history_red.text = number + "\n" + history_red.text;
			history_green.text = "\n" + history_green.text;
		break;
		case "0F0":
			history_black.text = "\n" + history_black.text;
			history_red.text = "\n" + history_red.text;
			history_green.text = number + "\n" + history_green.text;
		break;
	}
}

function place_bet(area){
	var area_max_amount = Math.round(max_amount/bet_areas[area].divider);
	if(place_bets){
		if(current_bet+selected_chip <= max_amount){ 
			if(/*current_bet+*/selected_chip <= current_balance){//not include current bet because current balance already have current bet deducted
				if(area_bets_amount[area]+selected_chip <= area_max_amount){
				
					after_spin = false;
					$("#btn_clear").slideDown(500);
					if(current_bet+selected_chip >= min_amount){$("#btn_spin").slideDown(500);}
					current_bet += selected_chip;
					adjust_balance(selected_chip*-1);
					area_bets_chip_count[area] ++;
					area_bets_amount[area] += selected_chip;
					new_chips.push({value:selected_chip, area:area});
				
				}else{
					alert("The maximum bet on this field is " + currency_symbol + area_max_amount);
				}
			}else{
				fire_not_enough_balance("Not enough balance");
			}
		}else{
			alert("Spin Limit is "+max_amount);
		}
	}
}

function get_prize_chips_list(amount){
	var reversed_chips = chips_values.slice(0).reverse();
	//var amount = Math.floor(amount);
	var temp_amount = amount;
	var place_chips = new Array();
	
	for(var i=0;i<reversed_chips.length;i++){
		var current_chip = reversed_chips[i];
		var result = temp_amount / current_chip;
		if(result >= 1){
			var new_chips_count = Math.floor(result);
			for(var e=0;e<new_chips_count;e++){
				place_chips.push(current_chip);
			}
			temp_amount -= new_chips_count*current_chip;
		}
	}
	return place_chips;
}

function rebet(spin){
	var time = 0;
	if(placed_chips.length > 0){time = 1500;}
	clear_table("player");	
	setTimeout("place_rebet();",time);
	if(spin){setTimeout("spin();",time+1000);}
}

function place_rebet(){
	var bets = previous_bets.split(",");
	for(var i = 0; i < bets.length; i++){
		var bet_parts = bets[i].split("|");
		var pre_chips = get_prize_chips_list(bet_parts[1]);
		for(var e = 0; e<pre_chips.length; e++){
			change_chip(pre_chips[e]);
			place_bet(bet_parts[0]);
		}		
		
	}	
}

function clear_table(direction){
	hide_message();
	move_marker_out = true;
	hide_message();
	if(!after_spin){
		adjust_balance(current_bet*1);
	}
	current_bet = 0;
	clear_betareas_amounts();
	clear_bets(direction);
	$(".game_btn").hide(500);
	place_bets = true;
}

function clear_bets(direction){
	for(var i = 0; i < bet_areas_data.length; i++){
		if(area_bets_amount_text[bet_areas_data[i].id]){
			area_bets_amount_text[bet_areas_data[i].id].text = "";
		}
	}
	clear_chips_to = direction;
}

function clear_betareas_amounts(){
	for(var i = 0; i < bet_areas_data.length; i++){
		area_bets_chip_count[bet_areas_data[i].id] = 0;
		area_bets_amount[bet_areas_data[i].id] = 0;
	}
}

function remove_area_bet(area){
	for(var i=placed_chips.length-1; i>-1; i--){
		if(placed_chips[i].area == area){
			
			var cvalue = placed_chips[i].chip.texture.key.replace("chip","");

			
			if(current_bet-cvalue == 0){$("#btn_clear").hide(500);}
			if(current_bet-cvalue < min_amount){$("#btn_spin").hide(500);}
			current_bet -= cvalue;
			area_bets_chip_count[area] --;
			area_bets_amount[area] -= cvalue;
			adjust_balance(cvalue*1);
			
			if(area_bets_chip_count[area] > 0){
				area_bets_amount_text[area].text = currency_symbol + area_bets_amount[area];
			}else{
				area_bets_amount_text[area].text = "";
			}
						
			placed_chips[i].chip.destroy();
			placed_chips.splice(i,1);
			
			break;
		}
	}	
}

function change_chip(value){
	selected_chip = value;
	
	for(var i = 0; i < chips_values.length; i++){
		chips[chips_values[i]].alpha = unselected_chip_alpha;	
	}
	chips[value].alpha = 1;
}


function show_message(msg){
	$("#game_msg").html(msg);
	$("#game_msg").slideDown(700);
}


function add_message(msg){
	$("#game_msg").html($("#game_msg").html() + msg);
}


function scroll_down(){
	$('html, body').animate({scrollTop:$(document).height()}, 'slow');
}


function hide_message(){
	$(".game_msg").slideUp(700);
}

function mute(){
	if(is_mute){
		is_mute = false;
		$("#btn_mute").removeClass("blue");
	}else{
		is_mute = true;
		$("#btn_mute").addClass("blue");
	}
	set_mute = true;
}

function update_pf_data(){
	
	if(pfdata){
	
		$("#pf_player_num").val(getRandomInt(0,37));
		$("#pf_n_shash").val(pfdata.nxnr);
		$("#pf_l_shash").val(pfdata.lxhs);
		$("#pf_l_sec1").val(pfdata.lsc1);
		$("#pf_l_sec2").val(pfdata.lsc2);
		$("#pf_l_snum").val(pfdata.lxnr);
		$("#pf_l_pnum").val(pfdata.lpnr);
		$("#pf_l_resnum").val(pfdata.lpos);
	
	}
	
}

