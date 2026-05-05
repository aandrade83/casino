//Set game vars
var selected_chip = 1;
var unselected_chip_alpha = 0.4;
var core_url = 'https://play.casinogamesonline.com/utilities/games/spanish_blackjack_contest/action.php?gid='+gid+'&';
var table_text_style = { font:"16px Arial", fill: "#f1f400", align: "center" };
var chip_speed = 2000;
var card_speed = 2000;
var chips_values = [0.25,1,5,25,100];
var original_card_width = 200;

//general vars
var chips = new Array();
var new_chips = new Array();
var placed_chips = new Array();
var temp_placed_chips = new Array();
var game_status = "";
var game_status2 = "";
var current_bet = 0;
var current_second_bet = 0;
var previous_bet = 0;
var test_text = null;
var bet_amount_text = null;
var bet_amount_text2 = null;
var win_amount_text = null;
var dealer_hand_text = null;
var player_hand_text = null;
var player_hand_text2 = null;
var chip_scale = 1;
var card_scale = 1;
var chips_delimeter = null;
var chips_delimeter2 = null;
var dealer_chips_delimeter = null;
var flip_delimeter = null;
var pcard_delimeter1 = null;
var pcard_delimeter2 = null;
var dcard_delimeter1 = null;
var new_chips_count = 0;
var new_chips_count2 = 0;
var new_dealer_chips_count = 0;
var player_cards_count = 0;
var player_cards_count2 = 0;
var dealer_cards_count = 0;
var flags = new Array();
var dealing_cards = new Array();
var placed_cards = new Array();
var temp_placed_cards = new Array();
var dealing_card = null;
var player_hand_value = 0;
var dealer_hand_value = 0;
var game_finished = 0;
var game_finished2 = 0;
var dealer_hidden_card = "";
var is_mute = false;
var set_mute = false;
var arrow1 = null;
var arrow2 = null;
var player_cards_positions = new Array();
var player2_cards_positions = new Array();
var dealer_cards_positions = new Array();
var moving_cards = new Array();
var creation_cards = new Array();
var sound_flip = null;
var player1_hit_index = 0;
var player2_hit_index = 0;
var dealer_hit_index = 0;
var contest_logo = null;
var contest_logo_name = "";
var load_contest_logo = false;
var logo_card_marriage = new Array();
var info_btn = null;
var pay_table = null;

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
	//Background
	this.load.image('background', 'utilities/games/spanish_blackjack_contest/imgs/back.jpg?v=2');
	
	this.load.image('info', 'utilities/games/spanish_blackjack_contest/imgs/info.png');
	this.load.image('pay_table', 'utilities/games/spanish_blackjack_contest/imgs/paytable.png');
	
	//chips
	this.load.image('chip0.25', 'utilities/images/games/chips/chip0.25.png');
	this.load.image('chip1', 'utilities/images/games/chips/chip1.png');
	this.load.image('chip5', 'utilities/images/games/chips/chip5.png');	
	this.load.image('chip25', 'utilities/images/games/chips/chip25.png');
	this.load.image('chip100', 'utilities/images/games/chips/chip100.png');
	this.load.image('chip500', 'utilities/images/games/chips/chip500.png');
	//delimeter
	this.load.image('delimeter', 'utilities/images/games/line_delimeter.jpg')
	this.load.image('hdelimeter', 'utilities/images/games/h_delimeter.jpg')
	//cards
	this.load.image('card_back', 'utilities/images/games/cards/card_back.png')
	this.load.image('card_flip', 'utilities/images/games/cards/card_back_flip.png')
	for(var i = 0; i < deck.length; i++){
		this.load.image('card_'+deck[i], 'utilities/images/games/cards/'+deck[i]+'.png?v=2')
	}
	//Others
	this.load.image('arrow', 'utilities/games/spanish_blackjack_contest/imgs/green_arrow.png');
	//sounds
	this.load.audio('card_flip_fx', 'utilities/games/spanish_blackjack_contest/sounds/cardflip.wav');
	
}

