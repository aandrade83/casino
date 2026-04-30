
function number_format(number) {
	number = number*1;
    return number.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
}
function random_str(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}
function random_number(from, to){
	return Math.floor((Math.random() * to) + from);	
}
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
function isOdd(n){
   return isNumber(n) && (Math.abs(n) % 2 == 1);
}
function IsNumeric(n){
   return n == parseFloat(n);
}

function contains(full, search_str){
	var found = false;
	if(full.indexOf(search_str) != -1){found = true;}
	return found;
}
function pop_shadow(url, title, width, heigth){
	Shadowbox.init({
			players:  [ 'iframe'] 
		});
		Shadowbox.open({
			player:     'iframe',
			title:      title,
			content:    url,
			height:     heigth,
			width:      width
		});
}

function resize_this_shadow(width,height,title){
	window.parent.$("#sb-wrapper-inner").css("height",height+"px");
	window.parent.$("#sb-wrapper").css("width",(width+2)+"px");
	if(title != ''){
		window.parent.$("#sb-title-inner").html(title);
	}
}


function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}



  
 //EXCEL
function create_excel(tableid){
	var data = new Blob([document.getElementById(tableid).innerHTML], {
		type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
	});	
	saveAs(data,"report.xls");
}

function excel_export(tid){
	
	var tab = null;
	
	if($("#"+tid).is("div")){
		$("#"+tid+" table").each(function(){
			$(this).attr("id","exportertable")
			tab = document.getElementById("exportertable");
		});
	}else{
		tab = document.getElementById(tid);
	}
	
    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); 

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html","replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus(); 
        sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
    }  
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  

    return (sa);
} 

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function toggle_rctabs(show_class, hide_class, all_class, btn){
	$('.'+hide_class).hide(0);
	$('.'+show_class).show(500);
	$('.'+all_class).removeClass('tabs_link_on');
	btn.addClass('tabs_link_on');
}

function avoid_doubleclick(id){
	$("#"+id).prop('disabled', true);
  	setTimeout(function() {
    	$("#"+id).prop('disabled', false);
  	}, 1000);
}

function fire_not_enough_balance(msg){
	if(!msg){msg = "Not enough balance!";}
	
	$("#balance_msg").show();
	$("#balance_msg_txt").html(msg);
	
}