//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/video_poker_af/action.php?gid='+gid+'&';

//general vars
var current_bet = 0;
var total_bet = 0;
var bet_amount_text = null;
var hand_amount_text = null;
var balance_amount_text = null;
var total_amount_text = null;
var win_amount_text = null;
var back_sound = null;
var win_sound = null;
var card_sound = null;
var cards = new Array();
var hold_btns = new Array();
var hold_btns_statuses = new Array(0,0,0,0,0);
var hold_btns_flags = new Array("","","","","");
var deal_btn = null;
var is_dealed = false;
var coins = 0;
var is_mute = false;
var set_mute = false;
var hands_amounts = new Array(1,5,10,25,50);
var hand_num_text = null;
var all_hands = null;
var current_hand_index = 0;
var arrowR = null;
var arrowL = null;

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
	this.load.image('background', 'utilities/games/video_poker_af/imgs/back.jpg?v2');
	this.load.image('card_back', 'utilities/games/video_poker_af/imgs/card_back.png');
	this.load.image('hold_off', 'utilities/games/video_poker_af/imgs/hold_off.png');
	this.load.image('hold_on', 'utilities/games/video_poker_af/imgs/hold_on.png?v4');
	this.load.image('arrow_l', 'utilities/games/video_poker_af/imgs/arrow_l.png?v4');
	this.load.image('arrow_r', 'utilities/games/video_poker_af/imgs/arrow_r.png?v4');
	for(var i = 0; i < deck.length; i++){
		this.load.image('card_'+deck[i], 'utilities/images/games/cards/'+deck[i]+'.png');
	}
	//Sounds
	this.load.audio('background_sound', 'utilities/games/video_poker_af/sounds/music.mp3');
	this.load.audio('winning_sound', 'utilities/games/video_poker_af/sounds/winner.mp3');
	this.load.audio('card_sound', 'utilities/games/video_poker_af/sounds/card.mp3');
	
	
	
}

