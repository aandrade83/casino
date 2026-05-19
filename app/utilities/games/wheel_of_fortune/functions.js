//Set game vars
var core_url = '/utilities/games/wheel_of_fortune/action.php?gid='+gid+'&';

//general vars
var current_bet = 0;
var total_bet = 0;
var bet_amount_text = null;
var balance_amount_text = null;
var total_amount_text = null;
var win_amount_text = null;
var back_sound = null;
var spin_lock = false;
var music = null;
var is_mute = false;
var set_mute = false;
var pin_sound = null;
var spinning_sound = null;
var winning_sounds = new Array();
var winning_lines = new Array();
var winning_lines_spositions = new Array();
var pfdata = null;
var win_music = null;
var is_spinning = false;
var is1_spinning = false;
var is2_spinning = false;

var wheel1 = null;
var wheel2 = null;
var pin1 = null;
var pin2 = null;
var lights = null;
var lights_direction = "off";

var wheel1_speed = 0.5;
var wheel2_speed = 0.8;
var light_speed = 0.01;

var spin1_count = 4;
var spin1_angles_counter = 0;
var spin2_count = 4;
var spin2_angles_counter = 0;

var wheel1_stop_angle = "na";
var wheel2_stop_angle = "na";

var wheel1_positions = null;
var wheel2_positions = null;

var big_win = false;
var finish_spin = false;
var last_win_amount = 0;
var last_received_balance = 0;

var music_started = false;
var blink_wheel = false;
var blink_wheel_direction = "off";


//calculate width
var game_width = $(window).width()*0.8;

if(game_width > 1900){game_width = 1900;}

//calculate height for size 9/16
var game_height = (game_width * 9)/16; 

