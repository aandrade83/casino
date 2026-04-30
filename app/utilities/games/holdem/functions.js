//Set game vars
var core_url = 'https://play.casinogamesonline.com/utilities/games/holdem/action.php?gid='+gid+'&';

//general vars
var is_dealed = false;
var chips_values = [0.25,1,5,25,100];
var chips = new Array();
var new_chips = new Array();
var mooving_chips = new Array();
var placed_chips = new Array();
var winned_chips = new Array();
var open_bet_area = "ante";
var game_status = "";
var bet_areas_data = new Array();
var bet_areas = new Array();
var selected_chip = 1;
var unselected_chip_alpha = 0.4;
var chip_speed = 1000;
var cards_speed = 1500;
var test_text = null;
var area_bets_chip_count = new Array();
var area_bets_amount = new Array();
var area_bets_amount_text = new Array();
var player_cards_text = null;
var dealer_cards_text = null;
var winned_chips_text = null;
var player_hand_text = null;
var dealer_hand_text = null;
var clear_chips_to = "";
var winning_amount = 0;
var moving_cards = new Array();
var creation_cards = new Array();
var moving_dealer_chips = new Array();
var creation_dealer_chips = new Array();
var placed_cards = new Array();
var cards_positions = new Array();
var game_levels_str = new Array();
var shift_on = false;
var playing = false;
var move_cards_out = false;
var move_winned_chips_out = false;

var current_bet = 0;
var open_bet = 0;
var bet_amount_text = null;
var balance_amount_text = null;
var total_amount_text = null;
var win_amount_text = null;
var cards = new Array();
var is_mute = false;
var set_mute = false;
var after_game_clear = "";
var loading_game = false;

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
	this.load.image('background', '/utilities/games/holdem/imgs/back.jpg?v1');
	
	this.load.image('chip0.25', '/utilities/images/games/chips/chip0.25.png');
	this.load.image('chip1', '/utilities/images/games/chips/chip1.png');
	this.load.image('chip5', '/utilities/images/games/chips/chip5.png');	
	this.load.image('chip25', '/utilities/images/games/chips/chip25.png');
	this.load.image('chip100', '/utilities/images/games/chips/chip100.png');
	this.load.image('chip500', '/utilities/images/games/chips/chip500.png');
	this.load.image('area', '/utilities/games/baccarat/imgs/blank_area.png');
	
	//cards
	this.load.image('card_back', '/utilities/images/games/cards/card_back.png')
	this.load.image('card_flip', '/utilities/images/games/cards/card_back_flip.png')
	for(var i = 0; i < deck.length; i++){
		this.load.image('card_'+deck[i], '/utilities/images/games/cards/'+deck[i]+'.png');
	}
	//Sounds
	this.load.audio('card_flip_fx', '/utilities/games/holdem/sounds/cardflip.wav');
	
	
	
}

