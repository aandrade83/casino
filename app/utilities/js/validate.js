function validate(validations){
	var on_no_display = false; // No run validations on display = none
	var start = 0;
	
	//Settings
	//example settings: validations.push({id:"setting",type:"validate_no_display", msg:""});
	if(validations[0].type == "validate_no_display"){
		on_no_display = true;
		start++;
	} 
	//End Settings
	
	var message = "";
	var first = true;
	var submit_form = true;
	for(var i=start;i<validations.length;i++){
		if(document.getElementById(validations[i].id)){
			document.getElementById(validations[i].id).style.border = "";
		}
	}
	for(var i=start;i<validations.length;i++){
		if(document.getElementById(validations[i].id)){
			var input = document.getElementById(validations[i].id);
			
			if(document.getElementById("validations_message")){
				breaker = "<br>";
				document.getElementById("validations_message").innerHTML = "";
			}else{
				breaker = "\n";
			}
			
			if((input.style.display != "none" && input.parentNode.style.display != "none") || on_no_display){
				//null
				if(input.value == "" && validations[i].type == "null"){
					message += validations[i].msg + breaker;
					input.style.border = "1px solid #ff0000";			
					if(first){input.focus(); first = false;}
					submit_form = false;
				}
				//numeric
				if(!IsNumeric(input.value) && validations[i].type == "numeric"){
					message += validations[i].msg + breaker;
					input.style.border = "1px solid #ff0000";
					if(first){input.focus(); first = false;}
					submit_form = false;
				}
				//no numeric
				if(IsNumeric(input.value) && validations[i].type == "no_numeric"){
					message += validations[i].msg + breaker;
					input.style.border = "1px solid #ff0000";
					if(first){input.focus(); first = false;}
					submit_form = false;
				}
				//either
				if(validations[i].type.indexOf("either:") != -1){
					comp_id = validations[i].type.replace("either:","");
					if(document.getElementById(comp_id).value == "" &&  input.value == ""){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						document.getElementById(comp_id).style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//equal
				if(validations[i].type.indexOf("compare:") != -1){
					comp_id = validations[i].type.replace("compare:","");
					if(document.getElementById(comp_id).value != input.value){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//equal string
				if(validations[i].type.indexOf("compare_str:") != -1){
					comp = validations[i].type.replace("compare_str:","");
					if(comp != input.value){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}	
				//different
				if(validations[i].type.indexOf("different:") != -1){
					comp_id = validations[i].type.replace("different:","");
					if(document.getElementById(comp_id).value == input.value){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}		
				//different string
				if(validations[i].type.indexOf("different_str:") != -1){
					comp = validations[i].type.replace("different_str:","");
					if(comp == input.value){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//bigger
				if(validations[i].type.indexOf("bigger:") != -1){
					comp_id = validations[i].type.replace("bigger:","");
					if(parseInt(document.getElementById(comp_id).value) > parseInt(input.value)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//smaller
				if(validations[i].type.indexOf("smaller:") != -1){
					comp_id = validations[i].type.replace("smaller:","");
					if(parseInt(document.getElementById(comp_id).value) < parseInt(input.value)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//bigger than
				if(validations[i].type.indexOf("bigger_than:") != -1){
					comp = validations[i].type.replace("bigger_than:","");
					if(parseInt(comp) < parseInt(input.value)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//smaller than
				if(validations[i].type.indexOf("smaller_than:") != -1){
					comp = validations[i].type.replace("smaller_than:","");
					if(parseInt(comp) > parseInt(input.value)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//count bigger
				if(validations[i].type.indexOf("bigger_length:") != -1){
					comp = validations[i].type.replace("bigger_length:","");
					if(input.value.length > parseInt(comp)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//count smaller
				if(validations[i].type.indexOf("smaller_length:") != -1){
					comp = validations[i].type.replace("smaller_length:","");
					if(input.value.length < parseInt(comp)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//count bigger input
				if(validations[i].type.indexOf("bigger_length_input:") != -1){
					comp = document.getElementById(validations[i].type.replace("bigger_length_input:","")).value;
					if(input.value.length > parseInt(comp)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//count smaller input
				if(validations[i].type.indexOf("smaller_length_input:") != -1){
					comp = document.getElementById(validations[i].type.replace("smaller_length_input:","")).value;
					if(input.value.length < parseInt(comp)){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//email
				if(validations[i].type == "email"){
					var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
					if(reg.test(input.value) == false) {
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//radio
				//radio:divid,id1,id2,id3...
				if(validations[i].type.indexOf("radio:") != -1){
					full = validations[i].type.replace("radio:","");
					arr = full.split(",");
					var onecheck = false;
					for(var c = 0; c < arr.length; c++){
						if(document.getElementById(arr[c]).checked){onecheck = true;}
					}
					if(!onecheck){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";			
						if(first){input.focus(); first = false;}
						submit_form = false;
					}
				}
				//checkbox
				//checkbox:basename_,1
				if(validations[i].type.indexOf("checkbox:") != -1){
					full = validations[i].type.replace("checkbox:","");
					arr = full.split(",");
					base_id = arr[0];
					min_checks = arr[1];
					chks = document.getElementsByTagName("input");
					count = 0;
					for(var e=0;e<chks.length;e++){
						if(chks[e].type == "checkbox"){
							if(chks[e].checked && chks[e].id.indexOf(base_id) != -1){
								count++;
							}
						}
					}
					if(count < min_checks){
						message += validations[i].msg + breaker;
						input.style.border = "1px solid #ff0000";
						submit_form = false;
					}
				}
			}
		}
	}
	if(message != ""){
		if(document.getElementById("validations_message")){
			document.getElementById("validations_message").innerHTML = message;
		}else{
			alert(message);
		}		
	}
	return submit_form;
}
function IsNumeric(input){
   return (input - 0) == input && input.length > 0;
}
function inArray(a, obj){
  for(var i = 0; i < a.length; i++) {
    if(a[i] === obj){
      return true;
    }
  }
  return false;
}

//selects value on list when load
function set_select_input(ddlID, value, change){
	var ddl = document.getElementById(ddlID);
	for (var i = 0; i < ddl.options.length; i++) {
		if (ddl.options[i].value == value) {
			if (ddl.selectedIndex != i) {
				ddl.selectedIndex = i;
				if (change){ddl.onchange();}
			}
		   break;
	   }
   }
}
//loads into element external content
function load_external_content(url, div, code){
		if(location.href.indexOf("www.") != -1){
			url = "http://www." + url;
		}else{
			url = "http://" + url;
		}
		if (window.XMLHttpRequest) {    
			req = new XMLHttpRequest();    
		}    
		else if (window.ActiveXObject) {  
			req = new ActiveXObject("Microsoft.XMLHTTP");    
		}
		return read_from_file(url, req, div, code);
}
function read_from_file(filename, req, div, code) {
	nocache = Math.random();
	if(filename.indexOf("?") != -1){
		req.open('GET', filename + "&nocache="+nocache);
	}else{
		req.open('GET', filename + "?nocache="+nocache);
	}
	req.onreadystatechange = function() {   
		if (req.readyState == 4) {
			content = req.responseText;
			if(content == ""){content = "No Information Available";}
			if(document.getElementById(div) != null){
				document.getElementById(div).innerHTML = content;
				if(code != null){setTimeout(code,1);}
			}
		} 
	}
	req.send("");
}

//Example:  onfocus="reverse_msg(this,'Escriba su nombre');"
function reverse_msg(txt_box, msg){
	if(txt_box.value == msg){
		txt_box.value = "";
	}else if(txt_box.value == "" || txt_box.value == " "){
		txt_box.value = msg;
	}
}
function contains(full, search_str){
	var found = false;
	if(full.indexOf(search_str) != -1){found = true;}
	return found;
}