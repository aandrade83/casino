<?
$games = $_player->get_allowed_games();
$categories = get_all_categories();
$jgames = array();


$link = "https://play.casinogamesonline.com/?cid=".$_company ->vars["id"]."&cps=".$_company ->vars["password"]."&token=".urlencode($player_token)."&account=".$_player ->vars["account"]."&game=";

foreach($games as $game){
    $jgames[] = array("name"=>$game->vars["name"],
                      "logo"=>"utilities/games/".$game->vars["path"] ."/imgs/lobby.png",
                      "url"=>$link.$game->vars["id"],
					  "category"=>$categories[$game->vars["category"]] ->vars["name"],
					  "category_id"=>$game->vars["category"]
                      );	
}

echo json_encode($jgames);

?>