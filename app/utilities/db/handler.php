<?php
require_once($_SERVER['DOCUMENT_ROOT'] .'/utilities/db/connection.php');
require_once($_SERVER['DOCUMENT_ROOT'] .'/utilities/db/manager.php');


function get_all_companies(){ 
	db_connect("main");
	$sql = "SELECT * FROM company ORDER BY name ASC";
	return get($sql, "_company", false, "id"); 
}

function get_company($cid){
	db_connect("main");
	$sql = "SELECT * FROM company WHERE id = '$cid'";
	return get($sql, "_company", true); 
}

function get_validated_comany($id, $password){
	db_connect("main");
	$sql = "SELECT * FROM company where id = '$id' AND password = '$password'";
	return get($sql, "_company", true); 
}

function get_comany($id){
	db_connect("main");
	$sql = "SELECT * FROM company where id = '$id'";
	return get($sql, "_company", true); 
}


function get_company_player($account, $company){
	db_connect("main");
	$sql = "select * from player where account = '$account' AND company = '$company'";
	return get($sql, "_player", true); 
}

function get_all_categories(){
	db_connect("main");
	$sql = "select * from category ORDER BY id ASC";
	return get($sql, "_category",false,"id"); 
}

function get_player($pid){
	db_connect("main");
	$sql = "select * from player where id = '$pid'";
	return get($sql, "_player", true); 
}

function get_agent_players($aid){
	db_connect("main");
	$sql = "select * from player where agent = '$aid' ORDER BY account ASC";
	return get($sql, "_player"); 
}

function get_agent($aid){
	db_connect("main");
	$sql = "select * from agent where id = '$aid'";
	return get($sql, "_agent", true); 
}

function get_company_agent_by_name($agent, $company){
	db_connect("main");
	$sql = "select * from agent where account = '$agent' AND company = '$company'";
	return get($sql, "_agent", true); 
}

function get_login_agent($agent, $password){
	db_connect("main");
	$sql = "select * from agent where account = '$agent' AND password = '$password'";
	return get($sql, "_agent", true); 
}

function get_player_settle($pid, $from, $to){
	db_connect("main");
	$sql = "
		SELECT
		(SELECT ISNULL(SUM(settle),0) as total from settle_log WHERE ldate >= '$from' AND ldate < '$to' AND player = '$pid' AND free_play = 0)
		+
		(SELECT ISNULL(SUM(win_amount),0) as total from settle_log WHERE ldate >= '$from' AND ldate < '$to' AND player = '$pid' AND free_play = 1) as total
	";
	return get_str($sql, true); 
}


function get_game_by_company($gid, $cid, $just_active = false){
	db_connect("main");
	if($just_active){$sql_ac = " AND visible = 1 ";}
	$sql = "select g.*, gc.min_amount, gc.max_amount from game as g, game_by_company as gc where g.id = '$gid' AND gc.company = '$cid' AND gc.game = g.id $sql_ac";
	return get($sql, "_game", true); 
}

function get_game($gid){
	db_connect("main");
	$sql = "select * from game where id = '$gid'";
	return get($sql, "_game", true); 
}


function get_all_games(){
	db_connect("main");
	$sql = "select * from game ";
	return get_str($sql, false, 'id'); 
}






function get_all_company_games($cid, $just_active = false, $category = ""){
	db_connect("main");
	$sql_ac ="";
	$sql_category = "";
	if($just_active){$sql_ac = " AND visible = 1 ";}
	if($category != ""){$sql_category = " AND category = '$category' ";}
	$sql = "select * from game as g, game_by_company as gc where g.id = gc.game and gc.company = '$cid' $sql_ac $sql_category ORDER BY position ASC";
	return get($sql, "_game"); 
}

function get_games_by_agent($aid, $just_active = false){
	db_connect("main");
	$sql_ac ="";
	if($just_active){$sql_ac = " AND visible = 1 ";}
	$sql = "select * from game as g, game_by_person as gc where g.id = gc.game and gc.person = '$aid' $sql_ac AND is_agent = 1";
	return get($sql, "_game", false, "game"); 
}

function get_games_by_player($pid, $just_active = false){
	db_connect("main");
	$sql_ac ="";
	if($just_active){$sql_ac = " AND visible = 1 ";}
	$sql = "select * from game as g, game_by_person as gc where g.id = gc.game and gc.person = '$pid' $sql_ac AND is_agent = 0";
	return get($sql, "_game", false, "game"); 
}

function update_company_game_limit($cid, $gid, $active, $min, $max){
	db_connect("main");
	$sql = "UPDATE game_by_company SET visible = '$active', min_amount = '$min', max_amount = '$max' WHERE company = '$cid' AND game = '$gid'";
	execute($sql);
}

