<?php

function insert_error_log($type,$query){
	//not implemented
}


function is_email($email){
	$res = false;
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$res = true;
	}
	return $res;
}

function remove_br($string) {
    $string = str_replace('<br>', "", $string);
    $string = str_replace('<br/>', "", $string);
    $string = str_replace('<br />', "", $string);
    return $string;
}

function nicetime($date){
    $periods  = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths  = array("60","60","24","7","4.35","12","10");
   
    $now        = time() + 3600;			
    $unix_date  = strtotime($date);
    
	// is it future date or past date
    if($now > $unix_date) {   
      $difference  = $now - $unix_date;
      $tense       = "ago";       
    }else{
		$difference  = $unix_date - $now;
	}
   
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
   
    $difference = round($difference);
   
    if($difference != 1) {
      $periods[$j].= "s";
    }
   
    return "about $difference $periods[$j] {$tense}";
}

function nicetime2($date, $to = ""){
    if(empty($date)) {
        return "No date provided";
    }

    $periods         = array("segundo", "mnuto", "hora", "día", "semana", "mes", "año", "decada");
    $lengths         = array("60","60","24","7","4.35","12","10");

    if($to == ""){$now             = time();}
	else{$now = strtotime($to);}
	
    $unix_date         = strtotime($date);

       // check validity of date
    if(empty($unix_date)) {
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {
        $difference     = $now - $unix_date;
        $tense         = " atras";

    } else {
        $difference     = $unix_date - $now;
        $tense         = " restantes";
    }

    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if($difference != 1) {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] {$tense}";
}

function get_message($id){
	$error_messages[1] = "Information has been updated";		
	return $error_messages[$id]; 
}

function weekday($day="", $now="") {
  $now = $now ? $now : "now";
  $day = $day ? $day : "now";

  $rel = date("N", strtotime($day)) - date("N");

  $time = strtotime("$rel days", strtotime($now));

  return date("Y-m-d", $time);
}

function get_fisrt_last_day_of_week($date){

  $week_range= array();	
  $Current = date('N', StrToTime($date)); 
  $DaysToSunday = 7 - $Current; 
  $DaysFromMonday = $Current - 1; 
  $week_range["Monday"] = date('Y/m/d', strtotime("- {$DaysFromMonday} Day",strtotime($date)));  
  $week_range["Sunday"] = date('Y/m/d', strtotime("+ {$DaysToSunday} Day",strtotime($date))); 
  
  return $week_range;
}

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function count_down($target){
	$target = strtotime($target);
	$today = time () ;	
	$difference =($target-$today) ;	
	$sec =(int) ($difference) ;	
	return sec2hms($sec);	
}

function sec2hms ($sec, $padHours = false) {
	$hms = "";
	$hours = intval(intval($sec) / 3600); 
	$hms .= ($padHours) 
		  ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
		  : $hours. " hours ";
	$minutes = intval(($sec / 60) % 60); 
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). " min ";
	$seconds = intval($sec % 60); 
	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT). " seconds";
	return $hms;
}
function print_boolean($num){
	if($num == 1){
		$string = "Yes";
	}else{
		$string = "No";
	}
	return $string;
}
function phone_format($phone){
	if(strlen($phone) == 8){
		$res = substr($phone,0,4)."-".substr($phone,4,4);	
	}else{
		$res = $phone;	
	}
	return $res;
}
function send_email($email, $sub, $content, $html = false, $from = "info@clintonpresidente.com"){
		$headers = 'From: ClintonPresidente.com <'.$from.'>' . "\r\n" .
		'Reply-To: '.$from . "\r\n";
		if($html){$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";}
		if (@mail($email, $sub, $content, $headers)) {} else {}		
}

function secure_input($str) {
	$str = preg_replace("/[^A-Za-z0-9,.@\-_\/?=&| ]/", "", $str);
	$str = str_replace("--","",$str);
	$str = str_replace("//","",$str);
	return trim($str);  
}

function short_text($text, $num){
	if(strlen($text) > $num){
		$text = substr($text,0,$num) . "...";
	}
	return $text;
}

function contains($full,$search){
	$found = false;
	if(strlen(strstr($full,$search))>0){
		$found = true;
	}
	return $found;
}

function encript($pass){
	$original = md5($pass);
	$original = str_replace("7","v8c5",$original);
	$original = str_replace("c","m16f7",$original);
	$original = str_replace("f","x3y",$original);
	$l = strlen($original);
	$key = str_replace("0","wr2",substr($original,0,$l -20));
	$key2 = str_replace("b","prlat7",substr($original,2,$l -15));
	$original = $key2.$key;
	return md5($original);
}

function db_connect($db_name){
	global $sbo_db;
	$sbo_db->connect($db_name);	
}

function str_center($first, $second, $string){
	$exnum = strlen($first);
	$pos = strpos($string,$first);
	$pos2 = strpos($string,$second);
	$extra = substr($string,$pos2);	
	return str_replace($extra,"",substr($string,$pos+$exnum));
}

function is_mobile(){
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
		return true;
	}else{
		return false;
	}	
}

