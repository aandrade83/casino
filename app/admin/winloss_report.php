<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Win Loss Report</h1>
    </div>
</div>

<?
$from = param("from");
if($from == ""){$from = date("Y-m-d");}	
$to = param("to");	
if($to == ""){$to = date("Y-m-d");}	
$sagent = param("sagent");	
$splayer = param("splayer");
$sids = param("iks");	
?>

<form method="post">
    
    From: 
    <input name="from" class="form-control small_control" type="date" value="<? echo $from ?>">&nbsp;&nbsp;
    
    To:
    <input name="to" class="form-control small_control" type="date" value="<? echo $to ?>">&nbsp;&nbsp;
    
    Agent:
    <? 
	$list = $_agent ->get_all_kids(true); 
	$ids = array();
	?>
    <select class="form-control small_control" name="sagent" id="sagent">
        <option value="">All</option>
        <? foreach($list as $item){ $ids[] = $item ->vars["id"]  ?>
            <option value="<? echo $item ->vars["id"] ?>" <? if($sagent == $item ->vars["id"]){ ?> selected <? } ?>>
                <? echo $item ->vars["account"] ?>
            </option>
        <? } ?>
    </select>&nbsp;&nbsp;
    
    Player:
    <? $list = $_agent ->get_all_players(); ?>
    <select class="form-control small_control" name="splayer" id="splayer">
        <option value="">All</option>
        <? foreach($list as $item){ ?>
            <option value="<? echo $item ->vars["id"] ?>" <? if($splayer == $item ->vars["id"]){ ?> selected <? } ?> >
                <? echo $item ->vars["account"] ?>
            </option>
        <? } ?>
    </select>&nbsp;&nbsp;
    
    <button type="submit" class="btn btn-default">Search</button>

</form>

<?

if($sagent != ""){
	$base_agent = get_agent($sagent);
	if(!is_null($base_agent)){
		$sagents = $base_agent->get_kids_ids(true);	
	}
}



$data = get_winloss_report($from, $to, implode(",",$ids), $sagents, $splayer);
$totals = array();
?>
<br><br>
<div class="dataTable_wrapper">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Player</th>
                <th>Plays</th>
                <th>Wagered</th>
                <th>Winloss</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; foreach($data as $item){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
            <tr class="<? echo $class ?>">
                <td><? echo $item["account"] ?></td>
                <td><? echo $item["plays"]; $totals["plays"] +=$item["plays"];  ?></td>
                <td><? echo $item["wagered"]; $totals["wagered"] +=$item["wagered"]; ?></td>
                <td><? echo $item["winloss"]; $totals["winloss"] +=$item["winloss"]; ?></td>
            </tr>
            <? } ?>
            
        </tbody>
         <thead>
            <tr>
                <th>Total:</th>
                <th><? echo $totals["plays"] ?></th>
                <th><? echo $totals["wagered"] ?></th>
                <th><? echo $totals["winloss"] ?></th>
            </tr>
        </thead>
    </table>
    
</div>




<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>