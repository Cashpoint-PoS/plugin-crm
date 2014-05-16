<?
plugins_register_backend($plugin,array("icon"=>"icon-user","sub"=>array("customers"=>"Kunden","addrs"=>"Adressen",/*"groups"=>"Gruppen"*/)));
require("class.Customer.php");
require("class.Address.php");
//require("class.Group.php");
require("class.CRM_MailAddr.php");

plugins_register_target($plugin,"app/index.php","CRM");