function is_iphone(){
    if(strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
		$is = true;	
	}else{
		$is = false;	
	}
	return $is;
}

function get_qs_symbol($url = ""){
	if($url == ""){$url = curPageURL();}
	if(contains($url,"?")){
		$symbol = "&";
	}else{
		$symbol = "?";
	}
	return $symbol;
}

function do_post_request($url, $data, $files = false){
	$postdata = http_build_query($data);
	if($files){$type = "multipart/form-data";}
	else{$type = "application/x-www-form-urlencoded";}
	$opts = array('http'=>array('method'=>'POST','header'  => 'Content-type: '.$type,'content' => $postdata));
	$context  = stream_context_create($opts);
	return file_get_contents($url, false, $context);
}

function aorb($a, $b){
	if(!nothing($a)){
		return $a;
	}else{
		return $b;
	}
}

function create_list($name, $id, $data, $selected = NULL, $default_name = "", $onchange = "", $style = ""){
	?>
	<select name="<?php echo $name ?>" id="<?php echo $id ?>" onchange="<?php echo $onchange ?>" class=" <?php echo $style ?> ">
    	<?php if($default_name != ""){ ?><option value=""><?php echo $default_name ?></option><?php } ?>
    	<?php foreach($data as $item){ ?>
        	<option value="<?php echo $item["id"] ?>" <?php if($item["id"] == $selected){echo 'selected="selected"';} ?>><?php echo $item["label"] ?></option>
        <?php } ?>
    </select>
    <?php
}
function create_objects_list($name, $id, $data, $idvar, $labvar, $default_name = "", $selected = NULL, $onchange = "", $class = ""){
	?>
	<select name="<?php echo $name ?>" id="<?php echo $id ?>" onchange="<?php echo $onchange ?>" class="<?php echo $class ?>">
    	<?php if($default_name != ""){ ?><option value=""><?php echo $default_name ?></option><?php } ?>
    	<?php foreach($data as $item){ ?>
        	<option value="<?php echo $item->vars[$idvar] ?>" <?php if($item->vars[$idvar] == $selected){echo 'selected="selected"';} ?>><?php echo ($item->vars[$labvar])  ?></option>
        <?php } ?>
    </select>
    <?php
}

function get_monday($date, $format = "Y-m-d", $next = false){
	$days = (date("N",strtotime($date)))-1;
	$monday = date($format,strtotime($date . "- $days days"));
	if($next){$monday = date($format,strtotime($monday . "+ 7 days"));}
	return $monday;
}

