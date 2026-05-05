<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<?
$days = get_fisrt_last_day_of_week(date("Y-m-d"));
$list = $_agent ->get_all_kids(true); 
$ids = array();
foreach($list as $item){ $ids[] = $item ->vars["id"];}
$data = get_winloss_report($days["Monday"], date("Y-m-d",strtotime($days["Sunday"] . "+ 1 day")), implode(",",$ids), "", "", false);
$pcount = get_player_count($days["Monday"], date("Y-m-d",strtotime($days["Sunday"] . "+ 1 day")), implode(",",$ids));
print_r($pcount);
?>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">This Week Numbers</h1>
    </div>
</div>

<div class="dash_box" align="center">
	<h2 class="dash_icon"><i class="fa fa-users fa-fw"></i></h2>
    <h3>Players</h3>
    <h1 class="highlighted"><? echo $pcount["total"] ?></h1>
</div>

<div class="dash_box" align="center">
	<h2 class="dash_icon"><i class="fa fa-money fa-fw"></i></h2>
    <h3>Win / Loss</h3>
    <h1 class="highlighted"><? echo $data[0]["winloss"] ?></h1>
</div>

<div class="dash_box" align="center">
	<h2 class="dash_icon"><i class="fa fa-trophy fa-fw"></i></h2>
    <h3>Wagered</h3>
    <h1 class="highlighted"><? echo $data[0]["wagered"] ?></h1>
</div>




<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>