<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prueba HTML5</title>
<script type="text/javascript" src="http://www.sportsbettingonline.ag/engine/sbo/sbo_file.php?file=jquery"></script>

<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #000;
}
.game_container{
   width: 100%;
   text-align:center;
}


.main_btn{
	position: absolute;
    left: 0;
	right: 0;
	margin-left: auto;
	margin-right: auto;
	display:none;
}

</style>
</head>

<body>

<div class="game_container">
	<canvas id="myCanvas" width="200" height="100">Your browser does not support this game, please update ir or try anotherone.</canvas>
</div>
<input type="image" src="http://www.vrbmarketing.com/html5/imgs/btn_deal.png" onclick="deal();" id="deal_btn" class="main_btn" />
<script>
//create canvas
var canvas = document.getElementById("myCanvas");
var ctx = canvas.getContext("2d");

//calculate width
var game_width = $(window).width();
if(game_width > 1920){game_width > 1920;}

//calculate height for size 9/16
var game_height = (game_width * 9)/16; 

//set canvas size
canvas.width = game_width;
canvas.height = game_height;

//position btns
$(".main_btn").css("top",(game_height/3)*2);
$(".main_btn").show(1000);


//create and set canvas background
var background = new Image();
background.src = "http://www.vrbmarketing.com/html5/imgs/back.jpg";
background.onload = function(){
    ctx.drawImage(background,0,0,game_width,game_height);   
}


function deal(){
	$("#deal_btn").hide();
	
	//deal player card 1
	var card1 = new Image();
	card1.src = "http://www.vrbmarketing.com/html5/imgs/cards/card1.png";
	card1.onload = function(){
		var start_x = game_width-36;
		var start_y = 0;
		var end_x = (game_width/2)-63;
		var end_y = (game_height/2)+100;
		var current_x = start_x;
		var current_y = start_y;
		
		draw_image(card1,current_x,current_y, end_x, end_y)
		
		/*while(current_x < end_x || current_y  < end_y){
			current_x--;
			current_y++;
			ctx.drawImage(card1,current_x,current_y,125,180);  
		}*/
	}
	
	
}

function draw_image(img,x,y, maxx, maxy){
	ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
	ctx.drawImage(background,0,0,game_width,game_height);   
	ctx.drawImage(img,x,y,125,180);
	if(x < maxx || y  < maxy){
		x-=10;
		y+=10;
		setTimeout(function(){draw_image(img,x,y, maxx, maxy)},1);
	}
		
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

</script> 


</body>
</html>