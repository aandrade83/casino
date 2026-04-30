<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/web/header.php"); ?>

<?
$contest = get_active_contest($_player ->vars["company"]);

if(!is_null($contest) && !is_null($_player)){
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><? echo $contest ->vars["name"] ?> History</h1>
    </div>
</div>

<?
$data = array();
$teams = get_all_contest_teams($contest ->vars["id"]);
$data = get_player_contest_team_history($contest ->vars["id"], $_player ->vars["id"]);
?>


<div class="dataTable_wrapper">

	<? if(count($data)){ ?>

    <table style="max-width:300px !important;" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="50" align="center">Card</th>
                <th align="center">Points</th>
                <th align="center">Date</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; foreach($data as $item){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
            <tr class="<? echo $class ?>">
                <td align="center">
					<img src="utilities/images/contests/<? echo $contest ->vars["imgs_folder"]."/".$teams[$item["team"]] ->vars["image"] ?>" width="50"/><br />
					<? echo $teams[$item["team"]] ->vars["name"] ?>
                </td>
                <td align="center"><br /><? echo $item["points"] ?></td>
                <td align="center"><br /><? echo $item["tdate"] ?></td>
          </tr>
            <? } ?>
            
        </tbody>
    </table>
    <? }else{echo "You haven't got any cards yet";} ?>
    
</div>

<? }else{echo "No data found";} ?>


<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/web/footer.php"); ?>