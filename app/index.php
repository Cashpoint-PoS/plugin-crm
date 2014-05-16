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
    <title>CashPoint CRM</title>
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

$(document).on("cashpoint_view_index",function() {
	$("#index").show();
});

function customers_request_list(start,length) {
	console.glog("customers_request_list","data request for",start,length);
	var $c=$("#customers tbody").empty();
	$("#customers_rangestart,#customers_rangeend,#customers_pagination").html("");
	doAPIRequest("list",{mod:"crm",sub:"customers",rangeStart:start,rangeLength:length},function(data) {
		var range=data.range;
		data=data.data;
		console.glog("customers_request_list","data arrived",data);
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._elements._type).appendTo($r);
			$("<td>").html(e._raw.person_name_given_name).appendTo($r);
			$("<td>").html(e._raw.person_name_family_name).appendTo($r);
			$("<td>").html(e._elements._person_name).appendTo($r);
			$("<td>").html("").appendTo($r);
			$("<td>").html("").appendTo($r);
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Anzeigen").appendTo($btcell).click(function() {
				location.hash="customer_details/"+e._raw.id;
			});
		});
		$("#customers_rangestart").html(range.start+1);
		$("#customers_rangeend").html(range.start+range.length);
		var nPages=Math.ceil(range.total/range.length);
		var $pc=$("#customers_pagination");
		for(var i=0;i<nPages;i++) {
			var $e=$("<span>").appendTo($pc);
			if(i*range.length==range.start)
				$("<span>").css("font-weight","bold").html(i+1).appendTo($e);
			else
				$("<a>").html(i+1).appendTo($e).attr("href","#customers/"+(i*range.length)+"-"+range.length);
			if(i+1<nPages)
				$pc.appendText(" – ");
		}
	});
}
$(document).on("cashpoint_view_customers",function(a,b) {
	var range=b.args;
	if(!range) {
		location.hash="customers/0-20";
		return;
	}
	range=range.split("-");
	if(range.length!=2) {
		location.hash="customers/0-20";
		return;
	}
	
	$("#customers").show();
	customers_request_list(range[0],range[1]);
});

$(document).on("cashpoint_view_customer_details",function(a,b) {
	var id=b.args;
	if(!id)
		return;
	var $c=$("#customer_details_details");
	$(".dc",$c).html();
	$("#customer_details").show();
	doAPIRequest("view",{mod:"crm",sub:"customers",id:id},function(data) {
		data=data.data;
		$(".data-id",$c).html(data._raw.id);
		$(".data-type",$c).html(data._all.type.data[data._raw.type]);
		$(".data-companyname",$c).html(data._raw.company_name);
		$(".data-salutation",$c).html(data._raw.salutation);
		$(".data-gender",$c).html(data._all.person_gender.data[data._raw.person_gender]);
		$(".data-nameprefix",$c).html(data._raw.person_name_prefix);
		$(".data-namegiven",$c).html(data._raw.person_name_given_name);
		$(".data-namemiddle",$c).html(data._raw.person_name_middle_name);
		$(".data-namefamily",$c).html(data._raw.person_name_family_name);
		$(".data-namesuffix",$c).html(data._raw.person_name_suffix);
		$(".data-vatid",$c).html(data._raw.vat_id);
		$("#customer_details_edit").off("click").html("Bearbeiten").click(function() {
			var $typef=$("<select>").appendTo($(".data-type",$c).html(""));
			data._all.type.data.forEach(function(e,i) {
				$("<option>").attr("value",i).html(e).appendTo($typef);
			});
			$typef.val(data._raw.type).change();
			var $salutationf=$("<input>").appendTo($(".data-salutation",$c).html("")).attr("type","text").val(data._raw.salutation).attr("placeholder","Sehr geehrte Damen und Herren");
			var $genderf=$("<select>").appendTo($(".data-gender",$c).html(""));
			data._all.person_gender.data.forEach(function(e,i) {
				$("<option>").attr("value",i).html(e).appendTo($genderf);
			});
			$genderf.val(data._raw.person_gender).change();
			var $companynamef=$("<input>").appendTo($(".data-companyname",$c).html("")).attr("type","text").val(data._raw.company_name).attr("placeholder","Beispiel GmbH");
			var $prefixf=$("<input>").appendTo($(".data-nameprefix",$c).html("")).attr("type","text").val(data._raw.person_name_prefix).attr("placeholder","Prof. Dr.");
			var $givenf=$("<input>").appendTo($(".data-namegiven",$c).html("")).attr("type","text").val(data._raw.person_name_given_name).attr("placeholder","Max");
			var $middlef=$("<input>").appendTo($(".data-namemiddle",$c).html("")).attr("type","text").val(data._raw.person_name_middle_name).attr("placeholder","Friedrich Johann");
			var $familyf=$("<input>").appendTo($(".data-namefamily",$c).html("")).attr("type","text").val(data._raw.person_name_family_name).attr("placeholder","Mustermann");
			var $suffixf=$("<input>").appendTo($(".data-namesuffix",$c).html("")).attr("type","text").val(data._raw.person_name_suffix).attr("placeholder","Sr.");
			var $vatidf=$("<input>").appendTo($(".data-vatid",$c).html("")).attr("type","text").val(data._raw.vat_id).attr("placeholder","DE1234567890");
			$(this).off("click").html("Speichern").click(function() {
				var sobj={ids:[id],data:{}};
				sobj.data[id]={
					type:$typef.val(),
					company_name:$companynamef.val(),
					salutation:$salutationf.val(),
					person_gender:$genderf.val(),
					person_name_prefix:$prefixf.val(),
					person_name_given_name:$givenf.val(),
					person_name_middle_name:$middlef.val(),
					person_name_family_name:$familyf.val(),
					person_name_suffix:$suffixf.val(),
					vat_id:$vatidf.val(),
				};
				doAPIRequest("submit",{mod:"crm",sub:"customers",json_input:JSON.stringify(sobj)},function(data) {
					location.hash="customer_details/"+data.data._raw.id;
					if(data.data._raw.id==id)
						$(document).trigger("cashpoint_view_customer_details",{args:id});
				});
			});
		});
	});
});
$(document).on("cashpoint_view_cgroups",function() {
	$("#cgroups").show();
});

