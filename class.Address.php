<?
//(postal) addresses
//
class CRM_Address extends DBObj {
  protected static $__table="crm_addrs";
  public static $mod="crm";
  public static $sub="addrs";
  
  public static $elements=array(
  //""=>array("title"=>"","mode"=>"string","dbkey"=>""),
  "crm_customers_id"=>array("title"=>"Kunde","mode"=>"string","dbkey"=>"crm_customers_id"),
   "type"=>array("title"=>"Typ","mode"=>"select","data"=>array("Unbekannt","Arbeit","Privat"),"dbkey"=>"type"),
	"prefix_line"=>array("title"=>"ZVZ1 (Versandkennzeichen)","mode"=>"string","dbkey"=>"prefix_line"),
	"name"=>array("title"=>"Namenszeile","mode"=>"string","dbkey"=>"name"),
	"line2"=>array("title"=>"Adresszeile 2","mode"=>"string","dbkey"=>"line2"),
	"line3"=>array("title"=>"Adresszeile 3","mode"=>"string","dbkey"=>"line3"),
	"street"=>array("title"=>"Straße","mode"=>"string","dbkey"=>"street"),
	"house_nr"=>array("title"=>"Hausnummer","mode"=>"string","dbkey"=>"house_nr"),
	"postal_code"=>array("title"=>"Postleitzahl","mode"=>"string","dbkey"=>"postal_code"),
	"city"=>array("title"=>"Stadt","mode"=>"string","dbkey"=>"city"),
	"country"=>array("title"=>"Land","mode"=>"string","dbkey"=>"country"),
	"formatted"=>array("title"=>"Formatiert","mode"=>"text","dbkey"=>"formatted"),
        "customer_name"=>array("title"=>"Kunde","mode"=>"process"),
  );
  
  public static $link_elements=array(
//    "formatted","city","country"
  );
  public static $list_elements=array(
    "customer_name","name","city","country","type"
  );
  public static $detail_elements=array(
    "customer_name","prefix_line","name","line2","line3","street","house_nr","postal_code","city","country","formatted","type"
  );
  public static $edit_elements=array(
    "type","crm_customers_id","prefix_line","name","line2","line3","street","house_nr","postal_code","city","country","formatted"
  );
  public static $links=array(
//    "User"=>array("title"=>"Mitglieder","table"=>"link_users_groups"),
  );
  public function processProperty($key) {
    $ret=NULL;
    switch($key) {
      case "customer_name":
        if($this->crm_customers_id==0) {
          $ret="(unbekannt)";
          break;
        } else {
          $obj=CRM_Customer::getById($this->crm_customers_id);
          $ret=$obj->toString();
        }
      break;
    }
    return $ret;
  }
  
  public function toString() {
    return $this->formatted;
  }
  
  public function commit() {
    if(trim($this->formatted)=="") {
      if(trim($this->prefix_line)!="")
        $this->formatted.=$this->prefix_line."\n";
      if(trim($this->name)!="")
        $this->formatted.=$this->name."\n";
      if(trim($this->line2)!="")
        $this->formatted.=$this->line2."\n";
      if(trim($this->line3)!="")
        $this->formatted.=$this->line3."\n";
      if(trim($this->street)!="")
        $this->formatted.=$this->street." ";
      if(trim($this->house_nr)!="")
        $this->formatted.=$this->house_nr."\n";
      if(trim($this->postal_code)!="")
        $this->formatted.=$this->postal_code;
      if(trim($this->city)!="")
        $this->formatted.=" ".$this->city."\n";
      else
      	$this->formatted.="\n";
      if(trim($this->country)!="")
        $this->formatted.=$this->country."\n";
    }
    parent::commit();
  }
}

plugins_register_backend_handler($plugin,"addrs","list",array("CRM_Address","listView"));
plugins_register_backend_handler($plugin,"addrs","edit",array("CRM_Address","editView"));
plugins_register_backend_handler($plugin,"addrs","view",array("CRM_Address","detailView"));
plugins_register_backend_handler($plugin,"addrs","submit",array("CRM_Address","processSubmit"));
