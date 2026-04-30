<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<?
$contest = get_contest(param("cid"));
if(!is_null($contest)){
	$title = "Edit Contest";
}else{
	$title = "New Contest";
}
?>



<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><? echo $title ?></h1>
    </div>
</div>


<div>
    
    <script type="text/javascript">
	var validations = new Array();
	validations.push({id:"name",type:"null", msg:"Please insert a Name"});
	validations.push({id:"start_date",type:"null", msg:"Please insert a start date"});
	validations.push({id:"end_date",type:"null", msg:"Please insert an end date"});
	validations.push({id:"imgs_folder",type:"null", msg:"Please insert the images folder"});
	validations.push({id:"rules",type:"null", msg:"Please insert the rules"});
	</script>
    <form action="../utilities/process/actions/admin/new_contest.php" method="post" onsubmit="return validate(validations);">
    
    	<input type="hidden" name="cid" value="<? echo $contest ->vars["id"]; ?>" />
    
        <div class="form-group">
            <label>Name</label>
            <input id="name" name="name" class="form-control" value="<? echo $contest ->vars["name"] ?>">
        </div>
        
        <div class="form-group">
            <label>Start Date</label>
            <input id="start_date" name="start_date" type="date" class="form-control" value="<? echo $contest ->vars["start_date"] ?>">
        </div>
        
        <div class="form-group">
            <label>End Date</label>
            <input id="end_date" name="end_date" type="date" class="form-control" value="<? echo $contest ->vars["end_date"] ?>">
        </div>
        
        <div class="form-group">
            <label>Images Folder</label>
            <input id="imgs_folder" name="imgs_folder" class="form-control" value="<? echo $contest ->vars["imgs_folder"] ?>">
        </div>
        
        <div class="form-group">
          	<label>Rules</label>
            <textarea name="rules" class="form-control" rows="10" id="rules"><? echo remove_br($contest ->vars["rules"]) ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-default">Save</button>
    
    </form>

</div>

<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>