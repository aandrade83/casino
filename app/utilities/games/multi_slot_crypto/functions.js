//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/multi_slot_crypto/action.php?gid='+gid+'&';

//general vars
var reel1 = null; 
var reel2 = null;
var reel3 = null;
var reels = [reel1,reel2,reel3];
var figures = new Array(null,null,null,null,null,null,null,null,null,null,null,null,null,null,null);
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
var freespins_amount_text = null;
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
var visible_winning_lines = new Array();
var visible_winning_lines_direction = new Array();
var vibrating_figures = new Array();
var vibrating_figures_direction = new Array();
var pay_table = null;
var info_btn = null;
var pfdata = null;
var win_animation = null;
var fmist_animation = null;
var win_music = null;
var win_animation_fadeout = false;
var win_x5 = false;
var mist_direction = "off";

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
	this.load.image('background', 'utilities/games/multi_slot_crypto/imgs/back.png?vx2'); 
	this.load.image('reel_back', 'utilities/games/multi_slot_crypto/imgs/reel_back.png?x1'); 
	this.load.image('pay_table', 'utilities/games/multi_slot_crypto/imgs/pay_table.png?x=41');
	this.load.image('info', 'utilities/games/multi_slot_crypto/imgs/info.png');
	
	this.load.image('reel1', 'utilities/games/multi_slot_crypto/imgs/reel1.png?x1'); 
	this.load.image('reel2', 'utilities/games/multi_slot_crypto/imgs/reel2.png?x1');
	this.load.image('reel3', 'utilities/games/multi_slot_crypto/imgs/reel3.png?x1');
	this.load.image('reel4', 'utilities/games/multi_slot_crypto/imgs/reel4.png?x1');
	this.load.image('reel5', 'utilities/games/multi_slot_crypto/imgs/reel5.png?x1'); 
	
	
	for(var i=1; i<= 13; i++){
		this.load.image('figure'+i, 'utilities/games/multi_slot_crypto/imgs/figures/fig'+i+'.png?vx1'); 
	}
	
	this.load.image('line_screen', 'utilities/games/multi_slot_crypto/imgs/line_screen.png?x1');
	for(var i=1; i<=25; i++){
		this.load.image('line_'+i, 'utilities/games/multi_slot_crypto/imgs/lines/'+i+'.png');
	}
	
	//Animations
	for(var i=2; i<=50; i+=2){
		this.load.image('animation_'+i, 'utilities/games/multi_slot_crypto/imgs/animation1/Cryptoland_Big_Win_Animation_000'+i+'.png');
	}
	
	//disabled for speed
	for(var i=0; i<=40; i+=2){
		//this.load.image('fmist_'+i, 'utilities/games/multi_slot_crypto/imgs/mist1/Mist_Front0'+i+'.png');
	}
	
	
	//Sounds
	this.load.audio('pin', 'utilities/games/multi_slot_crypto/sounds/reel_stop.wav');
	this.load.audio('spin', 'utilities/games/multi_slot_crypto/sounds/roll.wav');
	this.load.audio('win1', 'utilities/games/multi_slot_crypto/sounds/line_win.wav');
	this.load.audio('win2', 'utilities/games/multi_slot_crypto/sounds/line_win2.wav');
	this.load.audio('big_win', 'utilities/games/multi_slot_crypto/sounds/win.wav');
	
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
	pin_sound = this.sound.add("pin", {mute: false,volume: 0.5,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	spinning_sound = this.sound.add("spin", {mute: false,volume: 0.5,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	winning_sounds.push(this.sound.add("win1", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	winning_sounds.push(this.sound.add("win2", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}));
	
	win_music = this.sound.add("big_win", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	
	var yfposes = [6.75,2.59,1.55];
	var xfposes = [11.82,3.95,2.33,1.65,1.29];
	
	var reel_back1 = this.add.image(game_width/11.3, game_height/2.63, 'reel_back');
	reel_back1.displayWidth=game.config.width*0.15; 
	reel_back1.scaleY=reel_back1.scaleX;
	
	var reel_back2 = this.add.image(game_width/3.92, game_height/2.63, 'reel_back');
	reel_back2.displayWidth=game.config.width*0.15; 
	reel_back2.scaleY=reel_back2.scaleX;
	
	var reel_back3 = this.add.image(game_width/2.34, game_height/2.63, 'reel_back');
	reel_back3.displayWidth=game.config.width*0.15; 
	reel_back3.scaleY=reel_back3.scaleX;
	
	var reel_back4 = this.add.image(game_width/1.65, game_height/2.63, 'reel_back');
	reel_back4.displayWidth=game.config.width*0.15; 
	reel_back4.scaleY=reel_back3.scaleX;
	
	var reel_back5 = this.add.image(game_width/1.29, game_height/2.63, 'reel_back');
	reel_back5.displayWidth=game.config.width*0.15; 
	reel_back5.scaleY=reel_back5.scaleX;
	
	
	var mask1 = reel_back1.createBitmapMask();
	var mask2 = reel_back2.createBitmapMask();
	var mask3 = reel_back3.createBitmapMask();
	var mask4 = reel_back4.createBitmapMask();
	var mask5 = reel_back5.createBitmapMask();
	
	
	reels[0] = this.add.image(game_width/11.82, (game_height/-50)*52.5, 'reel1');
	reels[0].displayWidth=game.config.width*0.12; 
	reels[0].scaleY=reels[0].scaleX;
	reels[0].alpha = 0;
	reels[0].setMask(mask1);
	
	reels[1] = this.add.image(game_width/3.95, (game_height/-50)*52.5, 'reel2');
	reels[1].displayWidth=game.config.width*0.12; 
	reels[1].scaleY=reels[1].scaleX;
	reels[1].alpha = 0;
	reels[1].setMask(mask2);
	
	reels[2] = this.add.image(game_width/2.33, (game_height/-50)*52.5, 'reel3');
	reels[2].displayWidth=game.config.width*0.12; 
	reels[2].scaleY=reels[2].scaleX;
	reels[2].alpha = 0;
	reels[2].setMask(mask3);
	
	reels[3] = this.add.image(game_width/1.65, (game_height/-50)*52.5, 'reel4');
	reels[3].displayWidth=game.config.width*0.12; 
	reels[3].scaleY=reels[3].scaleX;
	reels[3].alpha = 0;
	reels[3].setMask(mask4);
	
	reels[4] = this.add.image(game_width/1.29, (game_height/-50)*52.5, 'reel5');
	reels[4].displayWidth=game.config.width*0.12; 
	reels[4].scaleY=reels[4].scaleX;
	reels[4].alpha = 0;
	reels[4].setMask(mask5);
	
	
	
	var fi = 0;
	for(var i=0; i<xfposes.length; i++){
		for(var n=0; n<yfposes.length; n++){
			figures[fi] = this.add.image(game_width/xfposes[i], game_height/yfposes[n], "figure" +  (Math.floor(Math.random() * 13) + 1) );
			figures[fi].displayWidth=game.config.width*0.12; 
			figures[fi].scaleY=figures[fi].scaleX;
			figures[fi].alpha = 1;
			fi++;
			
		}	
	}
	
	
	
	var screen_lines_positions = [[2.3, 2.49],[2.3, 6.43],[2.3, 1.55],[2.87, 1.9],[2.66, 4.4],[6.97, 6.71],[5.91, 1.51],[3.8, 7.43],[3.8, 1.47],[2.85, 3.26],[14.2, 1.47],[9.58, 2.82],[19.15, 3.06],[1.9, 3.33],[1.9, 2.07],[1.47, 3.04],[1.32, 1.68],[1.3, 2.99],[1.33, 1.97],[1.26, 2.52],[4.91, 2.37],[1.4, 2.37],[1.25, 1.44],[1.46, 2.03],[1.51, 4.23]];
	
	for(var i=0; i<25; i++){
	
		lines.push(this.add.image(game_width/2.3, game_height/2.5, 'line_'+(i+1)));
		lines[i].displayWidth=game.config.width*0.87; 
		lines[i].scaleY=lines[i].scaleX;
		lines[i].alpha = 0;
	
	}
	
	//set text settings
	line_text_style = { font: (game_width*0.018) + "px Arial", fill: "#fefaf1", align: "left" };
	
	for(var i=0; i<25; i++){
		
		lines_screens.push(this.add.image(game_width/screen_lines_positions[i][0], game_height/screen_lines_positions[i][1], 'line_screen'));
		lines_screens[i].displayWidth=game.config.width*0.1; 
		lines_screens[i].scaleY=lines_screens[i].scaleX;
		lines_screens[i].alpha = 0;
		
		lines_txts.push(this.add.text(lines_screens[i].x, lines_screens[i].y, "0", line_text_style));
		lines_txts[i].setOrigin(0.5);
		lines_txts[i].alpha = 0;
	
	}
	
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
	
	
	//Set background
	
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	
	
	//set text settings
	machine_text_style = { font: (game_width*0.018) + "px Arial", fill: "#fefaf1", align: "center" };
	
	//set texts positions
	bet_amount_text = this.add.text(game_width/11.29, game_height/1.15, current_bet, machine_text_style);
	lines_amount_text = this.add.text(game_width/3.5, game_height/1.15, 25, machine_text_style);
	balance_amount_text = this.add.text(game_width/1.145, game_height/5.2, current_balance, machine_text_style);
	freespins_amount_text = this.add.text(game_width/1.145, game_height/2.94, free_spins, machine_text_style);
	
	var initial_bet = current_bet * max_lines;
	if(free_spins > 0){
		initial_bet = "FREE";
	}	
	total_amount_text = this.add.text(game_width/1.145, game_height/2.05, initial_bet, machine_text_style);
	win_amount_text = this.add.text(game_width/1.145, game_height/1.57, "0", machine_text_style);
	
	info_btn = this.add.image(game_width/1.095, game_height/9.95, 'info').setInteractive();
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


	
	//set reels positions NOT USED
	/*reel_positions.push(game_height/0.615);
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
	reel_positions.push((game_height/-50)*52.5);*/
	
	
	
	//set bns size
	
	$('.game_btns_box').css("margin-top",(game_height*0.81)+"px");
	$('.sub_btn_box').width(game_width*0.95);
	
	$(".btn_big").css("width",(game_width*0.12)+"px");
	
	$("#betmax_btn").css("margin-left",(game_width*0.053)+"px");
	$("#spin_btn").css("margin-left",(game_width*0.28)+"px");
	
	
	$(".machine_text").css("font-size",(game_height*0.02)+"px");
	$(".machine_text").css("display","block");
	
	$(".btn_small").css("width",(game_width*0.13)+"px");
	$(".btn_small").css("height",(game_height*0.11)+"px");
	
	$("#coin_value_btn").css("margin-left",(game_width*0.019)+"px");
	$("#lines_btn").css("margin-left",(game_width*0.053)+"px");
	
	$(".btn_big").show();
	$(".btn_small").css("display","inline-block");
	
	
	//animations
	
	//disabled for speed
	/*var fmistframes = [];
	for(var i=0; i<=40; i+=2){
		fmistframes.push({ key: 'fmist_'+i });
	}
	this.anims.create({
        key: 'fmist',
        frames: fmistframes,
        frameRate: 10,
        repeat: -1,
		yoyo: true
    });
	
	fmist_animation = this.add.sprite(game_width/2, game_height/2, 'fmist_0');
	fmist_animation.displayWidth=game.config.width; 
	fmist_animation.scaleY=fmist_animation.scaleX;
	fmist_animation.alpha = 1; 
	
	fmist_animation.play('fmist');*/
	
	var aniframes = [];
	for(var i=2; i<=50; i+=2){
		aniframes.push({ key: 'animation_'+i });
	}
	this.anims.create({
        key: 'big_win',
        frames: aniframes,
        frameRate: 10,
        repeat: 0
    });
	
	win_animation = this.add.sprite(game_width/2, game_height/2, 'animation_2');
	win_animation.displayWidth=game.config.width*0.8; 
	win_animation.scaleY=win_animation.scaleX;
	win_animation.alpha = 0;
	
	
	
	
	
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	
	test_text = this.add.text(game_width/2, game_height/2, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
}

function show_paytable(){
	pay_table.alpha = 1;
	$("#betmax_btn").hide();
	$("#spin_btn").hide();	
}

function hide_paytable(){
	pay_table.alpha = 0;
	$("#betmax_btn").show();
	$("#spin_btn").show();	
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
	
	if(win_animation_fadeout && win_animation.alpha > 0){
		win_animation.alpha -= 0.05;
	}
	
	//disabled for speed
	/*if(mist_direction == "off"){
		fmist_animation.alpha -= 0.01;
	}else{
		fmist_animation.alpha += 0.01;
	}
	
	if(fmist_animation.alpha  < 0.3){
		mist_direction = "on";
	}else if(fmist_animation.alpha == 1){
		mist_direction = "off";
	}*/
	
	//lines glow
	for(var i = 0; i < visible_winning_lines.length; i++){
		if(visible_winning_lines_direction[visible_winning_lines[i]] == "off"){
			lines[visible_winning_lines[i]].alpha -= 0.05;
		}else{
			lines[visible_winning_lines[i]].alpha += 0.05;
		}
		
		if(lines[visible_winning_lines[i]].alpha  < 0.2){
			visible_winning_lines_direction[visible_winning_lines[i]] = "on";
		}else if(lines[visible_winning_lines[i]].alpha >= 0.9){
			visible_winning_lines_direction[visible_winning_lines[i]] = "off";
		}
	}
	
	//figures movements
	
	for(var i = 0; i < vibrating_figures.length; i++){ 
		if(vibrating_figures_direction[vibrating_figures[i]] == "off"){
			figures[vibrating_figures[i]].alpha -= 0.05;
		}else{
			figures[vibrating_figures[i]].alpha += 0.05;
		}
		
		if(figures[vibrating_figures[i]].alpha  < 0.2){
			vibrating_figures_direction[vibrating_figures[i]] = "on";
		}else if(figures[vibrating_figures[i]].alpha >= 0.9){
			vibrating_figures_direction[vibrating_figures[i]] = "off";
		}
	}


	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//spin reel
	for(var i = 0; i < reels_spinnings.length; i++){
		reel_spin(i)
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
	if(win_x5){
		win_animation_fadeout = false;
		win_animation.alpha = 1;
		win_animation.play('big_win');
		win_music.play(); 
		setTimeout("win_animation_fadeout = true;",2000);
	}
	win_amount_text.text = amount;
}

function update_balance(new_balance){
	current_balance = new_balance;
	balance_amount_text.text = Math.round((current_balance + Number.EPSILON) * 100) / 100;
	$("#balance_field").html(number_format(current_balance));
}

function update_free_spins(new_fps, uscreens){
	free_spins = new_fps;
	freespins_amount_text.text = free_spins;
	if(uscreens){update_screens();}
}


//freespins_amount_text

function show_winning_lines(){
	timer = 700;
	if(winning_lines[0] != ""){
		alpha_all_figures(0.3);
		var snd = 0;
		for(var i = 0; i < winning_lines.length; i++){
			var parts = winning_lines[i].split("|");
			var win_line_num = parts[0];
			var win_line_amount = parts[1];
			
			setTimeout("load_win_line("+(win_line_num-1)+",'"+win_line_amount+"',"+snd+")",timer);
			timer += 700;
			if(snd == 0){snd = 1;}else{snd = 0;}
			
		}
	}
	setTimeout("spin_lock = false;",timer)	
}

function alpha_all_figures(alpha){
	for(var i = 0; i< figures.length; i++){
		figures[i].alpha = alpha;
	}
}

function add_figure_to_vibrating(num){
	if(vibrating_figures.indexOf(num) == -1){
		vibrating_figures.push(num);
	}
}

function load_win_line(num,amount,sound){
	
	for(var i = 0; i< winning_lines_spositions[num+1].length; i++){
		figures[winning_lines_spositions[num+1][i]-1].alpha = 1;
		add_figure_to_vibrating(winning_lines_spositions[num+1][i]-1);
	} 
	
	lines[num].alpha = 1;
	//visible_winning_lines.push(num);
	lines_screens[num].alpha = 1;
	lines_txts[num].alpha = 1;
	lines_txts[num].text = amount;
	winning_sounds[sound].play();
}

function spin(){

	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2]  && !reels_spinnings[3]  && !reels_spinnings[4] && !spin_lock){
		
		vibrating_figures = new Array();
		for(var i = 0; i < figures.length; i++){
			figures[i].angle = 0;
		}
		
		visible_winning_lines = new Array();
		for(var i = 0; i < lines.length; i++){
			lines[i].alpha = 0;
			lines_screens[i].alpha = 0;
			lines_txts[i].alpha = 0;
			lines_txts[i].text = "";
		}
		
		if(total_bet <= current_balance){
			
			spin_lock = true;
			
			win_animation.alpha = 0;
			win_animation_fadeout = false;
			win_amount_text.text = "0";
			win_x5 = false;
			
			
			if(free_spins > 0){
				update_free_spins(free_spins-1,false);
			}else{
				update_balance(current_balance-total_bet);
			}
			
			//Provably fair
			if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}

			$.getJSON(core_url + "action=spin&bet=" + current_bet + "&lines=" + current_lines + "&pnr="+player_number+"&oskr=" + Math.random()  ,function(data){
				
				alpha_all_figures(1);
				
				if(!data.error){ //change the way to check error
				
					spinning_sound.play();
					winning_lines = data.winning_lines.split(",");
					win_x5 = data.win_x5;
					
					var positions = data.positions.split("|");
					for(var i = 0; i < positions.length; i++){
						figures[i].setTexture("figure"+(positions[i].replace("fig","")));
						figures[i].displayWidth=game.config.width*0.12; 
						figures[i].scaleY=figures[i].scaleX;
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
					setTimeout("update_free_spins("+data.free_spins+",true)",timer+300);
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

function reset_reels(){
	
	for(var i=0; i<reels.length; i++){
		reels[i].alpha = 1;
	}
	for(var i=0; i<figures.length; i++){
		figures[i].alpha = 0;
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
		show_winning_lines();
	}
}

function change_lines(set_max){
	
	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2]  && !reels_spinnings[3]  && !reels_spinnings[4]) {
		if(set_max){
			current_lines = max_lines;
		}else{
			if(current_lines+1 <= max_lines){
				current_lines++;
			}else{
				current_lines = 1;	
			}
		}
		update_screens();
	}
}

function change_bet(type){
	
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
	
		if((current_bet*1 + step) <= max_amount){
			current_bet = Math.round(((current_bet*1 + step) + Number.EPSILON) * 100) / 100;
		}else{
			current_bet = min_amount;
		}
		update_screens();
	
	}
}

function update_screens(){	
	bet_amount_text.text = current_bet;
	lines_amount_text.text = current_lines;
	total_bet = current_bet*current_lines;
	if(free_spins > 0){
		total_amount_text.text = "FREE";
	}else{
		total_amount_text.text = total_bet;
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