function delete_all_agent_games($aid){
	db_connect("main");
	$sql = "DELETE game_by_person WHERE person = '$aid' AND is_agent = 1";
	execute($sql);
}

function insert_agent_game_limit($aid, $gid, $active, $min, $max){
	db_connect("main");
	$sql = "INSERT INTO game_by_person (person, game, visible, min_amount, max_amount, is_agent) VALUES('$aid','$gid','$active','$min','$max','1')";
	execute($sql);
}

function delete_all_player_games($pid){
	db_connect("main");
	$sql = "DELETE game_by_person WHERE person = '$pid' AND is_agent = 0";
	execute($sql);
}

function insert_player_game_limit($pid, $gid, $active, $min, $max){
	db_connect("main");
	$sql = "INSERT INTO game_by_person (person, game, visible, min_amount, max_amount, is_agent) VALUES('$pid','$gid','$active','$min','$max','0')";
	execute($sql);
}

function get_blackjack_session_by_player($pid, $open = true, $type = "regular"){
	db_connect("main");
	if($open){$sql_open = "AND (finished = 0 OR (finished2 = 0 AND splited = 1))";}
	$sql = "select TOP 1 * from blackjack_session where player = '$pid' AND bj_type = '$type' $sql_open ORDER BY id DESC";
	return get($sql, "_bj_session", true); 
}

function search_blackjack_session($pid, $from, $to, $type = "bj"){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, 'Blackjack ($type)' as game_name from blackjack_session where player = '$pid' AND start_date >= '$from' AND start_date <= '$to' AND bj_type = '$type' ORDER BY id DESC";
	return get($sql, "_bj_session", false, "start_date"); 
}

function search_slots_session($pid, $from, $to, $game){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, gdate as start_date, 'Slot' as game_name from slot_session where player = '$pid' AND gdate >= '$from' AND gdate <= '$to' AND game = '$game' ORDER BY id DESC";
	return get($sql, "_slot_session", false, "start_date"); 
}

function search_videopoker_session($pid, $from, $to, $type){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, 'Video Poker ' + vp_type as game_name from video_poker_session where player = '$pid' AND start_date >= '$from' AND start_date <= '$to' AND vp_type = '$type' ORDER BY id DESC";
	return get($sql, "_vp_session", false, "start_date"); 
}

function search_keno_session($pid, $from, $to){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, gdate as start_date, 'Keno' as game_name from keno_session where player = '$pid' AND gdate >= '$from' AND gdate <= '$to' ORDER BY id DESC";
	return get($sql, "_keno_session", false, "start_date"); 
}

function search_roulette_session($pid, $from, $to){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, gdate as start_date, 'Roulette' as game_name from roulette_session where player = '$pid' AND gdate >= '$from' AND gdate <= '$to' ORDER BY id DESC";
	return get($sql, "_roulette_session", false, "start_date"); 
}

function search_baccarat_session($pid, $from, $to){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, gdate as start_date, 'Baccarat' as game_name from baccarat_session where player = '$pid' AND gdate >= '$from' AND gdate <= '$to' ORDER BY id DESC";
	return get($sql, "_baccarat_session", false, "start_date"); 
}

function search_poker_session($pid, $from, $to, $type){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, 'Poker ' + poker_type as game_name, (ante_bet+call_bet+turn_bet+river_bet) as bet_amount from poker_session 
			where player = '$pid' AND start_date >= '$from' AND start_date <= '$to' AND poker_type = '$type'
			ORDER BY id DESC";
	return get($sql, "_poker_session", false, "start_date"); 
}

function search_craps_session($pid, $from, $to){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select *, start_date, 'Craps' as game_name from craps_session where player = '$pid' AND start_date >= '$from' AND start_date <= '$to' ORDER BY id DESC";
	return get($sql, "_craps_session", false, "start_date"); 
}

function search_craps_session_rolls($pid, $from, $to){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	$sql = "select r.*, rdate as start_date, 'Craps' as game_name from craps_session as s, craps_roll as r where s.player = '$pid' 
			AND s.id = r.session AND s.start_date >= '$from' AND s.start_date <= '$to' ORDER BY id DESC";
	return get($sql, "_craps_roll", false, "start_date"); 
}

function get_craps_rolls_by_session($sid){
	db_connect("main");
	$sql = "select * from craps_roll where session = '$sid' order by id desc";
	return get($sql, "_craps_roll"); 
}


function get_video_poker_session_by_player($pid, $type, $open = true){
	db_connect("main");
	if($open){$sql_open = "AND finished = 0";}
	$sql = "select TOP 1 * from video_poker_session where player = '$pid' $sql_open AND vp_type = '$type' ORDER BY id DESC";
	return get($sql, "_vp_session", true); 
}

