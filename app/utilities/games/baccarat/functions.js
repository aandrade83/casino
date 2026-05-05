//Set game vars
var core_url = "/utilities/games/baccarat/action.php?gid=" + gid + "&";

//general vars
var current_bet = 0;

var place_bets = true;

var chips_values = [0.25, 1, 5, 25, 100];
var chips = new Array();
var new_chips = new Array();
var mooving_chips = new Array();
var placed_chips = new Array();
var winned_chips = new Array();
var bet_areas = new Array();
var bet_areas_data = new Array();
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
var clear_chips_to = "";
var winning_amount = 0;
var winning_areas = new Array();
var moving_cards = new Array();
var creation_cards = new Array();
var moving_dealer_chips = new Array();
var creation_dealer_chips = new Array();
var placed_cards = new Array();
var cards_positions = new Array();
var previous_bets = "";
var shift_on = false;
var playing = false;
var move_cards_out = false;
var move_winned_chips_out = false;

var sound_flip = null;
var sound_bwins = null;
var sound_pwins = null;
var sound_bcard = null;
var sound_pcard = null;

var is_mute = false;
var set_mute = false;
var pfdata = null;

//calculate width
var game_width = $(window).width();
if (game_width > 1900) {
  game_width = 1900;
}

//calculate height for size 9/16
var game_height = (game_width * 9) / 16;

var config = {
  type: Phaser.AUTO,
  width: game_width,
  height: game_height,
  physics: {
    default: "arcade",
  },
  scene: {
    preload: preload,
    create: create,
    update: update,
  },
  audio: {
    disableWebAudio: true,
  },
};

var game = new Phaser.Game(config);

function preload() {
  //Loading Bar
  var progressBar = this.add.graphics();
  var progressBox = this.add.graphics();
  progressBox.fillStyle(0x222222, 0.8);
  progressBox.fillRect(game_width / 2 - 150, game_height / 2, 320, 50);

  var loadingText = this.make.text({
    x: game_width / 2,
    y: game_height / 2 - 50,
    text: "Loading...",
    style: {
      font: "20px monospace",
      fill: "#ffffff",
    },
  });
  loadingText.setOrigin(0.5, 0.5);

  this.load.on("progress", function (value) {
    progressBar.clear();
    progressBar.fillStyle(0xffffff, 1);
    progressBar.fillRect(
      game_width / 2 - 140,
      game_height / 2 + 10,
      300 * value,
      30,
    );
  });

  //Load elements
  //Images
  this.load.image("background", "utilities/games/baccarat/imgs/back.jpg?v3");

  this.load.image("chip0.25", "utilities/images/games/chips/chip0.25.png");
  this.load.image("chip1", "utilities/images/games/chips/chip1.png");
  this.load.image("chip5", "utilities/images/games/chips/chip5.png");
  this.load.image("chip25", "utilities/images/games/chips/chip25.png");
  this.load.image("chip100", "utilities/images/games/chips/chip100.png");
  this.load.image("chip500", "utilities/images/games/chips/chip500.png");
  this.load.image("area", "utilities/games/baccarat/imgs/blank_area.png");

  //cards
  this.load.image("card_back", "utilities/images/games/cards/card_back.png");
  this.load.image(
    "card_flip",
    "utilities/images/games/cards/card_back_flip.png",
  );
  for (var i = 0; i < deck.length; i++) {
    this.load.image(
      "card_" + deck[i],
      "utilities/images/games/cards/" + deck[i] + ".png",
    );
  }

  //Sounds
  this.load.audio(
    "card_flip_fx",
    "utilities/games/baccarat/sounds/cardflip.wav",
  );
  this.load.audio(
    "banker_wins",
    "utilities/games/baccarat/sounds/m_BankerWins.mp3",
  );
  this.load.audio(
    "card_to_banker",
    "utilities/games/baccarat/sounds/m_Card2Banker.mp3",
  );
  this.load.audio(
    "card_to_player",
    "utilities/games/baccarat/sounds/m_Card2Player.mp3",
  );
  this.load.audio(
    "player_wins",
    "utilities/games/baccarat/sounds/m_PlayerWins.mp3",
  );
}

