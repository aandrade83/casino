//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/classic_slot/action.php?gid='+gid+'&';

//general vars
var reel1 = null;
var reel2 = null;
var reel3 = null;
var reels = [reel1,reel2,reel3];
var reel1_move = 0;
var reel2_move = 0;
var reel3_move = 0;
var reels_moves = [reel1_move,reel2_move,reel3_move];
var reel_positions = new Array();
var reel1_spinning = false;
var reel2_spinning = false;
var reel3_spinning = false;
var reels_spinnings = [reel1_spinning,reel2_spinning,reel3_spinning];
var current_bet = 0;
var total_bet = 0;
var bet_amount_text = null;
var balance_amount_text = null;
var total_amount_text = null;
var win_amount_text = null;
var play_winning_sound = false;
var play_spinning_sound = false;
var back_sound = null;
var spin_lock = false;
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
	this.load.image('background', '/utilities/games/classic_slot/imgs/back.png?v4');
	this.load.image('reel', '/utilities/games/classic_slot/imgs/reel.png?v2');
	this.load.image('reel2', '/utilities/games/classic_slot/imgs/reel2.png?v2');
	this.load.image('reel3', '/utilities/games/classic_slot/imgs/reel3.png?v2');
	this.load.image('reel_back', '/utilities/games/classic_slot/imgs/reel_back.jpg?v2');
	//Sounds
	this.load.audio('spin_sound', '/utilities/games/classic_slot/sounds/Slot_Spin.mp3');
	this.load.audio('winning_sound', '/utilities/games/classic_slot/sounds/winning.mp3');
	this.load.audio('background_sound', '/utilities/games/classic_slot/sounds/sound_back.mp3');
	
	
	
}

