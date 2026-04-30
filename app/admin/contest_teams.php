<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<? $contest = get_contest(param("cid")); ?>

<? if(!is_null($contest)){ ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><? echo $contest ->vars["name"] ?> Teams</h1>
    </div>
</div>


<?
$teams = get_all_contest_teams($contest ->vars["id"]);
?>
<br><br>
<div class="dataTable_wrapper">

	<div><a href="new_contest_team.php?cid=<? echo $contest ->vars["id"] ?>">+ New Team</a><br /><br /></div>

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Points</th>
                <th>Players</th>
                <th>Active</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; foreach($teams as $team){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
            <tr class="<? echo $class ?>">
                <td width="50"><img src="../utilities/images/contests/<? echo $contest ->vars["imgs_folder"]."/".$team ->vars["image"] ?>" width="50"/></td>
                <td><? echo $team ->vars["name"] ?> <a id="t<? echo $team ->vars["id"] ?>"></a></td>
                <td><? echo $team ->vars["points"] ?></td>
                <td><a href="contest_team_players.php?tid=<? echo $team ->vars["id"] ?>">Leaderboard</a></td>
                <td><a href="../utilities/process/actions/admin/activate_team.php?tid=<? echo $team ->vars["id"] ?>"> <? echo print_boolean($team ->vars["active"]) ?> </a></td>
                <td><a href="new_contest_team.php?tid=<? echo $team ->vars["id"] ?>&cid=<? echo $contest ->vars["id"] ?>">Edit</a></td>
            </tr>
            <? } ?>
            
        </tbody>
       
    </table>
    
</div>


<? }else{echo "Contest not found";} ?>

<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>