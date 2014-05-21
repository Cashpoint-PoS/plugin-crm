<?
require("../../../lib.php");
//check if we're logged in
if(!isset($_SESSION["user"]))
  redir("../../../index.php");

?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=0,width=320.1" />
    <meta name="google" value="notranslate" />
    <title>CashPoint CRM Selector</title>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/console.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/api.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/jquery-2.1.0.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/jquery.ba-hashchange.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/jquery.appendText.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/sprintf.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/date.format.js"></script>
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/i18n.js"></script>
    <script type="text/javascript" src="i18n/de.js"></script>
    
    <script type="text/javascript">
var appconfig={
  apiurl:"<?=$config["paths"]["api"]?>",
  webroot:"<?=$config["paths"]["webroot"]?>",
  localurl:"http://localhost/ks_services/api.php",
  deflang:"de",
};
if(typeof appstate!="object")
  appstate={};
$(document).on("cashpoint_view_search",function(a,b) {
	if(!b.args) {
		location.hash="#error";
		return;
	}
	var params=b.args.split("/");
	if(params.length!=3) {
		location.hash="#error";
		return;
	}
	var target=params[0].split("-");
	if(target.length!=2) {
		location.hash="#error";
		return;
	}
	var mod=target[0];
	var sub=target[1];
	var range=params[1].split("-");
	if(range.length!=2) {
		location.hash="#error";
		return;
	}
	$("#sel_classname").html(mod+"/"+sub);
	$("#search").show();
	search_request_list(mod,sub,range[0],range[1]);
});
function search_request_list(mod,sub,start,length) {
	console.glog("search_request_list","data request for",mod,sub,start,length);
	var $c=$("#search tbody").empty();
	$("#search_rangestart,#search_rangeend,#search_pagination").html("");
	doAPIRequest("list",{mod:mod,sub:sub,rangeStart:start,rangeLength:length,plainExport:true},function(data) {
		var range=data.range;
		data=data.data;
		console.glog("search_request_list","data arrived",data);
		var $headrow=$("<tr>").appendTo($("#search thead").empty());
		if(data.length>0) {
			var obj=data[0];
			$("<th>").html("ID").appendTo($headrow);
			obj._keys.list.forEach(function(k) {
				$("<th>").html(obj._all[k].title).appendTo($headrow);
			});
			$("<th>").html("Aktion").appendTo($headrow);
		}
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			e._keys.list.forEach(function(k) {
				$("<td>").html(e._elements[k]).appendTo($r);
			});
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Auswählen").appendTo($btcell).click(function() {
				if(window.opener) {
					window.opener._cashpoint_selector_return(e._raw.id);
				}
				window.close();
			});
		});
		$("#search_rangestart").html(range.start+1);
		$("#search_rangeend").html(range.start+range.length);
		var nPages=Math.ceil(range.total/range.length);
		var $pc=$("#search_pagination");
		for(var i=0;i<nPages;i++) {
			var $e=$("<span>").appendTo($pc);
			if(i*range.length==range.start)
				$("<span>").css("font-weight","bold").html(i+1).appendTo($e);
			else
				$("<a>").html(i+1).appendTo($e).attr("href","#search/"+(i*range.length)+"-"+range.length);
			if(i+1<nPages)
				$pc.appendText(" – ");
		}
	});
}

$(document).on("cashpoint_view_error",function() {
	$("#error").show();
});
$(document).ready(function() {
	if(location.hash==""||location.hash=="#") {
		location.hash="#error";
		return;
	}
});
    </script>
		<script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/view.js"></script>
    <style type="text/css">
/* <!-- */
/* joe fucks up highlight when tag name is just a * m( */
*,html {
  margin:0;
  padding:0;
}
html,body {
	height:100%;
	width:100%;
}
.view {
display:none;
}
td,th {
padding: 0px 5px;
}
table.striped tbody tr:nth-child(even) {background: #CCC}
table.striped tbody tr:nth-child(odd) {background: #FFF}

/* --> */
    </style>
  </head>
  <body>
  <div id="error" class="view">
  	<h2>Fehler</h2>
  	Diese Seite ist nicht dafür gedacht, direkt aufgerufen zu werden.
  </div>
  <div id="search" class="view">
	  <h1>Objektwahl <span id="sel_classname"></span></h1>
  	<h2>Suchkriterien</h2>
	  <h2>Liste</h2>
	  <table class="striped">
	  	<thead></thead>
	  	<tbody></tbody>
	  	<tfoot>
		  	<tr><td colspan="8">Einträge <span id="search_rangestart"></span>-<span id="search_rangeend"></span> &mdash; Seite: <span id="search_pagination"></span></td></tr>
	  	</tfoot>
	  </table>
	</div>
  </body>
</html>