function get_poker_session_by_player($pid, $type, $open = true){
	db_connect("main");
	if($open){$sql_open = "AND finished = 0";}
	$sql = "select TOP 1 * from poker_session where player = '$pid' $sql_open AND poker_type = '$type' ORDER BY id DESC";
	return get($sql, "_poker_session", true); 
}

function get_craps_session_by_player($pid, $open = true){
	db_connect("main");
	if($open){$sql_open = "AND finished = 0";}
	$sql = "select TOP 1 * from craps_session where player = '$pid' $sql_open ORDER BY id DESC";
	return get($sql, "_craps_session", true); 
}

function get_pending_craps_bets_by_player($pid){
	db_connect("main");
	$sql = "select top 1 current_bets as pending_bets from craps_session where player = '$pid' AND finished = 1 ORDER BY id DESC";
	return get_str($sql, true); 
}

function get_agent_kids($aid){
	db_connect("main");
	$sql = "SELECT * from agent where parent = '$aid'";
	return get($sql, "_agent"); 
}




function get_winloss_report($from, $to, $control_agents, $agents, $player, $by_player = true){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	if($player != ""){$sqlp = " AND p.id = '$player' ";}
	if($agents != ""){$sqla = " AND p.agent IN($agents) ";}
	if($by_player){$sql_bp = "group by p.account"; $sql_it = ", p.account";}
	
	$sql = "SELECT SUM(settle) as winloss, COUNT(*) as plays, SUM(bet_amount) as wagered $sql_it FROM settle_log as l, player as p WHERE ldate >= '$from' AND ldate <= '$to' 
			AND p.id = l.player AND p.agent IN ($control_agents)  $sqlp $sqla $sql_bp";
	
	/*$sql = "with temp_sessions as(
				select id, 'blackjack' as game, settle, (bet_amount+ISNULL(bet_amount2,0)) as bet_amount, player from blackjack_session where
				start_date >= '$from' AND start_date <= '$to'
				UNION
				select id, 'slot' as game, settle, bet_amount, player from slot_session where
				gdate >= '$from' AND gdate <= '$to'
			)
			SELECT SUM(settle) as winloss, COUNT(*) as plays, SUM(bet_amount) as wagered $sql_it from temp_sessions as s, player as p
			where s.player = p.id AND p.agent IN ($control_agents)  $sqlp $sqla $sql_bp";*/
	
	return get_str($sql); 
}


function get_holdpercentage_report($from, $to,$control_players){
	db_connect("main");
	$to = date("Y-m-d",strtotime($to." + 1 day"));
	// player 7 is Betowitest
	$sql = "Select game, SUM(settle) as 'winloss', SUM(bet_amount) as 'wagered', COUNT(id) as 'games' from settle_log as l WHERE 
     l.player IN ($control_players) AND l.player != 7 AND ldate >= '".$from."' AND ldate <= '".$to."' group by game";
	return get_str($sql); 
}

function get_player_count($from, $to, $control_agents){
	db_connect("main");
	$sql = "SELECT COUNT(distinct player) as total from settle_log as s, player as p
			where s.player = p.id AND p.agent IN ($control_agents) AND ldate >= '$from' AND ldate <= '$to'";
			
	return get_str($sql, true); 
}

function get_game_freeplays($game, $player, $free_play = 0){
	db_connect("main");
	$sql = "select ISNULL(SUM(amount),0) as total from free_play where player = '$player' AND game = '$game' AND used = 0 AND ufree_play = '$free_play'";			
	return get_str($sql, true); 
}

function get_player_next_freeplay($game, $player, $free_play = 0){
	db_connect("main");
	$sql = "select TOP 1 * from free_play where player = '$player' AND game = '$game' AND used = 0  AND ufree_play = '$free_play' ORDER BY id ASC";			
	return get($sql, "_free_play", true); 
}

function get_active_contest($company){
	db_connect("main");
	$today = date("Y-m-d");
	$sql = "select TOP 1 c.* from contest as c, contest_by_company as cc where active = 1 AND cc.company = $company 
			AND cc.contest = c.id AND start_date <= '$today' AND end_date > '$today'";			
	return get($sql, "_contest", true); 	
}

function get_all_contests(){
	db_connect("main");
	$today = date("Y-m-d");
	$sql = "select * from contest ORDER BY id DESC";			
	return get($sql, "_contest", false, "id"); 	
}

function get_contest($cid){
	db_connect("main");
	$today = date("Y-m-d");
	$sql = "select * from contest WHERE id = '$cid'";			
	return get($sql, "_contest", true); 	
}

