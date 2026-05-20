//Set game vars
var core_url = '/utilities/games/keno/action.php?gid='+gid+'&';

//general vars
var current_bet = 0;
var total_bet = 0;
var bet_amount_text = null;
var balance_amount_text = null;
var total_amount_text = null;
var win_amount_text = null;
var spin_lock = false;
var music = null;
var sounds_fxs = new Array();
var is_mute = false;
var set_mute = false;

var winning_lines_spositions = new Array();
var pfdata = null;
var is_spinning = false;
var is_autoplay = false;
var is_picking = false;

var selected_numbers = new Array();

var spin_btn = null;
var clear_btn = null;
var pick_btn = null;
var auto_btn = null;
var main_txt = null;
var payable_levels_nums = new Array();
var payable_levels_vals = new Array();
var payable_levels_prices = new Array();
var paytable_light = null;

var paytable_positions = new Array();
var nums_positions = new Array();
var numbers = new Array();
var win_count = 0;

var big_win = false;
var finish_spin = false;
var last_win_amount = 0;
var last_received_balance = 0;
var music_started = false;


//calculate width
var game_width = $(window).width()*0.9;

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
	this.load.image('area', 'utilities/games/keno/imgs/blank_area.png');
	this.load.image('btn_play', 'utilities/games/keno/imgs/btn_play.png');
	this.load.image('btn_stop', 'utilities/games/keno/imgs/btn_stop.png');
	this.load.image('btn_auto', 'utilities/games/keno/imgs/btn_auto.png');
	this.load.image('btn_clear', 'utilities/games/keno/imgs/btn_clear.png');
	this.load.image('btn_pick', 'utilities/games/keno/imgs/btn_pick.png?v=3');
	this.load.image('paytable_light', 'utilities/games/keno/imgs/paytable_light.png');
	this.load.image('num_light_1', 'utilities/games/keno/imgs/num_light_1.png');
	this.load.image('num_light_2', 'utilities/games/keno/imgs/num_light_2.png');
	this.load.image('num_light_3', 'utilities/games/keno/imgs/num_light_3.png');
	
	//Sounds
	this.load.audio('music', 'utilities/games/keno/sounds/music.wav');
	this.load.audio('bip', 'utilities/games/keno/sounds/bip.wav');
	this.load.audio('bip_martch', 'utilities/games/keno/sounds/bip_martch.wav?v=2');
	this.load.audio('click', 'utilities/games/keno/sounds/click.mp3');
	this.load.audio('win', 'utilities/games/keno/sounds/win.wav');
	
}