function create() {
  //Show limits box
  $("#limit_box").css("top", game_height * 0.01 + "px");
  $("#limit_box").css("left", game_width * 0.01 + "px");
  $("#limit_box").show(500);

  //Show balance box
  $("#balance_box").css("top", game_height - game_height * 0.15 + "px");
  $("#balance_box").css("left", game_width * 0.01 + "px");
  $("#balance_box").show(500);

  //position message box
  $(".game_msg").css("top", game_height / 2 + "px");

  this.scale.pageAlignHorizontally = true;

  //set spacer size
  $("#spacer").css("width", game_width + "px");
  $("#spacer").css("height", game_height + "px");

  //sounds
  //ball_spin_sound = this.sound.add("ball_spin", {mute: false,volume: 1,rate: 1,detune: 0,seek: 0,loop: false,delay: 0});

  //Set background
  var background = this.add.image(
    game_width / 2,
    game_height / 2,
    "background",
  );
  background.displayWidth = game.config.width;
  background.scaleY = background.scaleX;

  //Buttons
  $("#btn_box").css("top", game_height - game_height * 0.06 + "px");

  //chips
  var chip_separator = game.config.width * 0.043;
  for (var i = 0; i < chips_values.length; i++) {
    chips[chips_values[i]] = this.add
      .image(
        game_width / 2.41 + chip_separator * i,
        game_height / 1.12,
        "chip" + chips_values[i],
      )
      .setInteractive();
    chips[chips_values[i]].displayWidth = game.config.width * 0.04;
    chips[chips_values[i]].scaleY = chips[chips_values[i]].scaleX;
    chips[chips_values[i]].on("pointerup", function (pointer) {
      var cvalue = this.texture.key.replace("chip", ""); // get the chip value based on the texture name (ex: chip1)
      change_chip(cvalue * 1, false);
    });
  }
  change_chip(1);

  //bet areas
  bet_areas_data.push({
    id: "tie",
    xpos: game_width / 2.07,
    ypos: game_height / 1.88,
    w: game.config.width * 0.2,
    h: game.config.width * 0.04,
  });
  bet_areas_data.push({
    id: "banker",
    xpos: game_width / 2.07,
    ypos: game_height / 1.57,
    w: game.config.width * 0.2,
    h: game.config.width * 0.04,
  });
  bet_areas_data.push({
    id: "player",
    xpos: game_width / 2.07,
    ypos: game_height / 1.3,
    w: game.config.width * 0.3,
    h: game.config.width * 0.04,
  });

  for (var i = 0; i < bet_areas_data.length; i++) {
    bet_areas[bet_areas_data[i].id] = this.add
      .image(bet_areas_data[i].xpos, bet_areas_data[i].ypos, "area")
      .setInteractive();
    bet_areas[bet_areas_data[i].id].name = bet_areas_data[i].id;
    bet_areas[bet_areas_data[i].id].displayWidth = bet_areas_data[i].w;
    bet_areas[bet_areas_data[i].id].displayHeight = bet_areas_data[i].h;
    bet_areas[bet_areas_data[i].id].alpha = 0.5;
    bet_areas[bet_areas_data[i].id].on("pointerup", function (pointer) {
      place_bet(this.name, true);
    });
  }

  clear_betareas_amounts();

  //card positions P1, D1, P2, D2, P3, D3
  cards_positions.push(game_width / 3.02);
  cards_positions.push(game_width / 1.75);
  cards_positions.push(game_width / 2.92);
  cards_positions.push(game_width / 1.715);
  cards_positions.push(game_width / 2.83);
  cards_positions.push(game_width / 1.675);

  //set text settings
  machine_text_style = {
    font: game_width * 0.01 + "px Arial",
    fill: "#ffffff",
    align: "center",
  };

  player_cards_text = this.add.text(
    game_width / 3.35,
    game_height / 3.34,
    "",
    machine_text_style,
  );
  dealer_cards_text = this.add.text(
    game_width / 1.86,
    game_height / 3.34,
    "",
    machine_text_style,
  );
  winned_chips_text = this.add.text(
    game_width / 4.05,
    game_height / 1.36,
    "",
    machine_text_style,
  );

  test_text = this.add.text(
    game_width / 2.84,
    game_height / 1.341,
    "X: Y:",
    machine_text_style,
  );
  test_text.alpha = 0;

  this.input.keyboard.on(
    "keydown_SHIFT",
    function () {
      shift_on = true;
    },
    this,
  );
  this.input.keyboard.on(
    "keyup_SHIFT",
    function () {
      shift_on = false;
    },
    this,
  );

  if (window.innerHeight > window.innerWidth) {
    alert(
      "This game displays better in Landscape mode, please turn your device to get better image quality.",
    );
  }

  //sounds
  sound_flip = this.sound.add("card_flip_fx", {
    mute: false,
    volume: 1,
    rate: 1,
    detune: 0,
    seek: 0,
    loop: false,
    delay: 0,
  });
  sound_bwins = this.sound.add("banker_wins", {
    mute: false,
    volume: 1,
    rate: 1,
    detune: 0,
    seek: 0,
    loop: false,
    delay: 0,
  });
  sound_pwins = this.sound.add("player_wins", {
    mute: false,
    volume: 1,
    rate: 1,
    detune: 0,
    seek: 0,
    loop: false,
    delay: 0,
  });
  sound_bcard = this.sound.add("card_to_banker", {
    mute: false,
    volume: 1,
    rate: 1,
    detune: 0,
    seek: 0,
    loop: false,
    delay: 0,
  });
  sound_pcard = this.sound.add("card_to_player", {
    mute: false,
    volume: 1,
    rate: 1,
    detune: 0,
    seek: 0,
    loop: false,
    delay: 0,
  });

  //positions tests
  /*var angle_mod = 0;	
	var R = 185;	
	var num_angle = 123;
	numeric_wheel.angle += angle_mod;	
	ball.x = R*Math.cos((numeric_wheel.angle + num_angle)*Math.PI/180) + game_width/2;
    ball.y = R*Math.sin((numeric_wheel.angle + num_angle)*Math.PI/180) + game_height/2;*/

  //setInterval("testmode();",1000);
}