function disable_all_contests(){
	db_connect("main");
	$today = date("Y-m-d");
	$sql = "update contest set active = 0";			
	return execute($sql); 	
}


function get_active_contest_by_player($player){
	db_connect("main");
	$today = date("Y-m-d");
	$sql = "select TOP 1 c.* from contest as c, contest_by_company as cc, player as p where active = 1 AND cc.company = p.company AND cc.contest = c.id AND p.id = $player
			AND cc.contest = c.id AND start_date <= '$today' AND end_date > '$today'";			
	return get($sql, "_contest", true); 	
}


function get_random_contest_team($cid){
	db_connect("main");
	$sql = "select top 1 * from contest_teams where contest = $cid AND active = 1 order by NEWID()";			
	return get($sql, "_contest_team", true); 	
}

function get_all_contest_teams($cid){
	db_connect("main");
	$sql = "select * from contest_teams where contest = $cid ORDER BY name ASC";			
	return get($sql, "_contest_team", false, "id"); 	
}

function get_contest_team($tid){
	db_connect("main");
	$sql = "select * from contest_teams where id = '$tid'";			
	return get($sql, "_contest_team", true); 	
}

function get_player_contest_team_totals($cid, $pid){
	db_connect("main");
	$sql = "select team, COUNT(*) as total, SUM(points) as points from contest_team_by_player where player = $pid AND contest = $cid group by team order by points desc";			
	return get_str($sql); 	
}

function get_contest_team_player_totals($tid){
	db_connect("main");
	$sql = "select p.id as player_id, account, COUNT(*) as total, SUM(points) as points 
			from contest_team_by_player as cp, player as p 
			where team = $tid AND cp.player = p.id
			group by p.id, account order by points desc";			
	return get_str($sql); 	
}

function get_player_contest_team_history($cid, $pid){
	db_connect("main");
	$sql = "select * from contest_team_by_player where player = $pid AND contest = $cid order by id desc";			
	return get_str($sql); 	
}
/*
function count_casino_open_hands($pid){ //add games in here 
	db_connect("main");
	$sql = "SELECT (
			(select COUNT(*) from blackjack_session where finished = 0 AND player = $pid) + 
			(select COUNT(*) from craps_session where finished = 0 AND player = $pid) + 
			(select COUNT(*) from poker_session where finished = 0 AND player = $pid) + 
			(select COUNT(*) from video_poker_session where finished = 0 AND player = $pid)
			) as total
";			
	return get_str($sql, true); 	
}
*/


function count_casino_open_hands($pid){ //add games in here 
    db_connect("main");
    $sql = "SELECT (
            (select COUNT(*) from blackjack_session where finished = 0 AND player = $pid) + 
            (select COUNT(*) from craps_session where finished = 0 AND player = $pid) + 
            (select COUNT(*) from poker_session where finished = 0 AND player = $pid) + 
            (select COUNT(*) from video_poker_session where finished = 0 AND player = $pid)
            ) as total
";          
    return get_str($sql, true);     
}




function get_avr_bet_by_player($pid, $from, $to){
	db_connect("main");
	$sql = "select AVG(bet_amount) as total from blackjack_session where player = $pid 
			AND start_date >= '$from' AND start_date <= '$to' AND finished = 1";			
	return get_str($sql, true); 	
}

function get_vp_extra_hands($sid){
	db_connect("main");
	$sql = "select * from video_poker_extra_hand where session = '$sid'";			
	return get($sql, "_vp_extra_hand");
		
}

function get_latest_bets(){
	db_connect("main");
	$sql = "select top 10 g.name as game, p.account, ldate, bet_amount, win_amount  from settle_log as l, game as g, player as p where g.id = l.game and p.id = l.player and bet_amount > 0 order by l.id desc";			
	return get_str($sql);
		
}

function insert_contest_by_company($cid, $contest){ 
	db_connect("main");
	$sql = "insert into contest_by_company VALUES ($cid,$contest,NULL)";
	return execute($sql); 
}

//***** */

function get_company_by_name($name, $password){
    db_connect("main");

    $sql = "SELECT * FROM company WHERE name = '$name' AND password = '$password'";

    return get($sql, "_company", true);
}

function get_company_by_api_key($api_key){
	 db_connect("main");

    $sql = "SELECT * FROM company WHERE api_key  = '$api_key'";

    return get($sql, "_company", true);
}


function get_company_by_site($site){
	 db_connect("main");

    $sql = "SELECT * FROM company WHERE site_url  = '$site'";

    return get($sql, "_company", true);
}

function get_validated_player($account, $company, $encrypted_password){
	db_connect("main");
	$sql = "select * from player where account = '$account' AND company = '$company' AND password = '$encrypted_password'";
	return get($sql, "_player", true);
}


?>