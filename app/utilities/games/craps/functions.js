
//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/craps/action.php?gid='+gid+'&';

//general vars
var current_bet = 0;
var marker = null;
var rolling1 = false;
var rolling2 = false;
var start_rolling = false;
var place_bets = true;
var chips_values = [0.25,1,5,25,100];
var chips = new Array();
var new_chips = new Array();
var new_dealer_chips = new Array();
var mooving_chips = new Array();
var placed_chips = new Array();
var won_chips = new Array();
var bet_areas = new Array();
var bet_areas_data = new Array();
var markers_areas_data = new Array();
var markers_areas = new Array();
var winning_area = null;
var selected_chip = 1;
var unselected_chip_alpha = 0.4;
var chip_speed = 1000;
var test_text = null;
var description_text = null;
var area_bets_chip_count = new Array();
var area_bets_amount = new Array();
var area_bets_amount_text = new Array();
var clear_chips_to = "";
var clear_chips_to_dealer = false;
var clear_chips_to_player = false;
var winning_amount = 0;
var winning_areas = new Array();
var removing_areas = new Array();
var moving_areas = new Array();
var move_marker = false;
var move_marker_out = false;
var shift_on = false;
var is_mute = false;
var set_mute = false;
var dice_animation = null;
var dice_positions = [35,39,111,6,47,43];
var dice1 = null;
var dice1_phase = "start";
var dice2 = null;
var dice2_phase = "start";
var dice1_value = 0;
var dice2_value = 0;
var table_status = "come_out";
var new_balance = 0;
var dice1_start_x = 0;
var dice2_start_y = 0;
var dice1_start_x = 0;
var dice2_start_y = 0;
var come_bet_areas = ["come1","come2"];
var dont_come_bet_areas = ["dont_come1","dont_come2"];
var pass_bet_areas = ["pass1","pass2","pass3","pass4"];
var pass_odds_bet_areas = ["pass_odds1","pass_odds2"];
var dont_pass_odds_bet_areas = ["dont_pass_odds1","dont_pass_odds2"];
var dont_pass_bet_areas = ["dont_pass1","dont_pass2","dont_pass3","dont_pass4"];
var dont_come_numbers_bet_areas = ["dont_come_four","dont_come_five","dont_come_six","dont_come_eight","dont_come_nine","dont_come_ten"];
var come_numbers_bet_areas = ["come_four","come_five","come_six","come_eight","come_nine","come_ten"];
var won_chips_count = 0;
var won_chips_text = null;
var moving_from_dealer_to_player = false;
var point = 0;
var sounds = new Array();
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
	this.load.image('background', '/utilities/games/craps/imgs/back.jpg?v6');
	this.load.image('chip0.25', '/utilities/images/games/chips/chip0.25.png');
	this.load.image('chip1', '/utilities/images/games/chips/chip1.png');
	this.load.image('chip5', '/utilities/images/games/chips/chip5.png');	
	this.load.image('chip25', '/utilities/images/games/chips/chip25.png');
	this.load.image('chip100', '/utilities/images/games/chips/chip100.png');
	this.load.image('chip500', '/utilities/images/games/chips/chip500.png');
	this.load.image('marker', '/utilities/games/craps/imgs/marker_off.png');
	this.load.image('marker_on', '/utilities/games/craps/imgs/marker_on.png');
	this.load.spritesheet('dice', '/utilities/games/craps/imgs/dice_sheet.png?v8', { frameWidth: 70, frameHeight: 70 });
	this.load.image('area', '/utilities/games/craps/imgs/blank_area.png');
	//this.load.image('area', '/utilities/games/craps/imgs/area.jpg');
	
	
	//Sounds
	this.load.audio('dice1', '/utilities/games/craps/sounds/dice.mp3');
	this.load.audio('dice2', '/utilities/games/craps/sounds/dicelow.mp3');
	this.load.audio('dice3', '/utilities/games/craps/sounds/dicelow2.mp3');
	this.load.audio('craps', '/utilities/games/craps/sounds/f_Craps.mp3');
	this.load.audio('comming_out', '/utilities/games/craps/sounds/f_ComingOut.mp3');
	this.load.audio('craps_out', '/utilities/games/craps/sounds/f_CrapsOut.mp3');
	this.load.audio('easy10', '/utilities/games/craps/sounds/f_Easy10.mp3');
	this.load.audio('easy4', '/utilities/games/craps/sounds/f_Easy4.mp3');
	this.load.audio('easy6', '/utilities/games/craps/sounds/f_Easy6.mp3');
	this.load.audio('easy8', '/utilities/games/craps/sounds/f_Easy8.mp3');
	this.load.audio('hard10', '/utilities/games/craps/sounds/f_Hard10.mp3');
	this.load.audio('hard4', '/utilities/games/craps/sounds/f_Hard4.mp3');
	this.load.audio('hard6', '/utilities/games/craps/sounds/f_Hard6.mp3');
	this.load.audio('hard8', '/utilities/games/craps/sounds/f_Hard8.mp3');
	this.load.audio('point_is', '/utilities/games/craps/sounds/f_PointIs.mp3');
	this.load.audio('seven_out', '/utilities/games/craps/sounds/f_SevenOut.mp3');
	this.load.audio('winner', '/utilities/games/craps/sounds/f_Winner.mp3');
	this.load.audio('yoleven', '/utilities/games/craps/sounds/f_YoLeven.mp3');
	this.load.audio('roll_is', '/utilities/games/craps/sounds/f_TheRollIs.mp3');
	this.load.audio('12', '/utilities/games/craps/sounds/f_12.mp3');
	this.load.audio('2', '/utilities/games/craps/sounds/f_2.mp3');
	this.load.audio('5', '/utilities/games/craps/sounds/f_5.mp3');
	this.load.audio('9', '/utilities/games/craps/sounds/f_9.mp3');
	this.load.audio('3', '/utilities/games/craps/sounds/f_3.mp3');
	
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
	sounds["dice1"] = this.sound.add("dice1", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	sounds["dice2"] = this.sound.add("dice2", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	sounds["dice3"] = this.sound.add("dice3", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	sounds["comming_out"] = this.sound.add("comming_out", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["craps"] = this.sound.add("craps", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["craps_out"] = this.sound.add("craps_out", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["easy10"] = this.sound.add("easy10", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["easy4"] = this.sound.add("easy4", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["easy6"] = this.sound.add("easy6", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["easy8"] = this.sound.add("easy8", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["hard10"] = this.sound.add("hard10", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["hard4"] = this.sound.add("hard4", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["hard6"] = this.sound.add("hard6", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["hard8"] = this.sound.add("hard8", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["point_is"] = this.sound.add("point_is", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["seven_out"] = this.sound.add("seven_out", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["winner"] = this.sound.add("winner", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["yoleven"] = this.sound.add("yoleven", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["roll_is"] = this.sound.add("roll_is", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["12"] = this.sound.add("12", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["2"] = this.sound.add("2", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["5"] = this.sound.add("5", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["9"] = this.sound.add("9", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	sounds["3"] = this.sound.add("3", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	
	//Set background
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	//Buttons
	$("#btn_box").css("top",(game_height - (game_height*0.06)) + "px");	
	
	//text settings
	machine_text_style = { font: (game_width*0.01) + "px Arial", fill: "#ffffff", align: "center" };
	
	//chips
	var chip_separator = game.config.width*0.043;
	for(var i = 0; i < chips_values.length; i++){
		chips[chips_values[i]] = this.add.image(game_width/2+(chip_separator*i), game_height/1.15, 'chip'+chips_values[i]).setInteractive();
		chips[chips_values[i]].displayWidth=game.config.width*0.033; 
		chips[chips_values[i]].scaleY=chips[chips_values[i]].scaleX;
		chips[chips_values[i]].on('pointerup', function (pointer) {	
			var cvalue = this.texture.key.replace("chip",""); // get the chip value based on the texture name (ex: chip1)
			change_chip(cvalue*1, false);
		});		
	}	
	change_chip(1);
	
	//bet areas
	bet_areas_data.push({id: "pass1", xpos: game_width/9.49, ypos: game_height/3.46, w: game.config.width*0.03, h:game.config.width*0.2, angle:9, desc:"Pass Line (1:1)"});	
	bet_areas_data.push({id: "dont_pass1", xpos: game_width/7, ypos: game_height/3.66, w: game.config.width*0.035, h:game.config.width*0.18, angle:9, desc:"Don't Pass Line (1:1)"});	
	bet_areas_data.push({id: "dont_come1", xpos: game_width/5.16, ypos: game_height/5, w: game.config.width*0.045, h:game.config.width*0.12, angle:9, desc:"Don't Come (1:1)"});
	bet_areas_data.push({id: "come1", xpos: game_width/3.6, ypos: game_height/2.8, w: game.config.width*0.2, h:game.config.width*0.05, angle:0, desc:"Come (1:1)"});
	bet_areas_data.push({id: "field1", xpos: game_width/3.80, ypos: game_height/2.1, w: game.config.width*0.23, h:game.config.width*0.08, angle:0, desc:"Field (2:1)"});
	bet_areas_data.push({id: "dont_pass2", xpos: game_width/3.7, ypos: game_height/1.73, w: game.config.width*0.2, h:game.config.width*0.045, angle:0, desc:"Don't Pass Line (1:1)"});
	bet_areas_data.push({id: "pass2", xpos: game_width/3.7, ypos: game_height/1.51, w: game.config.width*0.2, h:game.config.width*0.045, angle:0, desc:"Pass Line (1:1)"});
	bet_areas_data.push({id: "pass_odds1", xpos: game_width/3.7, ypos: game_height/1.39, w: game.config.width*0.2, h:game.config.width*0.022, angle:0, desc:"Pass Line Odds"});
	bet_areas_data.push({id: "dont_pass_odds1", xpos: game_width/3.14, ypos: game_height/1.73, w: game.config.width*0.03, h:game.config.width*0.03, angle:0, desc:"Don't Pass Line Odds"});
	
	bet_areas_data.push({id: "pass3", xpos: game_width/1.195, ypos: game_height/3.46, w: game.config.width*0.03, h:game.config.width*0.2, angle:-9, desc:"Pass Line (1:1)"});	
	bet_areas_data.push({id: "dont_pass3", xpos: game_width/1.25, ypos: game_height/3.66, w: game.config.width*0.035, h:game.config.width*0.18, angle:-9, desc:"Don't Pass Line (1:1)"});	
	bet_areas_data.push({id: "dont_come2", xpos: game_width/1.33, ypos: game_height/5, w: game.config.width*0.045, h:game.config.width*0.12, angle:-9, desc:"Don't Come (1:1)"});
	bet_areas_data.push({id: "come2", xpos: game_width/1.42, ypos: game_height/2.8, w: game.config.width*0.2, h:game.config.width*0.05, angle:0, desc:"Come (1:1)"});
	bet_areas_data.push({id: "field2", xpos: game_width/1.37, ypos: game_height/2.1, w: game.config.width*0.23, h:game.config.width*0.08, angle:0, desc:"Field (2:1)"});
	bet_areas_data.push({id: "dont_pass4", xpos: game_width/1.4, ypos: game_height/1.73, w: game.config.width*0.2, h:game.config.width*0.045, angle:0, desc:"Don't Pass Line (1:1)"});
	bet_areas_data.push({id: "pass4", xpos: game_width/1.4, ypos: game_height/1.51, w: game.config.width*0.2, h:game.config.width*0.045, angle:0, desc:"Pass Line (1:1)"});	
	bet_areas_data.push({id: "pass_odds2", xpos: game_width/1.4, ypos: game_height/1.39, w: game.config.width*0.2, h:game.config.width*0.022, angle:0, desc:"Pass Line Odds"});	
	bet_areas_data.push({id: "dont_pass_odds2", xpos: game_width/1.32, ypos: game_height/1.73, w: game.config.width*0.03, h:game.config.width*0.03, angle:0, desc:"Don't Pass Line Odds"});
	
	bet_areas_data.push({id: "seven", xpos: game_width/2.06, ypos: game_height/3.05, w: game.config.width*0.13, h:game.config.width*0.035, angle:0, desc:"Seven (4:1)"});
	bet_areas_data.push({id: "any_craps", xpos: game_width/2.06, ypos: game_height/1.39, w: game.config.width*0.15, h:game.config.width*0.035, angle:0, desc:"Any Craps (7:1)"});	
	bet_areas_data.push({id: "hard_six", xpos: game_width/2.255, ypos: game_height/2.53, w: game.config.width*0.065, h:game.config.width*0.04, angle:0, desc:"Hard 6 (9:1)"});	
	bet_areas_data.push({id: "hard_ten", xpos: game_width/1.95, ypos: game_height/2.53, w: game.config.width*0.065, h:game.config.width*0.04, angle:0, desc:"Hard 10 (7:1)"});
	bet_areas_data.push({id: "hard_eight", xpos: game_width/2.255, ypos: game_height/2.09, w: game.config.width*0.065, h:game.config.width*0.04, angle:0, desc:"Hard 8 (9:1)"});	
	bet_areas_data.push({id: "hard_four", xpos: game_width/1.95, ypos: game_height/2.09, w: game.config.width*0.065, h:game.config.width*0.04, angle:0, desc:"Hard 4 (7:1)"});
	bet_areas_data.push({id: "two", xpos: game_width/2.255, ypos: game_height/1.78, w: game.config.width*0.065, h:game.config.width*0.045, angle:0, desc:"Horn 2 (30:1)"});	
	bet_areas_data.push({id: "twelve", xpos: game_width/1.95, ypos: game_height/1.78, w: game.config.width*0.065, h:game.config.width*0.045, angle:0, desc:"Horn 12 (10:1)"});
	bet_areas_data.push({id: "three", xpos: game_width/2.255, ypos: game_height/1.54, w: game.config.width*0.065, h:game.config.width*0.045, angle:0, desc:"Horn 3 (15:1)"});	
	bet_areas_data.push({id: "eleven", xpos: game_width/1.95, ypos: game_height/1.54, w: game.config.width*0.065, h:game.config.width*0.045, angle:0, desc:"Horn 11 (15:1)"});
	
	bet_areas_data.push({id: "win_four", xpos: game_width/3.85, ypos: game_height/3.6, w: game.config.width*0.08, h:game.config.width*0.022, angle:0, desc:"Place Win 4 (9:5)"});
	bet_areas_data.push({id: "win_five", xpos: game_width/2.87, ypos: game_height/3.6, w: game.config.width*0.08, h:game.config.width*0.022, angle:0, desc:"Place Win 5 (7:5)"});	
	bet_areas_data.push({id: "win_six", xpos: game_width/2.3, ypos: game_height/3.6, w: game.config.width*0.08, h:game.config.width*0.022, angle:0, desc:"Place Win 6 (7:6)"});
	bet_areas_data.push({id: "win_eight", xpos: game_width/1.92, ypos: game_height/3.6, w: game.config.width*0.08, h:game.config.width*0.022, angle:0, desc:"Place Win 8 (7:6)"});
	bet_areas_data.push({id: "win_nine", xpos: game_width/1.64, ypos: game_height/3.6, w: game.config.width*0.08, h:game.config.width*0.022, angle:0, desc:"Place Win 9 (7:5)"});
	bet_areas_data.push({id: "win_ten", xpos: game_width/1.44, ypos: game_height/3.6, w: game.config.width*0.08, h:game.config.width*0.022, angle:0, desc:"Place Win 10 (9:5)"});
	
	bet_areas_data.push({id: "buy_four", xpos: game_width/3.85, ypos: game_height/4.3, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Buy 4 (2:1 -5%vig)"});
	bet_areas_data.push({id: "buy_five", xpos: game_width/2.87, ypos: game_height/4.3, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Buy 5 (3:2 -5%vig)"});	
	bet_areas_data.push({id: "buy_six", xpos: game_width/2.3, ypos: game_height/4.3, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Buy 6 (6:5 -5%vig)"});
	bet_areas_data.push({id: "buy_eight", xpos: game_width/1.92, ypos: game_height/4.3, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Buy 8 (6:5 -5%vig)"});
	bet_areas_data.push({id: "buy_nine", xpos: game_width/1.64, ypos: game_height/4.3, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Buy 9 (3:2 -5%vig)"});
	bet_areas_data.push({id: "buy_ten", xpos: game_width/1.44, ypos: game_height/4.3, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Buy 10 (2:1 -5%vig)"});
	
	bet_areas_data.push({id: "come_four", xpos: game_width/4.2, ypos: game_height/5.6, w: game.config.width*0.04, h:game.config.width*0.025, angle:0, desc:"Come 4 (1:1)"});
	bet_areas_data.push({id: "come_five", xpos: game_width/3.08, ypos: game_height/5.6, w: game.config.width*0.04, h:game.config.width*0.025, angle:0, desc:"Come 5 (1:1)"});	
	bet_areas_data.push({id: "come_six", xpos: game_width/2.44, ypos: game_height/5.6, w: game.config.width*0.04, h:game.config.width*0.025, angle:0, desc:"Come 6 (1:1)"});
	bet_areas_data.push({id: "come_eight", xpos: game_width/2.02, ypos: game_height/5.6, w: game.config.width*0.04, h:game.config.width*0.025, angle:0, desc:"Come 8 (1:1)"});
	bet_areas_data.push({id: "come_nine", xpos: game_width/1.72, ypos: game_height/5.6, w: game.config.width*0.04, h:game.config.width*0.025, angle:0, desc:"Come 9 (1:1)"});
	bet_areas_data.push({id: "come_ten", xpos: game_width/1.50, ypos: game_height/5.6, w: game.config.width*0.04, h:game.config.width*0.025, angle:0, desc:"Come 10 (1:1)"});
	
	bet_areas_data.push({id: "lose_four", xpos: game_width/3.75, ypos: game_height/7.5, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Place Lose 4 (5:11)"});
	bet_areas_data.push({id: "lose_five", xpos: game_width/2.87, ypos: game_height/7.5, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Place Lose 5 (5:8)"});	
	bet_areas_data.push({id: "lose_six", xpos: game_width/2.3, ypos: game_height/7.5, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Place Lose 6 (4:5)"});
	bet_areas_data.push({id: "lose_eight", xpos: game_width/1.92, ypos: game_height/7.5, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Place Lose 8 (4:5)"});
	bet_areas_data.push({id: "lose_nine", xpos: game_width/1.66, ypos: game_height/7.5, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Place Lose 9 (5:8)"});
	bet_areas_data.push({id: "lose_ten", xpos: game_width/1.46, ypos: game_height/7.5, w: game.config.width*0.08, h:game.config.width*0.020, angle:0, desc:"Place Lose 10 (5:11)"});
	
	bet_areas_data.push({id: "lay_four", xpos: game_width/3.54, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Lay 4 (1:2 -5%vig)"});
	bet_areas_data.push({id: "lay_five", xpos: game_width/2.73, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Lay 5 (2:3 -5%vig)"});	
	bet_areas_data.push({id: "lay_six", xpos: game_width/2.22, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Lay 6 (5:6 -5%vig)"});
	bet_areas_data.push({id: "lay_eight", xpos: game_width/1.86, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Lay 8 (5:6 -5%vig)"});
	bet_areas_data.push({id: "lay_nine", xpos: game_width/1.61, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Lay 9 (2:3 -5%vig)"});
	bet_areas_data.push({id: "lay_ten", xpos: game_width/1.43, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Lay 10 (1:2 -5%vig)"});
	
	bet_areas_data.push({id: "dont_come_four", xpos: game_width/4.15, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Don't Come 4 (1:1)"});
	bet_areas_data.push({id: "dont_come_five", xpos: game_width/3.08, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Don't Come 5 (1:1)"});	
	bet_areas_data.push({id: "dont_come_six", xpos: game_width/2.44, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Don't Come 6 (1:1)"});
	bet_areas_data.push({id: "dont_come_eight", xpos: game_width/2.02, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Don't Come 8 (1:1)"});
	bet_areas_data.push({id: "dont_come_nine", xpos: game_width/1.73, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Don't Come 9 (1:1)"});
	bet_areas_data.push({id: "dont_come_ten", xpos: game_width/1.52, ypos: game_height/10.7, w: game.config.width*0.04, h:game.config.width*0.020, angle:0, desc:"Don't Come 10 (1:1)"});
	
	
	for(var i = 0; i < bet_areas_data.length; i++){
		bet_areas[bet_areas_data[i].id] = this.add.image(bet_areas_data[i].xpos, bet_areas_data[i].ypos, 'area').setInteractive();
		bet_areas[bet_areas_data[i].id].name = bet_areas_data[i].id;
		bet_areas[bet_areas_data[i].id].description = bet_areas_data[i].desc;
		bet_areas[bet_areas_data[i].id].displayWidth=bet_areas_data[i].w; 
		bet_areas[bet_areas_data[i].id].displayHeight=bet_areas_data[i].h; 
		bet_areas[bet_areas_data[i].id].alpha = 0.5;
		bet_areas[bet_areas_data[i].id].angle = bet_areas_data[i].angle;
		bet_areas[bet_areas_data[i].id].on('pointerup', function (pointer) { place_bet(this.name); });
		bet_areas[bet_areas_data[i].id].on('pointerover', function (pointer) { descrive_bet(this.description); });	
		
		area_bets_amount_text[bet_areas_data[i].id] = this.add.text(-500, -500, "", machine_text_style); 
			
	}
	
	clear_betareas_amounts();
	
	//Markers areas
	markers_areas_data.push({id: "point_4", xpos: game_width/3.75, ypos: game_height/19, w: game.config.width*0.08, h:game.config.width*0.020, angle:0});
	markers_areas_data.push({id: "point_5", xpos: game_width/2.87, ypos: game_height/19, w: game.config.width*0.08, h:game.config.width*0.020, angle:0});	
	markers_areas_data.push({id: "point_6", xpos: game_width/2.3, ypos: game_height/19, w: game.config.width*0.08, h:game.config.width*0.020, angle:0});
	markers_areas_data.push({id: "point_8", xpos: game_width/1.92, ypos: game_height/19, w: game.config.width*0.08, h:game.config.width*0.020, angle:0});
	markers_areas_data.push({id: "point_9", xpos: game_width/1.66, ypos: game_height/19, w: game.config.width*0.08, h:game.config.width*0.020, angle:0});
	markers_areas_data.push({id: "point_10", xpos: game_width/1.46, ypos: game_height/19, w: game.config.width*0.08, h:game.config.width*0.020, angle:0});
	markers_areas_data.push({id: "point_0", xpos: game_width/14.39, ypos: game_height/3.14, w: game.config.width*0.035, h:game.config.width*0.035, angle:0});
	
	for(var i = 0; i < markers_areas_data.length; i++){
		markers_areas[markers_areas_data[i].id] = this.add.image(markers_areas_data[i].xpos, markers_areas_data[i].ypos, 'area');
		markers_areas[markers_areas_data[i].id].name = markers_areas_data[i].id;
		markers_areas[markers_areas_data[i].id].displayWidth=markers_areas_data[i].w; 
		markers_areas[markers_areas_data[i].id].displayHeight=markers_areas_data[i].h; 
		markers_areas[markers_areas_data[i].id].alpha = 0.5;
		markers_areas[markers_areas_data[i].id].angle = markers_areas_data[i].angle;		
	}
	
	
	//Winning area
	winning_area = this.add.image(game_width/3.61, game_height/1.14, 'area').setInteractive();
	winning_area.name = "winning_area";
	winning_area.displayWidth = game.config.width*0.065; 
	winning_area.displayHeight = game.config.width*0.04; 
	
	//Dice
	var dice_config = {
        key: 'roll',
        frames: this.anims.generateFrameNumbers('dice'),
        frameRate: 40,
        yoyo: false,
        repeat: -1
    };
	
	dice1_start_x = game.config.width*1.2;
	dice1_start_y = game.config.width*0.23;
	dice2_start_x = game.config.width*1.3;
	dice2_start_y = game.config.width*0.27;
	
	dice_animation = this.anims.create(dice_config);
	dice1 = this.add.sprite(dice1_start_x, dice1_start_y, 'dice');	
	dice1.displayWidth = game.config.width*0.036842;
	dice1.displayHeight = dice1.displayWidth;
	dice1.depth = 1000000;
	dice1.anims.load('roll');
	dice1.anims.play();
	dice1.anims.pause();
	
	dice2 = this.add.sprite(dice2_start_x, dice2_start_y, 'dice');	
	dice2.displayWidth = game.config.width*0.036842;
	dice2.displayHeight = dice2.displayWidth;
	dice2.depth = 1000000;
	dice2.anims.load('roll');
	dice2.anims.play();
	dice2.anims.pause();
	
	
	//Marker
	marker = this.physics.add.image(game_width/14.94, game_height/3.09, 'marker');
	marker.displayWidth=game.config.width*0.04; 
	marker.scaleY=marker.scaleX;
	marker.depth = 999999;
	
	
	//Texts
	test_text = this.add.text(game_width/2.84, game_height/1.341, "X: Y:", machine_text_style);
	description_text = this.add.text(game_width/41.3, game_height/1.35, "Over bet area to see details", machine_text_style);
	test_text.alpha = 0;
	
	//winning text
	won_chips_text = this.add.text(game_width/3.73, game_height/1.11, "", machine_text_style);
	
	this.input.keyboard.on('keydown_SHIFT', function(){shift_on = true;}, this);
	this.input.keyboard.on('keyup_SHIFT', function(){shift_on = false;}, this);
	
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	game_start();
	
	
}

function update (){
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//rolling
	
	if(start_rolling){
		start_rolling = false;
		dice1.anims.resume();
		dice2.anims.resume();
	}
	
	if(rolling1){
		switch(dice1_phase){ 
			case "start":
				dice1.x -= game_width*0.021;
				dice1.y += game_width*0.0031;
				if(dice1.y > game_height/1.6){
					dice1_phase = "jump";
				}
			break;
			case "jump":
				dice1.x -= game_width*0.017;
				dice1.y -= game_width*0.0029;
				if(dice1.x < 100){
					dice1_phase = "rebound";
				}
			break;
			case "rebound":
				dice1.x += game_width*0.012;
				dice1.y -= game_width*0.0021;
				var stop_line = Math.floor(Math.random() * 3) * (Math.round(Math.random()) * 2 - 1)/10;
				if(dice1.x > game_width/(2.5+stop_line)){
					dice1_phase = "stop";
				}
			break;
			case "stop":
				rolling1 = false;
				var stop_position = dice_positions[dice1_value-1];
				dice1.anims.pause(dice1.anims.currentAnim.frames[stop_position-1]);
				if(!rolling2){
					setTimeout('clear_chips_to_dealer = true; clear_chips_to_player = true;  move_bets();',500);
					moving_from_dealer_to_player = true;
					move_marker = true;
					mute_dices_roll();
				}
			break;
		}
	
	}
	
	if(rolling2){
	
		switch(dice2_phase){ 
			case "start":
				dice2.x -= game_width*0.021;
				dice2.y += game_width*0.0031;
				if(dice2.y > game_height/1.5){
					dice2_phase = "jump";
				}
			break;
			case "jump":
				dice2.x -= game_width*0.017;
				dice2.y -= game_width*0.0029;
				if(dice2.x < 100){
					dice2_phase = "rebound";
				}
			break;
			case "rebound":
				dice2.x += game_width*0.012;
				dice2.y -= game_width*0.0011;
				var stop_line = Math.floor(Math.random() * 3) * (Math.round(Math.random()) * 2 - 1)/10;
				if(dice2.x > game_width/(3+stop_line)){
					dice2_phase = "stop";
				}
			break;
			case "stop":
				rolling2 = false;
				var stop_position = dice_positions[dice2_value-1];
				dice2.anims.pause(dice2.anims.currentAnim.frames[stop_position-1]);
				if(!rolling1){
					setTimeout('clear_chips_to_dealer = true; clear_chips_to_player = true;  move_bets();',500);
					moving_from_dealer_to_player = true;
					move_marker = true;
					mute_dices_roll();
				}
			break;
		}
	
	}
	
	//move chips from dealer to player
	if(moving_from_dealer_to_player){
		var won_amounts = get_prize_chips_list(winning_amount);
		//new_dealer_chips
		
		for(var i=0; i<won_amounts.length; i++){
		
			var new_chip = this.physics.add.image(game_width/2, game_height*-0.05, 'chip' + won_amounts[i]);
			new_chip.displayWidth = chips[won_amounts[i]].displayWidth;
			new_chip.displayHeight = chips[won_amounts[i]].displayHeight;	
			
			this.physics.moveTo(new_chip,winning_area.x,winning_area.y,chip_speed);						
			new_dealer_chips.push({chip:new_chip});
			
		}
		
		moving_from_dealer_to_player = false;
	}
	
	if(new_dealer_chips.length > 0){
		
		for(var i=0; i<new_dealer_chips.length; i++){
			var distance = Phaser.Math.Distance.Between(new_dealer_chips[i].chip.x, new_dealer_chips[i].chip.y, winning_area.x, winning_area.y);				
			if(distance < game_width*0.037){				
				var distanser = won_chips_count * (game_width*0.001);				
				new_dealer_chips[i].chip.body.reset(winning_area.x, winning_area.y-distanser);	
				won_chips_count++;	
				won_chips.push(new_dealer_chips[i]);
				new_dealer_chips.splice(i,1);			
			}
		}
	}
	
		
		
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;

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
				
				/*if(area_bets_chip_count[mooving_chips[i].area] > 0 && !area_bets_amount_text[mooving_chips[i].area]){ //placing first chip
					var txt_x = mooving_chips[i].chip.x - (mooving_chips[i].chip.displayWidth*0.1);;
					var txt_y = mooving_chips[i].chip.y + (mooving_chips[i].chip.displayHeight*0.40);
					area_bets_amount_text[mooving_chips[i].area] = this.add.text(txt_x, txt_y, currency_symbol+area_bets_amount[mooving_chips[i].area], machine_text_style);
				}else */if(area_bets_amount_text[mooving_chips[i].area]){ 
					if(area_bets_amount_text[mooving_chips[i].area].x < 0){ //move to position first time
						var txt_x = mooving_chips[i].chip.x - (mooving_chips[i].chip.displayWidth*0.1);;
						var txt_y = mooving_chips[i].chip.y + (mooving_chips[i].chip.displayHeight*0.40);
						area_bets_amount_text[mooving_chips[i].area].x = txt_x;
						area_bets_amount_text[mooving_chips[i].area].y = txt_y;
					}
					area_bets_amount_text[mooving_chips[i].area].text = currency_symbol+area_bets_amount[mooving_chips[i].area];
				}
				
				mooving_chips.splice(i,1);
			}			
		}
	}
	
	//move marker to point
	if(move_marker){
		this.physics.moveTo(marker,markers_areas["point_"+point].x,markers_areas["point_"+point].y,chip_speed);
		var distance = Phaser.Math.Distance.Between(marker.x, marker.y, markers_areas["point_"+point].x, markers_areas["point_"+point].y);
		if(distance < game_width*0.037){			
			marker.body.reset(markers_areas["point_"+point].x, markers_areas["point_"+point].y);
			if(point > 0){
				marker.setTexture("marker_on");
			}else{
				marker.setTexture("marker");
			}
			
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
		}
	}
	
	
	
	//move chips to player
	if(clear_chips_to_player){
		
		for(var i=0; i<placed_chips.length; i++){
			if(winning_areas.indexOf(placed_chips[i].area) != -1){
				area_bets_amount_text[placed_chips[i].area].text = "";
				this.physics.moveTo(placed_chips[i].chip,winning_area.x,winning_area.y,chip_speed);
				var distance = Phaser.Math.Distance.Between(placed_chips[i].chip.x, placed_chips[i].chip.y, winning_area.x, winning_area.y);
				
				if(distance < game_width*0.037){
					
					var distanser = won_chips_count * (game_width*0.001);
					
					placed_chips[i].chip.body.reset(winning_area.x, winning_area.y-distanser);	
					area_bets_chip_count[placed_chips[i].area]--;
					won_chips_count++;
					
					if(area_bets_chip_count[placed_chips[i].area] == 0){
						clear_betarea(placed_chips[i].area);
						winning_areas.splice(winning_areas.indexOf(placed_chips[i].area),1);
					}
					won_chips.push(placed_chips[i]);
					placed_chips.splice(i,1);
					
				}
				
			}
		}
		if(winning_areas.length == 0){
			clear_chips_to_player = false; 
			if(winning_amount > 0){won_chips_text.text = "$"+winning_amount;}
		}
		
	}
	
	//Remove chips to dealer
	if(clear_chips_to_dealer){
		for(var i=0; i<placed_chips.length; i++){
			if(removing_areas.indexOf(placed_chips[i].area) != -1){
				area_bets_amount_text[placed_chips[i].area].text = "";
				placed_chips[i].chip.y -= game_width*0.01;
				if(placed_chips[i].chip.y < (placed_chips[i].chip.displayHeight)*-2){
					
					area_bets_chip_count[placed_chips[i].area]--;
					
					if(area_bets_chip_count[placed_chips[i].area] == 0){
						clear_betarea(placed_chips[i].area);
						removing_areas.splice(removing_areas.indexOf(placed_chips[i].area),1);
					}
					
					placed_chips.splice(i,1);
				}
			}
		}
		if(removing_areas.length == 0){clear_chips_to_dealer = false;}
	}
	
	
}

function update_balance(new_balance){
	current_balance = new_balance;
	$("#balance_field").html(number_format(current_balance));
}

function adjust_balance(amount){
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

function has_pass_dont_pass_bet(){
	var has = false;
	for(var i=0; i<bet_areas_data.length; i++){
		if(area_bets_amount[bet_areas_data[i].id] > 0){
			if(pass_bet_areas.indexOf(bet_areas_data[i].id) != -1 || dont_pass_bet_areas.indexOf(bet_areas_data[i].id) != -1){
				has = true;
				break;
			}
		}
	}
	return has;
}

function reset_dices(){
	dice1_phase = "start";
	dice2_phase = "start";
	dice1.x = dice1_start_x;
	dice1.y = dice1_start_y;
	dice2.x = dice2_start_x;
	dice2.y = dice2_start_y;
}

function show_hide_btns(show){
	if(show){
		$("#btn_spin").slideDown(500);
		$("#btn_clear").slideDown(500);
	}else{
		$("#btn_spin").slideUp(500);
		$("#btn_clear").slideUp(500);
	}
}


function game_start(){
	$.getJSON(core_url + "action=start",function(data){

		if(!data.error){
			if(data.has_started_game){
				var total_bet = data.total_bet*1;
				point = data.point;
				move_marker = true;
				current_bet = total_bet;
				adjust_balance(total_bet);
				//move marker to point
				table_status = data.game_status;
				var bets_list = data.bets.split(",");
				
				for(var i=0; i<bets_list.length; i++){
					var bparts = bets_list[i].split("|");					
					var vchips = get_prize_chips_list(bparts[1]);
					for(var e=0; e<vchips.length; e++){
						auto_place_bet(vchips[e], bparts[0]);
					}
				}
				$("#btn_spin").slideDown(500);				
				
			}else{
				
				//place come, dont come bets pending from last session
				var bets_list = data.bets.split(",");
				for(var i=0; i<bets_list.length; i++){
					var bparts = bets_list[i].split("|");					
					var vchips = get_prize_chips_list(bparts[1]);
					for(var e=0; e<vchips.length; e++){
						place_blind_bet(vchips[e], bparts[0]);
					}
				}				
					
			}
			
			
		}else{
			alert("There was a problem: " + data.msg);
		}
	});
}

function play_roll_number(){
	var total = dice1_value+dice2_value;
	console.log(total);
	var sound_str = "";
	if(total == 2 || total == 3 || total == 5 || total == 9 || total == 12){
		sound_str = ""+total;
	}else if(total == 7){
		sound_str = "seven_out";
	}else if(total == 4){
		if(dice1_value == 2){
			sound_str = "hard4";
		}else{
			sound_str = "easy4";
		}
	}else if(total == 6){
		if(dice1_value == 3){
			sound_str = "hard6";
		}else{
			sound_str = "easy6";
		}
	}else if(total == 8){
		if(dice1_value == 4){
			sound_str = "hard8";
		}else{
			sound_str = "easy8";
		}
	}else if(total == 10){
		if(dice1_value == 5){
			sound_str = "hard10";
		}else{
			sound_str = "easy10";
		}
	}else if(total == 11){
		sound_str = "yoleven";
	}
	
	sounds[sound_str].play();
}

function play_dices_roll(){
	sounds["dice1"].play();
	sounds["dice2"].play();
	sounds["dice3"].play();
}

function mute_dices_roll(){
	sounds["dice1"].stop();
	sounds["dice2"].stop();
	sounds["dice3"].stop();
}


function roll(){
	if(!rolling1 && !rolling2){
		
		reset_dices();
		clear_winnings();
		place_bets = false;
		
		var bet_detail = get_bets_details();
		show_hide_btns(false);
		
		//Provably fair
		if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
		
		$.getJSON(core_url + "action=roll&bets=" + bet_detail.bets + "&pnr="+player_number + "&oskr=" + Math.random()  ,function(data){
				
			if(!data.error){ //change the way to check error
			
				dice1_value = data.dice1_value*1;
				dice2_value = data.dice2_value*1;
				winning_amount = data.win_amount;
				point = data.point;
				
				pfdata = data.pf;
				
				play_dices_roll();
				if(table_status == "come_out"){
					sounds["comming_out"].play();
					if(point > 0){
						setTimeout("sounds['point_is'].play();",1800);
						setTimeout("play_roll_number();",2800);
					}else{
						setTimeout("sounds['roll_is'].play();",1800);
						setTimeout("play_roll_number();",2800);	
					}
				}else{
					setTimeout("sounds['roll_is'].play();",1800);
					setTimeout("play_roll_number();",2800);
				}
				
				if(winning_amount > 0){
					setTimeout("sounds['winner'].play()",4200);
				}
				
				setTimeout("update_pf_data();",3000);
				
				start_rolling = true;
				rolling1 = true;
				rolling2 = true;
			
				
				table_status = data.game_status;
				setTimeout("update_balance("+(data.balance - data.keept_amount)+");",3000);
				setTimeout("place_bets = true;",3000);
				
				current_bet = 0;//data.current_bet	
				
				
				if(data.remove_bets != ""){removing_areas = data.remove_bets.split(",");}
				if(data.won_bets != ""){winning_areas = data.won_bets.split(",");}
				if(data.move_bets != ""){moving_areas = data.move_bets.split(",");}
				
				if(!data.finished){$("#btn_spin").slideDown(500);}
				
			}else{
				alert("There was a problem: " + data.msg);
			}
		});	
		
		
	}
}


function can_place_here(area){
	var can = true;
	if(
		(table_status == "come_out" && (come_bet_areas.indexOf(area) != -1  || dont_come_bet_areas.indexOf(area) != -1)) ||
		(table_status == "point" && (pass_bet_areas.indexOf(area) != -1  || dont_pass_bet_areas.indexOf(area) != -1)) ||
		(dont_come_numbers_bet_areas.indexOf(area) != -1 || come_numbers_bet_areas.indexOf(area) != -1) ||
		(table_status == "come_out" && (dont_pass_odds_bet_areas.indexOf(area) != -1 || pass_odds_bet_areas.indexOf(area) != -1)) ||
		(pass_odds_bet_areas.indexOf(area) != -1 && !is_betting_pass()) ||
		(dont_pass_odds_bet_areas.indexOf(area) != -1 && !is_betting_dontpass()) ||
		(pass_odds_bet_areas.indexOf(area) != -1 && sum_pass_odds_bets() >= (sum_pass_bets()*2)) ||
		(dont_pass_odds_bet_areas.indexOf(area) != -1 && sum_dontpass_odds_bets() >= (sum_dontpass_bets()*2)) 
	){
		can = false;
	}
	
	return can;
}

function is_betting_pass_dontpass(){	
	var is = false;
	for(var i=0; i<pass_bet_areas.length; i++){
		if(area_bets_chip_count[pass_bet_areas[i]] > 0){is = true;}
	}
	for(var i=0; i<dont_pass_bet_areas.length; i++){
		if(area_bets_chip_count[dont_pass_bet_areas[i]] > 0){is = true;}
	}
	return is;
}

function is_betting_pass(){	
	var is = false;
	for(var i=0; i<pass_bet_areas.length; i++){
		if(area_bets_chip_count[pass_bet_areas[i]] > 0){is = true;}
	}
	return is;
}

function sum_pass_bets(){	
	var total = 0;
	for(var i=0; i<pass_bet_areas.length; i++){
		total += area_bets_amount[pass_bet_areas[i]];
	}
	return total;
}

function sum_dontpass_bets(){	
	var total = 0;
	for(var i=0; i<dont_pass_bet_areas.length; i++){
		total += area_bets_amount[dont_pass_bet_areas[i]];
	}
	return total;
}

function sum_pass_odds_bets(){	
	var total = 0;
	for(var i=0; i<pass_odds_bet_areas.length; i++){
		total += area_bets_amount[pass_odds_bet_areas[i]];
	}
	return total;
}

function sum_dontpass_odds_bets(){	
	var total = 0;
	for(var i=0; i<dont_pass_odds_bet_areas.length; i++){
		total += area_bets_amount[dont_pass_odds_bet_areas[i]];
	}
	return total;
}

function is_betting_dontpass(){	
	var is = false;
	for(var i=0; i<dont_pass_bet_areas.length; i++){
		if(area_bets_chip_count[dont_pass_bet_areas[i]] > 0){is = true;}
	}
	return is;
}


function place_bet(area){
	if(place_bets){
		if(/*current_bet+*/selected_chip <= current_balance){//not include current bet because current balance already have current bet deducted
			if(table_status == "finished"){table_status = "come_out";}
			if(current_bet+selected_chip <= max_amount){
			
				if(can_place_here(area)){
			
					if(table_status == "come_out"){$("#btn_clear").slideDown(500);}
					current_bet += selected_chip;
					adjust_balance(selected_chip*-1);
					area_bets_chip_count[area] ++;
					area_bets_amount[area] += selected_chip;
					new_chips.push({value:selected_chip, area:area});
					
					if(current_bet+selected_chip >= min_amount && is_betting_pass_dontpass()){$("#btn_spin").slideDown(500);}
					
				}
			
			}else{
				alert("The maximum bet on this table is " + currency_symbol + max_amount);
			}
		}else{
			fire_not_enough_balance("Not enough balance");
		}
	}
}

function descrive_bet(area){
	description_text.text = area;
}

function auto_place_bet(cvalue, area){
	current_bet += selected_chip;
	adjust_balance(selected_chip*-1);
	area_bets_chip_count[area] ++;
	area_bets_amount[area] += selected_chip;
	new_chips.push({value:cvalue, area:area});
}

function place_blind_bet(cvalue, area){
	current_bet += selected_chip;
	area_bets_chip_count[area] ++;
	area_bets_amount[area] += selected_chip;
	new_chips.push({value:cvalue, area:area});
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


function clear_table(direction){
	hide_message();
	move_marker_out = true;
	hide_message();
	adjust_balance(current_bet*1);
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

function clear_winnings(){
	for(var i = 0; i < won_chips.length; i++){
		won_chips[i].chip.destroy();
	}
	won_chips_count = 0;
	won_chips_text.text = "";	
}

function move_bets(){
	for(var e = 0; e < moving_areas.length; e++){
		
		var parts = moving_areas[e].split("->");
		var moved_chips = 0;
				
		area_bets_chip_count[parts[1]] += area_bets_chip_count[parts[0]];
		area_bets_amount[parts[1]] += area_bets_amount[parts[0]];		
		
		for(var i=0; i<placed_chips.length; i++){
			if(placed_chips[i].area == parts[0]){
				
				var distanser = moved_chips * (game_width*0.001);
				placed_chips[i].chip.x = bet_areas[parts[1]].x;
				placed_chips[i].chip.y = bet_areas[parts[1]].y-distanser;
				
				placed_chips[i].area = parts[1];
				moved_chips++;
				
				if(area_bets_amount_text[parts[1]]){
					if(area_bets_amount_text[parts[1]].x < 0){ //move to position first time
						var txt_x = placed_chips[i].chip.x - (placed_chips[i].chip.displayWidth*0.1);;
						var txt_y = placed_chips[i].chip.y + (placed_chips[i].chip.displayHeight*0.40);
						area_bets_amount_text[parts[1]].x = txt_x;
						area_bets_amount_text[parts[1]].y = txt_y;
					}
					area_bets_amount_text[parts[1]].text = currency_symbol+area_bets_amount[parts[1]];
				}
				
			}
		}		
		
		clear_betarea(parts[0]);
	}
	
	moving_areas = new Array();
}

function clear_betarea(area){
	area_bets_amount_text[area].text = ""
	area_bets_chip_count[area] = 0;
	area_bets_amount[area] = 0; 
}

function remove_area_bet(area){
	if((table_status == "come_out" || (pass_bet_areas.indexOf(area) == -1  && dont_pass_bet_areas.indexOf(area) == -1)) && (dont_come_numbers_bet_areas.indexOf(area) == -1 && come_numbers_bet_areas.indexOf(area) == -1)){
		for(var i=placed_chips.length-1; i>-1; i--){
			if(placed_chips[i].area == area){
				
				var cvalue = placed_chips[i].chip.texture.key.replace("chip","");
				
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
				
				if(!has_pass_dont_pass_bet()){
					$("#btn_spin").slideUp(500);
				}
				
				break;
			}
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
		$("#pf_player_num").val(getRandomInt(1,6)+","+getRandomInt(1,6));
		$("#pf_n_shash").val(pfdata.nxnr);
		$("#pf_l_shash").val(pfdata.lxhs);
		$("#pf_l_sec1").val(pfdata.lsc1);
		$("#pf_l_sec2").val(pfdata.lsc2);
		$("#pf_l_snum").val(pfdata.lxnr);
		$("#pf_l_pnum").val(pfdata.lpnr);
		$("#pf_l_resnum").val(pfdata.lpos);
	}
	
}