function create (){
	
	//Responsive
	if(game_width > 1500){
		chip_scale = 0.7;
		table_text_style = { font:"16px Arial", fill: "#ffffff", align: "center" };
		card_scale = 0.6;
	}else if(game_width > 800){
		chip_scale = 0.5;
		table_text_style = { font:"14px Arial", fill: "#ffffff", align: "center" };
		card_scale = 0.4;
	}else{
		chip_scale = 0.3;
		table_text_style = { font:"12px Arial", fill: "#ffffff", align: "center" };
		card_scale = 0.3;
	}
	
	
	// Center game canvas on page
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
	
	//Set background
	var background = this.add.image(game_width/2, game_height/2, 'background');
	background.displayWidth=game.config.width; 
	background.scaleY=background.scaleX
	
	//hands arrows
	arrow1 = this.physics.add.image(game_width/3.5, game_height/2.5, 'arrow');
	arrow1.displayWidth=game.config.width*0.02; 
	arrow1.scaleY=arrow1.scaleX;
	arrow1.alpha = 0;
	
	arrow2 = this.physics.add.image(game_width/2.05, game_height/1.7, 'arrow');
	arrow2.displayWidth=game.config.width*0.02; 
	arrow2.scaleY=arrow2.scaleX;
	arrow2.alpha = 0;
	
	
	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	
	//Place and set action of main chips
	var prev_chip = 0;
	for(var i = 0; i < chips_values.length; i++){
		if(prev_chip){
			var chip_separator_x = (chips[ chips_values[Object.keys(chips_values)[0]] ].width*chip_scale);
			var chip_separator_y = (chips[ chips_values[Object.keys(chips_values)[0]] ].width*chip_scale)*0.4;
			var xpos = chips[prev_chip].x + chip_separator_x;
			var ypos = chips[prev_chip].y - chip_separator_y;
		}else{
			var xpos = game_width-(game_width*0.25);
			var ypos = game_height-(game_height*0.15);
		}
		
		chips[chips_values[i]] = this.add.image(xpos, ypos, 'chip'+chips_values[i]).setInteractive();
		chips[chips_values[i]].setScale(chip_scale);
		chips[chips_values[i]].on('pointerup', function (pointer) {	
			var cvalue = this.texture.key.replace("chip",""); // get the chip value based on the texture name (ex: chip1)
			change_chip(cvalue*1, false);
		});	
		prev_chip = chips_values[i];
	}
	
	
	
	//Chips delimeter
	chips_delimeter = this.physics.add.image(game_width/2.5, 300, 'delimeter');
	chips_delimeter.alpha = 0;
	
	chips_delimeter2 = this.physics.add.image(game_width/5.1, 300, 'delimeter');
	chips_delimeter2.alpha = 0;
	
	dealer_chips_delimeter = this.physics.add.image(game_width/2, (game_height/4)*3, 'hdelimeter');
	dealer_chips_delimeter.alpha = 0;
	
	
	//Buttons
	$("#btn_box").css("top",(game_height - (game_height*0.06)) + "px");
	
	
	//cards	
	flip_delimeter = this.physics.add.image(game_width/1.7, 300, 'delimeter');
	flip_delimeter.alpha = 0;
	
	pcard_delimeter1 = this.physics.add.image(game_width/2.25, 300, 'delimeter');
	pcard_delimeter1.alpha = 0;
	
	pcard_delimeter2 = this.physics.add.image(game_width/4, 300, 'delimeter');
	pcard_delimeter2.alpha = 0;
	
	dcard_delimeter1 = this.physics.add.image(game_width/2.5, 300, 'delimeter');
	dcard_delimeter1.alpha = 0;
	
	sound_flip = this.sound.add("card_flip_fx", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});
	
	//card positions P1, D1, P2, D2, T1, T2, T3, T4, T5
	player_cards_positions.push({x:game_width/2.14, y:game_height/1.37});
	player_cards_positions.push({x:game_width/2.09, y:game_height/1.37});
	player_cards_positions.push({x:game_width/2.04, y:game_height/1.37});
	player_cards_positions.push({x:game_width/1.99, y:game_height/1.37});
	player_cards_positions.push({x:game_width/1.94, y:game_height/1.37});
	player_cards_positions.push({x:game_width/1.9, y:game_height/1.37});
	player_cards_positions.push({x:game_width/1.86, y:game_height/1.37});
	
	dealer_cards_positions.push({x:game_width/2.14, y:game_height/2.94});	
	dealer_cards_positions.push({x:game_width/2.09, y:game_height/2.94});
	dealer_cards_positions.push({x:game_width/2.04, y:game_height/2.94});
	dealer_cards_positions.push({x:game_width/1.99, y:game_height/2.94});
	dealer_cards_positions.push({x:game_width/1.94, y:game_height/2.94});
	dealer_cards_positions.push({x:game_width/1.9, y:game_height/2.94});
	dealer_cards_positions.push({x:game_width/1.86, y:game_height/2.94});
	
	player2_cards_positions.push({x:game_width/3.62, y:game_height/1.91});
	player2_cards_positions.push({x:game_width/3.49, y:game_height/1.91});
	player2_cards_positions.push({x:game_width/3.37, y:game_height/1.91});
	player2_cards_positions.push({x:game_width/3.24, y:game_height/1.91});
	player2_cards_positions.push({x:game_width/3.11, y:game_height/1.91});
	player2_cards_positions.push({x:game_width/3.01, y:game_height/1.91});
	player2_cards_positions.push({x:game_width/2.91, y:game_height/1.91});
	
	
	//Bets and cards text
	bet_amount_text = this.add.text((game_width/2.5), game_height-(game_height*0.20), "", table_text_style);
	bet_amount_text2 = this.add.text((game_width/5), game_height-(game_height*0.32), "", table_text_style);	
	win_amount_text = this.add.text((game_width/2.9), game_height-(game_height*0.23), "", table_text_style);	
	dealer_hand_text = this.add.text(game_width/2.29, game_height/4.27, "", table_text_style);	
	//dealer_hand_text.setShadow(2, 2, 'rgba(0,0,0,0.5)', 1);	
	player_hand_text = this.add.text(game_width/2.29, game_height/1.6, "", table_text_style);
	player_hand_text2 = this.add.text(game_width/4.07, game_height/2.41, "", table_text_style); 
	
	test_text = this.add.text(game_width/2.84, game_height/1.83, "X:0 Y:0", table_text_style);
	test_text.alpha = 0;
	
	
	info_btn = this.add.image(game_width/1.06, game_height/9.76, 'info').setInteractive();
	info_btn.displayWidth=game.config.width*0.025; 
	info_btn.scaleY=info_btn.scaleX;
	info_btn.on('pointerup', function (pointer) {	
		show_paytable();
	});	
	
	pay_table = this.add.image(game_width/2, game_height/2, 'pay_table').setInteractive();
	pay_table.displayWidth=game.config.width*0.50; 
	pay_table.scaleY=pay_table.scaleX;
	pay_table.alpha = 0;
	pay_table.on('pointerup', function (pointer) {	
		hide_paytable();
	});
	
	
	//start game
	game_start();
	
	

	
}


