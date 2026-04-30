<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Casino Contests</h1>
    </div>
</div>


<?
$contests = get_all_contests();
?>
<br><br>
<div class="dataTable_wrapper">

	<div><a href="new_contest.php">+ New Contest</a><br /><br /></div>

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Active</th>
                <th>Teams</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; foreach($contests as $contest){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
            <tr class="<? echo $class ?>">
                <td><? echo $contest ->vars["name"] ?></td>
                <td><? echo $contest ->vars["start_date"] ?></td>
                <td><? echo $contest ->vars["end_date"] ?></td>
                <td>
                	<? 
					if($contest ->vars["active"]){
						?> <a href="../utilities/process/actions/admin/activate_contest.php?cid=<? echo $contest ->vars["id"] ?>&action=disable">Active</a> <?
					}else{
						?> <a href="javascript:;" onclick="if(confirm('This action will disable any other contest and set this one as the only one active. Do you want to continue?')){location.href = '../utilities/process/actions/admin/activate_contest.php?cid=<? echo $contest ->vars["id"] ?>&action=enable';}">Inactive</a> <?
					}
					?>
                </td>
                <td><a href="contest_teams.php?cid=<? echo $contest ->vars["id"] ?>">Manage Teams</a></td>
                <td><a href="new_contest.php?cid=<? echo $contest ->vars["id"] ?>">Edit</a></td>
            </tr>
            <? } ?>
            
        </tbody>
       
    </table>
    
</div>




<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>