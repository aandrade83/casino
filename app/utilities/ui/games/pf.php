<? 
$pf_use_session = $pf_use_session ?? false;
if($_company ->vars["prov_fair"]){ ?>
    
    <? if(empty($sword)){$sword = "Spin";} ?>
    
    <div class="pfbox" id="pfbox" style="display:none;">
    
    	<div class="pflimiter">
            
            <h2 align="center">PROVABLY FAIR</h2>
            
            <div class="pfhiw"><a href="<?php echo CASINO_BASE_URL; ?>/provably_fair.php?gid=<? echo $_game ->vars["id"] ?>" style="color:#069;" target="_blank">How it works?</a></div>
            
            <p>
            
                <strong>Next<? if($pf_use_session){echo "/Current";} ?> <? echo $sword ?>:</strong><br>
                <div class="pfrox">Server #: <br><input type="text" id="pf_n_shash" size="65" disabled readonly value="<? echo $server_num ?>"></div>
    
                <div class="pfrox">Player #: <br><input type="text" id="pf_player_num" size="65" value="<? echo $player_num ?>"></div>
            
            </p>
            
            <p>
            
                <strong>Last <? echo $sword ?>:</strong><br>
                <div class="pfrox">Server hash: <br><input type="text" id="pf_l_shash" value="-" disabled readonly size="65"></span></div>
                <div class="pfrox">Secret1: <br><input type="text" id="pf_l_sec1" value="-" disabled readonly size="65"></span></div>
                <div class="pfrox">Secret2: <br><input type="text" id="pf_l_sec2" value="-" disabled readonly size="65"></span></div>
                <div class="pfrox">Server #: <br><input type="text" id="pf_l_snum" value="-" disabled readonly size="65"></span></div>
                <div class="pfrox">Player #: <br><input type="text" id="pf_l_pnum" value="-" disabled readonly size="65"></span></div>
                <div class="pfrox">Result #: <br><input type="text" id="pf_l_resnum" value="-" disabled readonly size="65"></span></div>
            
            </p>
        
    	</div>
    
    </div>
    
    <? } ?>