function create (){
	
	this.scale.pageAlignHorizontally = true;
	
	current_bet = min_amount;
	
	//back sound
	back_sound = this.sound.add("background_sound", {mute: false,volume: 0.2,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	back_sound.play();
	
	win_sound = this.sound.add("winning_sound", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	card_sound = this.sound.add("card_sound", {mute: false,volume: 0.5,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");
	
	//Set background
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	arrowR = this.add.image(game_width/1.42, game_height/1.89, 'arrow_r').setInteractive();;
	arrowR.displayWidth=game.config.width*0.03; 
	arrowR.scaleY=arrowR.scaleX;
	arrowR.on('pointerup', function (pointer) {display_hand('+');});	
	arrowR.alpha = 0;
	
	arrowL = this.add.image(game_width/3.48, game_height/1.88, 'arrow_l').setInteractive();;
	arrowL.displayWidth=game.config.width*0.03; 
	arrowL.scaleY=arrowL.scaleX;
	arrowL.on('pointerup', function (pointer) {display_hand('-');});
	arrowL.alpha = 0;
	
	
	//cards
	cards[0] = this.add.image(game_width/2.83, game_height/1.88, 'card_back');
	cards[0].displayWidth=game.config.width*0.069; 
	cards[0].scaleY=cards[0].scaleX;
	
	cards[1] = this.add.image(game_width/2.355, game_height/1.88, 'card_back');
	cards[1].displayWidth=game.config.width*0.069; 
	cards[1].scaleY=cards[1].scaleX;
	
	cards[2] = this.add.image(game_width/2.015, game_height/1.88, 'card_back');
	cards[2].displayWidth=game.config.width*0.069; 
	cards[2].scaleY=cards[2].scaleX;
	
	cards[3] = this.add.image(game_width/1.76, game_height/1.88, 'card_back');
	cards[3].displayWidth=game.config.width*0.069; 
	cards[3].scaleY=cards[3].scaleX;
	
	cards[4] = this.add.image(game_width/1.562, game_height/1.88, 'card_back');
	cards[4].displayWidth=game.config.width*0.069; 
	cards[4].scaleY=cards[4].scaleX;
	
	//Hold BTNS
	hold_btns[0] = this.add.image(game_width/2.83, game_height/1.48, 'hold_off').setInteractive();
	hold_btns[0].displayWidth=game.config.width*0.052; 
	hold_btns[0].scaleY=hold_btns[0].scaleX;	
	hold_btns[0].alpha = 0;
	hold_btns[0].on('pointerup', function (pointer) {hold_card(0);});	
	
	hold_btns[1] = this.add.image(game_width/2.355, game_height/1.48, 'hold_off').setInteractive();
	hold_btns[1].displayWidth=game.config.width*0.052; 
	hold_btns[1].scaleY=hold_btns[1].scaleX;
	hold_btns[1].alpha = 0;
	hold_btns[1].on('pointerup', function (pointer) {hold_card(1);});	
	
	hold_btns[2] = this.add.image(game_width/2.015, game_height/1.48, 'hold_off').setInteractive();
	hold_btns[2].displayWidth=game.config.width*0.052; 
	hold_btns[2].scaleY=hold_btns[2].scaleX;
	hold_btns[2].alpha = 0;
	hold_btns[2].on('pointerup', function (pointer) {hold_card(2);});	
	
	hold_btns[3] = this.add.image(game_width/1.76, game_height/1.48, 'hold_off').setInteractive();
	hold_btns[3].displayWidth=game.config.width*0.052; 
	hold_btns[3].scaleY=hold_btns[3].scaleX;
	hold_btns[3].alpha = 0;
	hold_btns[3].on('pointerup', function (pointer) {hold_card(3);});	
	
	hold_btns[4] = this.add.image(game_width/1.562, game_height/1.48, 'hold_off').setInteractive();
	hold_btns[4].displayWidth=game.config.width*0.052; 
	hold_btns[4].scaleY=hold_btns[4].scaleX;
	hold_btns[4].alpha = 0;
	hold_btns[4].on('pointerup', function (pointer) {hold_card(4);});	
	
	
	//set text settings
	machine_text_style = { font: (game_width*0.0117) + "px Arial", fill: "#fff", align: "center" };
	machine_text_style2 = { font: (game_width*0.0117) + "px Arial", fill: "#034671", align: "center" };
	
	//set texts positions
	bet_amount_text = this.add.text(game_width/2.88, game_height/1.3, current_bet, machine_text_style2);
	hand_amount_text = this.add.text(game_width/2.02, game_height/1.3, '1', machine_text_style2);
	balance_amount_text = this.add.text(game_width/1.462, game_height/4.72, current_balance, machine_text_style);
	total_amount_text = this.add.text(game_width/1.462, game_height/3.62, "0", machine_text_style);
	win_amount_text = this.add.text(game_width/1.462, game_height/2.95, "0", machine_text_style);
	
	hand_num_text = this.add.text(game_width/2.12, game_height/2.45, "HAND #1", machine_text_style);
	
	test_text = this.add.text(game_width/9.63, game_height/1.09, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
	//set bns size
	$(".btn_big").css("margin-top",(game_height*0.74)+"px");
	$(".btn_small").css("margin-top",(game_height*0.75)+"px");
	
	$("#deal_btn").css("margin-left",(game_width*0.58)+"px");
	
	$("#betone_min").css("margin-left",(game_width*0.29)+"px");
	$("#betone_plus").css("margin-left",(game_width*0.38)+"px");
	
	$("#hand_min").css("margin-left",(game_width*0.43)+"px");
	$("#hand_plus").css("margin-left",(game_width*0.52)+"px");
	
	$(".btn_big").css("width",(game_width*0.12)+"px");
	$(".btn_big").css("height",(game_height*0.07)+"px");
	
	$(".btn_small").css("width",(game_width*0.04)+"px");
	$(".btn_small").css("height",(game_height*0.05)+"px");
	
	$(".machine_text").css("font-size",(game_height*0.02)+"px");
	$(".machine_text").css("display","block");
	
	coins = 1;
	update_total_bet();
	update_hands_txt();
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	game_start();
	
}




function update (){
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//turn hold btns on and off
	for(var i = 0; i < 5; i++){
		if(hold_btns_flags[i] != ""){
			hold_btns[i].setTexture('hold_' + hold_btns_flags[i]);
			hold_btns_flags[i] = "";
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

function game_start(){
	$.getJSON(core_url + "action=start",function(data){
		if(!data.error){
			if(data.has_started_game){
				
				is_dealed = true;
				
				if(data.pf){
					if(data.pf.lpnr && data.pf.lxhs){
						$("#pf_player_num").val(data.pf.lpnr);
						$("#pf_player_num").prop("readonly", true);
						$("#pf_n_shash").val(data.pf.lxhs);
					}
				}
				
				total_bet = data.total_bet*1;
				total_amount_text.text = total_bet;	
				coins = data.coins*1;
				update_hands_txt();
				
				update_current_bet(total_bet/coins);
				
				var current_cards = data.cards.split(",");
				var timer = 0;
				for(var i = 0; i<5; i++){
					setTimeout("cards["+i+"].setTexture('card_" + current_cards[i] + "');",timer);	
					setTimeout("hold_btns["+i+"].alpha = 1;",timer);		
					timer += 100;
				}
				
			}
		}else{
			alert("There was a problem: " + data.msg);
		}
	});
}

function display_hand(dir){
	if(dir == '+'){
		current_hand_index++;
		if(current_hand_index >= all_hands.length){current_hand_index = 0;}
	}else{
		current_hand_index--;
		if(current_hand_index < 0){current_hand_index = (all_hands.length-1);}
	}
	
	hand_num_text.text = "HAND #"+(current_hand_index+1);
	
	var current_cards = all_hands[current_hand_index].split(",");
	for(var i = 0; i<5; i++){
		if(!hold_btns_statuses[i]){
			cards[i].setTexture('card_'+current_cards[i]);
		}
	}
	
}

function display_arrows(disp){
	if(disp){
		arrowR.alpha = 1;
		arrowL.alpha = 1;	
	}else{
		arrowR.alpha = 0;
		arrowL.alpha = 0;	
	}
}

function change_bet(type){
	
	if(!is_dealed){
	
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
	
	update_total_bet();
}

function hold_card(position){
	if(is_dealed){
		var btn_status = hold_btns_statuses[position];	
		if(hold_btns_statuses[position]){
			hold_btns_statuses[position] = 0;
			hold_btns_flags[position] = "off";
		}else{
			hold_btns_statuses[position] = 1;
			hold_btns_flags[position] = "on";
		}
	}
}

function change_hand(dir){
	if(!is_dealed){
		
		var current_id = hands_amounts.indexOf(coins);
		
		if(dir == '+'){
			current_id ++;
		}else{
			current_id --;	
		}
		
		if(current_id >= hands_amounts.length){current_id = 0;}
		if(current_id < 0){current_id = (hands_amounts.length-1);}
		
		coins = hands_amounts[current_id];
	
		update_hands_txt();
		update_total_bet();
		
	}
}

function update_hands_txt(){
	hand_amount_text.text = coins;	
}

function update_total_bet(){
	total_bet = current_bet*coins;
	total_amount_text.text = total_bet;	
}

function deal(){
	if(!is_dealed){
		if(total_bet <= current_balance){
			
			display_arrows(false);
			hand_num_text.text = "HAND #1";
			current_hand_index = 0;
			
			//Provably fair
			if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
			$("#pf_player_num").prop("readonly", true);
			
			$.getJSON(core_url + "action=deal&bet=" + current_bet + "&coins=" + coins + "&pnr="+player_number ,function(data){
				
				if(!data.error){ //change the way to check error
				
					win_amount_text.text = "0";
					update_balance(current_balance-total_bet);	
				
					is_dealed = true;					
					update_balance(data.balance);
					var current_cards = data.cards.split(",");
					var timer = 0;
					for(var i = 0; i<5; i++){
						cards[i].setTexture('card_back');
						setTimeout("cards["+i+"].setTexture('card_" + current_cards[i] + "'); card_sound.play();",timer);	
						setTimeout("hold_btns["+i+"].alpha = 1;",timer);		
						timer += 100;
					}
					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
				
		}else{
			fire_not_enough_balance("Not enough balance");	
		}
	}else{
	
		var holds = hold_btns_statuses.join(",");

		$.getJSON(core_url + "action=draw&holds=" + holds ,function(data){
				
			if(!data.error){ //change the way to check error		
			
				pfdata = data.pf;
			
				is_dealed = false;
				update_balance(data.balance*1);
				win_amount_text.text = data.win_amount*1;
				
				all_hands = data.cards.split("|");
				console.log(all_hands);
				var current_cards = all_hands[0].split(",");
				var timer = 100;
				for(var i = 0; i<5; i++){
					if(!hold_btns_statuses[i]){
						cards[i].setTexture('card_back');
						setTimeout("cards["+i+"].setTexture('card_" + current_cards[i] + "'); card_sound.play();",timer);		
						timer += 100;
					}
					hold_btns[i].setTexture('hold_off');
					hold_btns[i].alpha = 0;
				}
				
				hold_btns_statuses = new Array(0,0,0,0,0);
				
				if(data.win_amount > 0){
					setTimeout("win_sound.play();", timer + 100);			
				}
				setTimeout("update_pf_data();", timer + 100);
				if(all_hands.length > 1){
					display_arrows(true);	
				}
				
			}else{
				alert("There was a problem: " + data.msg);
			}
		});	
		
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

function update_pf_data(){
	
	if(pfdata){
		$("#pf_player_num").prop("readonly", false);
		$("#pf_player_num").val(getRandomInt(0,100000000));
		$("#pf_n_shash").val(pfdata.nxnr);
		$("#pf_l_shash").val(pfdata.lxhs);
		$("#pf_l_sec1").val(pfdata.lsc1);
		$("#pf_l_sec2").val(pfdata.lsc2);
		$("#pf_l_snum").val(pfdata.lxnr);
		$("#pf_l_pnum").val(pfdata.lpnr);
		$("#pf_l_resnum").val(pfdata.lpos);
	}
	
}