var config = {
	type: Phaser.AUTO,
	width: game_width,
	height: game_height,
	transparent: true,
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

//loading
var progressBar = null;
var progressBox = null;
var loadingText = null;

function preload (){
	
	//Loading Bar
	progressBar = this.add.graphics();
	progressBox = this.add.graphics();
	progressBox.fillStyle(0x22222, 0.8);
	progressBox.fillRect((game_width/2)-150, game_height/2, 320, 50);
	
	loadingText = this.make.text({
		x: game_width / 2,
		y: game_height / 2 - 50,
		text: 'Loading...',
		style: {
			font: '20px monospace',
			fill: '#ffffff'
		}
	});
	
	this.load.on('progress', function (value) {
	loadingText.setOrigin(0.5, 0.5);
		progressBar.clear();
		progressBar.fillStyle(0xffffff, 1);
		progressBar.fillRect((game_width/2)-140, (game_height/2)+10, 300 * value, 30);
	});
	
	
	//Load elements
	//Images
	this.load.image('background', 'utilities/games/wheel_of_fortune/imgs/back.png?v=5'); 
	this.load.image('lights', 'utilities/games/wheel_of_fortune/imgs/lights.png?vx2'); 
	this.load.image('pin1', 'utilities/games/wheel_of_fortune/imgs/pin.png?vx2'); 
	this.load.image('pin2', 'utilities/games/wheel_of_fortune/imgs/pin2.png?vx2'); 
	this.load.image('wheel1', 'utilities/games/wheel_of_fortune/imgs/wheel.png?v=4'); 
	this.load.image('wheel2', 'utilities/games/wheel_of_fortune/imgs/wheel2.png?vx2'); 
	this.load.image('area', 'utilities/games/wheel_of_fortune/imgs/blank_area.png');
	
	
	//Sounds
	this.load.audio('music', 'utilities/games/wheel_of_fortune/sounds/music.mp3');
	this.load.audio('pin', 'utilities/games/wheel_of_fortune/sounds/pin.wav');
	this.load.audio('spin', 'utilities/games/wheel_of_fortune/sounds/wheel.wav');
	this.load.audio('win1', 'utilities/games/wheel_of_fortune/sounds/win1.wav');
	this.load.audio('win2', 'utilities/games/wheel_of_fortune/sounds/win2.wav');
	this.load.audio('win3', 'utilities/games/wheel_of_fortune/sounds/win3.mp3');
	this.load.audio('no_win', 'utilities/games/wheel_of_fortune/sounds/no_win.wav');
	
}

function create (){
	
	//hide preload
	progressBar.alpha = 0;
	progressBox.alpha = 0;
	loadingText.alpha = 0;
	
	this.scale.pageAlignHorizontally = true;
	
	current_bet = min_amount;
	current_lines = max_lines;
	total_bet = current_bet*current_lines;
	
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");
	
	//sounds
	music = this.sound.add("music", {mute: false,volume: 0.1,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	pin_sound = this.sound.add("pin", {mute: false,volume: 0.2,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	spinning_sound = this.sound.add("spin", {mute: false,volume: 0.2,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	winning_sounds.push(this.sound.add("win1", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	winning_sounds.push(this.sound.add("win2", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	winning_sounds.push(this.sound.add("win3", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	winning_sounds.push(this.sound.add("no_win", {mute: false,volume: 0.3,rate: 1,detune: 0,seek: 0,loop: false,delay: 0})); 
	
	line_text_style = { font: (game_width*0.018) + "px Arial", fill: "#fefaf1", align: "left" };
		
	
	//Set background
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	lights = this.add.image(game_width/1.948, game_height/8.6, 'lights');
	lights.displayWidth=game.config.width*0.537; 
	lights.scaleY=lights.scaleX;
	
	wheel1 = this.add.image(game_width/2.03, game_height/2.35, 'wheel1');
	wheel1.displayWidth=game.config.width*0.34; 
	wheel1.scaleY=wheel1.scaleX;
	
	wheel2 = this.add.image(game_width/2.03, game_height/2.35, 'wheel2');
	wheel2.displayWidth=game.config.width*0.0974; 
	wheel2.scaleY=wheel2.scaleX;
	
	pin1 = this.add.image(game_width/2.03, game_height/8.01, 'pin1');
	pin1.displayWidth=game.config.width*0.0245; 
	pin1.scaleY=pin1.scaleX;
	
	pin2 = this.add.image(game_width/2.03, game_height/2.43, 'pin2');
	pin2.displayWidth=game.config.width*0.023; 
	pin2.scaleY=pin2.scaleX;
	
	
	//btns
	var spin_area = this.add.image(game_width/1.32, game_height/1.98, 'area').setInteractive();
	spin_area.displayWidth=game.config.width*0.15; 
	spin_area.displayHeight=game.config.width*0.04; 
	spin_area.alpha = 0.5;
	spin_area.on('pointerup', function (pointer) { spin(); });
	
	var coin_value_area_plus = this.add.image(game_width/1.24, game_height/2.46, 'area').setInteractive();
	coin_value_area_plus.displayWidth=game.config.width*0.04; 
	coin_value_area_plus.displayHeight=game.config.width*0.04; 
	coin_value_area_plus.alpha = 0.5;
	coin_value_area_plus.on('pointerup', function (pointer) { change_bet(true); });
	
	var coin_value_area_minus = this.add.image(game_width/1.45, game_height/2.46, 'area').setInteractive();
	coin_value_area_minus.displayWidth=game.config.width*0.04; 
	coin_value_area_minus.displayHeight=game.config.width*0.04; 
	coin_value_area_minus.alpha = 0.5;
	coin_value_area_minus.on('pointerup', function (pointer) { change_bet(false); });
	
	wheel1_positions = new Array();
	wheel1_positions[1] = [6,66,150,-112,-76];
	wheel1_positions[5] = [18,90,-148,-54];
	wheel1_positions[25] = [30,164];
	wheel1_positions[0] = [42,102,138,175,-90,-66,-19,-136];
	wheel1_positions[2] = [54,126,-172,-100,-31];
	wheel1_positions[10] = [78,-160,-44];
	wheel1_positions[50] = [114,-124];
	wheel1_positions[100] = [-7];
	
	wheel2_positions = new Array();
	wheel2_positions[0] = [-27,-88,151,32];
	wheel2_positions[1] = [-120,-149,121,91];
	wheel2_positions[2] = [-56,-179,60];
	wheel2_positions[10] = [1];

	
	
	//set text settings
	machine_text_style = { font: (game_width*0.018) + "px Arial", fill: "#fefaf1", align: "center" };
	
	//set texts positions
	bet_amount_text = this.add.text(game_width/1.37, game_height/2.51, current_bet, machine_text_style);
	balance_amount_text = this.add.text(game_width/1.42, game_height/4.85, current_balance, machine_text_style);
	
	win_amount_text = this.add.text(game_width/1.37, game_height/3.25, "0", machine_text_style);


	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	test_text = this.add.text(game_width/52, game_height/1.12, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
}

function isInRange(number, target, range) {
	if(number < 0 && target < 0){number = Math.abs(number); target = Math.abs(target);}
	var lowerBound = target - range;
	var upperBound = target + range; 
	return (number >= lowerBound && number <= upperBound);
}


function update (){
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;

	//lights
	if(lights_direction == "off"){
		lights.alpha -= light_speed;
		if(lights.alpha < 0.05){lights_direction = "on";}	
	}else{
		lights.alpha += light_speed;
		if(lights.alpha > 0.95){lights_direction = "off";}	
	}
	
	//wheel blink
	if(blink_wheel){
		if(blink_wheel_direction == "off"){
			wheel1.alpha -= 0.05;
			if(wheel1.alpha < 0.7){blink_wheel_direction = "on";}	
		}else{
			wheel1.alpha += 0.05;
			if(wheel1.alpha > 0.99){blink_wheel_direction = "off";}	
		}
	}

	//wheels rotations
	
	if(spin1_angles_counter > 0){
			
		if(wheel1_speed < 0.7){
			wheel1_speed -= 0.005;
			if(wheel1_speed < 0.05){wheel1_speed = 0.05;}
		}else{
			wheel1_speed -= 0.007;
		}
		
		if(spin1_angles_counter < 1 && isInRange(Math.round(wheel1.angle), wheel1_stop_angle, 0.1)){		
			pin_sound.play();	
			wheel1_speed = 0;
			spin1_angles_counter = 0;
			wheel1.angle = wheel1_stop_angle;
			is1_spinning = false;
		}
		
		spin1_angles_counter -= wheel1_speed;
		if(spin1_angles_counter < 0 && wheel1_speed > 0){spin1_angles_counter = 1;} 
	}
	
	if(spin2_angles_counter > 0){
			
		if(wheel2_speed < 0.7){
			wheel2_speed -= 0.005;
			if(wheel2_speed < 0.05){wheel2_speed = 0.05;}
		}else{
			wheel2_speed -= 0.007;
		}
		
		if(spin2_angles_counter < 1 && isInRange(Math.round(wheel2.angle), wheel2_stop_angle, 0.1)){			
			wheel2_speed = 0;
			spin2_angles_counter = 0;
			wheel2.angle = wheel2_stop_angle;
			is2_spinning = false;
		}
		
		spin2_angles_counter -= wheel2_speed;
		if(spin2_angles_counter < 0 && wheel2_speed > 0){spin2_angles_counter = 1;}
		
	}
	
	wheel1.angle += wheel1_speed;	
	wheel2.angle -= wheel2_speed;
	
	if(is_spinning && !is1_spinning && !is2_spinning){
		is_spinning = false;
		finish_spin = true;
		if(spinning_sound){spinning_sound.stop();}
	}
	
	if(finish_spin){
		finish_spin = false;
		setTimeout("finilize_spin();",500);
	}

	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	
}



function scroll_down(){
	$('html, body').animate({scrollTop:$(document).height()}, 'slow');
}

function update_win_amount(amount,play_sounds){
	win_amount_text.text = amount;
	if(play_sounds){
		
		var win_rate = amount / current_bet;
		
		if(big_win){
			winning_sounds[1].play(); 
			blink_wheel = true;
			light_speed = 0.2;
		}else if(win_rate >= 5){
			winning_sounds[2].play();
			blink_wheel = true;
			light_speed = 0.2;
		}else if(amount > 0){
			winning_sounds[0].play();
		}else{
			winning_sounds[3].play();	
		}
		
	}
	
}

function update_balance(new_balance){
	current_balance = new_balance;
	balance_amount_text.text = Math.round((current_balance + Number.EPSILON) * 100) / 100;
	$("#balance_field").html(number_format(current_balance));
}


function finilize_spin(){
	update_win_amount(last_win_amount,true);
	update_balance(last_received_balance);
	update_pf_data();
}

function spin(){

	if(!is_spinning){
		
		if(current_bet <= current_balance){
			
			update_balance(current_balance-current_bet);
			update_win_amount(0,false);
			is_spinning = true;
			is1_spinning = true;
			is2_spinning = true;
			big_win = false;
			light_speed = 0.01;
			wheel1.alpha = 1;
			blink_wheel = false;
			
			if(!music_started){music.play(); music_started = true;}
			
			
			//Provably fair
			if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}

			$.getJSON(core_url + "action=spin&bet=" + current_bet +  "&pnr="+player_number+"&oskr=" + Math.random()  ,function(data){
				
				if(!data.error){ //change the way to check error
					spinning_sound.play();
					
					big_win = data.big_win;
					last_received_balance = data.balance;
					last_win_amount = data.win_amount;
					
					var positions = data.positions.split("|");
					
					var w1angle = wheel1_positions[positions[0]][Math.floor(Math.random() * wheel1_positions[positions[0]].length)];
					var w2angle = wheel2_positions[positions[1]][Math.floor(Math.random() * wheel2_positions[positions[1]].length)];
					
					wheel1.angle = w1angle;
					wheel1_stop_angle = w1angle;
					
					wheel2.angle = w2angle;
					wheel2_stop_angle = w2angle;
					
					wheel1_speed = 5;
					wheel2_speed = 5;
					
					spin1_angles_counter = (spin1_count*360)-30;
					spin2_angles_counter = (spin2_count*360)-30;
					
					pfdata = data.pf;				
				
					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
		
		}else{
			fire_not_enough_balance("Not enough balance");	
		}
	}
	
}


function update_pf_data(){
	
	if(pfdata){
	
		var new_pnum =  new Array();
		for(var i = 0; i < 15; i++){
			new_pnum.push(getRandomInt(0,199));
		}
		
		$("#pf_player_num").val(new_pnum.join(","));
		$("#pf_n_shash").val(pfdata.nxnr);
		$("#pf_l_shash").val(pfdata.lxhs);
		$("#pf_l_sec1").val(pfdata.lsc1);
		$("#pf_l_sec2").val(pfdata.lsc2);
		$("#pf_l_snum").val(pfdata.lxnr);
		$("#pf_l_pnum").val(pfdata.lpnr);
		$("#pf_l_resnum").val(pfdata.lpos);
	
	}
	
}


function change_bet(increase){
	 
	if(!is_spinning) {
		
		
		var step = 0.25;
		if(current_bet*1 >= 3){
			step = 1;	
		}else if(current_bet*1 >= 1){
			step = 0.5;
		}else if(current_bet*1 >= 0.25){
			step = 0.25;
		}else if(current_bet*1 >= 0.05){
			step = 0.05;
		}else if(current_bet*1 == 0.01){
			step = 0.04;
		}
		
		
		if(increase){
		
			if((current_bet*1 + step) <= max_amount){
				current_bet = Math.round(((current_bet*1 + step) + Number.EPSILON) * 100) / 100;
			}else{
				current_bet = min_amount;
			}
		
		}else{
		
			if((current_bet*1 - step) >= min_amount){
				current_bet = Math.round(((current_bet*1 - step) + Number.EPSILON) * 100) / 100;
			}else{
				current_bet = max_amount;
			}
			
		}
		
		bet_amount_text.text = current_bet;
	
	}
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