function update() {
  //mute / unmute
  if (set_mute) {
    this.sound.setMute(is_mute);
    set_mute = false;
  }

  //test text
  test_text.text =
    "X:" +
    Math.round((game_width / game.input.mousePointer.x) * 100) / 100 +
    " Y:" +
    Math.round((game_height / game.input.mousePointer.y) * 100) / 100;

  //move cards out
  if (move_cards_out) {
    for (var p = 0; p < placed_cards.length; p++) {
      this.physics.moveTo(
        placed_cards[p],
        game_width / 2.07,
        game_height / 18.41,
        cards_speed,
      );
      var odistance = Phaser.Math.Distance.Between(
        placed_cards[p].x,
        placed_cards[p].y,
        game_width / 2.07,
        game_height / 18.41,
      );
      if (odistance < game_width * 0.04) {
        placed_cards[p].destroy();
        placed_cards.splice(p, 1);
      }
    }
    if (placed_cards.length == 0) {
      move_cards_out = false;
    }
  }

  //move winned chips out
  if (move_winned_chips_out) {
    for (var p = 0; p < winned_chips.length; p++) {
      this.physics.moveTo(
        winned_chips[p],
        game_width / 2,
        game_height,
        chip_speed,
      );
      var odistance = Phaser.Math.Distance.Between(
        winned_chips[p].x,
        winned_chips[p].y,
        game_width / 2,
        game_height,
      );
      if (odistance < game_width * 0.04) {
        winned_chips[p].destroy();
        winned_chips.splice(p, 1);
      }
    }
    if (winned_chips.length == 0) {
      move_winned_chips_out = false;
    }
  }

  //move cards to table
  for (var r = 0; r < creation_cards.length; r++) {
    var new_card = this.physics.add.image(
      game_width / 1.28,
      game_height / 3.6,
      "card_back",
    );
    new_card.displayWidth = game.config.width * 0.065;
    new_card.scaleY = new_card.scaleX;
    moving_cards.push({
      position: creation_cards[r].position,
      image: creation_cards[r].image,
      object: new_card,
    });
    creation_cards.splice(r, 1);
  }

  for (var c = 0; c < moving_cards.length; c++) {
    this.physics.moveTo(
      moving_cards[c].object,
      cards_positions[moving_cards[c].position],
      game_height / 2.46,
      cards_speed,
    );
    var flip_distance = Phaser.Math.Distance.Between(
      moving_cards[c].object.x,
      moving_cards[c].object.y,
      game_width / 1.53,
      game_height / 3.14,
    );
    if (flip_distance < game_width * 0.037) {
      moving_cards[c].object.setTexture("card_flip");
    }
    var distance = Phaser.Math.Distance.Between(
      moving_cards[c].object.x,
      moving_cards[c].object.y,
      cards_positions[moving_cards[c].position],
      game_height / 2.6,
    );
    if (distance < game_width * 0.04) {
      placed_cards.push(moving_cards[c].object);
      moving_cards[c].object.setTexture(moving_cards[c].image);
      moving_cards[c].object.body.reset(
        cards_positions[moving_cards[c].position],
        game_height / 2.46,
      );
      moving_cards.splice(c, 1);
    }
  }

  //move dealer chips to player
  for (var r = 0; r < creation_dealer_chips.length; r++) {
    var new_chip = this.physics.add.image(
      game_width / 2,
      game_height / 39.56,
      "chip" + creation_dealer_chips[r],
    );
    new_chip.displayWidth = chips[creation_dealer_chips[r]].displayWidth;
    new_chip.displayHeight = chips[creation_dealer_chips[r]].displayHeight;
    moving_dealer_chips.push(new_chip);
    creation_dealer_chips.splice(r, 1);
  }

  for (var c = 0; c < moving_dealer_chips.length; c++) {
    var distanser = winned_chips.length * (game_width * 0.001);
    this.physics.moveTo(
      moving_dealer_chips[c],
      game_width / 3.87,
      game_height / 1.43,
      chip_speed,
    );
    var distance = Phaser.Math.Distance.Between(
      moving_dealer_chips[c].x,
      moving_dealer_chips[c].y,
      game_width / 3.87,
      game_height / 1.43,
    );
    if (distance < game_width * 0.04) {
      winned_chips.push(moving_dealer_chips[c]);
      moving_dealer_chips[c].body.reset(
        game_width / 3.87,
        game_height / 1.43 - distanser,
      );
      moving_dealer_chips.splice(c, 1);
    }
  }

  //move chips to table
  if (new_chips.length > 0) {
    for (var i = 0; i < new_chips.length; i++) {
      var new_chip = this.physics.add
        .image(
          chips[new_chips[i].value].x,
          chips[new_chips[i].value].y,
          "chip" + new_chips[i].value,
        )
        .setInteractive();
      new_chip.displayWidth = chips[new_chips[i].value].displayWidth;
      new_chip.displayHeight = chips[new_chips[i].value].displayHeight;
      new_chip.area = new_chips[i].area;

      new_chip.on("pointerup", function (pointer) {
        if (shift_on) {
          remove_area_bet(this.area);
        } else {
          place_bet(this.area, true);
        }
      });

      this.physics.moveTo(
        new_chip,
        bet_areas[new_chips[i].area].x,
        bet_areas[new_chips[i].area].y,
        chip_speed,
      );

      mooving_chips.push({ chip: new_chip, area: new_chips[i].area });
      new_chips.splice(i, 1);
    }
  }

  if (mooving_chips.length > 0) {
    for (var i = 0; i < mooving_chips.length; i++) {
      var distance = Phaser.Math.Distance.Between(
        mooving_chips[i].chip.x,
        mooving_chips[i].chip.y,
        bet_areas[mooving_chips[i].area].x,
        bet_areas[mooving_chips[i].area].y,
      );
      if (distance < game_width * 0.035) {
        var distanser =
          area_bets_chip_count[mooving_chips[i].area] * (game_width * 0.001);

        mooving_chips[i].chip.body.reset(
          bet_areas[mooving_chips[i].area].x,
          bet_areas[mooving_chips[i].area].y - distanser,
        );
        placed_chips.push(mooving_chips[i]);

        if (
          area_bets_chip_count[mooving_chips[i].area] > 0 &&
          !area_bets_amount_text[mooving_chips[i].area]
        ) {
          //placing first chip
          var txt_x =
            mooving_chips[i].chip.x - mooving_chips[i].chip.displayWidth * 0.1;
          var txt_y =
            mooving_chips[i].chip.y + mooving_chips[i].chip.displayHeight * 0.4;
          area_bets_amount_text[mooving_chips[i].area] = this.add.text(
            txt_x,
            txt_y,
            currency_symbol + area_bets_amount[mooving_chips[i].area],
            machine_text_style,
          );
        } else if (area_bets_amount_text[mooving_chips[i].area]) {
          area_bets_amount_text[mooving_chips[i].area].text =
            currency_symbol + area_bets_amount[mooving_chips[i].area];
        }

        mooving_chips.splice(i, 1);
      }
    }
  }

  //clear chips from table
  if (clear_chips_to != "") {
    if (clear_chips_to == "player") {
      for (var i = 0; i < placed_chips.length; i++) {
        placed_chips[i].chip.y += game_width * 0.01;
        if (
          placed_chips[i].chip.y >
          game_height + placed_chips[i].chip.displayHeight * 2
        ) {
          placed_chips[i].chip.destroy();
          placed_chips.splice(i, 1);
        }
      }
      if (placed_chips.length == 0) {
        clear_chips_to = "";
      }
    } else if (clear_chips_to == "dealer") {
      for (var i = 0; i < placed_chips.length; i++) {
        if (winning_areas != placed_chips[i].area) {
          placed_chips[i].chip.y -= game_width * 0.01;
          if (
            placed_chips[i].chip.y <
            placed_chips[i].chip.displayHeight * -2
          ) {
            placed_chips[i].chip.destroy();
            placed_chips.splice(i, 1);
          }
        }
      }
      if (placed_chips.length == 0) {
        clear_chips_to = "";
      }
    }
  }
}