function rand_str($length = 10, $type = "") {
	
	if($type == "text"){
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}else if($type == "number"){
		$characters = '0123456789';
	}else if($type == "symbols"){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%/&(*)=-_?{}[].,+:;|<>';
	}else{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function get_ip(){
	$ip = $_SERVER["REMOTE_ADDR"];
	if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	return $ip;	
}

function param($name, $secure = true, $default = ""){
	$val = "";
	if(isset($_GET[$name])){$val = $_GET[$name];}
	else if(isset($_POST[$name])){$val = $_POST[$name];}
	else if(isset($_SESSION[$name])){$val = $_SESSION[$name];}
	if($secure){$val = secure_input($val);}
	$val = trim($val);
	if($val == ""){$val = $default;}
	return $val;
	
}

function upload_file($id, $path, $name = ""){
	global $acceptable_images;
	$filename = "";
	$maxsize = 5197152;

	if(($_FILES[$id]['size'] <= $maxsize) && ($_FILES[$id]["size"] > 0)) {
		
		if (!empty($_FILES[$id]['name'])) {
		
			$ext = strtolower(pathinfo($_FILES[$id]['name'], PATHINFO_EXTENSION));
			if(in_array($ext,$acceptable_images)){
			  if($name != ""){$filename = $name . strrchr(basename($_FILES[$id]['name']),'.');}else{$filename = basename($_FILES[$id]['name']);}		  
			  $filepath = $path; 
			  $filefull = $filepath.$filename; 
			  if (!move_uploaded_file($_FILES[$id]['tmp_name'], $filefull)){$filename = "";}
			}
		}
	}
  
	return $filename;
}

function upload_multiple_file($id, $path, $index, $name = ""){
	if(!is_int($index)){
		// swap legacy call
		$tmp = $name;
		$name = $index;
		$index = $tmp;
	}

	global $acceptable_images;
	$filename = "";
	$maxsize = 5197152;

	if(($_FILES[$id]['size'][$index] <= $maxsize) && ($_FILES[$id]["size"][$index] > 0)) {
		if (!empty($_FILES[$id]['name'][$index])) {
			$ext = strtolower(pathinfo($_FILES[$id]['name'][$index], PATHINFO_EXTENSION));
			if(in_array($ext,$acceptable_images)){
			  if($name != ""){
				  $filename = $name . strrchr(basename($_FILES[$id]['name'][$index]),'.');
			  }else{
				  $filename = basename($_FILES[$id]['name'][$index]);
			  }		  
			  $filepath = $path; 
			  $filefull = $filepath.$filename; 
			  if (!move_uploaded_file($_FILES[$id]['tmp_name'][$index], $filefull)){
				  $filename = "";
			  }
			}
		}
	}
  
	return $filename;
}




function redirection(){
	$url = curPageURL();
	if(!contains($url,"www.")){	
		$url = str_replace("://","://www.",$url);	
		header("Location: $url");
	}
}

function get_images_in_dir($dir, $show_all = false){
	global $acceptable_images;
	$files = array();
	$all = scandir($dir);
	foreach($all as $file){
	
		$parts = explode(".",$file);
		if(in_array($parts[count($parts)-1],$acceptable_images) || $show_all){
			$files[] = $file;
		}
		
	}
	
	return $files;
	
}

function _date($date = ""){
	global $_date_format;
	
	if($date != ""){$res = date($_date_format,strtotime($date));}
	else{$res = date($_date_format);}
	
	return $res;	
}
function _date_time($date = ""){
	global $_time_format;
	
	if($date != ""){$res = date($_time_format,strtotime($date));}
	else{$res = date($_time_format);}
	
	return $res;	
}


function _time($date = ""){
	global $_hour_format;
	
	if($date != ""){$res = date($_hour_format,strtotime($date));}
	else{$res = date($_hour_format);}
	
	return $res;	
}

function _date_eng($date){
	return date("Y-m-d",strtotime($date));
}

function insert_agents($agents_list){
	global $_company, $_CODEX;
	$prev_agent = NULL;
	$temp_agents = array_reverse($agents_list);
	foreach($temp_agents as $iagent){
		$temp_agent = get_company_agent_by_name($iagent->agent, $_company ->vars["id"]);
		
		if(is_null($temp_agent)){
		
			$temp_agent = new _agent();
			$temp_agent ->vars["account"] = strtoupper($iagent->agent);
			$temp_agent ->vars["company"] = $_company ->vars["id"];
			$temp_agent ->vars["external_id"] = $iagent->agent_id;
			$temp_agent ->vars["parent"] = $prev_agent ->vars["id"];
			$temp_agent ->vars["password"] = $_CODEX->encrypt(rand_str(4,"number"));
			$temp_agent->insert();
		
		}else if($temp_agent ->vars["parent"] != $prev_agent ->vars["id"]){
			$temp_agent ->vars["parent"] = $prev_agent ->vars["id"];
			$temp_agent->update("parent");
		}
		
		$prev_agent = $temp_agent;
		
	}
	
	return $prev_agent;
		
}

function get_logged_player_limits($player = NULL){

    global $_company;

    // 🔥 asegurar $_player válido
    if(!is_null($player)){
        $_player = $player;
    } else {
        global $_player;
    }

    // 🔥 fallback si no hay player
    if(empty($_player) || empty($_player->vars)){
        return [
            "day_max_win"  => $_company->vars["day_max_win"]  ?? 0,
            "day_max_loss" => $_company->vars["day_max_loss"] ?? 0,
            "week_max_win" => $_company->vars["week_max_win"] ?? 0,
            "week_max_loss"=> $_company->vars["week_max_loss"]?? 0,
            "origin"       => "Casino"
        ];
    }

    $limits = [
        "day_max_win"  => null,
        "day_max_loss" => null,
        "week_max_win" => null,
        "week_max_loss"=> null,
        "origin"       => null
    ];

    // 🔥 TEMP LIMITS
    if(
        isset($_player->vars["temp_day_max_win"], $_player->vars["temp_day_max_loss"],
              $_player->vars["temp_week_max_win"], $_player->vars["temp_week_max_loss"],
              $_player->vars["temp_limit_expiration"])
        &&
        $_player->vars["temp_day_max_win"] >= 0 &&
        $_player->vars["temp_day_max_loss"] >= 0 &&
        $_player->vars["temp_week_max_win"] >= 0 &&
        $_player->vars["temp_week_max_loss"] >= 0 &&
        strtotime($_player->vars["temp_limit_expiration"]) > time()
    ){
        $limits["day_max_win"]  = $_player->vars["temp_day_max_win"];
        $limits["day_max_loss"] = $_player->vars["temp_day_max_loss"];
        $limits["week_max_win"] = $_player->vars["temp_week_max_win"];
        $limits["week_max_loss"]= $_player->vars["temp_week_max_loss"];
        $limits["origin"]       = "Temp Limit";
    }

    // 🔥 PLAYER LIMITS
    else if(
        isset($_player->vars["day_max_win"], $_player->vars["day_max_loss"],
              $_player->vars["week_max_win"], $_player->vars["week_max_loss"])
        &&
        $_player->vars["day_max_win"] >= 0 &&
        $_player->vars["day_max_loss"] >= 0 &&
        $_player->vars["week_max_win"] >= 0 &&
        $_player->vars["week_max_loss"] >= 0
    ){
        $limits["day_max_win"]  = $_player->vars["day_max_win"];
        $limits["day_max_loss"] = $_player->vars["day_max_loss"];
        $limits["week_max_win"] = $_player->vars["week_max_win"];
        $limits["week_max_loss"]= $_player->vars["week_max_loss"];
        $limits["origin"]       = "Player";
    }

    // 🔥 AGENT LIMITS
    if(
        !is_numeric($limits["day_max_win"]) ||
        !is_numeric($limits["day_max_loss"]) ||
        !is_numeric($limits["week_max_win"]) ||
        !is_numeric($limits["week_max_loss"])
    ){

        $next_id = $_player->vars["agent"] ?? 0;

        while($next_id > 0){

            $temp_agent = get_agent($next_id);

            if(empty($temp_agent) || empty($temp_agent->vars)){
                break;
            }

            if(
                isset($temp_agent->vars["day_max_win"], $temp_agent->vars["day_max_loss"],
                      $temp_agent->vars["week_max_win"], $temp_agent->vars["week_max_loss"])
                &&
                $temp_agent->vars["day_max_win"] >= 0 &&
                $temp_agent->vars["day_max_loss"] >= 0 &&
                $temp_agent->vars["week_max_win"] >= 0 &&
                $temp_agent->vars["week_max_loss"] >= 0
            ){
                $limits["day_max_win"]  = $temp_agent->vars["day_max_win"];
                $limits["day_max_loss"] = $temp_agent->vars["day_max_loss"];
                $limits["week_max_win"] = $temp_agent->vars["week_max_win"];
                $limits["week_max_loss"]= $temp_agent->vars["week_max_loss"];
                $limits["origin"]       = "Agent ".$temp_agent->vars["account"];
                break;
            }

            $next_id = $temp_agent->vars["parent"] ?? 0;
        }
    }

    // 🔥 COMPANY LIMITS FINAL
    if(
        !is_numeric($limits["day_max_win"]) ||
        !is_numeric($limits["day_max_loss"]) ||
        !is_numeric($limits["week_max_win"]) ||
        !is_numeric($limits["week_max_loss"])
    ){
        $limits["day_max_win"]  = $_company->vars["day_max_win"]  ?? 0;
        $limits["day_max_loss"] = $_company->vars["day_max_loss"] ?? 0;
        $limits["week_max_win"] = $_company->vars["week_max_win"] ?? 0;
        $limits["week_max_loss"]= $_company->vars["week_max_loss"]?? 0;
        $limits["origin"]       = "Casino";
    }

    return $limits;
}

/*
function get_logged_player_limits($player = NULL){
	global $_company;
	if(!is_null($player)){
		$_player = $player;	
	}else{
		global $_player;
	}
	$limits = array();
	if($_player ->vars["temp_day_max_win"] >= 0 && $_player ->vars["temp_day_max_loss"] >= 0 && $_player ->vars["temp_week_max_win"] >= 0 && $_player ->vars["temp_week_max_loss"] >= 0 && strtotime($_player ->vars["temp_limit_expiration"]) > time()){
		$limits["day_max_win"] = $_player ->vars["temp_day_max_win"];
		$limits["day_max_loss"] = $_player ->vars["temp_day_max_loss"];
		$limits["week_max_win"] = $_player ->vars["temp_week_max_win"];
		$limits["week_max_loss"] = $_player ->vars["temp_week_max_loss"];
		$limits["origin"] = "Temp Limit";
	}else if($_player ->vars["day_max_win"] >= 0 && $_player ->vars["day_max_loss"] >= 0 && $_player ->vars["week_max_win"] >= 0 && $_player ->vars["week_max_loss"] >= 0){
		$limits["day_max_win"] = $_player ->vars["day_max_win"];
		$limits["day_max_loss"] = $_player ->vars["day_max_loss"];
		$limits["week_max_win"] = $_player ->vars["week_max_win"];
		$limits["week_max_loss"] = $_player ->vars["week_max_loss"];
		$limits["origin"] = "Player";
	}
	
	if(!is_numeric($limits["day_max_win"]) || !is_numeric($limits["day_max_loss"]) || !is_numeric($limits["week_max_win"]) || !is_numeric($limits["week_max_loss"])){
	
		$run = true;
		$next_id = $_player ->vars["agent"];
		while($run){
			
			$temp_agent = get_agent($next_id);
			$next_id = $temp_agent ->vars["parent"];
			
			if($temp_agent ->vars["day_max_win"] >= 0 && $temp_agent ->vars["day_max_loss"] >= 0 && $temp_agent ->vars["week_max_win"] >= 0 && $temp_agent ->vars["week_max_loss"] >= 0){
				$limits["day_max_win"] = $temp_agent ->vars["day_max_win"];
				$limits["day_max_loss"] = $temp_agent ->vars["day_max_loss"];
				$limits["week_max_win"] = $temp_agent ->vars["week_max_win"];
				$limits["week_max_loss"] = $temp_agent ->vars["week_max_loss"];
				$limits["origin"] = "Agent ".$temp_agent ->vars["account"]."";
				$run = false;
			}
			
			if($next_id < 1){$run = false;}
			
		}
		
	}
	
	if(!is_numeric($limits["day_max_win"]) || !is_numeric($limits["day_max_loss"]) || !is_numeric($limits["week_max_win"]) || !is_numeric($limits["week_max_loss"])){
	
		$limits["day_max_win"] = $_company ->vars["day_max_win"];
		$limits["day_max_loss"] = $_company ->vars["day_max_loss"];
		$limits["week_max_win"] = $_company ->vars["week_max_win"];
		$limits["week_max_loss"] = $_company ->vars["week_max_loss"];
		$limits["origin"] = "Casino";
		
	}
	
	return $limits;
	
}
*/

function get_agent_limits($agent){
	global $_company;
	$limits = array();
	if($agent ->vars["day_max_win"] >= 0 && $agent ->vars["day_max_loss"] >= 0 && $agent ->vars["week_max_win"] >= 0 && $agent ->vars["week_max_loss"] >= 0){
		$limits["day_max_win"] = $agent ->vars["day_max_win"];
		$limits["day_max_loss"] = $agent ->vars["day_max_loss"];
		$limits["week_max_win"] = $agent ->vars["week_max_win"];
		$limits["week_max_loss"] = $agent ->vars["week_max_loss"];
		$limits["origin"] = "Agent";
	}
	
	if(!is_numeric($limits["day_max_win"]) || !is_numeric($limits["day_max_loss"]) || !is_numeric($limits["week_max_win"]) || !is_numeric($limits["week_max_loss"])){
	
		if($agent ->vars["parent"] > 0){
			$run = true;
			$next_id = $agent ->vars["parent"];
			while($run){
				
				$temp_agent = get_agent($next_id);
				$next_id = $temp_agent ->vars["parent"];
				
				if($temp_agent ->vars["day_max_win"] >= 0 && $temp_agent ->vars["day_max_loss"] >= 0 && $temp_agent ->vars["week_max_win"] >= 0 && $temp_agent ->vars["week_max_loss"] >= 0){
					$limits["day_max_win"] = $temp_agent ->vars["day_max_win"];
					$limits["day_max_loss"] = $temp_agent ->vars["day_max_loss"];
					$limits["week_max_win"] = $temp_agent ->vars["week_max_win"];
					$limits["week_max_loss"] = $temp_agent ->vars["week_max_loss"];
					$limits["origin"] = "Agent ".$temp_agent ->vars["account"]."";
					$run = false;
				}
				
				if($next_id < 1){$run = false;}
				
			}
		}
		
	}
	
	if(!is_numeric($limits["day_max_win"]) || !is_numeric($limits["day_max_loss"]) || !is_numeric($limits["week_max_win"]) || !is_numeric($limits["week_max_loss"])){
	
		$limits["day_max_win"] = $_company ->vars["day_max_win"];
		$limits["day_max_loss"] = $_company ->vars["day_max_loss"];
		$limits["week_max_win"] = $_company ->vars["week_max_win"];
		$limits["week_max_loss"] = $_company ->vars["week_max_loss"];
		$limits["origin"] = "Casino";
		
	}
	
	return $limits;
	
}


function get_agent_games_limits($agent){
	global $_company;	
	$agent_games = get_games_by_agent($agent ->vars["id"]);
	$games = get_all_company_games($_company ->vars["id"]);
	$limits = array();	

	if(!count($agent_games) && $agent ->vars["parent"] > 0){
		$run = true;
		$next_id = $agent ->vars["parent"];
		while($run){
			$temp_agent_games = get_games_by_agent($next_id);
			$temp_agent = get_agent($next_id);
			$next_id = $temp_agent ->vars["parent"];

			if(count($temp_agent_games)){
				$run = false;
				$agent_games = $temp_agent_games;
				$agent_origin = " ".$temp_agent ->vars["account"];
			}
			
			if($next_id < 1){$run = false;}
			
		}
	}
	
	
	foreach($games as $game){
		
		if(count($agent_games)>0){
			$limits["origin"] = "Agent" . $agent_origin;
			$limits["games"][$game ->vars["id"]] = array("active"=>$agent_games[$game ->vars["id"]]->vars["visible"],
															"min"=>$agent_games[$game ->vars["id"]]->vars["min_amount"]*1,
															"max"=>$agent_games[$game ->vars["id"]]->vars["max_amount"]*1);
		}else{
			$limits["origin"] = "Casino";
			$limits["games"][$game ->vars["id"]] = array("active"=>$game->vars["visible"],"min"=>$game->vars["min_amount"],"max"=>$game->vars["max_amount"]);
		}
			
	}
	
	
	return $limits;
	
}

function get_player_games_limits($player){
	global $_company;	
	$player_games = get_games_by_player($player ->vars["id"]);
	$games = get_all_company_games($_company ->vars["id"]);
	$limits = array();
	
	foreach($games as $game){
		
		if(count($player_games)>0){
			$limits["origin"] = "Player";
			$limits["games"][$game ->vars["id"]] = array("active"=>$player_games[$game ->vars["id"]]->vars["visible"],
															"min"=>$player_games[$game ->vars["id"]]->vars["min_amount"]*1,
															"max"=>$player_games[$game ->vars["id"]]->vars["max_amount"]*1);
		}else{
			$pagent = get_agent($player ->vars["agent"]);
			$limits = get_agent_games_limits($pagent);
			if($limits["origin"] == "Agent"){
				$limits["origin"] = "Agent ".$pagent ->vars["account"];
			}
		}
			
	}
	
	
	return $limits;
	
}

function rc_random($from, $to){
	$nums = array();
	for($i=0;$i<100;$i++){
		$nums[] = mt_rand($from, $to);
	}
	shuffle($nums);
	return $nums[mt_rand(0,99)];
}

function prepare_hand($hand){
	$parts = explode(",",$hand);
	$str = array();
	
	foreach($parts as $part){
		$sym = substr($part,0,1);
		$num = substr($part,1);
		
		if($sym == "H" || $sym == "D"){
			$num = "<span style='color:#c21924'>$num</span>";
		}
		
		$sym = str_replace("D",'<img src="https://play.casinogamesonline.com/utilities/images/web/cards/diamond.png" width="10" height="12" />',$sym);
		$sym = str_replace("S",'<img src="https://play.casinogamesonline.com/utilities/images/web/cards/spade.png" width="10" height="12" />',$sym);
		$sym = str_replace("H",'<img src="https://play.casinogamesonline.com/utilities/images/web/cards/hearts.png" width="10" height="12" />',$sym);
		$sym = str_replace("C",'<img src="https://play.casinogamesonline.com/utilities/images/web/cards/cubs.png" width="10" height="12" />',$sym);
		
		$str[] = $num.$sym;
	}
	
	return implode(" , ",$str);
}

function prepare_reel($reel, $game){
	$res = "";
	switch($game){ 
		case 4:
			
			$lines = explode(",",$reel);
			$str = array();
			
			$i=0;
			foreach($lines as $line){
				$i++;
				if($i>1){$str[] = "<br />";}
				$parts = explode("|",$line);		
				foreach($parts as $part){
					$str[] = '<img src="https://play.casinogamesonline.com/utilities/images/web/reel/'.$part.'.png" width="20" height="20" />';
				}
			
			}
			$res = implode(" ",$str);
			
		break;
		case 9:
			
			if($reel != "nd"){
			
			$list = json_decode($reel,true);

			$x=0;
			foreach($list as $item){
				$x++;
				$list[$x] = explode("|",$item);
			}
			
			for($i=0;$i<3;$i++){
				for($e=1;$e<=5;$e++){
					$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
				}
				$res .= "<br />";
			}
			
			}
			
		break;
		case 11:
			
			if($reel != "nd"){
			
			$list = json_decode($reel,true);

			$x=0;
			foreach($list as $item){
				$x++;
				$list[$x] = explode("|",$item);
			}
			
			for($i=0;$i<3;$i++){
				for($e=1;$e<=5;$e++){
					$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_FS/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
				}
				$res .= "<br />";
			}
			
			}
			
		break;
		
		case 14:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_FS2/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 17:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_sweet/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 18:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_crypto/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 19:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_dragon/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 24:
			
			$parts = explode("|",$reel);
			$res = "X".$parts[0].",X".$parts[1];
			
		break;
		
		case 25:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_giants/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 29:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_alien/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 32:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_zeus/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
		case 34:
			
			if($reel != "nd"){
			
				$list = json_decode($reel,true);
	
				$x=0;
				foreach($list as $item){
					$x++;
					$list[$x] = explode("|",$item);
				}
				
				for($i=0;$i<3;$i++){
					for($e=1;$e<=5;$e++){
						$res .=  '<img src="https://play.casinogamesonline.com/utilities/games/multi_slot_viking/imgs/figures/'.$list[$e][$i].'.png" width="50" height="50" />';
					}
					$res .= "<br />";
				}
			
			}
			
		break;
		
	}
		
	return $res;
	
	
}

function sort_players($players){
	$splayers = array();
	foreach($players as $p){
		$splayers[$p ->vars["account"]]	= $p;
	}
	ksort($splayers);
	
	return $splayers;
}

function generate_server_seed($text = "", $from = 1000000, $to = 1999999, $hard_number = ""){
	global $_CODEX;
	$server_seed1 = rand_str(mt_rand(15,75),"symbols");
	$server_seed2 = rand_str(mt_rand(15,75),"symbols");
	
	if($hard_number != ""){
		$server_number = $hard_number;	
	}else{
		$server_number = mt_rand($from,$to);	
	}
	
	$server_hash = password_hash($server_seed1.$server_number.$server_seed2, PASSWORD_BCRYPT );
		
	$_SESSION[$text."xz1"] = $_CODEX->encrypt($server_seed1);
	$_SESSION[$text."xz2"] = $_CODEX->encrypt($server_seed2);
	$_SESSION[$text."xnr"] = $_CODEX->encrypt($server_number);
	$_SESSION[$text."xhs"] = $_CODEX->encrypt($server_hash);
	
	return $server_hash;
}

function discover_server_seed($text = ""){
	global $_CODEX;
	$data = array();
	$data["xz1"] = $_CODEX->decrypt($_SESSION[$text."xz1"]);
	$data["xz2"] = $_CODEX->decrypt($_SESSION[$text."xz2"]);
	$data["xnr"] = $_CODEX->decrypt($_SESSION[$text."xnr"]);
	$data["xhs"] = $_CODEX->decrypt($_SESSION[$text."xhs"]);	
	return $data;
}

function sum_multi_nums($list1, $list2, $del = ",", $top = ""){

	$nums1 = explode($del,$list1);
	$nums2 = explode($del,$list2);
	$nums_res = array();
	
	for($i=0;$i<count($nums1);$i++){
		
		if(!is_numeric($nums2[$i])){$nums2[$i] = 0;}
		
		$new_num = $nums1[$i] + $nums2[$i];
		
		if(is_numeric($top)){
			if($new_num > $top){
				$new_num = $new_num - $top;
			}	
		}
		
		$nums_res[] = $new_num;
	}
	
	return implode($del,$nums_res);
	
}
function get_lobby_url(){


//var_dump($_SESSION);
//exit;

    global $_company, $player_token, $_player, $cashier_code;

    // 🔥 BASE URL DINÁMICA
    $base = defined("CASINO_BASE_URL")
        ? CASINO_BASE_URL
        : (isset($_SERVER['HTTP_HOST']) ? "http://".$_SERVER['HTTP_HOST'] : "");

    // 🔹 STANDALONE
    if($_company->vars["provider_system_id"] == 3){
        return $base . "/index.php";
    }

    // 🔹 DGS / ASIS (MISMO FORMATO PERO MISMO HOST)
    return $base . "/index.php?cid=".$_company->vars["id"]
        ."&cps=".$_company->vars["password"]
        ."&token=".urlencode($player_token)
        ."&cshcd=".$cashier_code
        ."&account=".$_player->vars["account"];
}




function seed_shuffle($array, $seed){
	mt_srand($seed);
	$order = array();
	for($i=0;$i<count($array);$i++){
		$order[] = mt_rand();
	}
	array_multisort($order, $array); //sorts array2 based on the values of array 1

	return $array;
}

function prepare_decks($amount){
	global $_deck;
	$cards = array();
	foreach($_deck as $card){
		for($i=0;$i<$amount;$i++){
			$cards[] = 	$card;
		}
	}	
	return $cards;
}

function prepare_spanish_decks($amount){
	global $_spanish_deck;
	$cards = array();
	foreach($_spanish_deck as $card){
		for($i=0;$i<$amount;$i++){
			$cards[] = 	$card;
		}
	}	
	return $cards;
}

function get_files_in_folder($dir){
	$files = array();
	if (is_dir($dir)) {
	  if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
		  if ($file != "." && $file != "..") {  // exclude "." and ".." directories
		  
			$files[] = $file;
			
		  }
		}
		closedir($dh);
	  }
	}
	return $files;	
}

function get_cashier_link($cashier_code){
	$link = "";
	if($cashier_code == "pbj"){
		$link = "https://www.playblackjack.com/core/external_cashier.php";
	}else if($cashier_code == "bitbet"){
		$link = "https://www.bitbet.com/core/external_cashier.php";
	}	
	
	return $link;
}

function record_transaction(
    $player_id,
    $type,
    $amount,
    $balance_before,
    $balance_after,
    $game_id = null,
    $round_id = null,
    $reference = null,
    $currency = "USD",
    $provider = "standalone"
){
    $trans = new _transactions();

    $trans->vars['player_id']      = intval($player_id);
    $trans->vars['type']           = $type;
    $trans->vars['amount']         = round(floatval($amount), 2);
    $trans->vars['balance_before'] = round(floatval($balance_before), 2);
    $trans->vars['balance_after']  = round(floatval($balance_after), 2);
    $trans->vars['game']           = $game_id ? intval($game_id) : null;
    $trans->vars['round_id']       = $round_id ?: null;
    $trans->vars['reference']      = $reference ?: null;
    $trans->vars['currency']       = $currency;
    $trans->vars['provider']       = $provider;

    $trans->insert();
}




?>