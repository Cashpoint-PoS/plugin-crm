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
    <script type="text/javascript" src="<?=$config["paths"]["webroot"]?>/shared-js/tinymce/js/tinymce/jquery.tinymce.min.js"></script>
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
			$("<td>").html(e._elements._primarymail).appendTo($r);
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
function cgroups_request_list(start,length) {
	console.glog("cgroups_request_list","data request for",start,length);
	var $c=$("#cgroups tbody").empty();
	$("#cgroups_rangestart,#cgroups_rangeend,#cgroups_pagination").html("");
	doAPIRequest("list",{mod:"crm",sub:"groups",rangeStart:start,rangeLength:length},function(data) {
		var range=data.range;
		data=data.data;
		console.glog("cgroups_request_list","data arrived",data);
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._raw.name).appendTo($r);
			$("<td>").html(e._raw.description).appendTo($r);
			$("<td>").html(e._links.CRM_Customer.length).appendTo($r);
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Anzeigen").appendTo($btcell).click(function() {
				location.hash="cgroup_details/"+e._raw.id;
			});
		});
		$("#cgroups_rangestart").html(range.start+1);
		$("#cgroups_rangeend").html(range.start+range.length);
		var nPages=Math.ceil(range.total/range.length);
		var $pc=$("#cgroups_pagination");
		for(var i=0;i<nPages;i++) {
			var $e=$("<span>").appendTo($pc);
			if(i*range.length==range.start)
				$("<span>").css("font-weight","bold").html(i+1).appendTo($e);
			else
				$("<a>").html(i+1).appendTo($e).attr("href","#cgroups/"+(i*range.length)+"-"+range.length);
			if(i+1<nPages)
				$pc.appendText(" – ");
		}
	});
}