function create (){
	
	//hide preload
	progressBar.alpha = 0;
	progressBox.alpha = 0;
	loadingText.alpha = 0;
	
	this.scale.pageAlignHorizontally = true;
	
	current_bet = min_amount;
	
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");
	
	//sounds
	music = this.sound.add("music", {mute: false,volume: 0.5,rate: 1,detune: 0,seek: 0,loop: true,delay: 0});
	music.play();
	sounds_fxs = {
		"bip": this.sound.add("bip", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}),
		"bip_martch": this.sound.add("bip_martch", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}),
		"click": this.sound.add("click", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0}),
		"win": this.sound.add("win", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0})
	};
	
	//btns
	spin_btn = this.add.image(game_width/1.145, game_height/1.6, 'btn_play').setInteractive();
	spin_btn.displayWidth=game.config.width*0.165; 
	spin_btn.scaleY=spin_btn.scaleX;
	spin_btn.alpha = 1;
	spin_btn.on('pointerup', function (pointer) { spin(false); });
	
	pick_btn = this.add.image(game_width/1.145, game_height/1.39, 'btn_pick').setInteractive();
	pick_btn.displayWidth=game.config.width*0.165; 
	pick_btn.scaleY=pick_btn.scaleX;
	pick_btn.alpha = 1;
	pick_btn.on('pointerup', function (pointer) { quick_pick(); });
	
	auto_btn = this.add.image(game_width/1.145, game_height/1.23, 'btn_auto').setInteractive();
	auto_btn.displayWidth=game.config.width*0.165; 
	auto_btn.scaleY=auto_btn.scaleX;
	auto_btn.alpha = 1;
	auto_btn.on('pointerup', function (pointer) { auto_play(); });
	
	clear_btn = this.add.image(game_width/1.145, game_height/1.1, 'btn_clear').setInteractive();
	clear_btn.displayWidth=game.config.width*0.165; 
	clear_btn.scaleY=clear_btn.scaleX;
	clear_btn.alpha = 1;
	clear_btn.on('pointerup', function (pointer) { clear_all_numbers(); });
	
	var coin_value_area_plus = this.add.image(game_width/1.08, game_height/2.76, 'area').setInteractive();
	coin_value_area_plus.displayWidth=game.config.width*0.04; 
	coin_value_area_plus.displayHeight=game.config.width*0.04; 
	coin_value_area_plus.alpha = 0.5;
	coin_value_area_plus.on('pointerup', function (pointer) { change_bet(true); });
	
	var coin_value_area_minus = this.add.image(game_width/1.22, game_height/2.76, 'area').setInteractive();
	coin_value_area_minus.displayWidth=game.config.width*0.04; 
	coin_value_area_minus.displayHeight=game.config.width*0.04; 
	coin_value_area_minus.alpha = 0.5;
	coin_value_area_minus.on('pointerup', function (pointer) { change_bet(false); });

	
	
	//set text settings
	machine_text_style = { font: (game_width*0.018) + "px Arial", fill: "#fefaf1", align: "center" };
	
	//set texts positions
	bet_amount_text = this.add.text(game_width/1.145, game_height/2.78, current_bet, machine_text_style).setOrigin(0.5);
	balance_amount_text = this.add.text(game_width/1.145, game_height/4.63, current_balance, machine_text_style).setOrigin(0.5);	
	win_amount_text = this.add.text(game_width/1.145, game_height/1.99, "0", machine_text_style).setOrigin(0.5);	
	main_txt = this.add.text(game_width/1.99, game_height/1.02, "PICK YOUR NUMBERS", machine_text_style).setOrigin(0.5);
	
	//paytable_texts
	
	paytable_positions[1] = 1.25;
	paytable_positions[2] = 1.35;
	paytable_positions[3] = 1.46;
	paytable_positions[4] = 1.59;
	paytable_positions[5] = 1.75;
	paytable_positions[6] = 1.96;
	paytable_positions[7] = 2.21;
	paytable_positions[8] = 2.52;
	paytable_positions[9] = 2.94;
	paytable_positions[10] = 3.56;
	
	paytable_light = this.add.image(game_width/9.92, game_height/paytable_positions[1], 'paytable_light');
	paytable_light.displayWidth=game.config.width*0.195; 
	paytable_light.scaleY=paytable_light.scaleX;
	paytable_light.alpha = 0;
	
	
	for(var i = 1; i<=10; i++){
		payable_levels_nums[i] = this.add.text(game_width/37.74, game_height/paytable_positions[i], i, machine_text_style).setOrigin(0.5);
		payable_levels_vals[i] = this.add.text(game_width/5.39, game_height/paytable_positions[i], (i*100)+'.00', machine_text_style).setOrigin(1,0.5);
		payable_levels_nums[i].alpha = 0;
		payable_levels_vals[i].alpha = 0;
	}
	
	
	
	//numbers
	
	var num_levels_positions = [4.84,3.34,2.58,2.08,1.67,1.44,1.28,1.14];
	var num_cols_positions = [3.71,3.13,2.7,2.36,2.11,1.9,1.73,1.59,1.47,1.37];
	
	var num_pos = 0;
	for(var i=0; i<8; i++){
		
		for(var e=0; e<10; e++){
			
			num_pos++;
			nums_positions[num_pos] = [num_cols_positions[e],num_levels_positions[i]];	
			
			numbers[num_pos] = this.add.image(game_width/nums_positions[num_pos][0], game_height/nums_positions[num_pos][1], 'num_light_1').setInteractive();
			numbers[num_pos].displayWidth=game.config.width*0.075; 
			numbers[num_pos].displayHeight=game.config.width*0.075; 
			numbers[num_pos].alpha=0.1;
			numbers[num_pos].number=num_pos;
			numbers[num_pos].on('pointerup', function (pointer) { press_number(this.number); });
			
		}
		
	}
	
	
	payable_levels_prices[1] = [3.7];
	payable_levels_prices[2] = [1,9];
	payable_levels_prices[3] = [1,2,16];
	payable_levels_prices[4] = [0.5,2,6,12];
	payable_levels_prices[5] = [0.5,1,3,15,50];
	payable_levels_prices[6] = [0.5,1,2,3,30,75];
	payable_levels_prices[7] = [0.5,0.5,1,6,12,36,100];
	payable_levels_prices[8] = [0.5,0.5,1,3,6,19,90,720];
	payable_levels_prices[9] = [0.5,0.5,1,2,4,8,20,80,1200];
	payable_levels_prices[10] = [0,0.5,1,2,3,5,10,30,600,1800];


	if(window.innerHeight > window.innerWidth){
		alert("This game displays better in Landscape mode, please turn your device to get better image quality.");
	}
	
	test_text = this.add.text(game_width/52, game_height/1.12, "X: Y:", machine_text_style);
	test_text.alpha = 0;
	
}


