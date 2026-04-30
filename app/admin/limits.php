<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Casino Limits</h1>
    </div>
</div>

<script type="text/javascript">
function change_limis_tab(tab){
	$(".page_tab").removeClass("selected_tab");
	$("#"+tab+"_tab").addClass("selected_tab");
	$(".tab_content").hide();
	$("#"+tab+"_content").show();
}
</script>

<? if($_agent ->vars["manage_company"]){ ?>
<div class="page_tab" id="company_tab" onclick="change_limis_tab('company');">Company</div>
<? } ?>
<div class="page_tab" id="agent_tab" onclick="change_limis_tab('agent');">Agents</div>
<div class="page_tab" id="player_tab" onclick="change_limis_tab('player');">Playes</div>
<div class="page_tab" id="games_tab" onclick="change_limis_tab('games');">Games</div>



<br /><br />
<? 
$agent_list = $_agent ->get_all_kids(true);
$player_list = $_agent ->get_all_players();
?>
<? if($_agent ->vars["manage_company"]){ ?>
<?
$company = get_company($_agent ->vars["company"]);	
?>
<div id="company_content" class="tab_content">
	<br />
	<h2>Company Limits</h2>
    
    <p>General Casino Limits</p>
    
    <script type="text/javascript">
	var validations = new Array();
	validations.push({id:"c_max_win_day",type:"numeric", msg:"Please insert a valid amount for Max win per day"});
	validations.push({id:"c_max_loss_day",type:"numeric", msg:"Please insert a valid amount for Max loss per day"});
	validations.push({id:"c_max_win_week",type:"numeric", msg:"Please insert a valid amount for Max win per week"});
	validations.push({id:"c_max_loss_week",type:"numeric", msg:"Please insert a valid amount for Max loss per week"});
	</script>
    <form action="../utilities/process/actions/admin/update_limits.php" method="post" onsubmit="return validate(validations);">
    
    	<input type="hidden" name="lt" value="<? echo $_CODEX->encrypt("company"); ?>" />
    
        <div class="form-group">
            <label>Max win per day</label>
            <input id="c_max_win_day" name="max_win_day" class="form-control" value="<? echo $company ->vars["day_max_win"] ?>">
        </div>
        
        <div class="form-group">
            <label>Max loss per day</label>
            <input id="c_max_loss_day" name="max_loss_day" class="form-control" value="<? echo $company ->vars["day_max_loss"] ?>">
        </div>
        
        <div class="form-group">
            <label>Max win per week</label>
            <input id="c_max_win_week" name="max_win_week" class="form-control" value="<? echo $company ->vars["week_max_win"] ?>">
        </div>
        
        <div class="form-group">
            <label>Max loss per week</label>
            <input id="c_max_loss_week" name="max_loss_week" class="form-control" value="<? echo $company ->vars["week_max_loss"] ?>">
        </div>
        
        <button type="submit" class="btn btn-default">Update Limits</button>
    
    </form>

</div>
<? } ?>
<div id="agent_content" class="tab_content">
	<br /><h2>Agents Limits</h2>
    
    <p>Limits for players under specific agent.</p>
    
    <script type="text/javascript">
    function load_agent_limits(data){
		var limits = [];
		var parts = data.split("__");
		if(data != ""){limits = parts[1].split("|");}
		
		$(".alimit").val("");
		$("#agent_detail").html("");	
		
		if(limits[0]*1 >= 0){
			$("#a_max_win_day").val(limits[0]);
			$("#agent_detail").html("Using his own limits. <a href='../utilities/process/actions/admin/delete_limits.php?lt=<? echo urlencode($_CODEX->encrypt("agent")); ?>&did="+parts[0]+"'>Delete this limits and use inherited limits</a>.");
		}else if(data != ""){
			$.getJSON("https://play.casinogamesonline.com/utilities/process/actions/admin/get_limits.php?lt=<? echo urlencode($_CODEX->encrypt("agent")); ?>&did="+parts[0]+"",function(jsdata){
				$("#agent_detail").html("Limits inherited from <strong>"+jsdata.origin+"</strong>:<br /><strong>Max win per day:</strong> "+jsdata.day_max_win+"<br /><strong>Max loss per day:</strong> "+jsdata.day_max_loss+"<br /><strong>Max win per week:</strong> "+jsdata.week_max_win+"<br /><strong>Max loss per week:</strong> "+jsdata.week_max_loss+"<br /><br />Complete the form to create limits for this agent.");	
			});
		}
		if(limits[1]*1 >= 0){$("#a_max_loss_day").val(limits[1]);}
		if(limits[2]*1 >= 0){$("#a_max_win_week").val(limits[2]);}
		if(limits[3]*1 >= 0){$("#a_max_loss_week").val(limits[3]);}
	}
	var validations2 = new Array();
	validations2.push({id:"agent",type:"null", msg:"Please select an Agent"});
	validations2.push({id:"a_max_win_day",type:"numeric", msg:"Please insert a valid amount for Max win per day"});
	validations2.push({id:"a_max_loss_day",type:"numeric", msg:"Please insert a valid amount for Max loss per day"});
	validations2.push({id:"a_max_win_week",type:"numeric", msg:"Please insert a valid amount for Max win per week"});
	validations2.push({id:"a_max_loss_week",type:"numeric", msg:"Please insert a valid amount for Max loss per week"});
    </script>
    
    <form action="../utilities/process/actions/admin/update_limits.php" method="post" onsubmit="return validate(validations2);">
    
    	<input type="hidden" name="lt" value="<? echo $_CODEX->encrypt("agent"); ?>" />
        
        <div class="form-group">
            <label>Agent</label>
            
            <select class="form-control" name="agent" id="agent" onchange="load_agent_limits(this.value);">
                <option value="">- Select -</option>
                <? foreach($agent_list as $item){ ?>
                	<option value="<? echo urlencode($_CODEX->encrypt($item ->vars["id"])); ?>__<? echo $item ->vars["day_max_win"]."|".$item ->vars["day_max_loss"]."|".$item ->vars["week_max_win"]."|".$item ->vars["week_max_loss"]; ?>">
						<? echo $item ->vars["account"] ?>
                    </option>
                <? } ?>
            </select>
            <p class="help-block" id="agent_detail"></p>
        </div>
    
        <div class="form-group">
            <label>Max win per day</label>
            <input id="a_max_win_day" name="max_win_day" class="form-control alimit">
        </div>
        
        <div class="form-group">
            <label>Max loss per day</label>
            <input id="a_max_loss_day" name="max_loss_day" class="form-control alimit">
        </div>
        
        <div class="form-group">
            <label>Max win per week</label>
            <input id="a_max_win_week" name="max_win_week" class="form-control alimit">
        </div>
        
        <div class="form-group">
            <label>Max loss per week</label>
            <input id="a_max_loss_week" name="max_loss_week" class="form-control alimit">
        </div>
        
        <button type="submit" class="btn btn-default">Update Limits</button>
    
    </form>
    
    