$(document).on("cashpoint_view_cgroups",function(a,b) {
	var range=b.args;
	if(!range) {
		location.hash="cgroups/0-20";
		return;
	}
	range=range.split("-");
	if(range.length!=2) {
		location.hash="cgroups/0-20";
		return;
	}
	
	$("#cgroups").show();
	cgroups_request_list(range[0],range[1]);
});

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
		$(".data-remarks",$c).html(data._raw.remarks);
		var $mc=$("#customer_details_mail tbody").empty();
		data._o2m.CRM_MailAddr.elements.forEach(function(e) {
			var $r=$("<tr>").appendTo($mc);
			$("<td>").html(e._raw.id).appendTo($r);
			var $typef=$("<td>").html(e._elements.type).appendTo($r);
			var $addrf=$("<td>").html(e._elements.addr).appendTo($r);
			var $activef=$("<td>").html(e._elements.active).appendTo($r);
			var $primaryf=$("<td>").html(e._elements.is_primary).appendTo($r);
			var $newsokf=$("<td>").html(e._elements.newsletter_ok).appendTo($r);
			
			var $atd=$("<td>").appendTo($r);
			$("<button>").html("Bearbeiten").appendTo($atd).click(function() {
				var $ts=$("<select>").appendTo($typef.empty());
				var $as=$("<select>").appendTo($activef.empty());
				var $ps=$("<select>").appendTo($primaryf.empty());
				var $ns=$("<select>").appendTo($newsokf.empty());
				
				var $af=$("<input>").attr("type","email").val(e._elements.addr).appendTo($addrf.empty());
				e._all.type.data.forEach(function(v,k) {
					$("<option>").html(v).attr("value",k).appendTo($ts);
				});
				e._all.active.data.forEach(function(v,k) {
					$("<option>").html(v).attr("value",k).appendTo($as);
				});
				e._all.is_primary.data.forEach(function(v,k) {
					$("<option>").html(v).attr("value",k).appendTo($ps);
				});
				e._all.newsletter_ok.data.forEach(function(v,k) {
					$("<option>").html(v).attr("value",k).appendTo($ns);
				});
				$ts.val(e._raw.type).change();
				$as.val(e._raw.active).change();
				$ps.val(e._raw.is_primary).change();
				$ns.val(e._raw.newsletter_ok).change();
				
				$(this).html("Speichern").off("click").click(function() {
          var sobj={ids:[e._raw.id],data:{}};
          sobj.data[e._raw.id]={
          	type:$ts.val(),
          	active:$as.val(),
          	is_primary:$ps.val(),
          	newsletter_ok:$ns.val(),
          	addr:$af.val(),
          };
          doAPIRequest("submit",{mod:"crm",sub:"mailaddrs",json_input:JSON.stringify(sobj)},function(data) {
            $(document).trigger("cashpoint_view_customer_details",{args:id});
          });
				});
			});
		});
		$("#customer_details_mail_addnew").off("click").click(function() {
			var $r=$("<tr>").appendTo($mc);
			$("<td>").html("neu").appendTo($r);
			var $typef=$("<td>").appendTo($r);
			var $addrf=$("<td>").appendTo($r);
			var $activef=$("<td>").appendTo($r);
			var $primaryf=$("<td>").appendTo($r);
			var $newsokf=$("<td>").appendTo($r);
			
			var $atd=$("<td>").appendTo($r);
      var $ts=$("<select>").appendTo($typef.empty());
      var $as=$("<select>").appendTo($activef.empty());
      var $ps=$("<select>").appendTo($primaryf.empty());
      var $ns=$("<select>").appendTo($newsokf.empty());
      
      var $af=$("<input>").attr("type","email").val("").appendTo($addrf.empty());
      ["Unbekannt","Arbeit","Privat"].forEach(function(v,k) {
        $("<option>").html(v).attr("value",k).appendTo($ts);
      });
      ["Nein","Ja"].forEach(function(v,k) {
        $("<option>").html(v).attr("value",k).appendTo($as);
      });
      ["Nein","Ja"].forEach(function(v,k) {
        $("<option>").html(v).attr("value",k).appendTo($ps);
      });
      ["Nein","Ja"].forEach(function(v,k) {
        $("<option>").html(v).attr("value",k).appendTo($ns);
      });
      $ts.val(0).change();
      $as.val(1).change();
      $ps.val(1).change();
      $ns.val(1).change();
      
			$("<button>").appendTo($atd).html("Speichern").click(function() {
        var sobj={ids:[0],data:{}};
        sobj.data[0]={
        	crm_customers_id:id,
          type:$ts.val(),
          active:$as.val(),
          is_primary:$ps.val(),
          newsletter_ok:$ns.val(),
          addr:$af.val(),
        };
        doAPIRequest("submit",{mod:"crm",sub:"mailaddrs",json_input:JSON.stringify(sobj)},function(data) {
          $(document).trigger("cashpoint_view_customer_details",{args:id});
        });
      });
		});
		var $ac=$("#customer_details_addrs tbody").empty();
		data._o2m.CRM_Address.elements.forEach(function(e) {
			var $r=$("<tr>").appendTo($ac);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._elements.type).appendTo($r);
			$("<td>").appendTo($r).append($("<pre>").html(e._raw.formatted));
			if(e._raw.id==data._raw.deladdr)
				$("<td>").html("Ja").appendTo($r);
			else
				$("<td>").html("Nein").appendTo($r);
			if(e._raw.id==data._raw.billaddr)
				$("<td>").html("Ja").appendTo($r);
			else
				$("<td>").html("Nein").appendTo($r);
			var $atd=$("<td>").appendTo($r);
			$("<button>").html("Standard Liefer").appendTo($atd).click(function() {
			});
			$("<button>").html("Standard Rechnung").appendTo($atd).click(function() {
			});
			
		});
		$("#customer_details_addrs_addnew").off("click").click(function() {
			var $r=$("<tr>").appendTo($("#customer_details_addrs tbody"));
			$("<td>").html("neu").appendTo($r);
			var $typef=$("<select>").appendTo($("<td>").appendTo($r));
			["Unbekannt","Arbeit","Privat"].forEach(function(v,k) {
				$("<option>").attr("value",k).html(v).appendTo($typef);
			});
			$typef.val(0).change();
			
			var $itable=$("<table>").appendTo($("<td>").appendTo($r));
			$("<td>").html("nein").appendTo($r);
			$("<td>").html("nein").appendTo($r);
			var $atd=$("<td>").appendTo($r);
			
			var $r1=$("<tr>").appendTo($itable);
			$("<th>").html("ZVZ1 (Versandvermerk)").appendTo($r1);
			var $prefixf=$("<input>").attr("type","text").attr("placeholder","per Einschreiben").appendTo($("<td>").appendTo($r1));
			
			var $r2=$("<tr>").appendTo($itable);
			$("<th>").html("Anrede/Firma").appendTo($r2);
			var $namef=$("<input>").attr("type","text").attr("placeholder","Beispiel GmbH").appendTo($("<td>").appendTo($r2));
			
			var $r3=$("<tr>").appendTo($itable);
			$("<th>").html("Zeile 2").appendTo($r3);
			var $line2f=$("<input>").attr("type","text").attr("placeholder","Buchhaltung").appendTo($("<td>").appendTo($r3));
			
			var $r4=$("<tr>").appendTo($itable);
			$("<th>").html("Zeile 3").appendTo($r4);
			var $line3f=$("<input>").attr("type","text").attr("placeholder","z. Hd. Fr. Meier").appendTo($("<td>").appendTo($r4));
			
			var $r5=$("<tr>").appendTo($itable);
			$("<th>").html("Straße").appendTo($r5);
			var $streetf=$("<input>").attr("type","text").attr("placeholder","Beispielstraße").appendTo($("<td>").appendTo($r5));
			
			var $r6=$("<tr>").appendTo($itable);
			$("<th>").html("Hausnummer").appendTo($r6);
			var $hnf=$("<input>").attr("type","text").attr("placeholder","6a").appendTo($("<td>").appendTo($r6));
			
			var $r7=$("<tr>").appendTo($itable);
			$("<th>").html("Postleitzahl").appendTo($r7);
			var $postalcodef=$("<input>").attr("type","text").attr("placeholder","31337").appendTo($("<td>").appendTo($r7));
			
			var $r8=$("<tr>").appendTo($itable);
			$("<th>").html("Stadt").appendTo($r8);
			var $cityf=$("<input>").attr("type","text").attr("placeholder","Beispielstadt").appendTo($("<td>").appendTo($r8));
			
			var $r9=$("<tr>").appendTo($itable);
			$("<th>").html("Land").appendTo($r9);
			var $countryf=$("<input>").attr("type","text").attr("placeholder","Deutschland").appendTo($("<td>").appendTo($r9));
			
			$("<button>").appendTo($atd).html("Speichern").click(function() {
				var sobj={ids:[0],data:{}};
				sobj.data[0]={
					crm_customers_id:id,
					type:$typef.val(),
					prefix_line:$prefixf.val(),
					name:$namef.val(),
					line2:$line2f.val(),
					line3:$line3f.val(),
					street:$streetf.val(),
					house_nr:$hnf.val(),
					postal_code:$postalcodef.val(),
					city:$cityf.val(),
					country:$countryf.val(),
				};
				doAPIRequest("submit",{mod:"crm",sub:"addrs",json_input:JSON.stringify(sobj)},function(data) {
					$(document).trigger("cashpoint_view_customer_details",{args:id});
				});
			});
			
			$("<button>").appendTo($atd).html("Abbrechen").click(function() {
				$r.remove();
			});
			
		});
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
			var $remarksf=$("<input>").appendTo($(".data-remarks",$c).html("")).attr("type","text").val(data._raw.remarks).attr("placeholder","");
			
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
					remarks:$remarksf.val(),
				};
				doAPIRequest("submit",{mod:"crm",sub:"customers",json_input:JSON.stringify(sobj)},function(data) {
					location.hash="customer_details/"+data.data._raw.id;
					if(data.data._raw.id==id)
						$(document).trigger("cashpoint_view_customer_details",{args:id});
				});
			});
		});
		if(id==0)
			$("#customer_details_edit").click();
	});
});
$(document).on("cashpoint_view_cgroup_details",function(a,b) {
	var id=b.args;
	if(!id)
		return;
	var $c=$("#cgroup_details_details");
	$(".dc",$c).html();
	$("#cgroup_details").show();
	doAPIRequest("view",{mod:"crm",sub:"groups",id:id},function(data) {
		data=data.data;
		$(".data-id",$c).html(data._raw.id);
		$(".data-name",$c).html(data._raw.name);
		$(".data-description",$c).html(data._raw.description);
		$("#cgroup_details_edit").off("click").html("Bearbeiten").click(function() {
			var $namef=$("<input>").appendTo($(".data-name",$c).html("")).attr("type","text").val(data._raw.name);
			var $descf=$("<input>").appendTo($(".data-description",$c).html("")).attr("type","text").val(data._raw.description);
			$(this).off("click").html("Speichern").click(function() {
				var sobj={ids:[id],data:{}};
				sobj.data[id]={
					name:$namef.val(),
					description:$descf.val(),
				};
				doAPIRequest("submit",{mod:"crm",sub:"groups",json_input:JSON.stringify(sobj)},function(data) {
					location.hash="cgroup_details/"+data.data._raw.id;
					if(data.data._raw.id==id)
						$(document).trigger("cashpoint_view_cgroup_details",{args:id});
				});
			});
		});
		if(id!=0)
			cgroup_memberlist_get(id,0,5);
		window._cashpoint_selector_return=function(mid) {
			doAPIRequest("addLink",{mod:"crm",sub:"groups",id:id,target:"CRM_Customer",targetId:mid},function() {
				$(document).trigger("cashpoint_view_cgroup_details",{args:id});
			});
		}
		$("#cgroup_members_addnew").off("click").click(function() {
			var popup=window.open("selector.php#search/crm-customers/0-20/[]","_blank","top=0,left=0,location=no,menubar=no,scrollbars=yes");
			popup.focus();
		});
	});
});
$(document).ready(function() {
	$("#customers_addnew").click(function() {
		location.hash="customer_details/0";
	});
	$("#cgroups_addnew").click(function() {
		location.hash="cgroup_details/0";
	});
	$("#email_campaigns_addnew").click(function() {
		location.hash="mailcampaign_details/0";
	});
	$("#email_templates_addnew").click(function() {
		location.hash="mailtemplate_details/0";
	});
	$("#email_sendaccounts_addnew").click(function() {
		location.hash="mailsendaccount_details/0";
	});
});
function cgroup_memberlist_get(gid,start,length) {
	console.glog("cgroup_memberlist_get","group",gid,"start",start,"length",length);
	var $c=$("#cgroup_members tbody").empty();
	$("#cgroup_members_rangestart,#cgroup_members_rangeend,#cgroup_members_pagination").html("");
	doAPIRequest("link",{mod:"crm",sub:"groups",id:gid,rangeStart:start,rangeLength:length,plainExport:true,target:"CRM_Customer"},function(data) {
		var range=data.range;
		data=data.data;
		console.glog("cgroups_memberlist_get","data arrived",data);
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._elements._type).appendTo($r);
			$("<td>").html(e._elements.company_name).appendTo($r);
			$("<td>").html(e._elements._person_name).appendTo($r);
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Entfernen").appendTo($btcell).click(function() {
				doAPIRequest("removeLink",{mod:"crm",sub:"groups",id:gid,target:"CRM_Customer",targetId:e._raw.id},function() {
					$(document).trigger("cashpoint_view_cgroup_details",{args:gid});
				});
			});
		});
		$("#cgroup_members_rangestart").html(range.start+1);
		$("#cgroup_members_rangeend").html(range.start+range.length);
		var nPages=Math.ceil(range.total/range.length);
		var $pc=$("#cgroup_members_pagination");
		for(var i=0;i<nPages;i++) {
			var $e=$("<span>").appendTo($pc);
			console.log("button",i,range,"nextstart",(i*range.length));
			if(i*range.length==range.start)
				$("<span>").css("font-weight","bold").html(i+1).appendTo($e);
			else {
				var tmp=i; //fuck javascript scoping. if using i instead of tmp below, it will use the "final" value of i
				$("<a>").html(i+1).appendTo($e).click(function() {
					cgroup_memberlist_get(gid,(tmp*range.length),range.length);
				});
			}
			if(i+1<nPages)
				$pc.appendText(" – ");
		}
	});
}
$(document).on("cashpoint_view_file_details",function() {
	var id=b.args;
	if(!id)
		return;
	var $c=$("#file_details");
	$(".dc",$c).html();
	$("#file_details").show();
	doAPIRequest("view",{mod:"filerepo",sub:"files",id:id},function(data) {
		data=data.data;
		$(".data-id",$c).html(data._raw.id);
	});
});
$(document).on("cashpoint_view_email_marketing",function() {
	$("#email_marketing").show();
	doAPIRequest("list",{mod:"crm",sub:"mailcampaigns"},function(data) {
		data=data.data;
		var $c=$("#email_campaigns_list tbody").empty();
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._elements.status).appendTo($r);
			$("<td>").html(e._elements.name).appendTo($r);
			$("<td>").html(e._elements.mail_templates_id).appendTo($r);
			
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Ansicht").appendTo($btcell).click(function() {
				location.hash="mailcampaign_details/"+e._raw.id;
			});
		});
	});
	doAPIRequest("list",{mod:"mail",sub:"templates"},function(data) {
		data=data.data;
		var $c=$("#email_templates_list tbody").empty();
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._elements.name).appendTo($r);
			$("<td>").html(e._elements.subject).appendTo($r);
			
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Ansicht").appendTo($btcell).click(function() {
				location.hash="mailtemplate_details/"+e._raw.id;
			});
		});
	});
});
$(document).on("cashpoint_view_mailtemplate_details",function(a,b) {
	if(!b.args)
		return;
	var id=b.args;
	var $c=$("#mailtemplate_details_details");
	$(".dc",$c).html();
	$("#mailtemplate_details").show();
	doAPIRequest("view",{mod:"mail",sub:"templates",id:id},function(data) {
		data=data.data;
		$(".data-id",$c).html(data._raw.id);
		$(".data-name",$c).html(data._elements.name);
		$(".data-subject",$c).html(data._elements.subject);
		$(".data-text",$c).html("<pre>"+data._elements.content_text+"</pre>");
		var $ifr=$("<iframe>").appendTo($(".data-html",$c).empty());
		$ifr.attr("seamless",true).attr("sandbox","allow-forms allow-scripts allow-popups");
		$ifr.get(0).setAttribute("srcdoc",data._elements.content_html);
		$("#mailtemplate_details_edit").html("Bearbeiten").off("click").click(function() {
			var $namef=$("<input>").appendTo($(".data-name",$c).html("")).attr("type","text").val(data._elements.name);
			var $subjectf=$("<input>").appendTo($(".data-subject",$c).html("")).attr("type","text").val(data._elements.subject);
			var $textf=$("<textarea>").attr("rows",10).attr("cols",80).appendTo($(".data-text",$c).html("")).text(data._elements.content_text).val(data._elements.content_text);
			var $htmlta=$("<textarea>").attr("rows",10).attr("cols",80).appendTo($(".data-html",$c).html("")).text(data._elements.content_html).val(data._elements.content_html);
			$htmlta.tinymce({
				script_url:"<?=$config["paths"]["webroot"]?>/shared-js/tinymce/js/tinymce/tinymce.min.js",
			});
			$(this).off("click").html("Speichern").click(function() {
				var sobj={ids:[id],data:{}};
				sobj.data[id]={
					name:$namef.val(),
					subject:$subjectf.val(),
					content_text:$textf.val(),
					content_html:$htmlta.tinymce().getContent(),
				};
				doAPIRequest("submit",{mod:"mail",sub:"templates",json_input:JSON.stringify(sobj)},function(data) {
					location.hash="mailtemplate_details/"+data.data._raw.id;
					if(data.data._raw.id==id)
						$(document).trigger("cashpoint_view_mailtemplate_details",{args:id});
				});
			});
		});
		if(id==0)
			$("#mailtemplate_details_edit").click();
	});
});
$(document).on("cashpoint_view_mailcampaign_details",function(a,b) {
	if(!b.args)
		return;
	var id=b.args;
	var $c=$("#mailcampaign_details_details");
	$(".dc",$c).html();
	$("#mailcampaign_details").show();
	doAPIRequest("view",{mod:"crm",sub:"mailcampaigns",id:id},function(data) {
		data=data.data;
		$(".data-id",$c).html(data._raw.id);
		$(".data-name",$c).html(data._elements.name);
		$(".data-status",$c).html(data._elements.status);
		$(".data-mailtemplate",$c).html(data._elements.mail_templates_id);
		$(".data-mailsendaccount",$c).html(data._elements.mail_sendaccounts_id);
		$(".data-testgroup",$c).html(data._elements.testgroup_id);
		$(".data-prodgroup",$c).html(data._elements.prodgroup_id);
		$("#mailcampaign_sendtestmails").off("click").click(function() {
			var $log=$("#mailcampaign_log").html("");
			doAPIRequest("specialAction",{mod:"crm",sub:"mailcampaigns",id:id,target:"sendtestmails"},function(data) {
				$log.html(data.data.log);
			});
		});
		$("#mailcampaign_details_edit").html("Bearbeiten").off("click").click(function() {
			var $namef=$("<input>").appendTo($(".data-name",$c).html("")).attr("type","text").val(data._elements.name);
			var $statusf=$("<select>").appendTo($(".data-status",$c).html(""));
			data._all.status.data.forEach(function(v,k) {
				$("<option>").attr("value",k).html(v).appendTo($statusf);
			});
			$statusf.val(data._raw.status).change();
			$mtcell=$(".data-mailtemplate",$c).empty();
			var $mtidf=$("<input>").attr("type","number").val(data._raw.mail_templates_id).appendTo($mtcell);
			$("<button>").html("Durchsuchen").appendTo($mtcell).click(function() {
				window._cashpoint_selector_return=function(ret) {
					$mtidf.val(ret);
				}
				var popup=window.open("selector.php#search/mail-templates/0-20/[]","_blank","top=0,left=0,location=no,menubar=no,scrollbars=yes");
				popup.focus();
			});
			$mfcell=$(".data-mailsendaccount",$c).empty();
			var $mfidf=$("<input>").attr("type","number").val(data._raw.mail_sendaccounts_id).appendTo($mfcell);
			$("<button>").html("Durchsuchen").appendTo($mfcell).click(function() {
				window._cashpoint_selector_return=function(ret) {
					$mfidf.val(ret);
				}
				var popup=window.open("selector.php#search/mail-sendaccounts/0-20/[]","_blank","top=0,left=0,location=no,menubar=no,scrollbars=yes");
				popup.focus();
			});
			$tgcell=$(".data-testgroup",$c).empty();
			var $tgidf=$("<input>").attr("type","number").val(data._raw.testgroup_id).appendTo($tgcell);
			$("<button>").html("Durchsuchen").appendTo($tgcell).click(function() {
				window._cashpoint_selector_return=function(ret) {
					$tgidf.val(ret);
				}
				var popup=window.open("selector.php#search/crm-groups/0-20/[]","_blank","top=0,left=0,location=no,menubar=no,scrollbars=yes");
				popup.focus();
			});
			$pgcell=$(".data-prodgroup",$c).empty();
			var $pgidf=$("<input>").attr("type","number").val(data._raw.prodgroup_id).appendTo($pgcell);
			$("<button>").html("Durchsuchen").appendTo($pgcell).click(function() {
				window._cashpoint_selector_return=function(ret) {
					$pgidf.val(ret);
				}
				var popup=window.open("selector.php#search/crm-groups/0-20/[]","_blank","top=0,left=0,location=no,menubar=no,scrollbars=yes");
				popup.focus();
			});
			
			$(this).off("click").html("Speichern").click(function() {
				var sobj={ids:[id],data:{}};
				sobj.data[id]={
					name:$namef.val(),
					status:$statusf.val(),
					mail_templates_id:$mtidf.val(),
					mail_sendaccounts_id:$mfidf.val(),
					testgroup_id:$tgidf.val(),
					prodgroup_id:$pgidf.val(),
				};
				doAPIRequest("submit",{mod:"crm",sub:"mailcampaigns",json_input:JSON.stringify(sobj)},function(data) {
					location.hash="mailcampaign_details/"+data.data._raw.id;
					if(data.data._raw.id==id)
						$(document).trigger("cashpoint_view_mailcampaign_details",{args:id});
				});
			});
		});
		if(id==0)
			$("#mailcampaign_details_edit").click();
	});
});
$(document).on("cashpoint_view_email_internals",function() {
	$("#email_internals").show();
	doAPIRequest("list",{mod:"mail",sub:"sendaccounts"},function(data) {
		data=data.data;
		var $c=$("#email_sendaccounts_list tbody").empty();
		data.forEach(function(e) {
			var $r=$("<tr>").appendTo($c);
			$("<td>").html(e._raw.id).appendTo($r);
			$("<td>").html(e._elements.name).appendTo($r);
			$("<td>").html(e._elements.from_addr).appendTo($r);
			
			var $btcell=$("<td>").appendTo($r);
			$("<button>").html("Ansicht").appendTo($btcell).click(function() {
				location.hash="mailsendaccount_details/"+e._raw.id;
			});
		});
	});
});
$(document).on("cashpoint_view_mailsendaccount_details",function(a,b) {
	if(!b.args)
		return;
	var id=b.args;
	var $c=$("#email_sendaccount_details_details");
	$(".dc",$c).html();
	$("#email_sendaccount_details").show();
	doAPIRequest("view",{mod:"mail",sub:"sendaccounts",id:id},function(data) {
		data=data.data;
		$(".data-id",$c).html(data._raw.id);
		$(".data-name",$c).html(data._elements.name);
		$(".data-fromaddr",$c).html(data._elements.from_addr);
		$(".data-fromname",$c).html(data._elements.from_name);
		$(".data-type",$c).html(data._elements.type);
		var cred_obj;
		try {
			cred_obj=JSON.parse(data._elements.credentials);
			if(!cred_obj.hasOwnProperty("user"))
				throw "user missing";
			if(!cred_obj.hasOwnProperty("pass"))
				throw "pass missing";
		} catch(err) {
			cred_obj={user:"",pass:""}
		}
		var conn_obj;
		try {
			conn_obj=JSON.parse(data._elements.conn_details);
			if(!conn_obj.hasOwnProperty("host"))
				throw "host missing";
			if(!conn_obj.hasOwnProperty("port"))
				throw "port missing";
			if(!conn_obj.hasOwnProperty("sectsp"))
				throw "sectsp missing";
		} catch(err) {
			conn_obj={host:"",port:25,sectsp:""}
		}
		$(".data-smtpuser",$c).html(cred_obj.user);
		$(".data-smtppass",$c).html(cred_obj.pass);
		$(".data-smtphost",$c).html(conn_obj.host);
		$(".data-smtpport",$c).html(conn_obj.port);
		$(".data-smtpsectsp",$c).html(conn_obj.sectsp);
		$("#email_sendaccount_details_edit").html("Bearbeiten").off("click").click(function() {
			var $namef=$("<input>").appendTo($(".data-name",$c).html("")).attr("type","text").val(data._elements.name);
			var $fromnamef=$("<input>").appendTo($(".data-fromname",$c).html("")).attr("type","text").val(data._elements.from_name);
			var $fromaddrf=$("<input>").appendTo($(".data-fromaddr",$c).html("")).attr("type","text").val(data._elements.from_addr);
			var $typef=$("<select>").appendTo($(".data-type",$c).html(""));
			var $smtpuserf=$("<input>").appendTo($(".data-smtpuser",$c).html("")).attr("type","text").val(cred_obj.user);
			var $smtppassf=$("<input>").appendTo($(".data-smtppass",$c).html("")).attr("type","text").val(cred_obj.pass);
			var $smtphostf=$("<input>").appendTo($(".data-smtphost",$c).html("")).attr("type","text").val(conn_obj.host);
			var $smtpportf=$("<input>").appendTo($(".data-smtpport",$c).html("")).attr("type","text").val(conn_obj.port);
			var $smtpsectspf=$("<input>").appendTo($(".data-smtpsectsp",$c).html("")).attr("type","text").val(conn_obj.sectsp);
			
			data._all.type.data.forEach(function(v,k) {
				$("<option>").attr("value",k).html(v).appendTo($typef);
			});
			$typef.val(data._raw.type).change();
			$(this).off("click").html("Speichern").click(function() {
				var sobj={ids:[id],data:{}};
				sobj.data[id]={
					name:$namef.val(),
					from_name:$fromnamef.val(),
					from_addr:$fromaddrf.val(),
					type:$typef.val(),
					credentials:JSON.stringify({user:$smtpuserf.val(),pass:$smtppassf.val()}),
					conn_details:JSON.stringify({host:$smtphostf.val(),port:$smtpportf.val(),sectsp:$smtpsectspf.val()}),
				};
				doAPIRequest("submit",{mod:"mail",sub:"sendaccounts",json_input:JSON.stringify(sobj)},function(data) {
					location.hash="mailsendaccount_details/"+data.data._raw.id;
					if(data.data._raw.id==id)
						$(document).trigger("cashpoint_view_mailsendaccount_details",{args:id});
				});
			});
		});
		if(id==0)
			$("#email_sendaccount_details_edit").click();
	});
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
html,body,iframe {
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
table.striped tbody tr:nth-child(even) {background: #CCC}
table.striped tbody tr:nth-child(odd) {background: #FFF}

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
					<li><a href="#email_marketing">eMail-Marketing</a></li>
					<li><a href="#email_internals">eMail-Interna</a></li>
					<li><a href="#files">Dateien</a></li>
				</ul>
			</div>
			<div id="content">
				<div class="view" id="index">
					<h1>Willkommen zu CashPoint CRM</h1>
					<p>Bitte wählen Sie links im Menü eine Funktion aus.</p>
				</div>
				<div class="view" id="customer_details">
					<h1>Kunden-Details</h1>
					<h2>Basis</h2>
					<table id="customer_details_details" class="striped">
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
						<tr>
							<th>Anmerkungen</th>
							<td class="dc data-remarks"></td>
						</tr>
						<tr><td colspan="2"><button id="customer_details_edit">Bearbeiten</button></td></tr>
					</table>
					<h2>Adressen</h2>
					<table id="customer_details_addrs" class="striped">
						<thead>
							<tr><th>ID</th><th>Typ</th><th>Datensatz</th><th>Standard-Liefer</th><th>Standard-Rechnung</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="4"><button id="customer_details_addrs_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
					<h2>eMail</h2>
					<table id="customer_details_mail" class="striped">
						<thead>
							<tr><th>ID</th><th>Typ</th><th>Adresse</th><th>Aktiv</th><th>Primär</th><th>Newsletter OK</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="7"><button id="customer_details_mail_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
					<h2>Telefon</h2>
					<table id="customer_details_phone" class="striped">
						<thead>
							<tr><th>ID</th><th>Nummer</th><th>Typ</th><th>Aktiv</th><th>Primär</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="5"><button id="customer_details_phone_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
				</div>
				<div class="view" id="customers">
					<h1>Kunden-Übersicht</h1>
					<table class="striped">
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
					<table class="striped">
						<thead>
							<tr><th>ID</th><th>Gruppen-Name</th><th>Gruppen-Beschreibung</th><th>Mitglieder</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="8">Einträge <span id="cgroups_rangestart"></span>-<span id="cgroups_rangeend"></span> &mdash; Seite: <span id="cgroups_pagination"></span></td></tr>
							<tr><td colspan="8"><button id="cgroups_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
				</div>
				<div class="view" id="cgroup_details">
					<h1>Gruppen-Details</h1>
					<h2>Basis</h2>
					<table id="cgroup_details_details" class="striped">
						<tr>
							<th>ID</th>
							<td class="dc data-id"></td>
						</tr>
						<tr>
							<th>Name</th>
							<td class="dc data-name"></td>
						</tr>
						<tr>
							<th>Beschreibung</th>
							<td class="dc data-description"></td>
						</tr>
						<tr><td colspan="2"><button id="cgroup_details_edit">Bearbeiten</button></td></tr>
					</table>
					<h2>Mitglieder</h2>
					<table class="striped" id="cgroup_members">
						<thead>
							<tr><th>ID</th><th>Typ</th><th>Firmenname</th><th>Name</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="5">Einträge <span id="cgroup_members_rangestart"></span>-<span id="cgroup_members_rangeend"></span> &mdash; Seite: <span id="cgroup_members_pagination"></span></td></tr>
							<tr><td colspan="5"><button id="cgroup_members_addnew">Hinzufügen</button></td></tr>
						</tfoot>
					</table>
				</div>
				<div class="view" id="files">
					<h1>Dateien</h1>
					<table class="striped">
						<thead>
							<tr><th>ID</th><th>Pfad</th><th>Dateiname</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="4">Einträge <span id="files_rangestart"></span>-<span id="files_rangeend"></span> &mdash; Seite: <span id="files_pagination"></span></td></tr>
							<tr><td colspan="4"><button id="files_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
				</div>
				<div class="view" id="file_details">
					<h1>Datei-Details</h1>
					<table id="file_details" class="striped">
						<tr>
							<th>ID</th>
							<td class="dc data-id"></td>
						</tr>
						<tr>
							<th>Name</th>
							<td class="dc data-name"></td>
						</tr>
						<tr>
							<th>Datei</th>
							<td class="dc data-description"></td>
						</tr>
						<tr>
							<th>Prüfsumme</th>
							<td class="dc data-checksum"></td>
						</tr>
						<tr><td colspan="2"><button id="file_details_edit">Bearbeiten</button></td></tr>
					</table>
				</div>
				<div class="view" id="mailcampaign_details">
					<h1>Kampagnen-Details</h1>
					<h2>Basis</h2>
					<table id="mailcampaign_details_details" class="striped">
						<tr>
							<th>ID</th>
							<td class="dc data-id"></td>
						</tr>
						<tr>
							<th>Status</th>
							<td class="dc data-status"></td>
						</tr>
						<tr>
							<th>Name</th>
							<td class="dc data-name"></td>
						</tr>
						<tr>
							<th>Mail-Vorlage</th>
							<td class="dc data-mailtemplate"></td>
						</tr>
						<tr>
							<th>Absenderkonto</th>
							<td class="dc data-mailsendaccount"></td>
						</tr>
						<tr>
							<th>Testgruppe</th>
							<td class="dc data-testgroup"></td>
						</tr>
						<tr>
							<th>Produktivgruppe</th>
							<td class="dc data-prodgroup"></td>
						</tr>
						<tr><td colspan="2"><button id="mailcampaign_details_edit">Bearbeiten</button></td></tr>
					</table>
					<h2>Aktionen</h2>
					<button id="mailcampaign_sendtestmails">Sende Mails an Testgruppe</button>
					<button id="mailcampaign_sendprodmails">Sende Mails an Produktivgruppe</button>
					<h2>Aktionsprotokoll</h2>
					<pre id="mailcampaign_log"></pre>
				</div>
				<div class="view" id="mailtemplate_details">
					<h1>Vorlagen-Details</h1>
					<table id="mailtemplate_details_details" class="striped">
						<tr>
							<th>ID</th>
							<td class="dc data-id"></td>
						</tr>
						<tr>
							<th>Name</th>
							<td class="dc data-name"></td>
						</tr>
						<tr>
							<th>Betreff</th>
							<td class="dc data-subject"></td>
						</tr>
						<tr>
							<th>Inhalt HTML</th>
							<td class="dc data-html"></td>
						</tr>
						<tr>
							<th>Inhalt Text</th>
							<td class="dc data-text"></td>
						</tr>
						<tr><td colspan="2"><button id="mailtemplate_details_edit">Bearbeiten</button></td></tr>
					</table>
				</div>
				<div class="view" id="email_marketing">
					<h1>eMail-Marketing</h1>
					<h2>Kampagnen</h2>
					<table id="email_campaigns_list" class="striped">
						<thead>
							<tr><th>ID</th><th>Status</th><th>Name</th><th>Mailvorlage</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="5"><button id="email_campaigns_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
					<h2>Vorlagen</h2>
					<table id="email_templates_list" class="striped">
						<thead>
							<tr><th>ID</th><th>Name</th><th>Betreff</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="4"><button id="email_templates_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
				</div>
				<div class="view" id="email_sendaccount_details">
					<h1>Sendekonto-Details</h1>
					<table id="email_sendaccount_details_details" class="striped">
						<tr>
							<th>ID</th>
							<td class="dc data-id"></td>
						</tr>
						<tr>
							<th>Name</th>
							<td class="dc data-name"></td>
						</tr>
						<tr>
							<th>Absender-Name</th>
							<td class="dc data-fromname"></td>
						</tr>
						<tr>
							<th>Absender-Mailadresse</th>
							<td class="dc data-fromaddr"></td>
						</tr>
						<tr>
							<th>Typ</th>
							<td class="dc data-type"></td>
						</tr>
						<tr>
							<th>SMTP User</th>
							<td class="dc data-smtpuser"></td>
						</tr>
						<tr>
							<th>SMTP Passwort</th>
							<td class="dc data-smtppass"></td>
						</tr>
						<tr>
							<th>SMTP Host</th>
							<td class="dc data-smtphost"></td>
						</tr>
						<tr>
							<th>SMTP Port</th>
							<td class="dc data-smtpport"></td>
						</tr>
						<tr>
							<th>SMTP Secure Transport</th>
							<td class="dc data-smtpsectsp"></td>
						</tr>
						<tr><td colspan="2"><button id="email_sendaccount_details_edit">Bearbeiten</button></td></tr>
					</table>
				</div>
				<div class="view" id="email_internals">
					<h1>eMail-Interna</h1>
					<h2>Jobs</h2>
					<table id="email_jobs_list" class="striped">
						<thead>
							<tr><th>ID</th><th>Status</th><th>Mail</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
						</tfoot>
					</table>
					<h2>Sende-Konten</h2>
					<table id="email_sendaccounts_list" class="striped">
						<thead>
							<tr><th>ID</th><th>Name</th><th>Mail-Adresse</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="3"><button id="email_sendaccounts_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
					<h2>Empfangs-Konten</h2>
					<table id="email_recvaccounts_list" class="striped">
						<thead>
							<tr><th>ID</th><th>Name</th><th>Aktion</th></tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr><td colspan="3"><button id="email_recvaccounts_addnew">Neu hinzufügen</button></td></tr>
						</tfoot>
					</table>
			</div>
		</div>
  </body>
</html>
