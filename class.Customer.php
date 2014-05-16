<?
class CRM_Customer extends DBObj {
  protected static $__table="crm_customers";
  public static $mod="crm";
  public static $sub="customers";
  
  public static $elements=array(
    "type"=>array("title"=>"Typ","mode"=>"select","data"=>array("Unbekannt","Nat. Person","Rechtl. Person"),"dbkey"=>"type"),
    "person_gender"=>array("title"=>"Geschlecht","mode"=>"select","data"=>array("Unbekannt","Männlich","Weiblich"),"dbkey"=>"person_gender"),
    "salutation"=>array("title"=>"Volle Anrede im Brief","mode"=>"string","dbkey"=>"salutation"),
    "company_name"=>array("title"=>"Firmenname","mode"=>"string","dbkey"=>"company_name"),
    "person_name_prefix"=>array("title"=>"Namenspräfix","mode"=>"string","dbkey"=>"person_name_prefix"),
    "person_name_given_name"=>array("title"=>"Vorname(n)","mode"=>"string","dbkey"=>"person_name_given_name"),
    "person_name_middle_name"=>array("title"=>"Mittelname(n)","mode"=>"string","dbkey"=>"person_name_middle_name"),
    "person_name_family_name"=>array("title"=>"Nachname","mode"=>"string","dbkey"=>"person_name_family_name"),
    "person_name_suffix"=>array("title"=>"Namenssuffix","mode"=>"string","dbkey"=>"person_name_suffix"),
    "vat_id"=>array("title"=>"USt-ID","mode"=>"string","dbkey"=>"vat_id"),
    "_person_name"=>array("title"=>"Name","mode"=>"process"),
    "_type"=>array("title"=>"Typ","mode"=>"process"),
    "deladdr"=>array("title"=>"Standard-Lieferadresse","mode"=>"one2many","dbkey"=>"deladdr","data"=>"CRM_Address"),
    "billaddr"=>array("title"=>"Standard-Rechnungsadresse","mode"=>"one2many","dbkey"=>"billaddr","data"=>"CRM_Address"),
  );
  
  public static $link_elements=array(
  );
  public static $list_elements=array(
    "_type","company_name","_person_name","vat_id"
  );
  public static $detail_elements=array(
    "_type","company_name","salutation","person_name_prefix","person_name_given_name","person_name_middle_name","person_name_family_name","person_name_suffix","vat_id","deladdr","billaddr"
  );
  public static $edit_elements=array(
    "type","company_name","salutation","person_gender","person_name_prefix","person_name_given_name","person_name_middle_name","person_name_family_name","person_name_suffix","vat_id","deladdr","billaddr"
  );
  public static $links=array(
//    "User"=>array("title"=>"Mitglieder","table"=>"link_users_groups"),
  );
  public static $one2many=array(
    "CRM_Address"=>array("title"=>"Adressen"),
  );
  
  public function processProperty($key) {
    $ret=NULL;
    switch($key) {
      case "_person_name":
        $ret="";
        if($this->type==2) {
        	if($this->company_name!="") {
        		$ret=$this->company_name;
        	} else {
            if($this->person_name_given_name!=="")
              $ret.=$this->person_name_given_name." ";
            if($this->person_name_family_name!=="")
              $ret.=$this->person_name_family_name." ";
        	}
        } else if($this->type==1) {
          if($this->person_name_prefix!=="")
            $ret.=$this->person_name_prefix." ";
          if($this->person_name_given_name!=="")
            $ret.=$this->person_name_given_name." ";
          if($this->person_name_middle_name!=="")
            $ret.=$this->person_name_middle_name." ";
          if($this->person_name_family_name!=="")
            $ret.=$this->person_name_family_name." ";
          if($this->person_name_suffix!=="")
            $ret.=$this->person_name_suffix;
        }
        $ret=trim($ret);
      break;
      case "_type":
        $ret=static::$elements["type"]["data"][$this->type];
        if($this->type!=1)
          break;
        switch($this->person_gender) {
          case 0: $ret.=" (Unbekannt)"; break;
          case 1: $ret.=" (männlich)"; break;
          case 2: $ret.=" (weiblich)"; break;
        }
      break;
    }
    return $ret;
  }
  public function commit() {
  	if(trim($this->salutation)=="") {
  		if($this->type==0) //Unbekannt
  			$this->salutation="Sehr geehrte Damen und Herren";
  		else if($this->type==1) { //Nat. Person
  			if($this->person_gender==0) //Unbekannt
  				$this->salutation="Sehr geehrte Dame, sehr geehrter Herr ";
  			else if($this->person_gender==1) //Männlich
  				$this->salutation="Sehr geehrter Herr ";
  			else if($this->person_gender==2) //Weiblich
  				$this->salutation="Sehr geehrte Frau ";
  			
  			if($this->person_name_prefix!="") 
  				$this->salutation.=trim($this->person_name_prefix)." ";
  			if($this->person_name_family_name!="") 
  				$this->salutation.=trim($this->person_name_family_name)." ";
  			if($this->person_name_suffix!="") 
  				$this->salutation.=trim($this->person_name_suffix)." ";
  		} else if($this->type==2) { //Rechtl. Person
  			if($this->person_gender==0) //Unbekannt
  				$this->salutation="Sehr geehrte Damen und Herren ";
  			else if($this->person_gender==1) //Männlich
  				$this->salutation="Sehr geehrter Herr ";
  			else if($this->person_gender==2) //Weiblich
  				$this->salutation="Sehr geehrte Frau ";
  			
  			if($this->person_name_prefix!="") 
  				$this->salutation.=trim($this->person_name_prefix)." ";
  			if($this->person_name_family_name!="") 
  				$this->salutation.=trim($this->person_name_family_name)." ";
  			if($this->person_name_suffix!="") 
  				$this->salutation.=trim($this->person_name_suffix)." ";
  		}
  	}
  	parent::commit();
  }
  public function toString() {
    if($this->type==1)
      return $this->company_name;
    else
      return $this->processProperty("_person_name");
  }
}
plugins_register_backend_handler($plugin,"customers","list",array("CRM_Customer","listView"));
plugins_register_backend_handler($plugin,"customers","edit",array("CRM_Customer","editView"));
plugins_register_backend_handler($plugin,"customers","view",array("CRM_Customer","detailView"));
plugins_register_backend_handler($plugin,"customers","submit",array("CRM_Customer","processSubmit"));
