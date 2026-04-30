<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<?
$contest = get_contest(param("cid"));
$team = get_contest_team(param("tid"));
if(!is_null($team)){
	$title = "Edit Contest Team";
}else{
	$title = "New Contest Team";
}
?>

<? if(!is_null($contest)){ ?>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><? echo $title ?></h1>
    </div>
</div>


<div>
    
    <script type="text/javascript">
	var validations = new Array();
	validations.push({id:"name",type:"null", msg:"Please insert a Name"});
	validations.push({id:"image",type:"null", msg:"Please choose an image"});
	validations.push({id:"points",type:"null", msg:"Please insert the points"});
	</script>
    <form action="../utilities/process/actions/admin/new_contest_team.php" method="post" onsubmit="return validate(validations);">
    
    	<input type="hidden" name="tid" value="<? echo $team ->vars["id"]; ?>" />
        <input type="hidden" name="cid" value="<? echo $contest ->vars["id"]; ?>" />
    
        <div class="form-group">
            <label>Name</label>
            <input id="name" name="name" class="form-control" value="<? echo $team ->vars["name"] ?>">
        </div>
                
        <div class="form-group">
            <label>Image</label>
            <? $files = get_files_in_folder($_SERVER['DOCUMENT_ROOT'] ."/utilities/images/contests/".$contest ->vars["imgs_folder"]); ?>
            <select class="form-control" name="image" id="image">
            	<? foreach($files as $file){ ?>
                	<option value="<? echo $file ?>" <? if($file == $team ->vars["image"]){ ?>selected="selected" <? } ?> ><? echo $file ?></option> 
                <? } ?>            
            </select>
        </div>
        
        <div class="form-group">
            <label>Points</label>
            <input id="points" name="points" class="form-control" value="<? echo $team ->vars["points"] ?>">
        </div>
        
        <button type="submit" class="btn btn-default">Save</button>
    
    </form>

</div>

<? }else{echo "Contest not found";} ?>

<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>