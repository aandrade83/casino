//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/multi_slot_zeus/action.php?gid='+gid+'&';

//general vars
var reel1 = null;
var reel2 = null;
var reel3 = null;
var reels = [reel1,reel2,reel3];
var figures = new Array(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
var figures_on = new Array(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
var lines = new Array();
var lines_screens = new Array();
var lines_txts = new Array();
var reel1_move = 0;
var reel2_move = 0;
var reel3_move = 0;
var reel4_move = 0;
var reel5_move = 0;
var reels_moves = [reel1_move,reel2_move,reel3_move,reel4_move,reel5_move];
var reel_positions = new Array();
var reel1_spinning = false;
var reel2_spinning = false;
var reel3_spinning = false;
var reel4_spinning = false;
var reel5_spinning = false;
var reels_spinnings = [reel1_spinning,reel2_spinning,reel3_spinning,reel4_spinning,reel5_spinning];
var current_bet = 0;
var current_lines = 0;
var total_bet = 0;
var bet_amount_text = null;
var balance_amount_text = null;
var total_amount_text = null;
var win_amount_text = null;
var lines_amount_text = null;
var back_sound = null;
var spin_lock = false;
var is_mute = false;
var set_mute = false;
var pin_sound = null;
var spinning_sound = null;
var winning_sounds = new Array();
var winning_lines = new Array();
var winning_lines_spositions = new Array();
var pay_table = null;
var info_btn = null;
var pfdata = null;
var giant_animation = null;
var win_music = null;
var music = null;
var playing_music = false;
var win_x5 = false;
var vibrating_figures = new Array();
var vibrating_figures_direction = new Array();

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
	this.load.image('background', 'utilities/games/multi_slot_zeus/imgs/back.png?v1');
	this.load.image('pay_table', 'utilities/games/multi_slot_zeus/imgs/pay_table.png?v=7');
	this.load.image('info', 'utilities/games/multi_slot_zeus/imgs/info.png');
	this.load.image('main_btn', 'utilities/games/multi_slot_zeus/imgs/bet_btn.png?v=2');
	this.load.image('giant', 'utilities/games/multi_slot_zeus/imgs/giant.png');
	this.load.image('area', 'utilities/games/multi_slot_zeus/imgs/blank_area.png');
	//this.load.image('area', 'utilities/games/multi_slot_zeus/imgs/area.jpg');
	
	
	this.load.image('reel_back', 'utilities/games/multi_slot_crypto/imgs/reel_back.png');
	this.load.image('reel1', 'utilities/games/multi_slot_zeus/imgs/reel1.png?v1');
	this.load.image('reel2', 'utilities/games/multi_slot_zeus/imgs/reel2.png?v1');
	this.load.image('reel3', 'utilities/games/multi_slot_zeus/imgs/reel3.png?v1');
	this.load.image('reel4', 'utilities/games/multi_slot_zeus/imgs/reel4.png?v1');
	this.load.image('reel5', 'utilities/games/multi_slot_zeus/imgs/reel5.png?v1');
	
	for(var i=1; i<= 10; i++){
		this.load.image('figure'+i, 'utilities/games/multi_slot_zeus/imgs/figures/fig'+i+'.png?v=2?v=4'); 
	}
	
	for(var i=1; i<= 10; i++){
		this.load.image('figure_on'+i, 'utilities/games/multi_slot_zeus/imgs/figures_on/fig'+i+'.png?v=4'); 
	}
	
	//Animations
	for(var i=1; i<=29; i++){
		this.load.image('animation_'+i, 'utilities/games/multi_slot_zeus/imgs/animation/'+i+'.png');
	}
		
	
	//Sounds
	this.load.audio('pin', 'utilities/games/multi_slot_zeus/sounds/pin.wav?v=1');
	this.load.audio('spin', 'utilities/games/multi_slot_zeus/sounds/spin_sound.wav?v=1');
	this.load.audio('win1', 'utilities/games/multi_slot_zeus/sounds/win1.wav?v=1');
	this.load.audio('win2', 'utilities/games/multi_slot_zeus/sounds/win2.wav?v=1');
	this.load.audio('big_win', 'utilities/games/multi_slot_zeus/sounds/big_win.mp3?v=1');
	this.load.audio('music', 'utilities/games/multi_slot_zeus/sounds/music.mp3?v=1');
	this.load.audio('scream', 'utilities/games/multi_slot_zeus/sounds/scream.wav?v=1');
	
	
	
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
	pin_sound = this.sound.add("pin", {mute: false,volume: 0.3,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	spinning_sound = this.sound.add("spin", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	winning_sounds.push(this.sound.add("win1", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	winning_sounds.push(this.sound.add("win2", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	winning_sounds.push(this.sound.add("scream", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	
	win_music = this.sound.add("big_win", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	music = this.sound.add("music", {mute: false,volume: 0.6,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	
	//Set background
	
	
	
	var main_btn = this.add.image(game_width/1.06, game_height/2, 'main_btn');
	main_btn.displayWidth=game.config.width*0.1; 
	main_btn.scaleY=main_btn.scaleX;
	
	/*var giant = this.add.image(game_width/8.25, game_height/2, 'giant');
	giant.displayWidth=game.config.width*0.21; 
	giant.scaleY=giant.scaleX;*/
	
	var aniframes = [];
	for(var i=1; i<=29; i++){
		aniframes.push({ key: 'animation_'+i });
	}
	this.anims.create({
        key: 'giant_move',
        frames: aniframes,
        frameRate: 10,
        repeat: 1
    });
	
	giant_animation = this.add.sprite(game_width/8.25, game_height/2, 'animation_1');
	giant_animation.displayWidth=game.config.width*0.35; 
	giant_animation.scaleY=giant_animation.scaleX;
	
	
	//btns
	var cvalue_plus = this.add.image(game_width/1.03, game_height/2.47, 'area').setInteractive();
	cvalue_plus.displayWidth=game.config.width*0.03; 
	cvalue_plus.displayHeight=game.config.width*0.03; 
	cvalue_plus.alpha = 0.5;
	cvalue_plus.on('pointerup', function (pointer) { change_bet(true); });
	
	var cvalue_minus = this.add.image(game_width/1.09, game_height/2.47, 'area').setInteractive();
	cvalue_minus.displayWidth=game.config.width*0.03; 
	cvalue_minus.displayHeight=game.config.width*0.03; 
	cvalue_minus.alpha = 0.5;
	cvalue_minus.on('pointerup', function (pointer) { change_bet(false); });
	
	var lines_plus = this.add.image(game_width/1.03, game_height/1.64, 'area').setInteractive();
	lines_plus.displayWidth=game.config.width*0.03; 
	lines_plus.displayHeight=game.config.width*0.03; 
	lines_plus.alpha = 0.5;
	lines_plus.on('pointerup', function (pointer) { change_lines(true); });
	
	var lines_minus = this.add.image(game_width/1.09, game_height/1.64, 'area').setInteractive();
	lines_minus.displayWidth=game.config.width*0.03; 
	lines_minus.displayHeight=game.config.width*0.03; 
	lines_minus.alpha = 0.5;
	lines_minus.on('pointerup', function (pointer) { change_lines(false); });
	
	var spin_btn = this.add.image(game_width/1.05, game_height/1.94, 'area').setInteractive();
	spin_btn.displayWidth=game.config.width*0.06; 
	spin_btn.displayHeight=game.config.width*0.06; 
	spin_btn.alpha = 0.5;
	spin_btn.on('pointerup', function (pointer) { spin(); });
	
	var yfposes = [3.98,2.13,1.44];
	var xfposes = [3.11,2.31,1.82,1.51,1.29];
	
	/*var reel_back1 = this.add.image(game_width/3.15, game_height/1.75, 'area');
	reel_back1.displayWidth=game.config.width*0.12; 
	reel_back1.displayHeight=game.config.width*0.38; */
	
	var background = this.add.image(game_width/1.82, game_height/2, 'background');
	background.displayWidth=game.config.width*0.62; 
	background.scaleY=background.scaleX;
	
	var maskGraphics = this.add.graphics();
	maskGraphics.fillStyle(0x000000, 0);
	maskGraphics.fillRect(game_width/4, game_height/6.5, game.config.width*0.62, game.config.width*0.355);
	
	var mask1 = maskGraphics.createGeometryMask();	
	
	reels[0] = this.add.image(game_width/xfposes[0], (game_height/-50)*52.5, 'reel1');
	reels[0].displayWidth=game.config.width*0.09; 
	reels[0].scaleY=reels[0].scaleX;
	reels[0].alpha = 0;
	reels[0].setMask(mask1);
	
	reels[1] = this.add.image(game_width/xfposes[1], (game_height/-50)*52.5, 'reel2');
	reels[1].displayWidth=game.config.width*0.09; 
	reels[1].scaleY=reels[1].scaleX;
	reels[1].alpha = 0;
	reels[1].setMask(mask1);
	
	reels[2] = this.add.image(game_width/xfposes[2], (game_height/-50)*52.5, 'reel3');
	reels[2].displayWidth=game.config.width*0.09; 
	reels[2].scaleY=reels[2].scaleX;
	reels[2].alpha = 0;
	reels[2].setMask(mask1);
	
	reels[3] = this.add.image(game_width/xfposes[3], (game_height/-50)*52.5, 'reel4');
	reels[3].displayWidth=game.config.width*0.09; 
	reels[3].scaleY=reels[3].scaleX;
	reels[3].alpha = 0;
	reels[3].setMask(mask1);
	
	reels[4] = this.add.image(game_width/xfposes[4], (game_height/-50)*52.5, 'reel5');
	reels[4].displayWidth=game.config.width*0.09; 
	reels[4].scaleY=reels[4].scaleX;
	reels[4].alpha = 0;
	reels[4].setMask(mask1);
	
	var fi = 0;
	var fignames = new Array("1","2","3","4","5","6","7","8","9","10");
	for(var i=0; i<xfposes.length; i++){
		for(var n=0; n<yfposes.length; n++){
			figures[fi] = this.add.image(game_width/xfposes[i], game_height/yfposes[n], "figure" + fignames[ Math.floor(Math.random() * 10) ]);
			figures[fi].displayWidth=game.config.width*0.09; 
			figures[fi].scaleY=figures[fi].scaleX;
			figures[fi].alpha = 1;
			
			figures_on[fi] = this.add.image(game_width/xfposes[i], game_height/yfposes[n], "figure_on" + fignames[ Math.floor(Math.random() * 10) ]);
			figures_on[fi].displayWidth=game.config.width*0.09; 
			figures_on[fi].scaleY=figures_on[fi].scaleX;
			figures_on[fi].alpha = 0;
			
			fi++;
		}	
	}
	
	
	
	
	
	//set text settings
	machine_text_style = { font: (game_width*0.018) + "px Arial", fill: "#ffffff", align: "center" };
	var machine_text_small = { font: (game_width*0.015) + "px Arial", fill: "#ffffff", align: "center" };
	
	//set texts positions
	bet_amount_text = this.add.text(game_width/1.06, game_height/2.72, current_bet, machine_text_small);
	bet_amount_text.setOrigin(0.5);
	lines_amount_text = this.add.text(game_width/1.06, game_height/1.59, 25, machine_text_small);
	lines_amount_text.setOrigin(0.5);
	balance_amount_text = this.add.text(game_width/2.83, game_height/1.07, current_balance, machine_text_style);
	balance_amount_text.setOrigin(0.5);
	total_amount_text = this.add.text(game_width/1.34, game_height/1.07, current_bet * max_lines, machine_text_style);
	total_amount_text.setOrigin(0.5);
	win_amount_text = this.add.text(game_width/1.81, game_height/1.07, "0", machine_text_style);
	win_amount_text.setOrigin(0.5);
	
	
	
	info_btn = this.add.image(game_width/1.06, game_height/3.85, 'info').setInteractive();
	info_btn.displayWidth=game.config.width*0.025; 
	info_btn.scaleY=info_btn.scaleX;
	info_btn.on('pointerup', function (pointer) {	
		show_paytable();
	});	
	
	pay_table = this.add.image(game_width/2, game_height/2, 'pay_table').setInteractive();
	pay_table.displayWidth=game.config.width*0.90; 
	pay_table.scaleY=pay_table.scaleX;
	pay_table.alpha = 0;
	pay_table.on('pointerup', function (pointer) {	
		hide_paytable();
	});	


	
	//set reels positions
	reel_positions.push(game_height/0.615);
	reel_positions.push(game_height/0.725);
	reel_positions.push(game_height/0.875);
	reel_positions.push(game_height/1.115);
	reel_positions.push(game_height/1.53495);
	reel_positions.push(game_height/2.46);
	reel_positions.push(game_height/5.8);	
	reel_positions.push((game_height/-50)*3.9);
	reel_positions.push((game_height/-50)*16);
	reel_positions.push((game_height/-50)*27.8);
	reel_positions.push((game_height/-50)*40.2);
	reel_positions.push((game_height/-50)*52.5);	
	
	
	
	winning_lines_spositions[1] = [2,5,8,11,14];
	winning_lines_spositions[2] = [1,4,7,10,13];
	winning_lines_spositions[3] = [3,6,9,12,15];
	winning_lines_spositions[4] = [1,5,9,11,13];
	winning_lines_spositions[5] = [3,5,7,11,15];
	winning_lines_spositions[6] = [1,4,8,10,13];
	winning_lines_spositions[7] = [3,6,8,12,15];
	winning_lines_spositions[8] = [2,4,7,10,14];
	winning_lines_spositions[9] = [2,6,9,12,14];
	winning_lines_spositions[10] = [1,4,8,12,15];
	winning_lines_spositions[11] = [3,6,8,10,13];
	winning_lines_spositions[12] = [2,4,8,12,14];
	winning_lines_spositions[13] = [2,6,8,10,14];
	winning_lines_spositions[14] = [1,5,7,11,13];
	winning_lines_spositions[15] = [3,5,9,11,15];
	winning_lines_spositions[16] = [1,5,8,11,13];
	winning_lines_spositions[17] = [3,5,8,11,15];
	winning_lines_spositions[18] = [2,4,8,10,14];
	winning_lines_spositions[19] = [2,6,8,12,14];
	winning_lines_spositions[20] = [2,5,7,11,14];
	winning_lines_spositions[21] = [2,5,9,11,14];
	winning_lines_spositions[22] = [1,6,9,12,13];
	winning_lines_spositions[23] = [3,4,7,10,15];
	winning_lines_spositions[24] = [1,6,7,12,13];
	winning_lines_spositions[25] = [3,4,9,10,15];
	
	
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	
	test_text = this.add.text(game_width/9.63, game_height/1.09, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
}

function show_paytable(){
	pay_table.alpha = 1;
}

function hide_paytable(){
	pay_table.alpha = 0;
}

function move_reel_test(direction){
	var tochange = 0;
	var cvalue = $("#reeltpos").val()*1;
	if(direction == '+'){
		tochange = 0.1;
	}else if (direction == '-'){
		tochange = -0.1;
	}
	
	cvalue += tochange;
	$("#reeltpos").val(cvalue);
	reels[0].y = (game_height/-50)*cvalue;	
}


function update (){
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;
	
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//spin reel
	for(var i = 0; i < reels_spinnings.length; i++){
		reel_spin(i)
	}
	
	//figures movements
	
	for(var i = 0; i < vibrating_figures.length; i++){ 
		if(vibrating_figures_direction[vibrating_figures[i]] == "off"){
			figures_on[vibrating_figures[i]].alpha -= 0.05;
		}else{
			figures_on[vibrating_figures[i]].alpha += 0.05;
		}
		
		if(figures_on[vibrating_figures[i]].alpha  < 0.2){
			vibrating_figures_direction[vibrating_figures[i]] = "on";
		}else if(figures_on[vibrating_figures[i]].alpha >= 0.9){
			vibrating_figures_direction[vibrating_figures[i]] = "off";
		}
	}
	
	
}

function reel_spin(num){
	if(reels_spinnings[num]){
		reels_moves[num]++
		if(reels_moves[num] == 30){
			reels[num].y = (game_height/-50)*52.5;
			reels_moves[num] = 0;
		}else{
			reels[num].y += game_width*0.05;
		}
	}
	
}

function add_figure_to_vibrating(num){
	if(vibrating_figures.indexOf(num) == -1){
		vibrating_figures.push(num);
	}
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

function update_win_amount(amount){
	if(amount > 0){
		winning_sounds[Math.round(Math.random())].play();
		setTimeout("winning_sounds[2].play(); /*Play animation here*/",1000);
		giant_animation.play('giant_move');
	}
	
	if(win_x5){
		playing_music = false;
		music.stop();
		win_music.play();
	}
	
	win_amount_text.text = amount;
}

function update_balance(new_balance){
	current_balance = new_balance;
	balance_amount_text.text = Math.round((current_balance + Number.EPSILON) * 100) / 100;
	$("#balance_field").html(number_format(current_balance));
}


function spin(){

	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2]  && !reels_spinnings[3]  && !reels_spinnings[4] && !spin_lock){
		
		if(!playing_music){
			playing_music = true;
			music.play();
		}
		
		for(var i = 0; i < lines.length; i++){
			lines[i].alpha = 0;
			lines_screens[i].alpha = 0;
			lines_txts[i].alpha = 0;
			lines_txts[i].text = "";
		}
		
		vibrating_figures = new Array();
		
		if(total_bet <= current_balance){
			
			spin_lock = true;
			win_x5 = false;
			
			win_amount_text.text = "0";
			update_balance(current_balance-total_bet);
			
			//Provably fair
			if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}

			$.getJSON(core_url + "action=spin&bet=" + current_bet + "&lines=" + current_lines + "&pnr="+player_number+"&oskr=" + Math.random()  ,function(data){
				
				if(!data.error){ //change the way to check error
					spinning_sound.play();
					winning_lines = data.winning_lines.split(",");
					win_x5 = data.win_x5;
					
					var positions = data.positions.split("|");
					for(var i = 0; i < positions.length; i++){
						figures[i].setTexture("figure"+positions[i].replace("fig",""));
						figures[i].displayWidth=game.config.width*0.09; 
						figures[i].scaleY=figures[i].scaleX;
						
						figures_on[i].setTexture("figure_on"+positions[i].replace("fig",""));
						figures_on[i].displayWidth=game.config.width*0.09; 
						figures_on[i].scaleY=figures_on[i].scaleX;
					}
					
					var timer = 1000;
					for(var i = 0; i < reels_spinnings.length; i++){
						reels_spinnings[i]  = true;
						reels[i].y = (game_height/-50)*52.5;
						setTimeout("stop_reel("+i+");",timer);
						timer += 400;
					}
					reset_reels();
					
					pfdata = data.pf;
					
					setTimeout("update_win_amount("+data.win_amount+")",timer+300);	
					setTimeout("update_balance("+data.balance+")",timer+300);
					setTimeout("update_pf_data()",timer+300);					
				
					
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
			new_pnum.push(getRandomInt(0,109));
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

function reset_reels(){
	
	for(var i=0; i<reels.length; i++){
		reels[i].alpha = 1;
	}
	for(var i=0; i<figures.length; i++){
		figures[i].alpha = 0;
		figures_on[i].alpha = 0;
	}
}

function stop_reel(num){
	reels_spinnings[num] = false;
	/*reels[num].y = reel_positions[position] + game_height*0.007;
	setTimeout("reels["+num+"].y = reel_positions["+position+"] - game_height*0.01;",50);*/
	
	reels[num].alpha = 0;
	var movement = game_height*0.012;
	
	figures[(num*3)].y = figures[(num*3)].y + movement;
	figures[(num*3)].alpha = 1;
	setTimeout("figures["+(num*3)+"].y = figures["+(num*3)+"].y - "+movement+";",50);
	
	figures[(num*3)+1].y = figures[(num*3)+1].y + movement;
	figures[(num*3)+1].alpha = 1;
	setTimeout("figures["+((num*3)+1)+"].y = figures["+((num*3)+1)+"].y - "+movement+";",50);
	
	figures[(num*3)+2].y = figures[(num*3)+2].y + movement;
	figures[(num*3)+2].alpha = 1;
	setTimeout("figures["+((num*3)+2)+"].y = figures["+((num*3)+2)+"].y - "+movement+";",50);
	
	
	
	pin_sound.play();
	if(num == 4){
		setTimeout("spin_lock = false;",100);
		
		for(var i = 0; i < winning_lines.length; i++){
			var line_parts = winning_lines[i].split("|");
			
			if(winning_lines_spositions[line_parts[0]]){
			
				for(var e = 0; e < winning_lines_spositions[line_parts[0]].length; e++){
					add_figure_to_vibrating(winning_lines_spositions[line_parts[0]][e]-1);
				}
			
			}
		}
		
	}
}

function change_lines(increase){
	
	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2]  && !reels_spinnings[3]  && !reels_spinnings[4]) {
		
		if(increase){
			if(current_lines+1 <= max_lines){
				current_lines++;
			}else{
				current_lines = 1;	
			}
		}else{
			if(current_lines-1 >= 1){
				current_lines--;
			}else{
				current_lines = max_lines;	
			}
		}
		
		
		update_screens();
	}
}

function change_bet(increase){
	
	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2]  && !reels_spinnings[3]  && !reels_spinnings[4]) {
	
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
		update_screens();
	
	}
}

function update_screens(){	
	bet_amount_text.text = current_bet;
	lines_amount_text.text = current_lines;
	total_bet = current_bet*current_lines;
	total_amount_text.text = total_bet;
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