</div>
<div id="player_content" class="tab_content">

<br /><h2>Player Limits</h2>
    
    <p>Limits for specific player.</p>
    
    <script type="text/javascript">
    function load_player_limits(data){
		var limits = [];
		var parts = data.split("__");
		if(data != ""){
			limits = parts[1].split("|");
			limits2 = parts[2].split("|");
		}
		
		$(".plimit").val("");
		$("#player_detail").html("");	
		
		if(limits[0]*1 >= 0){
			$("#p_max_win_day").val(limits[0]);
			$("#player_detail").html("Using his own limits. <a href='../utilities/process/actions/admin/delete_limits.php?lt=<? echo urlencode($_CODEX->encrypt("player")); ?>&did="+parts[0]+"'>Delete this limits and use inherited limits</a>.");
		}else if(data != ""){
			$.getJSON("https://play.casinogamesonline.com/utilities/process/actions/admin/get_limits.php?lt=<? echo urlencode($_CODEX->encrypt("player")); ?>&did="+parts[0]+"",function(jsdata){
				$("#player_detail").html("Limits inherited from <strong>"+jsdata.origin+"</strong>:<br /><strong>Max win per day:</strong> "+jsdata.day_max_win+"<br /><strong>Max loss per day:</strong> "+jsdata.day_max_loss+"<br /><strong>Max win per week:</strong> "+jsdata.week_max_win+"<br /><strong>Max loss per week:</strong> "+jsdata.week_max_loss+"<br /><br />Complete the form to create limits for this player.");	
			});
		}
		
		if(limits[1]*1 >= 0){$("#p_max_loss_day").val(limits[1]);}
		if(limits[2]*1 >= 0){$("#p_max_win_week").val(limits[2]);}
		if(limits[3]*1 >= 0){$("#p_max_loss_week").val(limits[3]);}
		
		if(limits2[0]*1 >= 0){$("#tp_max_win_day").val(limits2[0]);}
		if(limits2[1]*1 >= 0){$("#tp_max_loss_day").val(limits2[1]);}
		if(limits2[2]*1 >= 0){$("#tp_max_win_week").val(limits2[2]);}
		if(limits2[3]*1 >= 0){
			$("#tp_max_loss_week").val(limits2[3]);
			$("#expire_date").val(limits2[4]);
		}
	}
	var validations3 = new Array();
	validations3.push({id:"player",type:"null", msg:"Please select a Player"});
	validations3.push({id:"p_max_win_day",type:"numeric", msg:"Please insert a valid amount for Max win per day"});
	validations3.push({id:"p_max_loss_day",type:"numeric", msg:"Please insert a valid amount for Max loss per day"});
	validations3.push({id:"p_max_win_week",type:"numeric", msg:"Please insert a valid amount for Max win per week"});
	validations3.push({id:"p_max_loss_week",type:"numeric", msg:"Please insert a valid amount for Max loss per week"});
	
	validations3.push({id:"tp_max_win_day",type:"numeric", msg:"Please insert a valid amount for Temporary Max win per day"});
	validations3.push({id:"tp_max_loss_day",type:"numeric", msg:"Please insert a valid amount for Temporary Max loss per day"});
	validations3.push({id:"tp_max_win_week",type:"numeric", msg:"Please insert a valid amount for Temporary Max win per week"});
	validations3.push({id:"tp_max_loss_week",type:"numeric", msg:"Please insert a valid amount for Temporary Max loss per week"});
	validations3.push({id:"expire_date",type:"null", msg:"Please select the expiring date"});
    </script>
    
    <form action="../utilities/process/actions/admin/update_limits.php" method="post" onsubmit="return validate(validations3);">
    
    	<input type="hidden" name="lt" value="<? echo $_CODEX->encrypt("player"); ?>" />
        
        <div class="form-group">
            <label>PLayer</label>
            <select class="form-control" name="player" id="player" onchange="load_player_limits(this.value);">
                <option value="">- Select -</option>
                <? foreach($player_list as $item){ ?>
                	<option value="<? echo urlencode($_CODEX->encrypt($item ->vars["id"])); ?>__<? echo $item ->vars["day_max_win"]."|".$item ->vars["day_max_loss"]."|".$item ->vars["week_max_win"]."|".$item ->vars["week_max_loss"]; ?>__<? echo $item ->vars["temp_day_max_win"]."|".$item ->vars["temp_day_max_loss"]."|".$item ->vars["temp_week_max_win"]."|".$item ->vars["temp_week_max_loss"]."|".$item ->vars["temp_limit_expiration"]; ?>">
						<? echo $item ->vars["account"] ?>
                    </option>
                <? } ?>
            </select>
            <p class="help-block" id="player_detail"></p>
        </div>
        
        <h3>
        	<a href="javascript:;" class="tabs_link tabs_link_on tabs1" onclick="toggle_rctabs('reg_content', 'temp_content', 'tabs1', $(this));">Main Limits</a>  
            <a href="javascript:;" class="tabs_link tabs1" onclick="toggle_rctabs('temp_content', 'reg_content', 'tabs1', $(this));">Temporary Limits</a>
            <div class="tabs_under"></div>
        </h3>
    
        <div class="form-group reg_content">
            <label>Max win per day</label>
            <input id="p_max_win_day" name="max_win_day" class="form-control plimit">
        </div>
        
        <div class="form-group reg_content">
            <label>Max loss per day</label>
            <input id="p_max_loss_day" name="max_loss_day" class="form-control plimit">
        </div>
        
        <div class="form-group reg_content">
            <label>Max win per week</label>
            <input id="p_max_win_week" name="max_win_week" class="form-control plimit">
        </div>
        
        <div class="form-group reg_content">
            <label>Max loss per week</label>
            <input id="p_max_loss_week" name="max_loss_week" class="form-control plimit">
        </div>
    
        <div class="form-group temp_content" style="display:none">
            <label>Temporary Max win per day</label>
            <input id="tp_max_win_day" name="tmax_win_day" class="form-control plimit">
        </div>
        
        <div class="form-group temp_content" style="display:none">
            <label>Temporary Max loss per day</label>
            <input id="tp_max_loss_day" name="tmax_loss_day" class="form-control plimit">
        </div>
        
        <div class="form-group temp_content" style="display:none">
            <label>Temporary Max win per week</label>
            <input id="tp_max_win_week" name="tmax_win_week" class="form-control plimit">
        </div>
        
        <div class="form-group temp_content" style="display:none">
            <label>Temporary Max loss per week</label>
            <input id="tp_max_loss_week" name="tmax_loss_week" class="form-control plimit">
        </div>
        
        <div class="form-group temp_content" style="display:none">
            <label>Expiring Date</label>
            <input id="expire_date" name="expire_date" type="date" class="form-control plimit" value="<? echo date("Y-m-d",time()+86400) ?>">
        </div>
        
        <br /><br />
        
        <button type="submit" class="btn btn-default">Update Limits</button>
    
    </form>

