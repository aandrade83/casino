<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<? $team = get_contest_team(param("tid")); ?>

<? if(!is_null($team)){ ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><? echo $team ->vars["name"] ?> Leaderboard</h1>
    </div>
</div>


<?
$players = get_contest_team_player_totals($team ->vars["id"]);
$contest = get_contest($team ->vars["contest"]);
?>
<br><br>
<div class="dataTable_wrapper">

	<div><a href="contest_teams.php?cid=<? echo $team ->vars["contest"] ?>"> << Back </a><br /><br /></div>

    <table style="width:300px !important;" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Player</th>
                <th>Points</th>
                <th>Avg Bet</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; foreach($players as $player){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
            <? 
			
			$avg = get_avr_bet_by_player($player["player_id"], $contest ->vars["start_date"], $contest ->vars["end_date"]); 
			?>
            <tr class="<? echo $class ?>">
                <td><? echo $player["account"] ?></td>
                <td><? echo $player["points"] ?></td>
                <td><? echo round($avg["total"]) ?></td>
            </tr>
            <? } ?>
            
        </tbody>
       
    </table>
    
</div>


<? }else{echo "Team not found";} ?>

<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>