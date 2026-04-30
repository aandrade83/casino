<li>
    <a href="dash.php"><i class="fa fa-line-chart fa-fw"></i> Dashboard</a>
</li>
<?php /*?><? if($_agent ->vars["manage_company"]){ ?>
<li>
    <a href="#"><i class="fa fa-building-o fa-fw"></i> Company</a>
</li>
<? } ?><?php */?>
<? if($_agent ->vars["ma_access"]){ ?>
<li>
    <a href="contests.php"><i class="fa fa-bar-chart fa-fw"></i> Contests</a>
</li>
<? } ?>
<li>
    <a href="limits.php"><i class="fa fa-arrow-up fa-fw"></i> Limits</a>
</li>


<li>
    <a href="javascript:;"><i class="fa fa-calendar fa-fw"></i>&nbsp;&nbsp;Reports<span class="fa arrow"></span></a>
    <ul class="nav nav-second-level">
        <li>
            <a href="winloss_report.php">Win / Loss Report</a>
        </li>
         <li>
            <a href="hold_percentage_report.php">Hold Percentage Report</a>
        </li>
        <li>
            <a href="history.php">Player History</a>
        </li>
    </ul>
</li>