function get_prize_chips_list(amount) {
  var reversed_chips = chips_values.slice(0).reverse();
  //var amount = Math.floor(amount);
  var temp_amount = amount;
  var place_chips = new Array();

  for (var i = 0; i < reversed_chips.length; i++) {
    var current_chip = reversed_chips[i];
    var result = temp_amount / current_chip;

    if (result >= 1) {
      var new_chips_count = Math.floor(result);
      for (var e = 0; e < new_chips_count; e++) {
        place_chips.push(current_chip);
      }
      temp_amount -= new_chips_count * current_chip;
    }
  }
  return place_chips;
}

/*function get_prize_chips_list(amount){
	var reversed_chips = chips_values.slice(0).reverse();
	var amount = Math.floor(amount);
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
}*/

function update_balance(new_balance) {
  current_balance = new_balance;
  $("#balance_field").html(number_format(current_balance));
}

function adjust_balance(amount) {
  current_balance += amount;
  $("#balance_field").html(number_format(current_balance));
}

function get_bets_details() {
  var bets = new Array();
  var total_bet = 0;
  for (var i = 0; i < bet_areas_data.length; i++) {
    if (area_bets_amount[bet_areas_data[i].id] > 0) {
      bets.push(
        bet_areas_data[i].id + "|" + area_bets_amount[bet_areas_data[i].id],
      );
      total_bet += area_bets_amount[bet_areas_data[i].id];
    }
  }
  return { bets: bets.join(","), total: total_bet };
}