function create (){
	
	this.scale.pageAlignHorizontally = true;
	
	current_bet = min_amount;
	
	//back sound
	back_sound = this.sound.add("background_sound", {mute: false,volume: 0.2,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	back_sound.play();
	
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");
	
	//Set background
	var reel_back = this.add.image(game_width/2, game_height/2, 'reel_back');
	reel_back.displayWidth=game.config.width*0.62; 
	reel_back.scaleY=reel_back.scaleX;
	
	reels[0] = this.add.image(game_width/2.58, game_height/-4, 'reel');
	reels[0].displayWidth=game.config.width*0.098; 
	reels[0].scaleY=reels[0].scaleX;
	
	reels[1] = this.add.image(game_width/2.02, game_height/-4, 'reel2');
	reels[1].displayWidth=game.config.width*0.098; 
	reels[1].scaleY=reels[1].scaleX;
	
	reels[2] = this.add.image(game_width/1.65, game_height/-4, 'reel3');
	reels[2].displayWidth=game.config.width*0.098; 
	reels[2].scaleY=reels[2].scaleX;
	
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	//set text settings
	machine_text_style = { font: (game_width*0.0117) + "px Arial", fill: "#2aff00", align: "center" };
	
	//set texts positions
	bet_amount_text = this.add.text(game_width/2.84, game_height/1.341, current_bet, machine_text_style);
	balance_amount_text = this.add.text(game_width/2.87, game_height/1.47, current_balance, machine_text_style);
	total_amount_text = this.add.text(game_width/2.1, game_height/1.47, "0", machine_text_style);
	win_amount_text = this.add.text(game_width/1.63, game_height/1.47, "0", machine_text_style);
	
	//set reels positions
	reel_positions.push(game_height/-4);
	reel_positions.push(game_height/-11);
	reel_positions.push((game_height/-50)*-3.5);
	reel_positions.push((game_height/-50)*-11.5);
	reel_positions.push((game_height/-50)*-19.5);
	reel_positions.push((game_height/-50)*-27.5);
	reel_positions.push((game_height/-50)*-35);
	reel_positions.push((game_height/-50)*-42.5);
	reel_positions.push((game_height/-50)*-50.5);
	reel_positions.push((game_height/-50)*-58.5);
	
	//set bns size
	$(".btn_big").css("margin-top",(game_height*0.71)+"px");
	$(".btn_small").css("margin-top",(game_height*0.72)+"px");
	
	$("#betone_btn").css("margin-left",(game_width*0.45)+"px");
	$("#betone_two").css("margin-left",(game_width*0.535)+"px");
	$("#betone_max").css("margin-left",(game_width*0.615)+"px");
	
	$("#betone_min").css("margin-left",(game_width*0.29)+"px");
	$("#betone_plus").css("margin-left",(game_width*0.4)+"px");
	
	$(".btn_big").css("width",(game_width*0.07)+"px");
	$(".btn_big").css("height",(game_height*0.07)+"px");
	
	$(".btn_small").css("width",(game_width*0.04)+"px");
	$(".btn_small").css("height",(game_height*0.05)+"px");
	
	$(".machine_text").css("font-size",(game_height*0.02)+"px");
	$(".machine_text").css("display","block");
	
	
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	
}


function update (){
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//spin reel
	for(var i = 0; i < reels_spinnings.length; i++){
		reel_spin(i)
	}
	
	//play sounds
	if(play_winning_sound){
		this.sound.play("winning_sound");
		play_winning_sound = false;
	}
	if(play_spinning_sound){
		this.sound.play("spin_sound");
		play_spinning_sound = false;
	}
	
	
}

function reel_spin(num){
	if(reels_spinnings[num]){
		reels_moves[num]++
		if(reels_moves[num] == 30){
			reels[num].y = game_height/-4;
			reels_moves[num] = 0;
		}else{
			reels[num].y += game_width*0.026;
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
	if(amount > 0){
		play_winning_sound = true;
	}
	win_amount_text.text = amount;
}

function update_balance(new_balance){
	current_balance = new_balance;
	balance_amount_text.text = current_balance;
	$("#balance_field").html(number_format(current_balance));
}

function spin(bet_times){
	
	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2] && !spin_lock){
		
		total_bet = current_bet*bet_times;
		
		if(total_bet <= current_balance){
			
			spin_lock = true;
			
			total_amount_text.text = total_bet;
			win_amount_text.text = "0";
			update_balance(current_balance-total_bet);
			
			//Provably fair
			if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
		
		
			$.getJSON(core_url + "action=spin&bet=" + total_bet + "&pnr="+player_number+"&oskr=" + Math.random()  ,function(data){
				
				console.log(data);
				
				if(!data.error){ //change the way to check error
				
					play_spinning_sound = true;	
					var positions = data.positions.split(",");
					
					var timer = 1000;
					for(var i = 0; i < reels_spinnings.length; i++){
						reels_spinnings[i]  = true;
						reels[i].y = game_height/-4;
						setTimeout("stop_reel("+i+", "+positions[i]+");",timer);
						timer += 500;
					}
					
					pfdata = data.pf;
					
					setTimeout("update_win_amount("+data.win_amount+")",timer-400);	
					setTimeout("update_balance("+data.balance+")",timer-400);
					setTimeout("update_pf_data()",timer-400);				
				
					
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
		$("#pf_player_num").val(getRandomInt(1000000,1999999));
		$("#pf_n_shash").val(pfdata.nxnr);
		$("#pf_l_shash").val(pfdata.lxhs);
		$("#pf_l_sec1").val(pfdata.lsc1);
		$("#pf_l_sec2").val(pfdata.lsc2);
		$("#pf_l_snum").val(pfdata.lxnr);
		$("#pf_l_pnum").val(pfdata.lpnr);
		$("#pf_l_resnum").val(pfdata.lpos);
	}
	
}


function stop_reel(num, position){
	reels_spinnings[num] = false;
	reels[num].y = reel_positions[position];
	if(num == 2){spin_lock = false;}
}

function change_bet(type){
	
	if(!reels_spinnings[0] && !reels_spinnings[1] && !reels_spinnings[2]){
	
		if(type == '+'){
			if((current_bet*1 + 0.25) <= max_amount){
				update_current_bet(current_bet*1 + 0.25);
			}
		}else if(type == '-'){
			if((current_bet*1 - 0.25) >= min_amount){
				update_current_bet(current_bet*1 - 0.25);
			}
		}
	
	}
}

function update_current_bet(new_amount){
	current_bet = new_amount;
	
	var extra_chars = 4-((current_bet+'').length);
	var bettext = "";
	for(var i = 0; i < extra_chars/2; i++){
		bettext += " ";
	}
	
	bettext += current_bet;
	
	bet_amount_text.text = bettext;
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