function update (){
	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	if(load_contest_logo){
		this.load.image(contest_logo_name, 'utilities/images/contests/'+contest_logo_name);
		this.load.start();		
		contest_logo_name = "";
		load_contest_logo = false;
	}
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;
	
	//create and move new player chips
	for(var i = 0; i < new_chips.length; i++){
		
		var is2 = false;
		var ctype = new_chips[i].type;
		
		if(new_chips[i].type == "player"){
			var start_x = chips[new_chips[i].value].x;
			var start_y = chips[new_chips[i].value].y;
			var dir_x = game_width/2.5;
			var dir_y = ((game_height/3)*2.3);
			var cdelimeter = chips_delimeter;
			var movechip = (5*new_chips_count);
		}else if(new_chips[i].type == "dealer"){
			var start_x = game_width/2.8;
			var start_y = 0;
			var dir_x = (game_width/2.8);
			var dir_y = ((game_height/3)*2.3);
			var cdelimeter = dealer_chips_delimeter;
			var movechip = (5*new_dealer_chips_count);
			new_dealer_chips_count++;
		}else if(new_chips[i].type == "player2"){
			var start_x = chips[new_chips[i].value].x;
			var start_y = chips[new_chips[i].value].y;
			var dir_x = game_width/4;
			var dir_y = ((game_height/3)*2);
			var cdelimeter = chips_delimeter2;
			var movechip = (5*new_chips_count2);
			is2 = true;
		}
		
		var new_chip = this.physics.add.image(start_x, start_y, 'chip' + new_chips[i].value);
		new_chip.setScale(chip_scale);
		
		this.physics.moveTo(new_chip,dir_x,dir_y,chip_speed);
		
		var collider = this.physics.add.overlap(new_chip, cdelimeter, function (chiponpoint){
			chiponpoint.body.stop();
			if(movechip > 0){
				new_chip.y -= movechip;
			}
			this.physics.world.removeCollider(collider);
		}, null, this);
		
		new_chips.splice(i,1);
		
		if(is2){new_chips_count2++;}
		else{new_chips_count++;}
			
		
		placed_chips.push({chip:new_chip,type:ctype});
		
	}
	
	
	//clear placed chips to player
	if(flags["clear_chips"]){
		for(var i = 0; i < placed_chips.length; i++){
			placed_chips[i].chip.y += chip_speed/60;
		}
		if(placed_chips[0].chip.y > game_height+500){
			flags["clear_chips"] = false;
			placed_chips = new Array();
			new_chips_count = 0;
			new_chips_count2 = 0;
			new_dealer_chips_count = 0;
		}
	}
	
	//clear placed chips to dealer
	if(flags["lose_chips"]){
		for(var i = 0; i < placed_chips.length; i++){
			placed_chips[i].chip.y -= chip_speed/60;
		}
		if(placed_chips[0].chip.y < -500){
			flags["lose_chips"] = false;
			placed_chips = new Array();
			new_chips_count = 0;
			new_chips_count2 = 0;
			new_dealer_chips_count = 0;
		}
	}
	
	
	//clear placed cards
	if(flags["clear_cards"]){
		for(var i = 0; i < placed_cards.length; i++){
			placed_cards[i].y -= card_speed/60;
		}

		if(placed_cards[placed_cards.length-1].y < -2000){
			flags["clear_cards"] = false;
			placed_cards = new Array();
			logo_card_marriage = new Array();
			player_cards_count = 0;
			player_cards_count2 = 0;
			dealer_cards_count = 0;
			player_hand_value = 0;
			dealer_hand_value = 0;			
		}
	}
	
	
	//Reveal dealer card
	if(flags["reveal_card"]){		
		placed_cards[1].setTexture("card_" + dealer_hidden_card);		
		flags["reveal_card"] = false;		
	}
	
	//move splited card
	if(flags["split_hand"]){		
		//placed_cards	
		
		this.physics.moveTo(placed_cards[2],player2_cards_positions[0].x,player2_cards_positions[0].x,card_speed);	
		var distance = Phaser.Math.Distance.Between(placed_cards[2].x, placed_cards[2].y, player2_cards_positions[0].x,player2_cards_positions[0].x);

		if(distance < game_width*0.04){			
			placed_cards[2].body.reset(player2_cards_positions[0].x,player2_cards_positions[0].y);	
			flags["split_hand"] = false;
		}							
	}
	
	
	//keep logo sicked in card
	if(logo_card_marriage.length){
		for(var g = 0; g<logo_card_marriage.length; g++){
			logo_card_marriage[g].logo.x = logo_card_marriage[g].card.x;
			logo_card_marriage[g].logo.y = logo_card_marriage[g].card.y;	
		}
	}
	
	
	//move cards to table NEW----------------
	for(var r = 0; r<creation_cards.length; r++){
		var new_card = this.physics.add.image(game_width/1.28, game_height/3.6, 'card_back');
		new_card.displayWidth=game.config.width*0.065; 
		new_card.scaleY=new_card.scaleX;
		moving_cards.push({posx:creation_cards[r].posx,posy:creation_cards[r].posy,image:creation_cards[r].image,object:new_card,logo:creation_cards[r].logo});
		creation_cards.splice(r, 1);
	}
	
	
	for(var c = 0; c<moving_cards.length; c++){
		this.physics.moveTo(moving_cards[c].object,moving_cards[c].posx, moving_cards[c].posy,card_speed);
		var flip_distance = Phaser.Math.Distance.Between(moving_cards[c].object.x, moving_cards[c].object.y, game_width/1.53, game_height/3.14);
		if(flip_distance < game_width*0.037){
			moving_cards[c].object.setTexture("card_flip");	
		}
		var distance = Phaser.Math.Distance.Between(moving_cards[c].object.x, moving_cards[c].object.y, moving_cards[c].posx, moving_cards[c].posy);
		if(distance < game_width*0.04){	
			placed_cards.push(moving_cards[c].object);
			moving_cards[c].object.setTexture(moving_cards[c].image);
			moving_cards[c].object.body.reset(moving_cards[c].posx, moving_cards[c].posy);	
			
			//add ace team logo
			if(moving_cards[c].logo != ''){
				var newlogo = this.physics.add.image(placed_cards[placed_cards.length-1].x, placed_cards[placed_cards.length-1].y, moving_cards[c].logo);
				newlogo.displayWidth=game.config.width*0.06; 
				newlogo.scaleY=newlogo.scaleX;
				
				logo_card_marriage.push({card:placed_cards[placed_cards.length-1], logo:newlogo});
				
			}
			
			moving_cards.splice(c, 1);	
			
		}
	}
	//----------------------------
	
	
	//dealing cards 
	for(var _i = 0; _i < dealing_cards.length; _i++){
		
		this.sound.play("card_flip_fx");

		if(dealing_cards[_i].type == "player"){
			var ydir = ((game_height/3)*2.3);
			player_cards_count++;
			var player_card_separator = (player_cards_count*((original_card_width*card_scale)*0.17));
		}else if(dealing_cards[_i].type == "dealer"){
			var ydir = ((game_height/3));
			dealer_cards_count++;
			var player_card_separator = (dealer_cards_count*((original_card_width*card_scale)*0.17));;
		}else if(dealing_cards[_i].type == "player2"){
			var ydir = ((game_height/2.35));
			player_cards_count2++;
			var player_card_separator = (player_cards_count2*((original_card_width*card_scale)*0.17));
		}
		

		dealing_card = this.physics.add.image(game_width-(game_width*0.25), game_height*0.25, "card_back");
		this.physics.moveTo(dealing_card,game_width/2.2,ydir,card_speed);
		dealing_card.setScale(card_scale);
		
		if(dealing_cards[_i].flip){
			
			var temp_card_data = dealing_cards[_i];
			var collider = this.physics.add.overlap(dealing_card, flip_delimeter, function (carend){
				
				dealing_card.setTexture("card_flip");
				this.physics.moveTo(dealing_card,game_width/2.2,ydir,card_speed);
				
				var collider2 = this.physics.add.overlap(dealing_card, temp_card_data.delimeter, function (carend){
					carend.body.stop();
								
					dealing_card.x += player_card_separator;
					dealing_card.setTexture("card_"+temp_card_data.card);					
					
					placed_cards.push(dealing_card);
					
					if(!flags["move_dealer_card_text"] && temp_card_data.type == "dealer"){
						//dealer_hand_text.y = dealing_card.y - ((original_card_width*card_scale)*0.85);	
						flags["move_dealer_card_text"] = true;
					}
					
					if(!flags["move_player_card_text"] && temp_card_data.type == "player"){
						//player_hand_text.y = dealing_card.y - ((original_card_width*card_scale)*0.70);	
						flags["move_player_card_text"] = true;
					}
					
					if(!flags["move_player_card_text2"] && temp_card_data.type == "player2"){
						//player_hand_text2.y = dealing_card.y - ((original_card_width*card_scale)*0.85);	
						flags["move_player_card_text2"] = true;
					}
					
					this.physics.world.removeCollider(collider2);
					
				}, null, this);
				
				this.physics.world.removeCollider(collider);
				
			}, null, this);
		
		}else{
		
			var collider = this.physics.add.overlap(dealing_card, dealing_cards[_i].delimeter, function (carend){
				
				carend.body.stop();				
				this.physics.world.removeCollider(collider);
				
			}, null, this);
			
		}
		
		dealing_cards.splice(_i,1);
		
	}
	
	
}

