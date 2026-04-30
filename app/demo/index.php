<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://transfers.ezpay.com/utilities/js/jquery.js"></script>
<script type="text/javascript" src="js/phaser.js"></script>
<title>VRB Game example</title>
<style type="text/css">
body {
	background-color: #000;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	text-align:center;
}

canvas {
   display: inline;
}
#spacer{
	position:absolute;
}
body,td,th {
	color: #FFF;
}
</style>
</head>

<body>
<div id="spacer"></div>
<script type="text/javascript">

	var fadein_deal_btn = false;
	var fadeout_deal_btn = false;
	var deal_card1 = false;
	var deal_card2 = false;
	var deal_card3 = false;
	var deal_card4 = false;
	
	var c1_y_isready = false;
	var c1_x_isready = false;
	var c2_y_isready = false;
	var c2_x_isready = false;
	var c3_y_isready = false;
	var c4_x_isready = false;
	var c4_y_isready = false;
	var c4_x_isready = false;
	
	var flip_card1 = false;
	var flip_card2 = false;
	var flip_card3 = false;
	
	var sound_card1 = false;
	var sound_card2 = false;
	var sound_card3 = false;
	var sound_card4 = false;
	

	//calculate width
	var game_width = $(window).width();
	if(game_width > 1900){game_width = 1900;}
	
	//calculate height for size 9/16
	var game_height = (game_width * 9)/16; 
	
	
	var player_cards_position_x = game_width/2;
	var player_cards_position_y = (game_height/3)*2;
	var dealer_cards_position_x = (game_width/2)-game_width*0.01;
	var dealer_cards_position_y = (game_height/3);
	var cards_separation = 30;
	
	if(game_width > 1100){
		var card_scale = 0.6;
	}else if(game_width > 700){
		var card_scale = 0.45;
	}else{
		var card_scale = 0.3;
	}
	
	
	
	
	
	
	
	//set spacer size
	$("#spacer").css("width",(game_width)+"px");
	$("#spacer").css("height",game_height+"px");

    var config = {
        type: Phaser.AUTO,
        width: game_width,
        height: game_height,
        scene: {
            preload: preload,
            create: create,
            update: update
        }
    };

    var game = new Phaser.Game(config);

    function preload (){
		
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
			console.log(value*100);
			progressBar.clear();
			progressBar.fillStyle(0xffffff, 1);
			progressBar.fillRect((game_width/2)-140, (game_height/2)+10, 300 * value, 30);
		});
		
		//this.load.image('background', 'imgs/heavy.jpg'); to test loading screen
		this.load.image('background', 'imgs/back.jpg');
    	this.load.image('deal_btn', 'imgs/btn_deal.png');
		this.load.image('reset_btn', 'imgs/btn_reset.png');
		this.load.image('card1', 'imgs/cards/card3.png');
		this.load.image('card2', 'imgs/cards/card2.png');
		this.load.image('card3', 'imgs/cards/card1.png');
		this.load.image('card_back', 'imgs/cards/card_back.png');
		this.load.image('card_flip', 'imgs/cards/card_back_flip.png');
		
		this.load.audio('card_flip_sound', 'sounds/cardflip.wav');
		
    }

    function create (){
		
		// Center game canvas on page
		game.scale.pageAlignHorizontally = true;
		//game.scale.pageAlignVertically = true;
		
		//Set background
		var background = this.add.image(game_width/2, game_height/2, 'background');
		background.displayWidth=game.config.width; 
		background.scaleY=background.scaleX
		
		
		
		//Player card 1
		this.player_card1 = this.add.image(game_width-game_width*0.1, -200, 'card_back');
		this.player_card1.setScale(card_scale); // set scale based on screen size
		
		//player card2
		this.player_card2 = this.add.image(game_width-game_width*0.1, -200, 'card_back');
		this.player_card2.setScale(card_scale); // set scale based on screen size
		
		//Dealer card1
		this.dealer_card1 = this.add.image(game_width-game_width*0.1, -200, 'card_back');
		this.dealer_card1.setScale(card_scale); // set scale based on screen size
		
		//Dealer card2
		this.dealer_card2 = this.add.image(game_width-game_width*0.1, -200, 'card_back');
		this.dealer_card2.setScale(card_scale); // set scale based on screen size
		
		
		
		//Deal BTN
		var sprite_btn_deal = this.add.sprite(game_width/2, game_height+50, 'deal_btn').setInteractive();
		sprite_btn_deal.on('pointerdown', function (pointer) {	
			//this.setTint(0xff0000);
			//sprite_btn_deal.destroy();	
			fadeout_deal_btn = true;
			deal_card1 = true;
		});
			
		sprite_btn_deal.on('pointerout', function (pointer) {	
			//this.clearTint();	
		});
	
		sprite_btn_deal.on('pointerup', function (pointer) {	
			//this.clearTint();	
		});		
		
		this.btn_deal = sprite_btn_deal;		
		fadein_deal_btn = true;
		
		
		
		
		
		//Reset BTN
		var sprite_btn_reset = this.add.sprite(100, 25, 'reset_btn').setInteractive();
		sprite_btn_reset.setScale(0.5);
		sprite_btn_reset.on('pointerdown', function (pointer) {	
			document.location.href = document.location.href;
		});
		
		
		
    }

    function update (){
		
		//Deal BTN fadein
		if(fadein_deal_btn){		
			if(this.btn_deal.y > (game_height/3)*2){
				this.btn_deal.y-=15;	
			}else{
				fadein_deal_btn = false;
			}			
		}		
		if(fadeout_deal_btn){		
			if(this.btn_deal.y < (game_height+50)){
				this.btn_deal.y+=15;
			}else{
				fadeout_deal_btn = false;
			}			
		}
		
		//Deal		
		if(deal_card1){
			
			if(!sound_card1){
				this.sound.play("card_flip_sound");
				sound_card1 = true;
			}
			
			if(this.player_card1.y < player_cards_position_y){
				this.player_card1.y+=37;
			}else{
				c1_y_isready = true;
			}
			if(this.player_card1.x > player_cards_position_x){
				this.player_card1.x-=30;
			}else{
				c1_x_isready = true;	
			}
			
			
			//Flip card
			if(this.player_card1.x < player_cards_position_x  + 200 && !flip_card1){
				var tempx = this.player_card1.x;
				var tempy = this.player_card1.y;
				this.player_card1.destroy();
				this.player_card1 = this.add.image(tempx, tempy, 'card_flip');
				this.player_card1.setScale(card_scale); // set scale based on screen size
				flip_card1 = true;
			}
			
			
			if(c1_y_isready && c1_x_isready){
				deal_card1 = false;
				deal_card2 = true;
				this.player_card1 = this.add.image(this.player_card1.x, this.player_card1.y, 'card1');
				this.player_card1.setScale(card_scale); // set scale based on screen size
			}
			
		}
		
		if(deal_card2){
			
			if(!sound_card2){
				this.sound.play("card_flip_sound");
				sound_card2 = true;
			}
			
			if(this.dealer_card1.y < dealer_cards_position_y){
				this.dealer_card1.y+=30;
			}else{
				c2_y_isready = true;
			}
			if(this.dealer_card1.x > dealer_cards_position_x){
				this.dealer_card1.x-=40;
			}else{
				c2_x_isready = true;	
			}
			
			if(c2_y_isready && c2_x_isready){
				deal_card2 = false;
				deal_card3 = true;
			}
			
		}
		
		if(deal_card3){
			
			if(!sound_card3){
				this.sound.play("card_flip_sound");
				sound_card3 = true;
			}
			
			if(this.player_card2.y < player_cards_position_y){
				this.player_card2.y+=37;
			}else{
				c3_y_isready = true;
			}
			if(this.player_card2.x > player_cards_position_x + cards_separation){
				this.player_card2.x-=30;
			}else{
				c3_x_isready = true;	
			}
			
			
			//Flip card
			if(this.player_card2.x < player_cards_position_x  + 200 && !flip_card2){
				var tempx = this.player_card2.x;
				var tempy = this.player_card2.y;
				this.player_card2.destroy();
				this.player_card2 = this.add.image(tempx, tempy, 'card_flip');
				this.player_card2.setScale(card_scale); // set scale based on screen size
				flip_card2 = true;
			}
			
			
			if(c3_y_isready && c3_x_isready){
				deal_card3 = false;
				deal_card4 = true;
				this.player_card2 = this.add.image(this.player_card2.x, this.player_card2.y, 'card2');
				this.player_card2.setScale(card_scale); // set scale based on screen size
			}
			
		}
		
		if(deal_card4){
			
			if(!sound_card4){
				this.sound.play("card_flip_sound");
				sound_card4 = true;
			}
			
			if(this.dealer_card2.y < dealer_cards_position_y){
				this.dealer_card2.y+=30;
			}else{
				c4_y_isready = true;
			}
			if(this.dealer_card2.x > dealer_cards_position_x + cards_separation + 10){
				this.dealer_card2.x-=40;
			}else{
				c4_x_isready = true;	
			}
			
			
			//Flip card
			if(this.dealer_card2.x < dealer_cards_position_x  + 200 && !flip_card3){
				var tempx = this.dealer_card2.x;
				var tempy = this.dealer_card2.y;
				this.dealer_card2.destroy();
				this.dealer_card2 = this.add.image(tempx, tempy, 'card_flip');
				this.dealer_card2.setScale(card_scale); // set scale based on screen size
				flip_card3 = true;
			}
			
			
			if(c4_y_isready && c4_x_isready){
				deal_card4 = false;
				this.dealer_card2 = this.add.image(this.dealer_card2.x, this.dealer_card2.y, 'card3');
				this.dealer_card2.setScale(card_scale); // set scale based on screen size
			}
			
		}
		
    }

</script>

</body>
</html>