function deal() {
  if (!playing) {
    var bet_detail = get_bets_details();
    playing = true;
    $(".game_btn").hide(500);
    previous_bets = bet_detail.bets;
    winning_areas = "";

    //Provably fair
    if ($("#pf_player_num").val()) {
      var player_number = $("#pf_player_num")
        .val()
        .replace(/[^\d,-]/g, "");
    }

    $.getJSON(
      core_url +
        "action=deal&bets=" +
        bet_detail.bets +
        "&pnr=" +
        player_number +
        "&oskr=" +
        Math.random(),
      function (data) {
        if (!data.error) {
          //change the way to check error

          winning_areas = data.game_result.winning_area;

          pfdata = data.pf;

          setTimeout(
            "deal_card(0,'card_" + data.game_result.player_hand[0] + "');",
            1,
          );
          setTimeout(
            "deal_card(1,'card_" + data.game_result.banker_hand[0] + "');",
            500,
          );

          setTimeout(
            "deal_card(2,'card_" + data.game_result.player_hand[1] + "');",
            1500,
          );
          setTimeout(
            "player_cards_text.text = '" +
              data.game_result.player_value1 +
              "';",
            2200,
          );

          setTimeout(
            "deal_card(3,'card_" + data.game_result.banker_hand[1] + "');",
            2000,
          );
          setTimeout(
            "dealer_cards_text.text = '" +
              data.game_result.banker_value1 +
              "';",
            2500,
          );

          var timer = 3000;

          if (data.game_result.player_hand[2]) {
            timer += 1000;
            setTimeout(
              "deal_card(4,'card_" +
                data.game_result.player_hand[2] +
                "'); sound_pcard.play()",
              timer,
            );
            setTimeout(
              "player_cards_text.text = '" +
                data.game_result.player_value2 +
                "';",
              timer + 700,
            );
            timer = 4500;
          }

          if (data.game_result.banker_hand[2]) {
            timer += 1000;
            setTimeout(
              "deal_card(5,'card_" +
                data.game_result.banker_hand[2] +
                "'); sound_bcard.play()",
              timer,
            );
            setTimeout(
              "dealer_cards_text.text = '" +
                data.game_result.banker_value2 +
                "';",
              timer + 500,
            );
            timer = timer + 500;
          }

          setTimeout(
            "after_play('" +
              data.game_result.winning_area +
              "'," +
              data.win_amount +
              "," +
              data.balance +
              ");",
            timer + 1000,
          );
        } else {
          alert("There was a problem: " + data.msg);
        }
      },
    );
  }
}