function game_start(){
	$.getJSON(core_url + "action=start",function(data){
		if(!data.error){
			if(data.has_started_game){
				
				
				if(data.pf && data.pf.lpnr && data.pf.lxhs){
					$("#pf_player_num").val(data.pf.lpnr);
					$("#pf_player_num").prop("readonly", true);
					$("#pf_n_shash").val(data.pf.lxhs);
				}
				
				move_balance(data.bet_amount*1);
				auto_bet(data.bet_amount);
				
				var timer = 0;
				var timer_base = 0;
				
				game_status = data.status;
				
				if(data.splited*1){
					
					move_balance(data.bet_amount2*1);
					place_split_bet(data.bet_amount2);
					
					timer_base = 3000;
					
					if(data.finished2*1){
						game_status2 = data.status2;
						var temp_player_cards = data.player_hand2.split(",");
						var temp_player_cards2 = data.player_hand.split(",");
						var hand_value1 = data.player_hand_value2;
						var hand_value2 = data.player_hand_value;
						var alpha_cards = "switch_hand_alpha("+(temp_player_cards.length-2)+");"; 
					}else{
						var temp_player_cards = data.player_hand.split(",");
						var temp_player_cards2 = data.player_hand2.split(",");	
						var hand_value1 = data.player_hand_value;
						var hand_value2 = data.player_hand_value2;				
						//var alpha_cards = "placed_cards[0].alpha = 0.2; placed_cards[5].alpha = 0.2; arrow1.alpha = 0.9;";
						var alpha_cards = "arrow1.alpha = 0.9;";
					}
					
					setTimeout('deal_card("card_'+temp_player_cards2[0]+'",player_cards_positions[0].x,player_cards_positions[0].y);',500);
					setTimeout('deal_card("card_back",dealer_cards_positions[0].x,dealer_cards_positions[0].y);',1000); //simulate flip to place card in same place as flipped ones
					setTimeout('deal_card("card_'+temp_player_cards[0]+'",player2_cards_positions[0].x,player2_cards_positions[0].y);',1500);
					setTimeout('deal_card("card_'+data.dealer_hand+'",dealer_cards_positions[1].x,dealer_cards_positions[1].y);',2000);
					setTimeout('deal_card("card_'+temp_player_cards[1]+'",player2_cards_positions[1].x,player2_cards_positions[1].y);',2500);
					setTimeout('deal_card("card_'+temp_player_cards2[1]+'",player_cards_positions[1].x,player_cards_positions[1].y);',3000);
					
					player1_hit_index = 2;
					player2_hit_index = 2;
					dealer_hit_index = 2;
					
					for(var i = 2; i < temp_player_cards.length; i++){
						timer = (500*(i-1))+timer_base;
						setTimeout('deal_card("card_'+temp_player_cards[i]+'",player2_cards_positions[player2_hit_index].x,player2_cards_positions[player2_hit_index].y); player2_hit_index++;',timer);
					}
					
					var temptimer = timer;
					if(temptimer == 0){temptimer = timer_base;}	

					for(var i = 2; i < temp_player_cards2.length; i++){
						timer = (500*(i-1))+temptimer;
						setTimeout('deal_card("card_'+temp_player_cards2[i]+'",player_cards_positions[player1_hit_index].x,player_cards_positions[player1_hit_index].y); player1_hit_index++;',timer);
					}
					
					if(timer == 0){timer = timer_base;}	
						
					setTimeout(alpha_cards,timer+1600);
					setTimeout('player_hand_text.text = "'+hand_value2+'";',timer+500);
					setTimeout('player_hand_text2.text = "'+hand_value1+'";',timer+500);					
							
					
				}else{
				
					timer_base = 2000;
					var temp_player_cards = data.player_hand.split(",");
					
					setTimeout('deal_card("card_'+temp_player_cards[0]+'",player_cards_positions[0].x,player_cards_positions[0].y);',500);
					setTimeout('deal_card("card_back",dealer_cards_positions[0].x,dealer_cards_positions[0].y);',1000); //simulate flip to place card in same place as flipped ones
					setTimeout('deal_card("card_'+temp_player_cards[1]+'",player_cards_positions[1].x,player_cards_positions[1].y);',1500);
					setTimeout('deal_card("card_'+data.dealer_hand+'",dealer_cards_positions[1].x,dealer_cards_positions[1].y);',2000);
					
					player1_hit_index = 2;
					dealer_hit_index = 2;
					
					
					for(var i = 2; i < temp_player_cards.length; i++){
						timer = (500*(i-1))+timer_base;
						setTimeout('deal_card("card_'+temp_player_cards[i]+'",player_cards_positions[player1_hit_index].x,player_cards_positions[player1_hit_index].y); player1_hit_index++;',timer);
					}
					
					if(timer == 0){timer = timer_base;}
					setTimeout('player_hand_text.text = "'+data.player_hand_value+'";',timer+500);
				
				}
				
				
				switch (game_status) {
				  case 'dealed':
						display_dealed_btns(data.can_split);
				  break;
				  case 'hitted':
						$("#btn_hit").slideDown(500);
						$("#btn_stand").slideDown(500);
						$("#btn_fold").slideDown(500);
						$("#btn_double").slideDown(500);
				  break;
				  case 'ask_insurance':
						setTimeout("$('#insurance').slideDown(500);",2000);
				  break;
				}
				
				
				
				
				setTimeout("scroll_down();",timer+500);
				
			}else{
				game_status = "place_bets";
				show_message("Place your bets!");
				setTimeout("scroll_down();",500);
			}
		}else{
			alert("There was a problem: " + data.msg);
		}
	});
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

function change_chip(value, is_auto){
	selected_chip = value;
	
	for(var i = 0; i < chips_values.length; i++){
		chips[chips_values[i]].alpha = unselected_chip_alpha;	
	}

	chips[value].alpha = 1;
	increase_bet(value, is_auto);
}

function increase_bet(amount, is_auto){
	if((game_status == "place_bets" || is_auto) && !flags["clear_chips"] && !flags["lose_chips"]){
		if((/*current_bet + */amount) <= current_balance){ //not include current bet because current balance already have current bet deducted
			if((current_bet + amount) <= max_amount || is_auto){
				current_bet += amount;
				previous_bet = current_bet;
				move_balance(amount*-1);
				bet_amount_text.text = currency_symbol + current_bet;	
				new_chips.push({id: random_number(1,10000), value: amount, type:"player"});
				//refresh_balance();
			}else{
				alert("The maximum bet on this table is " + currency_symbol + max_amount);
			}
			
			if(current_bet > 0 && game_status == "place_bets"){$("#btn_clear").slideDown(500);}
			if(current_bet >= min_amount && game_status == "place_bets"){$("#btn_deal").slideDown(500);}
		}else{
			fire_not_enough_balance("Not enough balance");
		}
		
	}
}

function increase_second_bet(amount){

	if(/*(game_status == "dealed") && */!flags["clear_chips"] && !flags["lose_chips"]){

			current_second_bet += amount;
			move_balance(amount*-1);
			bet_amount_text2.text = currency_symbol + current_second_bet;	
			new_chips.push({id: random_number(1,10000), value: amount, type:"player2"});		
		
	}
}




function clear_bets(to_player){
	if(game_status == "place_bets" || game_finished){
		if(!game_finished){move_balance(current_bet);}
		current_bet = 0;
		current_second_bet = 0;
		bet_amount_text.text = "";
		bet_amount_text2.text = "";
		win_amount_text.text = "";
		$("#btn_clear").slideUp(500);
		$("#btn_deal").slideUp(500);
		
		if(to_player){
			flags["clear_chips"] = true;
		}else{
			flags["lose_chips"] = true;	
		}
		refresh_balance();
	}
}

function move_balance(amount){
	current_balance = (current_balance*1) + amount;
	update_balance_box();
}

function refresh_balance(){
	$.getJSON(core_url + "action=balance",function(data){
		if(!data.error){
			current_balance = data.balance - current_bet;
			if(current_balance < 0 && game_status == "place_bets"){clear_bets(true);} //clear bets if all balance used in other part
			update_balance_box();
		}
	});		
}

function update_balance_box(){
	$("#balance_field").html(number_format(current_balance));
}

function display_end_btns(){
	$(".game_btn").hide();
	setTimeout('$("#btn_game_clear").slideDown(500)',1000);
	setTimeout('$("#btn_repeat").slideDown(500)',1000);
}

function display_dealed_btns(can_split){
	$(".game_btn").hide(0);
	$("#btn_fold").slideDown(500);
	$("#btn_double").slideDown(500);
	$("#btn_hit").slideDown(500);
	$("#btn_stand").slideDown(500);
	if(can_split){
		$("#btn_split").slideDown(500);
	}
}

function clear_game(){
	if(game_finished){
		arrow2.alpha = 0;
		dealer_hand_text.text = "";
		player_hand_text.text = "";
		player_hand_text2.text = "";
		flags["clear_cards"] = true;
		if(new_chips_count > 0){clear_bets(true);}
		reset_game_vars();
		refresh_balance();
		game_start();
		$(".game_btn").slideUp(500);
	}
}

function reveal_dealer_card(card){
	if(game_finished){
		dealer_hidden_card = card;
		flags["reveal_card"] = true;
		update_pf_data();
	}
}

function reset_game_vars(){
	game_status = "";
	current_bet = 0;
	current_second_bet = 0;
	new_dealer_chips_count = 0;
	player_cards_count = 0;
	player_cards_count2 = 0;
	dealer_cards_count = 0;
	player_hand_value = 0;
	dealer_hand_value = 0;
	game_finished = 0;	
	game_finished2 = 0;	
	dealer_hidden_card = "";
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

function credit_chips(amount){
	win_amount_text.text = currency_symbol + amount;	
	var chips_list = get_prize_chips_list(amount);
	
	for(var i = 0; i<chips_list.length; i++){
		//new_chips.push({id: random_number(1,10000), value: chips_list[i], type:"dealer"});	
		setTimeout('new_chips.push({id: '+random_number(1,10000)+', value: '+chips_list[i]+', type:"dealer"});	',(300*i));
	}
}

function rebet(){
	if(game_finished && !flags["clear_chips"] && !flags["lose_chips"]){
		clear_game();
		setTimeout('auto_bet(previous_bet)',1000);
	}
}

function auto_bet(amount){
	var pre_chips = get_prize_chips_list(amount);
	for(var i = 0; i<pre_chips.length; i++){
		setTimeout('change_chip('+pre_chips[i]+', true);',200*i);
	}
}

function place_split_bet(amount){
	var pre_chips = get_prize_chips_list(amount);
	for(var i = 0; i<pre_chips.length; i++){
		setTimeout('increase_second_bet('+pre_chips[i]+');',200*i);
	}
}

function deal_card(card,px,py,logo){
	if(!logo){logo = "";}
	creation_cards.push({image:card, posx:px, posy:py, logo:logo});
	sound_flip.play();
}

function deal(){
	if(game_status == "place_bets"){
		
		game_status = "dealed";	
		
		//Provably fair
		if($("#pf_player_num").val()){var player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
		
		$.getJSON(core_url + "action=deal&bet=" + current_bet  + "&pnr="+player_number,function(data){
			if(!data.error){ //change the way to check error 
			
			
				contest_logo_name = data.contest_logo;
				if(contest_logo_name != ""){load_contest_logo = true;}
			
				pfdata = data.pf;
				$("#pf_player_num").prop("readonly", true);
							
				previous_bet = current_bet;	
				hide_message();	
				
				$("#btn_clear").hide();
				$("#btn_deal").hide();
				
				current_balance = data.balance;
				
				//deal cards
				var temp_player_cards = data.player_hand.split(",");
				setTimeout('deal_card("card_'+temp_player_cards[0]+'",player_cards_positions[0].x,player_cards_positions[0].y);',1);
				setTimeout('deal_card("card_back",dealer_cards_positions[0].x,dealer_cards_positions[0].y);',500); //simulate flip to place card in same place as flipped ones
				setTimeout('deal_card("card_'+temp_player_cards[1]+'",player_cards_positions[1].x,player_cards_positions[1].y, "'+data.contest_logo+'");',1000);
				
				setTimeout('deal_card("card_'+data.dealer_hand+'",dealer_cards_positions[1].x,dealer_cards_positions[1].y);',1500);
				
				player1_hit_index = 2;
				dealer_hit_index = 2;
				
				setTimeout('player_hand_text.text = "'+data.player_hand_value+'";',2000);
				
				
				game_status = data.status;
				game_finished = data.finished;
				
				switch (game_status) {
				  case 'player_blackjack':
						setTimeout('show_message("PLAYER BLACKJACK<br /> You win '+currency_symbol + data.win_amount+'");',2000);
						setTimeout('reveal_dealer_card("'+data.dealer_hidden_card+'");',2000);
						setTimeout('dealer_hand_text.text = "'+data.dealer_hand_value+'";',2000);
						setTimeout('credit_chips('+data.win_amount+');',2000);
						setTimeout('update_balance_box();',2000);
						setTimeout('display_end_btns();',2500);				
				  break;
				  case 'push_blackjack':
						setTimeout('reveal_dealer_card("'+data.dealer_hidden_card+'");',2000);
						setTimeout('dealer_hand_text.text = "'+data.dealer_hand_value+'";',2000);
						setTimeout('show_message("BLACKJACK PUSH");',2000);
						setTimeout('display_end_btns();',2000);
						setTimeout('update_balance_box();',2000);
				  break;
				  case 'dealer_blackjack':
						setTimeout('reveal_dealer_card("'+data.dealer_hidden_card+'");',2000);
						setTimeout('dealer_hand_text.text = "'+data.dealer_hand_value+'";',2000);
						setTimeout('show_message("HOUSE BLACKJACK<br /> You lose '+currency_symbol + current_bet+'");',2000);
						setTimeout('update_balance_box();',2000);
						setTimeout('clear_bets(false)',3000);
						setTimeout('display_end_btns();',3500);
				  break;
				  case 'ask_insurance':
						setTimeout("$('#insurance').slideDown(500);",2000);
				  break;
				  default:
				  	if(game_finished){
						setTimeout('finish_game("'+data.dealer_hidden_card+'", "'+data.dealer_hand_value+'", "'+data.dealer_full_hand+'", "'+data.win_amount+'", '+data.balance+', 0, 0, 0);',2000);
					}else{
						setTimeout('display_dealed_btns('+data.can_split+');',2000);
					}
				  	
				  break;
				}
				
				
				
			}else{
				game_finished = true;
				game_status = "place_bets";
				alert("There was a problem: " + data.msg);
			}
		});	
	}	
}


function fold(){
	if(game_status == "dealed" || game_status == "hitted"){
		
		$(".game_btn").hide();
		
		$.getJSON(core_url + "action=fold",function(data){
			if(!data.error && data.finished){ //change the way to check error
			
				pfdata = data.pf;
			
				if(data.second_hand){
					game_status = data.status;
					current_balance = data.balance;
					update_balance_box();
					
					if(data.finished){
						switch_hand();
						display_dealed_btns(false);							
					}
					
				}else{
				
					current_balance = data.balance;
					game_status = data.status;
					game_finished = data.finished;
					
					finish_game(data.dealer_hidden_card, data.dealer_hand_value, data.dealer_hand, data.win_amount, data.balance, data.splited, data.win_amount2, data.bet_amount2);
					
				}			
				
			}else{
				alert("There was a problem: " + data.msg);
			}
		});	
	}	
}

function run_dealer_hits(cards){
	var timer = 700;
	for(var i = 0; i < cards.length; i++){
		setTimeout('deal_card("card_'+cards[i]+'",dealer_cards_positions[dealer_hit_index].x,dealer_cards_positions[dealer_hit_index].y); dealer_hit_index++;',(i+1)*timer);
	}
	return (i+1)*timer;
}

function finish_game(hidden_card, dealer_value, dealer_hand, win_amount, new_balance, splited, win_amount2, bet_amount2){	
	
	
	console.log(pfdata);
	
	var msg_function = "show_message";
	var timer = 0;
	
	setTimeout('reveal_dealer_card("'+hidden_card+'");',600);
	
	var temp_dealer_cards = dealer_hand.split(",").slice(2);
	if(temp_dealer_cards.length > 0){
		timer = run_dealer_hits(temp_dealer_cards);
	}
	
	setTimeout('dealer_hand_text.text = "'+dealer_value+'";',timer+600);
	
	setTimeout('current_balance = '+new_balance+';',timer+600);
	setTimeout('update_balance_box();',timer+600);
	
	for(var i=0;i<placed_cards.length;i++){
		setTimeout('placed_cards['+i+'].alpha = 1;',timer+600);
			
	}	
	
	var total_win = (win_amount*1) + (win_amount2*1);	
	if(total_win > 0){setTimeout('credit_chips('+total_win+');',timer+600);}
	
	
	if(splited*1){
		msg_function = "add_message";

		switch (game_status2) {
		  case 'busted':
				setTimeout('show_message("BUSTED<br /> You lose '+currency_symbol + bet_amount2+'");',timer+600);			
				//setTimeout('clear_second_bets(false);',timer+1000);		 // create the function					
		  break;
		  case 'lose':
				setTimeout('show_message("HOUSE WINS<br /> You lose '+currency_symbol + bet_amount2+'");',timer+600);			
				//setTimeout('clear_second_bets(false);',timer+1000);
									
		  break;
		  case 'dealer_bust':	
				setTimeout('show_message("DEALER BUSTED<br /> You win '+currency_symbol + win_amount2+'");',timer+600);				
		  break;
		  case 'win':
				setTimeout('show_message("You win '+currency_symbol + win_amount2+'");',timer+600);		
		  break;
		  case 'player_blackjack':
				setTimeout('show_message("PLAYER BLACKJACK<br /> You win '+currency_symbol + win_amount2+'");',timer+600);	
		  break;
		  case 'push':
				setTimeout('show_message("PUSH");',timer+600);	
		  break;
		  case "folded":
				setTimeout('show_message("PLAYER SURRENDER<br /> You get '+currency_symbol + win_amount2+'");',timer+600);			
				//if(!(splited*1)){setTimeout('clear_bets(false);',timer+1000);}
		  break;
		}
		setTimeout('add_message("<br />----------------------<br />");',timer+600);	
	}
	
	switch (game_status) {
		
	  case 'busted':
			setTimeout(msg_function + '("BUSTED<br /> You lose '+currency_symbol + current_bet+'");',timer+600);			
			if(!(splited*1)){setTimeout('clear_bets(false);',timer+1000);}					
	  break;
	  case 'lose':
			setTimeout(msg_function + '("HOUSE WINS<br /> You lose '+currency_symbol + current_bet+'");',timer+600);			
			if(!(splited*1)){setTimeout('clear_bets(false);',timer+1000);}
	  break;
	  case 'dealer_bust':	
			setTimeout(msg_function + '("DEALER BUSTED<br /> You win '+currency_symbol + win_amount+'");',timer+600);				
	  break;
	  case 'win':
			setTimeout(msg_function + '("You win '+currency_symbol + win_amount+'");',timer+600);		
	  break;
	  case 'player_blackjack':
			setTimeout(msg_function + '("PLAYER BLACKJACK<br /> You win '+currency_symbol + win_amount+'");',timer+600);
	  break;
	  case 'push':
			setTimeout(msg_function + '("PUSH");',timer+600);	
	  break;
	  case "folded":
	  		setTimeout(msg_function + '("PLAYER SURRENDER<br /> You get '+currency_symbol + win_amount+'");',timer+600);			
			//if(!(splited*1)){setTimeout('clear_bets(false);',timer+1000);}
	  break;
	}
	
	setTimeout('display_end_btns();',timer+1000);
	
}

function hit(){

	if(game_status == "dealed" || game_status == "hitted"){

		$.getJSON(core_url + "action=hit" ,function(data){
			if(!data.error){ //change the way to check error
			
				pfdata = data.pf;

				if(data.second_hand){
					
					game_status = data.status;
					//$("#btn_fold").slideUp(500);
					//$("#btn_double").slideUp(500);
					
					//deal card
					deal_card("card_"+data.new_card,player2_cards_positions[player2_hit_index].x,player2_cards_positions[player2_hit_index].y);
					player2_hit_index++;

					setTimeout('player_hand_text2.text = "'+data.player_hand_value+'";',500);
					
					if(data.finished){
						setTimeout('switch_hand();',1500);
						setTimeout('display_dealed_btns(false);',1500);					
					}
										
				}else{
				
					game_status = data.status;
					game_finished = data.finished;
					//$("#btn_fold").slideUp(500);
					$("#btn_split").slideUp(500);
					//$("#btn_double").slideUp(500);
					
					//deal card
					deal_card("card_"+data.new_card,player_cards_positions[player1_hit_index].x,player_cards_positions[player1_hit_index].y);
					player1_hit_index++;
					
					setTimeout('player_hand_text.text = "'+data.player_hand_value+'";',500);
					
					if(game_finished){
						finish_game(data.dealer_hidden_card, data.dealer_hand_value, data.dealer_hand, data.win_amount, data.balance, data.splited, data.win_amount2, data.bet_amount2);
					}
					
				}
				
				
			}else{
				alert("There was a problem: " + data.msg);
			}
		});	
	}	
}

function stand(){
	if(game_status == "dealed" || game_status == "hitted"){

		$(".game_btn").slideUp(500);

		$.getJSON(core_url + "action=stand" ,function(data){
			if(!data.error){ //change the way to check error
			
				console.log(data);
			
				pfdata = data.pf;
			
				if(data.second_hand){

					game_status = data.status;
					if(data.finished){
						switch_hand();
						display_dealed_btns(false);							
					}
				}else{
					game_status = data.status;
					game_finished = data.finished;
					if(game_finished){
						finish_game(data.dealer_hidden_card, data.dealer_hand_value, data.dealer_hand, data.win_amount, data.balance, data.splited, data.win_amount2, data.bet_amount2)
					}	
				}
				
				
			}else{
				alert("There was a problem: " + data.msg);
			}
		});	
	}
}

function switch_hand_alpha(second_count){
	/*placed_cards[0].alpha = 1; 
	placed_cards[5].alpha = 1;
	placed_cards[2].alpha = 0.2;
	placed_cards[4].alpha = 0.2;*/
	arrow1.alpha = 0;
	arrow2.alpha = 0.9;
	/*var count = 0;
	for(var i=0;i<placed_cards.length;i++){
		if(i > 5 && count < second_count){
			placed_cards[i].alpha = 0.2;
			count++;
		}	
	}*/
}

function switch_hand(){
	var temp_status = game_status2;
	game_status2 = game_status;
	game_status = temp_status;
	if(game_status == ""){game_status = "dealed";}
	switch_hand_alpha(100);
		
	$.getJSON(core_url + "action=game_data" ,function(data){
		
		console.log(data);

		if(!data.error){ //change the way to check error
		
			pfdata = data.pf;
		
			if(data.finished*1){
				game_status = data.status;
				game_finished = data.finished;
				$(".game_btn").slideUp(500);
				setTimeout('finish_game("'+data.dealer_hidden_card+'", "'+data.dealer_hand_value+'", "'+data.dealer_hand+'", "'+data.win_amount+'", '+data.balance+', '+data.splited+', "'+data.win_amount2+'", "'+data.bet_amount2+'");',500);			
			}
			
		}else{
			alert("There was a problem: " + data.msg);
		}
	});	
	
}

function double(){
	if(game_status == "dealed" || game_status == "hitted"){
		
		if(current_bet <= current_balance){
		
			$.getJSON(core_url + "action=double" ,function(data){
				if(!data.error){ //change the way to check error
				
					pfdata = data.pf;
				
					if(data.second_hand){
						
						//IF SECOND HAND
						place_split_bet(current_bet);
						game_status = data.status;
						
						//deal card
						setTimeout('deal_card("card_'+data.new_card+'",player2_cards_positions[player2_hit_index].x,player2_cards_positions[player2_hit_index].y); player2_hit_index++;',1000);
						setTimeout('player_hand_text2.text = "'+data.player_hand_value+'";',1500);
						
						if(data.finished){
							setTimeout('switch_hand();',2500);
							setTimeout('display_dealed_btns(false);',2500);							
						}
						
						
					}else{
						
						// IF MAIN HAND				
						auto_bet(current_bet);
						setTimeout('previous_bet = previous_bet/2;',1000); //maintain bet amount when rebet
						
						game_status = data.status;
						game_finished = data.finished;
						
						//deal card
						setTimeout('deal_card("card_'+data.new_card+'",player_cards_positions[player1_hit_index].x,player_cards_positions[player1_hit_index].y); player1_hit_index++;',1000);
						setTimeout('player_hand_text.text = "'+data.player_hand_value+'";',1500);
						
						if(game_finished){
							$(".game_btn").slideUp(500);
							setTimeout('finish_game("'+data.dealer_hidden_card+'", "'+data.dealer_hand_value+'", "'+data.dealer_hand+'", "'+data.win_amount+'", '+data.balance+', '+data.splited+', "'+data.win_amount2+'", "'+data.bet_amount2+'");',1500);
						}
						
								
					}
				
					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
		
		}else{
			fire_not_enough_balance("Not enough balance to double");	
		}
	}
}

function insurance(accepted){
	if(game_status == "ask_insurance"){
		if(current_bet/2 <= current_balance || !accepted){
			
			hide_message();
			if(accepted){move_balance((current_bet/2)*-1);}
			$.getJSON(core_url + "action=insurance&accepted=" + accepted ,function(data){
				if(!data.error){ //change the way to check error
					
					pfdata = data.pf;
					
					current_balance = data.balance;					
					game_status = data.status;
					game_finished = data.finished;
					
					if(game_finished){
						reveal_dealer_card(data.dealer_hidden_card);
						dealer_hand_text.text = data.dealer_hand_value;
						
						if(data.win_amount > 0){
							show_message("HOUSE BLACKJACK<br /> Insurance pays "+currency_symbol + data.win_amount);
						}else{
							show_message("HOUSE BLACKJACK<br /> You lose "+currency_symbol + current_bet);
							clear_bets(false);
						}
						
						update_balance_box();
						display_end_btns();	
					}else{
						display_dealed_btns(data.can_split);	
					}
					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});				
			
		}else{
			fire_not_enough_balance("Not enough balance to pay insurance");
		}
	}
}

function split_hand(){
	if(game_status == "dealed"){
		
		if(current_bet <= current_balance){
			
			$(".game_btn").slideUp(500);
		
			$.getJSON(core_url + "action=split" ,function(data){
				if(!data.error){ //change the way to check error
				
					pfdata = data.pf;
					
					contest_logo_name = data.contest_logo;
					if(contest_logo_name != ""){load_contest_logo = true;}
					
					setTimeout("contest_logo_name = '"+data.contest_logo2+"'; if(contest_logo_name != ''){load_contest_logo = true;}",1000);
					
					place_split_bet(current_bet);
					flags["split_hand"] = true;
					player_hand_text.text = "";
					
					player_cards_count = 1;
					player_cards_count2 = 1;
					game_status = data.status;
					game_status2 = data.status2;
					game_finished2 = data.finished2;
					
					var temp_player_cards = data.player_hand.split(",");
					var temp_player_cards2 = data.player_hand2.split(",");
					setTimeout('deal_card("card_'+temp_player_cards2[1]+'",player2_cards_positions[1].x,player2_cards_positions[1].y, "'+data.contest_logo+'");',1000);
					setTimeout('player_hand_text2.text = "'+data.player_hand_value2+'";',1000);
					setTimeout('deal_card("card_'+temp_player_cards[1]+'",player_cards_positions[1].x,player_cards_positions[1].y, "'+data.contest_logo2+'");',1500);
					setTimeout('player_hand_text.text = "'+data.player_hand_value+'";',1500);
					
					player1_hit_index = 2;
					player2_hit_index = 2;
					
					//setTimeout('placed_cards[0].alpha = 0.2; placed_cards[5].alpha = 0.2; arrow1.alpha = 0.9;',2500);
					setTimeout('arrow1.alpha = 0.9;',2500);
					setTimeout('display_dealed_btns(false);',2500);	
					
					if(data.finished){
						setTimeout('switch_hand();',2600);
					}
					
					
								
					
				}else{
					alert("There was a problem: " + data.msg);
				}
			});	
		
		}else{
			fire_not_enough_balance("Not enough balance to split");	
		}
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

function show_paytable(){
	pay_table.alpha = 1;
}

function hide_paytable(){
	pay_table.alpha = 0;
}