function update (){
	
	//test text
	test_text.text = "X:" + Math.round(game_width/game.input.mousePointer.x * 100) / 100 + " Y:" + Math.round(game_height/game.input.mousePointer.y * 100) / 100;

	
	if(!is_autoplay){
		if(is_picking || is_spinning){
			spin_btn.alpha = 0.2;
			pick_btn.alpha = 0.2;
			clear_btn.alpha = 0.2;
			auto_btn.alpha = 0.2;
			main_txt.alpha = 0;
		}else{
			spin_btn.alpha = 1;
			pick_btn.alpha = 1;
			clear_btn.alpha = 1;
			auto_btn.alpha = 1;
			main_txt.alpha = 1;
		}
	}
	

	
	//mute / unmute
	if(set_mute){
		this.sound.setMute(is_mute); 
		set_mute = false;
	}
	
	
}


function quick_pick(){
	if(!is_spinning){
		is_picking = true;
		var timer = 0;
		clear_all_numbers();
		var rand_nums = generate_random_numbers();
		for(var i = 0; i < 10; i++){
			setTimeout("press_number("+rand_nums[i]+")",timer);
			timer += 100;
		}
		setTimeout("is_picking = false;",timer);
	}
	
}

function generate_random_numbers() {
  var count = 10;
  var numbers = [];

  while (numbers.length < count) {
    var randomNumber = Math.floor(Math.random() * (80 - 1 + 1)) + 1;
    
    if (!numbers.includes(randomNumber)) {
      numbers.push(randomNumber);
    }
  }

  return numbers;
}

function clear_all_numbers(){
	if(!is_spinning){
		selected_numbers = [];
		update_paytable();
		for(var i = 1; i < numbers.length; i++){
			numbers[i].setTexture("num_light_1");
			numbers[i].alpha = 0.1;
		}
	}
}

function clear_to_replay(){
	if(!is_spinning){
		paytable_light.alpha = 0;
		for(var i = 1; i < numbers.length; i++){
			
			numbers[i].setTexture("num_light_1");
			
			if(selected_numbers.indexOf(i) != -1){
				numbers[i].alpha = 1;
			}else{
				numbers[i].alpha = 0.1;
			}
			
		}
	}
}

function press_number(number){
	
	if(!is_spinning && !is_autoplay){
		
		clear_to_replay();
		
		
		if(selected_numbers.indexOf(number) == -1){
			if(selected_numbers.length < 10){
				sounds_fxs["click"].play();
				selected_numbers.push(number);
				numbers[number].setTexture("num_light_1");
				numbers[number].alpha=1;
				update_paytable();
			}
		}else{
			sounds_fxs["click"].play();
			selected_numbers = selected_numbers.filter((element) => element !== number);
			numbers[number].setTexture("num_light_1");
			numbers[number].alpha=0.1;
			update_paytable();
		}
		
		
			
	}
			
}