function after_play(win_area, win_amount, balance) {
  update_balance(balance);
  $(".after_game_btc").show(500);
  clear_bets("dealer");
  var msg_txt = win_area.toUpperCase() + " WINS";
  if (win_amount > 0) {
    msg_txt += "<br>You Win $" + win_amount;
    var timer = 1;
    var prize_chips = get_prize_chips_list(
      Math.ceil(win_amount - area_bets_amount[win_area]),
    ); //
    for (var x = 0; x < prize_chips.length; x++) {
      setTimeout(
        "creation_dealer_chips.push(" + prize_chips[x] + ");",
        timer + 200,
      );
      timer += 200;
    }
    setTimeout(
      "winned_chips_text.text = '$" +
        Math.round((win_amount - area_bets_amount[win_area]) * 100) / 100 +
        "';",
      timer + 500,
    );
    setTimeout(
      "area_bets_amount_text['" +
        win_area +
        "'].text = '$" +
        area_bets_amount[win_area] +
        "';",
      timer + 500,
    );
  }

  switch (win_area) {
    case "banker":
      sound_bwins.play();
      break;
    case "player":
      sound_pwins.play();
      break;
    //case "tie": sound_bwins.play(); break; //missing sound
  }

  update_pf_data();

  show_message(msg_txt);
}

function deal_card(position, card) {
  creation_cards.push({ position: position, image: card });
  sound_flip.play();
}

function place_bet(area, show_btns) {
  if (!playing) {
    if (/*current_bet+*/ selected_chip <= current_balance) {
      //not include current bet because current balance already have current bet deducted
      if (area_bets_amount[area] + selected_chip <= max_amount) {
        after_spin = false;
        if (show_btns) {
          $("#btn_clear").slideDown(500);
        }
        if (current_bet + selected_chip >= min_amount && show_btns) {
          $("#btn_spin").slideDown(500);
        }
        current_bet += selected_chip;
        adjust_balance(selected_chip * -1);
        area_bets_chip_count[area]++;
        area_bets_amount[area] += selected_chip;
        new_chips.push({ value: selected_chip, area: area });
      } else {
        alert(
          "The maximum bet on this field is " + currency_symbol + max_amount,
        );
      }
    } else {
      fire_not_enough_balance();
    }
  }
}