</div>

<div id="games_content" class="tab_content">
	<br /><h3>
    <? if($_agent ->vars["manage_company"]){ ?>
    <a href="javascript:;" class="tabs_link tabs_link_on tabs2" id="cgl_btn" onclick="toggle_rctabs('casino_games_limits', 'all_games_limits', 'tabs2', $(this));">Casino Games Limits</a>
    <? } ?>
    <a href="javascript:;" class="tabs_link tabs2" id="agl_btn" onclick="toggle_rctabs('agent_games_limits', 'all_games_limits', 'tabs2', $(this));">Agent Games Limits</a>
   <a href="javascript:;" class="tabs_link tabs2" id="pgl_btn" onclick="toggle_rctabs('player_games_limits', 'all_games_limits', 'tabs2', $(this));">Player Games Limits</a>
   <div class="tabs_under"></div><br />
    
    </h3>
    <? 
	$games = get_all_company_games($_agent ->vars["company"]); 
	$gltypes = array("player","agent");
	if($_agent ->vars["manage_company"]){$gltypes[] = "casino";}
	array_reverse($gltypes);
	?>
    
    <? foreach($gltypes as $glt){ ?>
    
    	<div class="<? echo $glt ?>_games_limits all_games_limits" <? if($glt != "casino"){ ?>style="display:none;"<? } ?>>  
    		
			<script type="text/javascript">
            var validations4_<? echo $glt ?> = new Array();	
            </script>
            
            <form action="../utilities/process/actions/admin/update_limits.php" method="post" onsubmit="return validate(validations4_<? echo $glt ?>);">
            <input type="hidden" name="lt" value="<? echo $_CODEX->encrypt("games"); ?>" />
            <input type="hidden" name="glt" value="<? echo $_CODEX->encrypt($glt); ?>" />
            
            <? if($glt == "agent"){ ?>
				<script type="text/javascript">
                validations4_<? echo $glt ?>.push({id:"gl_agent",type:"null", msg:"Please select an Agent"});
                function load_agent_games_limits(enc_ida){
					if(enc_ida != ''){
						$.getJSON("https://play.casinogamesonline.com/utilities/process/actions/admin/get_limits.php?lt=<? echo urlencode($_CODEX->encrypt("agent_games")); ?>&did="+enc_ida+"",function(jsdata){
							if(jsdata.origin == 'Agent'){
								$(".agent_games_field_agent").prop("disabled",false);
								$("#agent_games_detail").html("Using his own limits. <a href='../utilities/process/actions/admin/delete_limits.php?lt=<? echo urlencode($_CODEX->encrypt("agent_games")); ?>&did="+enc_ida+"'>Delete this limits and use inherited limits</a>.");
							}else{
								$(".agent_games_field_agent").prop("disabled",true);
								$("#agent_games_detail").html("Limits inherited from <strong>"+jsdata.origin+"</strong>. <a href='javascipr:;' onclick='activate_edition_agent()'>Create custom limits for this Agent</a>");
							}
							
							<? foreach($games as $game){  ?>
							$("#<? echo $game ->vars["id"] ?>_min_amount_agent").val(jsdata.games[<? echo $game ->vars["id"] ?>].min);
							$("#<? echo $game ->vars["id"] ?>_max_amount_agent").val(jsdata.games[<? echo $game ->vars["id"] ?>].max);
							$( "#<? echo $game ->vars["id"] ?>_active_agent" ).prop( "checked", jsdata.games[<? echo $game ->vars["id"] ?>].active*1 );
							<? } ?>
							
							
						});	
					}else{$("#agent_games_detail").html("");}
                }
				function activate_edition_agent(){
					$(".agent_games_field_agent").prop("disabled",false);	
					$("#agent_games_detail").html("Creating custom limits for this agent");
				}
                </script>
                
                 <div class="form-group">
                    <label>Agent</label>
                    
                    <select class="form-control" name="gl_agent" id="gl_agent" onchange="load_agent_games_limits(this.value);">
                        <option value="">- Select -</option>
                        <? foreach($agent_list as $item){ ?>
                            <option value="<? echo urlencode($_CODEX->encrypt($item ->vars["id"])); ?>">
                                <? echo $item ->vars["account"] ?>
                            </option>
                        <? } ?>
                    </select>
                    <p class="help-block" id="agent_games_detail"></p>
                </div>
                
                
                
            <? }else if($glt == "player"){ ?>
                <script type="text/javascript">
                validations4_<? echo $glt ?>.push({id:"gl_player",type:"null", msg:"Please select a Player"});
                function load_player_games_limits(enc_ida){
					if(enc_ida != ''){
						$.getJSON("https://play.casinogamesonline.com/utilities/process/actions/admin/get_limits.php?lt=<? echo urlencode($_CODEX->encrypt("player_games")); ?>&did="+enc_ida+"",function(jsdata){
							if(jsdata.origin == 'Player'){
								$(".agent_games_field_player").prop("disabled",false);
								$("#player_games_detail").html("Using his own limits. <a href='../utilities/process/actions/admin/delete_limits.php?lt=<? echo urlencode($_CODEX->encrypt("player_games")); ?>&did="+enc_ida+"'>Delete this limits and use inherited limits</a>.");
							}else{
								$(".agent_games_field_player").prop("disabled",true);
								$("#player_games_detail").html("Limits inherited from <strong>"+jsdata.origin+"</strong>. <a href='javascipr:;' onclick='activate_edition_player()'>Create custom limits for this Player</a>");
							}
							
							<? foreach($games as $game){  ?>
							$("#<? echo $game ->vars["id"] ?>_min_amount_player").val(jsdata.games[<? echo $game ->vars["id"] ?>].min);
							$("#<? echo $game ->vars["id"] ?>_max_amount_player").val(jsdata.games[<? echo $game ->vars["id"] ?>].max);
							$( "#<? echo $game ->vars["id"] ?>_active_player" ).prop( "checked", jsdata.games[<? echo $game ->vars["id"] ?>].active*1 );
							<? } ?>
							
							
						});	
					}else{$("#player_games_detail").html("");}
                }
				function activate_edition_player(){
					$(".agent_games_field_player").prop("disabled",false);	
					$("#player_games_detail").html("Creating custom limits for this Player");
				}
                </script>
                
                <div class="form-group">
                    <label>Player</label>
                    
                    <select class="form-control" name="gl_player" id="gl_player" onchange="load_player_games_limits(this.value);">
                        <option value="">- Select -</option>
                        <? foreach($player_list as $item){ ?>
                            <option value="<? echo urlencode($_CODEX->encrypt($item ->vars["id"])); ?>">
                                <? echo $item ->vars["account"] ?>
                            </option>
                        <? } ?>
                    </select>
                    <p class="help-block" id="player_games_detail"></p>
                </div>
                
            <? } ?>
            
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    
                        <tr>
                            <th class="centered">Active</th>
                            <th>Game</th>
                            <th>Min Amount</th>
                            <th>Max Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? $i=1; foreach($games as $game){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
                        <tr class="<? echo $class ?>">
                            <td align="center">
                                <script type="text/javascript">
                                validations4_<? echo $glt ?>.push({id:"<? echo $game ->vars["id"] ?>_min_amount_<? echo $glt ?>",type:"numeric", msg:"Please insert a valid Min Amount"});
                                validations4_<? echo $glt ?>.push({id:"<? echo $game ->vars["id"] ?>_max_amount_<? echo $glt ?>",type:"numeric", msg:"Please select a valid Max Amount"});
                                </script>
                                <input name="<? echo $game ->vars["id"] ?>_active" id="<? echo $game ->vars["id"] ?>_active_<? echo $glt ?>" type="checkbox" value="1" <? if($game ->vars["visible"] && $glt == "casino"){ ?> checked="checked" <? } ?> class="agent_games_field_<? echo $glt ?>" />
                            </td>
                            <td><? echo $game ->vars["name"] ?></td>
                            <td><input id="<? echo $game ->vars["id"] ?>_min_amount_<? echo $glt ?>" name="<? echo $game ->vars["id"] ?>_min_amount" class="form-control agent_games_field_<? echo $glt ?>" value="<? if($glt == "casino"){echo $game ->vars["min_amount"];} ?>"></td>
                            <td><input id="<? echo $game ->vars["id"] ?>_max_amount_<? echo $glt ?>" name="<? echo $game ->vars["id"] ?>_max_amount" class="form-control agent_games_field_<? echo $glt ?>" value="<? if($glt == "casino"){echo $game ->vars["max_amount"];} ?>"></td>
                        </tr>
                        <? } ?>
                        
                    </tbody>
                </table>
                
                <button type="submit" class="btn btn-default agent_games_field_<? echo $glt ?>">Update Limits</button>
                
            </div>
            </form>
        
        </div>
        
        <script type="text/javascript">
        <? if(param("subtype") == "agent"){ ?>
		toggle_rctabs('agent_games_limits', 'all_games_limits', 'tabs2', $("#agl_btn"));
		<? } ?>
		<? if(param("subtype") == "player"){ ?>
		toggle_rctabs('player_games_limits', 'all_games_limits', 'tabs2', $("#pgl_btn"));
		<? } ?>
        </script>
    
    <? } ?>
    
    
    
</div>



<script type="text/javascript">
<? if(param("active")){ ?>
	change_limis_tab('<? echo param("active") ?>');
<? }else if($_agent ->vars["manage_company"]){ ?>
	change_limis_tab('company');
<? }else{ ?>
	change_limis_tab('agent');
<? } ?>
</script>

<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>