$(document).on("cashpoint_view_email",function() {
	$("#email").show();
});
$(document).ready(function() {
  doAPIRequest("getsessiondata",{},function(data) {
    $("#header-username").html(data.data.displayname);
  });
  $("#header-logout").click(function() {
    location.href=appconfig.webroot+"/logout.php";
  });
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
#header {
	height:40px;
	border-bottom: 1px solid lightgrey;
}
#header li {
	display:inline-block;
	margin: 0 10px;
}
#header button {
box-sizing:border-box;
height:40px;
padding:10px;
}
#all {
	height:calc(100% - 41px);
}
.view {
display:none;
}
#menu {
	float:left;
	padding:10px;
	height:100%;
	box-sizing:border-box;
	width:150px;
}
#content {
	float:left;
	padding:10px;
	height:100%;
	box-sizing:border-box;
	width:calc(100% - 150px);
}
td,th {
padding: 0px 5px;
}
/* --> */
    </style>
  </head>
  <body>
    <div id="header">
      <ul>
        <li>Angemeldet als: <span id="header-username"></span>
        <li>Terminal ID: <span id="header-tid"></span>
        <li><button id="header-logout">Abmelden</button></li>
      </ul>
    </div>
		<div id="all">
			<div id="menu">
				<h1>Menü</h1>
				<ul>
					<li><a href="#index">Startseite</a></li>
					<li><a href="#customers">Kunden</a></li>
					<li><a href="#cgroups">Kundengruppen</a></li>
					<li><a href="#email">eMail-Marketing</a></li>
				</ul>
			</div>
			<div id="content">
				<div class="view" id="index">
					<h1>Willkommen zu CashPoint CRM</h1>
					<p>Bitte wählen Sie links im Menü eine Funktion aus.</p>
				</div>
				<div class="view" id="customer_details">
					<h1>Kunden-Details</h1>
					<table id="customer_details_details">
						<tr>
							<th>ID</th>
							<td class="dc data-id"></td>
						</tr>
						<tr>
							<th>Typ</th>
							<td class="dc data-type"></td>
						</tr>
						<tr>
							<th>Firmenname (jur. Person)</th>
							<td class="dc data-companyname"></td>
						</tr>
						<tr>
							<th>Anrede im Brief</th>
							<td class="dc data-salutation"></td>
						</tr>
						<tr>
							<th>Geschlecht (nat. Person)</th>
							<td class="dc data-gender"></td>
						</tr>
						<tr>
							<th>Titel (nat. Person)</th>
							<td class="dc data-nameprefix"></td>
						</tr>
						<tr>
							<th>Vorname (nat. Person)</th>
							<td class="dc data-namegiven"></td>
						</tr>
						<tr>
							<th>Mittelname(n) (nat. Person)</th>
							<td class="dc data-namemiddle"></td>
						</tr>
						<tr>
							<th>Nachname(n) (nat. Person)</th>
							<td class="dc data-namefamily"></td>
						</tr>
						<tr>
							<th>Namenssuffix(e) (nat. Person)</th>
							<td class="dc data-namesuffix"></td>
						</tr>
						<tr>
							<th>USt-ID</th>
							<td class="dc data-vatid"></td>
						</tr>
						<tr><td colspan="2"><button id="customer_details_edit">Bearbeiten</button></td></tr>
					</table>
				</div>
				<div class="view" id="customers">
					<h1>Kunden-Übersicht</h1>
					<table>
						<thead>
							<tr><th>ID</th><th>Typ</th><th>Vorname</th><th>Nachname</th><th>Name</th><th>Telefon</th><th>Mail</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="8">Einträge <span id="customers_rangestart"></span>-<span id="customers_rangeend"></span> &mdash; Seite: <span id="customers_pagination"></span></td></tr>
							<tr><td colspan="8"><button id="customers_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
				</div>
				<div class="view" id="cgroups">
					<h1>Kundengruppen</h1>
				</div>
				<div class="view" id="email">
					<h1>eMail-Marketing</h1>
				</div>
			</div>
		</div>
  </body>
</html>
