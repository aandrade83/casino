<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Hold Percentage Report</h1>
    </div>
</div>

<?
$from = param("from");
if($from == ""){$from = date("Y-m-d");}	
$to = param("to");	
if($to == ""){$to = date("Y-m-d");}	



$ids = array();	
$list = $_agent ->get_all_players();
foreach($list as $item){ $ids[] = $item ->vars["id"];  }
$data = get_holdpercentage_report($from, $to,implode(",",$ids));
$games = get_all_games();
$totals = array();

?>

<form method="post">
    
    From: 
    <input name="from" class="form-control small_control" type="date" value="<? echo $from ?>">&nbsp;&nbsp;
    
    To:
    <input name="to" class="form-control small_control" type="date" value="<? echo $to ?>">&nbsp;&nbsp;
    
    
    <button type="submit" class="btn btn-default">Search</button>

</form>


<br><br>
<div class="dataTable_wrapper">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Game</th>
                <th># Games</th>
                <th>Wagered</th>
                <th>Winloss</th>
                <th>Hold Percentage</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; if(empty($data)){$data = array();}
             foreach($data as $item){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
             <?
               $h_perc = round((($item["winloss"]*100)/$item["wagered"]),2);
               if($h_perc > 0) {$h_perc = $h_perc * -1;} else { $h_perc =abs($h_perc);}
             ?>
            <tr class="<? echo $class ?>">
                <td><? echo $games[$item["game"]]['name'] ?></td>
                <td><? echo $item["games"]; $totals["plays"] +=$item["games"];  ?></td>
                <td><? echo $item["wagered"]; $totals["wagered"] +=$item["wagered"]; ?></td>
                <td><? echo $item["winloss"]; $totals["winloss"] +=$item["winloss"]; ?></td>
                <td><? echo $h_perc." %";  ?></td>
            </tr>
            <? } ?>
            
        </tbody>
         <thead>
            <tr>
                <th>Total:</th>
                <th><? echo $totals["plays"] ?></th>
                <th><? echo $totals["wagered"] ?></th>
                <th><? echo $totals["winloss"] ?></th>
                <?
                $t_perc = round((($totals["winloss"]*100)/$totals["wagered"]),2);
                if($t_perc > 0) {$t_perc = $t_perc * -1;} else { $t_perc =abs($t_perc);}
                ?>
                <th><? echo $t_perc." %";?></th>
            </tr>
        </thead>
    </table>
    
</div>




<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>