function update_paytable(){
	
	paytable_light.alpha = 0;
	
	for(var i = 1; i<payable_levels_nums.length; i++){	
		payable_levels_vals[i].text = "";
		payable_levels_nums[i].alpha = 0;
		payable_levels_vals[i].alpha = 0;		
	}
	
	
	for(var i = 1; i<=selected_numbers.length; i++){		
		payable_levels_vals[i].text = (payable_levels_prices[selected_numbers.length][i-1]*current_bet).toFixed(2);
		payable_levels_nums[i].alpha = 1;
		payable_levels_vals[i].alpha = 1;						
	}
}



function scroll_down(){
	$('html, body').animate({scrollTop:$(document).height()}, 'slow');
}

function update_win_amount(amount,play_sounds){
	win_amount_text.text = amount;
	if(play_sounds && amount > 0){
		//winning_sounds[1].play(); 		
	}
	
}

function update_balance(new_balance){
	current_balance = new_balance;
	balance_amount_text.text = Math.round((current_balance + Number.EPSILON) * 100) / 100;
	$("#balance_field").html(number_format(current_balance));
}


function finilize_spin(){
	if(last_win_amount > 0){
		sounds_fxs["win"].play();
	}
	update_win_amount(last_win_amount,true);
	update_balance(last_received_balance);
	update_pf_data();
	is_spinning = false;
	if(is_autoplay){
		setTimeout("spin(true);",2000);
	}
}

function show_winning_numbers(numbers){
	var num_list = numbers.split(",");
	var timer = 0;
	
	for(var i = 0; i<num_list.length; i++){
		setTimeout("mark_winning_number("+num_list[i]+")",timer);
		timer += 250;
	}
	setTimeout("finilize_spin();",timer);
}

function mark_winning_number(number){
	
	if(selected_numbers.indexOf(number) == -1){
		sounds_fxs["bip"].play();
		numbers[number].setTexture("num_light_2");
	}else{
		sounds_fxs["bip_martch"].play();
		numbers[number].setTexture("num_light_3");
		win_count++;
	}
	numbers[number].alpha=1;
	
	if(win_count>0){
		paytable_light.y = game_height/paytable_positions[win_count];
		paytable_light.alpha = 1;
	}
	
}

function auto_play(){
	if(!is_spinning && selected_numbers.length > 0){
		spin_btn.setTexture("btn_stop");
		is_autoplay = true;
		pick_btn.alpha = 0.2;
		clear_btn.alpha = 0.2;
		auto_btn.alpha = 0.2;	
		main_txt.alpha = 0;	
		spin(true);
	}
}


function spin(auto_spin){

	if(is_autoplay && !auto_spin){
		//STOP
		is_autoplay = false;
		spin_btn.setTexture("btn_play");
		
	}else if((is_autoplay && auto_spin) || (!is_autoplay && !auto_spin)){
		//PLAY		
		
		if(!is_spinning && selected_numbers.length > 0){
			
			if(current_bet <= current_balance){
				
				clear_to_replay();
				update_balance(current_balance-current_bet);
				update_win_amount(0,false);
				is_spinning = true;
				win_count = 0;
				var str_nums = selected_numbers.join(",");
				
				
				//Provably fair
				var player_number = "0";
				if($("#pf_player_num").val()){player_number = $("#pf_player_num").val().replace(/[^\d,-]/g,'');}
	
				$.getJSON(core_url + "action=spin&bet=" + current_bet +  "&nums="+str_nums+"&pnr="+player_number+"&oskr=" + Math.random()  ,function(data){
					
					if(!data.error){ //change the way to check error
						
						last_received_balance = data.balance;
						last_win_amount = data.win_amount;
						
						show_winning_numbers(data.positions);					
						
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
		update_paytable();
	
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