function create (){
	
	this.scale.pageAlignHorizontally = true;
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");
	
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
	
	//Set background
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX;
	
	//set text settings
	machine_text_style = { font: (game_width*0.0117) + "px Arial", fill: "#2aff00", align: "center" };
	
	//Buttons
	$("#btn_box").css("top",(game_height - (game_height*0.06)) + "px");	
	
	//chips
	var chip_separator = game.config.width*0.043;
	for(var i = 0; i < chips_values.length; i++){
		chips[chips_values[i]] = this.add.image(game_width/2.41+(chip_separator*i), game_height/1.12, 'chip'+chips_values[i]).setInteractive();
		chips[chips_values[i]].displayWidth=game.config.width*0.040; 
		chips[chips_values[i]].scaleY=chips[chips_values[i]].scaleX;
		chips[chips_values[i]].on('pointerup', function (pointer) {	
			var cvalue = this.texture.key.replace("chip",""); // get the chip value based on the texture name (ex: chip1)
			change_chip(cvalue*1, false);
		});		
	}	
	change_chip(1);
	
	//bet areas
	bet_areas_data.push({id: "ante", xpos: game_width/2.82, ypos: game_height/1.58, w: game.config.width*0.06, h:game.config.width*0.050});
	bet_areas_data.push({id: "flop", xpos: game_width/2.33, ypos: game_height/1.82, w: game.config.width*0.06, h:game.config.width*0.050});
	bet_areas_data.push({id: "turn", xpos: game_width/1.98, ypos: game_height/1.82, w: game.config.width*0.06, h:game.config.width*0.050});
	bet_areas_data.push({id: "river", xpos: game_width/1.72, ypos: game_height/1.82, w: game.config.width*0.06, h:game.config.width*0.050});
	
	for(var i = 0; i < bet_areas_data.length; i++){
		bet_areas[bet_areas_data[i].id] = this.add.image(bet_areas_data[i].xpos, bet_areas_data[i].ypos, 'area').setInteractive();
		bet_areas[bet_areas_data[i].id].name = bet_areas_data[i].id;
		bet_areas[bet_areas_data[i].id].displayWidth=bet_areas_data[i].w; 
		bet_areas[bet_areas_data[i].id].displayHeight=bet_areas_data[i].h; 
		bet_areas[bet_areas_data[i].id].alpha = 0.5;
		if(bet_areas_data[i].id == "ante"){bet_areas[bet_areas_data[i].id].on('pointerup', function (pointer) { place_bet(this.name); });}
	}
	
	clear_betareas_amounts();
	
	//card positions P1, D1, P2, D2, T1, T2, T3, T4, T5
	cards_positions.push({x:game_width/2.25, y:game_height/1.39});
	cards_positions.push({x:game_width/2.25, y:game_height/5.11});
	cards_positions.push({x:game_width/2.19, y:game_height/1.39});
	cards_positions.push({x:game_width/2.19, y:game_height/5.11});
	
	cards_positions.push({x:game_width/2.9, y:game_height/2.51});
	cards_positions.push({x:game_width/2.43, y:game_height/2.51});
	cards_positions.push({x:game_width/2.09, y:game_height/2.51});
	cards_positions.push({x:game_width/1.835, y:game_height/2.51});
	cards_positions.push({x:game_width/1.636, y:game_height/2.51});
	
	//set text settings
	machine_text_style = { font: (game_width*0.01) + "px Arial", fill: "#ffffff", align: "center" };
	
	player_cards_text = this.add.text(game_width/3.35, game_height/3.34, "", machine_text_style);
	dealer_cards_text = this.add.text(game_width/1.86, game_height/3.34, "", machine_text_style);
	winned_chips_text = this.add.text(game_width/4.05, game_height/1.36, "", machine_text_style);
	player_hand_text = this.add.text(game_width/2.01, game_height/1.38, "", machine_text_style);
	dealer_hand_text = this.add.text(game_width/2.01, game_height/5.24, "", machine_text_style);
	
	
	test_text = this.add.text(game_width/2.84, game_height/1.341, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
	this.input.keyboard.on('keydown_SHIFT', function(){shift_on = true;}, this);
	this.input.keyboard.on('keyup_SHIFT', function(){shift_on = false;}, this);
	
	//Game Levels
	game_levels_str.push("HIGH CARD");
	game_levels_str.push("PAIR");
	game_levels_str.push("TWO PAIR");
	game_levels_str.push("THREE OF A KIND");
	game_levels_str.push("STRAIGHT");
	game_levels_str.push("FLUSH");
	game_levels_str.push("FULL HOUSE");
	game_levels_str.push("FOUR OF A KIND");
	game_levels_str.push("STRAIGHT FLUSH");
	game_levels_str.push("ROYAL STRAIGHT FLUSH");
	
	
	//set bns size
	$(".btn_big").css("margin-top",(game_height*0.71)+"px");
	$(".btn_small").css("margin-top",(game_height*0.72)+"px");
	
	$(".btn_big").css("width",(game_width*0.07)+"px");
	$(".btn_big").css("height",(game_height*0.07)+"px");
	
	$(".btn_small").css("width",(game_width*0.04)+"px");
	$(".btn_small").css("height",(game_height*0.05)+"px");
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	sound_flip = this.sound.add("card_flip_fx", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	
	game_start();
	
}




function update (){
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;
	
	
	//move cards out
	if(move_cards_out){
		for(var p = 0; p<placed_cards.length; p++){
			this.physics.moveTo(placed_cards[p],game_width/2.07, game_height/18.41,cards_speed);
			var odistance = Phaser.Math.Distance.Between(placed_cards[p].x, placed_cards[p].y, game_width/2.07, game_height/18.41);
			if(odistance < game_width*0.04){	
				placed_cards[p].destroy();
				placed_cards.splice(p, 1);	
			}
		}
		if(placed_cards.length == 0){
			move_cards_out = false;
		}
	}
	
	
	//move winned chips out
	if(move_winned_chips_out){
		for(var p = 0; p<winned_chips.length; p++){
			this.physics.moveTo(winned_chips[p],game_width/2, game_height,chip_speed);
			var odistance = Phaser.Math.Distance.Between(winned_chips[p].x, winned_chips[p].y, game_width/2, game_height);
			if(odistance < game_width*0.04){	
				winned_chips[p].destroy();
				winned_chips.splice(p, 1);	
			}
		}
		if(winned_chips.length == 0){
			move_winned_chips_out = false;
		}
	}
	
	
	//move cards to table
	for(var r = 0; r<creation_cards.length; r++){
		var new_card = this.physics.add.image(game_width/1.28, game_height/3.6, 'card_back');
		new_card.displayWidth=game.config.width*0.065; 
		new_card.scaleY=new_card.scaleX;
		moving_cards.push({position:creation_cards[r].position,image:creation_cards[r].image,object:new_card});
		creation_cards.splice(r, 1);
	}
	
	
	for(var c = 0; c<moving_cards.length; c++){
		this.physics.moveTo(moving_cards[c].object,cards_positions[moving_cards[c].position].x, cards_positions[moving_cards[c].position].y,cards_speed);
		var flip_distance = Phaser.Math.Distance.Between(moving_cards[c].object.x, moving_cards[c].object.y, game_width/1.53, game_height/3.14);
		if(flip_distance < game_width*0.037){
			moving_cards[c].object.setTexture("card_flip");	
		}
		var distance = Phaser.Math.Distance.Between(moving_cards[c].object.x, moving_cards[c].object.y, cards_positions[moving_cards[c].position].x, cards_positions[moving_cards[c].position].y);
		if(distance < game_width*0.04){	
			placed_cards.push(moving_cards[c].object);
			moving_cards[c].object.setTexture(moving_cards[c].image);		
			moving_cards[c].object.body.reset(cards_positions[moving_cards[c].position].x, cards_positions[moving_cards[c].position].y);	
			moving_cards.splice(c, 1);	
			
		}
	}
	
	
	//move dealer chips to player	
	for(var r = 0; r<creation_dealer_chips.length; r++){
		var new_chip = this.physics.add.image(game_width/2, game_height/39.56, 'chip'+creation_dealer_chips[r]);
		new_chip.displayWidth = chips[creation_dealer_chips[r]].displayWidth;
		new_chip.displayHeight = chips[creation_dealer_chips[r]].displayHeight;
		moving_dealer_chips.push(new_chip);
		creation_dealer_chips.splice(r, 1);
	}
	
	for(var c = 0; c<moving_dealer_chips.length; c++){
		var distanser = (winned_chips.length) * (game_width*0.001);
		this.physics.moveTo(moving_dealer_chips[c],game_width/3.87, game_height/1.43,chip_speed);
		var distance = Phaser.Math.Distance.Between(moving_dealer_chips[c].x, moving_dealer_chips[c].y, game_width/3.87, game_height/1.43);
		if(distance < game_width*0.04){	
			winned_chips.push(moving_dealer_chips[c]);
			moving_dealer_chips[c].body.reset(game_width/3.87, game_height/1.43-distanser);
			moving_dealer_chips.splice(c, 1);
			
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
				place_bet(this.area);
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
	
	
	
	//clear chips from table
	if(clear_chips_to != ""){
		if(clear_chips_to == "player"){
			for(var i=0; i<placed_chips.length; i++){
				placed_chips[i].chip.y += game_width*0.01;
				if(placed_chips[i].chip.y > game_height + (placed_chips[i].chip.displayHeight)*2){
					placed_chips[i].chip.destroy();
					placed_chips.splice(i,1);
				}
			}
			if(placed_chips.length == 0){clear_chips_to = "";}
		}else if(clear_chips_to == "dealer"){
			for(var i=0; i<placed_chips.length; i++){
				//if(winning_areas != placed_chips[i].area){
					placed_chips[i].chip.y -= game_width*0.01;
					if(placed_chips[i].chip.y < (placed_chips[i].chip.displayHeight)*-2){
						placed_chips[i].chip.destroy();
						placed_chips.splice(i,1);
					}
				//}
			}
			if(placed_chips.length == 0){clear_chips_to = "";}
		}
	}
	
}

function clear_betareas_amounts(){
	for(var i = 0; i < bet_areas_data.length; i++){
		area_bets_chip_count[bet_areas_data[i].id] = 0;
		area_bets_amount[bet_areas_data[i].id] = 0;
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
	$("#balance_field").html(number_format(current_balance));
}

function deal_card(position, card){
	creation_cards.push({position:position,image:card});
	sound_flip.play();
}

function place_bet(area){
	
	if((!playing || area != "ante") && (open_bet_area == area || loading_game)){
		$("#btn_rebet").hide(); $("#btn_rebet_spin").hide();
		if(/*current_bet+*/selected_chip <= current_balance){//not include current bet because current balance already have current bet deducted
			if(area_bets_amount[area]+selected_chip <= max_amount){
			
				if(area == "ante" && !loading_game){$("#btn_clear").slideDown(500);}
				if(current_bet+selected_chip >= min_amount && area == "ante" && !loading_game){$("#btn_spin").slideDown(500);}
				current_bet += selected_chip;
				adjust_balance(selected_chip*-1);
				area_bets_chip_count[area] ++;
				area_bets_amount[area] += selected_chip;
				new_chips.push({value:selected_chip, area:area});
			
			}else{
				alert("The maximum bet on this field is " + currency_symbol + max_amount);
			}
		}else{
			fire_not_enough_balance("Not enough balance");
		}
	}
}

function adjust_balance(amount){
	current_balance += amount;
	$("#balance_field").html(number_format(current_balance));
}

function refresh_balance(){
	$.getJSON(core_url + "action=balance",function(data){
		if(!data.error){
			current_balance = data.balance - current_bet;
			update_balance_box();
		}
	});		
}

function update_balance_box(){
	$("#balance_field").html(number_format(current_balance));
}

function clear_table(direction){
	
	if(after_game_clear != ""){direction = after_game_clear; after_game_clear = "";}
	
	playing = false;
	hide_message();
	move_cards_out = true;
	move_winned_chips_out = true;
	current_bet = 0;
	clear_betareas_amounts();
	clear_bets(direction);
	$(".game_btn").hide(500);
	player_cards_text.text = "";
	dealer_cards_text.text = "";
	winned_chips_text.text = "";
	player_hand_text.text = "";
	dealer_hand_text.text = "";
	refresh_balance();
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

function game_start(){
	$.getJSON(core_url + "action=start",function(data){
		if(!data.error){
			if(data.has_started_game){
				
				playing = true;
				is_dealed = true;
				loading_game = true;
				
				if(data.pf){ 
					$("#pf_player_num").val(data.pf.lpnr);
					$("#pf_player_num").prop("readonly", true);
					$("#pf_n_shash").val(data.pf.lxhs);
				}
				
				current_bet = (data.ante_bet*1) + (data.call_bet*1) + (data.turn_bet*1) + (data.river_bet*1);
				open_bet = data.ante_bet;
				game_status = data.game_status;
				
				var bets = new Array();
				bets.push({area:"ante",amount:data.ante_bet});
				bets.push({area:"flop",amount:data.call_bet});
				bets.push({area:"turn",amount:data.turn_bet});
				bets.push({area:"river",amount:data.river_bet});
				
				for(var x = 0; x<bets.length; x++){
					if(bets[x].amount > 0){
						var bet_chips = get_amount_chips_list(bets[x].amount);
						for(var i = 0; i<bet_chips.length; i++){
							change_chip(bet_chips[i]);
							place_bet(bets[x].area);
						}
					}
				}
				
				$(".game_btn").hide();
				if(game_status == "dealed"){
					open_bet_area = "flop";					
					$("#btn_call").show(500); 
					$("#btn_fold").show(500);
				}else if(game_status == "called"){
					open_bet_area = "turn";
					$("#btn_bet").show(500);
					$("#btn_check").show(500);
				}if(game_status == "turned"){
					open_bet_area = "river";
					$("#btn_bet").show(500);
					$("#btn_check").show(500);
				}
				
				
				
				
				var current_cards = data.cards.split(",");
				var timer = 0;
				for(var i = 0; i<current_cards.length; i++){
					setTimeout("deal_card("+i+",'card_"+current_cards[i]+"');",timer);	
					timer += 500;
				}
				
				loading_game = false;
				
			}else{
				
				//for testing UI place code here
				
					
			}
		}else{
			alert("There was a problem: " + data.msg);
		}
	});
}

function rebet(deal){
	clear_table("player");
	if(deal){loading_game = true;}
	setTimeout("place_rebet("+deal+");",1500);
	if(deal){setTimeout("loading_game = false; deal();",2500);}
}

function place_rebet(deal){
	var pre_chips = get_amount_chips_list(open_bet);
	for(var e = 0; e<pre_chips.length; e++){
		change_chip(pre_chips[e]);
		place_bet("ante");
	}
}


function deal(){
	
	
	if(!is_dealed){
		if(current_bet <= current_balance){
			
			$(".game_btn").hide(500);
					
			//Provably fair
			if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
			$("#pf_player_num").prop("readonly", true);
			
			$.getJSON(core_url + "action=deal&bet=" + current_bet + "&pnr="+player_number ,function(data){
				if(!data.error){ //change the way to check error
				
					update_balance(data.balance);
					is_dealed = true;
					playing = true;
					game_status = data.game_status;
					open_bet = current_bet;
					open_bet_area = "flop";
					
					var player_cards = data.cards.split(",");
					setTimeout("deal_card(0,'card_"+player_cards[0]+"');",1);
					setTimeout("deal_card(1,'card_back');",500);
					setTimeout("deal_card(2,'card_"+player_cards[1]+"');",1000);
					setTimeout("deal_card(3,'card_back');",1500);
					
					setTimeout('$("#btn_call").show(500); $("#btn_fold").show(500);',2000);

					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
				
		}else{
			alert("Not enough balance");	
		}
		
	}
}

function call(){
	
	if(is_dealed && game_status == 'dealed'){
		if((open_bet*2) <= current_balance){
			$(".game_btn").hide(500);
			
			var call_bet_chips = get_amount_chips_list(open_bet*2);
			for(var i = 0; i<call_bet_chips.length; i++){
				change_chip(call_bet_chips[i]);
				place_bet("flop");
			}
			
			$.getJSON(core_url + "action=call" ,function(data){
				
				if(!data.error){ //change the way to check error
				
					update_balance(data.balance);
					game_status = data.game_status;
					open_bet_area = "turn";
					
					var table_cards = data.cards.split(",");
					setTimeout("deal_card(4,'card_"+table_cards[0]+"');",1);
					setTimeout("deal_card(5,'card_"+table_cards[1]+"');",500);
					setTimeout("deal_card(6,'card_"+table_cards[2]+"');",1000);
					setTimeout("player_hand_text.text = '"+game_levels_str[data.best_player_game]+"';",1500);
					setTimeout('$("#btn_bet").show(500); $("#btn_check").show(500);',1500);

					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
				
		}else{
			alert("Not enough balance");	
		}
		
	}
}

function next_move(bet){
	if(game_status == "called"){
		turn(bet);
	}else if(game_status == "turned"){
		river(bet);
	}
}


function turn(bet){
	
	if(is_dealed && game_status == 'called'){
		if(!bet || open_bet <= current_balance){
			$(".game_btn").hide(500);
			
			if(bet){
				var call_bet_chips = get_amount_chips_list(open_bet);
				for(var i = 0; i<call_bet_chips.length; i++){
					change_chip(call_bet_chips[i]);
					place_bet("turn");
				}
			}
			
			$.getJSON(core_url + "action=turn&bet=" + bet ,function(data){
				
				if(!data.error){ //change the way to check error
				
					update_balance(data.balance);
					game_status = data.game_status;
					open_bet_area = "river";
					
					deal_card(7,'card_'+data.card);
					setTimeout("player_hand_text.text = '"+game_levels_str[data.best_player_game]+"';",500);
					setTimeout('$("#btn_bet").show(500); $("#btn_check").show(500);',500);

					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
				
		}else{
			alert("Not enough balance");	
		}
		
	}
}

function river(bet){
	
	if(is_dealed && game_status == 'turned'){
		if(!bet || open_bet <= current_balance){
			$(".game_btn").hide(500);
			
			if(bet){
				var call_bet_chips = get_amount_chips_list(open_bet);
				for(var i = 0; i<call_bet_chips.length; i++){
					change_chip(call_bet_chips[i]);
					place_bet("river");
				}
			}
			
			$.getJSON(core_url + "action=river&bet=" + bet ,function(data){
				
				if(!data.error){ //change the way to check error
					
					pfdata = data.pf;
					update_pf_data();
					
					game_status = data.game_status;
					is_dealed = false;
					open_bet_area = "ante";
					after_game_clear = data.winner;
					
					deal_card(8,'card_'+data.card);	
					setTimeout("player_hand_text.text = '"+game_levels_str[data.best_player_game]+"';",500);				
					setTimeout('show_dealer_cards("'+data.dealer_hand+'");',1000);					
					setTimeout("dealer_hand_text.text = '"+game_levels_str[data.best_dealer_game]+"';",1500);
					//show hands txt next to each one
					
					setTimeout('after_play('+data.win_amount+', "'+data.balance+'", "'+data.game_result+'")',2000);
					
					
										
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
				
		}else{
			alert("Not enough balance");	
		}
		
	}
}

function after_play(win_amount, balance, result){
	update_balance(balance);
	$("#btn_clear").show(500);
	$("#btn_rebet").show(500); 
	$("#btn_rebet_spin").show(500); 
	if(result == 'win'){
		msg_txt = 'You Win '+currency_symbol+win_amount;
		var timer = 1;
		var prize_chips = get_amount_chips_list(Math.ceil(win_amount)); //
		for(var x=0; x<prize_chips.length; x++){
			setTimeout("creation_dealer_chips.push("+prize_chips[x]+");",timer + 200);	
			timer += 200;
		}
		setTimeout("winned_chips_text.text = '"+currency_symbol+(win_amount)+"';",timer+500);
	}else if(result == 'lose'){
		msg_txt = "You Lose";	
		clear_bets("dealer");
	}else if(result == 'push'){
		msg_txt = "Push";
	}
	
	
	update_pf_data();
	show_message(msg_txt);
}

function show_dealer_cards(dealer_hand){
	var dealer_cards = dealer_hand.split(",");
	var deal_index = 0;
	
	//show dealer on cards on back, because position is not exact in slow computers
	for(var i = 0; i<placed_cards.length; i++){
		if(placed_cards[i].texture.key == "card_back"){
			placed_cards[i].setTexture("card_"+dealer_cards[deal_index]);
			deal_index++;
		}
	}
	
	//this way is not exact in slow computers
	/*placed_cards[1].setTexture("card_"+dealer_cards[0]);
	placed_cards[3].setTexture("card_"+dealer_cards[1]);*/
}

function fold(){
		
	if(is_dealed && game_status == "dealed"){
			
		$(".game_btn").hide(500);
		
		$.getJSON(core_url + "action=fold" ,function(data){
			
			if(!data.error){ //change the way to check error

				pfdata = data.pf;
				update_pf_data();
			
				update_balance(data.balance);
				is_dealed = false;
				game_status = "";
				open_bet_area = "ante";
				
				clear_table("dealer");
				
				setTimeout('$("#btn_rebet").show(500); $("#btn_rebet_spin").show(500);',500);

				
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

function change_chip(value){
	selected_chip = value;
	
	for(var i = 0; i < chips_values.length; i++){
		chips[chips_values[i]].alpha = unselected_chip_alpha;	
	}
	chips[value].alpha = 1;
}

function get_amount_chips_list(amount){
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