function rebet(deal) {
  clear_table("player");
  setTimeout("place_rebet(" + deal + ");", 1500);
  if (deal) {
    setTimeout("deal();", 2500);
  }
}

function place_rebet(deal) {
  var bets = previous_bets.split(",");
  for (var i = 0; i < bets.length; i++) {
    var bet_parts = bets[i].split("|");
    var pre_chips = get_prize_chips_list(bet_parts[1]);
    for (var e = 0; e < pre_chips.length; e++) {
      change_chip(pre_chips[e]);
      place_bet(bet_parts[0], !deal);
    }
  }
}

function refresh_balance() {
  $.getJSON(core_url + "action=balance", function (data) {
    if (!data.error) {
      current_balance = data.balance - current_bet;
      update_balance_box();
    }
  });
}

function update_balance_box() {
  $("#balance_field").html(number_format(current_balance));
}

function clear_table(direction) {
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
  refresh_balance();
}

function clear_bets(direction) {
  for (var i = 0; i < bet_areas_data.length; i++) {
    if (area_bets_amount_text[bet_areas_data[i].id]) {
      area_bets_amount_text[bet_areas_data[i].id].text = "";
    }
  }
  clear_chips_to = direction;
}

function clear_betareas_amounts() {
  for (var i = 0; i < bet_areas_data.length; i++) {
    area_bets_chip_count[bet_areas_data[i].id] = 0;
    area_bets_amount[bet_areas_data[i].id] = 0;
  }
}

function remove_area_bet(area) {
  for (var i = placed_chips.length - 1; i > -1; i--) {
    if (placed_chips[i].area == area) {
      var cvalue = placed_chips[i].chip.texture.key.replace("chip", "");

      if (current_bet - cvalue == 0) {
        $("#btn_clear").hide(500);
      }
      if (current_bet - cvalue < min_amount) {
        $("#btn_spin").hide(500);
      }
      current_bet -= cvalue;
      area_bets_chip_count[area]--;
      area_bets_amount[area] -= cvalue;
      adjust_balance(cvalue * 1);

      if (area_bets_chip_count[area] > 0) {
        area_bets_amount_text[area].text =
          currency_symbol + area_bets_amount[area];
      } else {
        area_bets_amount_text[area].text = "";
      }

      placed_chips[i].chip.destroy();
      placed_chips.splice(i, 1);

      break;
    }
  }
}

function change_chip(value) {
  selected_chip = value;

  for (var i = 0; i < chips_values.length; i++) {
    chips[chips_values[i]].alpha = unselected_chip_alpha;
  }
  chips[value].alpha = 1;
}

function show_message(msg) {
  $("#game_msg").html(msg);
  $("#game_msg").slideDown(700);
}

function add_message(msg) {
  $("#game_msg").html($("#game_msg").html() + msg);
}

function scroll_down() {
  $("html, body").animate({ scrollTop: $(document).height() }, "slow");
}

function hide_message() {
  $(".game_msg").slideUp(700);
}

function mute() {
  if (is_mute) {
    is_mute = false;
    $("#btn_mute").removeClass("blue");
  } else {
    is_mute = true;
    $("#btn_mute").addClass("blue");
  }
  set_mute = true;
}

function update_pf_data() {
  if (pfdata) {
    $("#pf_player_num").val(getRandomInt(0, 100000000));
    $("#pf_n_shash").val(pfdata.nxnr);
    $("#pf_l_shash").val(pfdata.lxhs);
    $("#pf_l_sec1").val(pfdata.lsc1);
    $("#pf_l_sec2").val(pfdata.lsc2);
    $("#pf_l_snum").val(pfdata.lxnr);
    $("#pf_l_pnum").val(pfdata.lpnr);
    $("#pf_l_resnum").val(pfdata.lpos);
  }
}
