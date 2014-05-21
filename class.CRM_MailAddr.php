<?
class CRM_MailAddr extends DBObj {
  protected static $__table="crm_mailaddrs";
  public static $mod="crm";
  public static $sub="mailaddrs";
  
  public static $elements=array(
    "crm_customers_id"=>array("title"=>"Kunde","mode"=>"string","dbkey"=>"crm_customers_id"),
    "active"=>array("title"=>"Aktiv","mode"=>"select","dbkey"=>"active","data"=>array("Nein","Ja")),
    "is_primary"=>array("title"=>"Primär","mode"=>"select","dbkey"=>"is_primary","data"=>array("Nein","Ja")),
    "newsletter_ok"=>array("title"=>"Newsletter OK","mode"=>"select","dbkey"=>"newsletter_ok","data"=>array("Nein","Ja")),
    "addr"=>array("title"=>"Adresse","mode"=>"string","dbkey"=>"addr"),
    "type"=>array("title"=>"Typ","mode"=>"select","data"=>array("Unbekannt","Arbeit","Privat"),"dbkey"=>"type"),
  );
  public static $link_elements=array(
  );
  public static $list_elements=array(
    "crm_customers_id",
    "active",
    "is_primary",
    "addr",
    "type",
    "newsletter_ok",
  );
  public static $detail_elements=array(
    "crm_customers_id",
    "active",
    "is_primary",
    "addr",
    "type",
    "newsletter_ok",
  );
  public static $edit_elements=array(
    "crm_customers_id",
    "active",
    "is_primary",
    "addr",
    "type",
    "newsletter_ok",
  );
  public static $links=array(
  );
  public function processProperty($key) {
    $ret=NULL;
    switch($key) {
    }
    return $ret;
  }
  public function commit() {
  	if($this->active==1) {
  		$list=static::getByFilter("where crm_customers_id=? and is_primary=1 and id!=?",$this->crm_customers_id,$this->id);
  		foreach($list as $obj) {
  			$obj->is_primary=0;
  			$obj->commit();
  		}
  	}
  	parent::commit();
  }
}

plugins_register_backend_handler($plugin,"mailaddrs","list",array("CRM_MailAddr","listView"));
plugins_register_backend_handler($plugin,"mailaddrs","edit",array("CRM_MailAddr","editView"));
plugins_register_backend_handler($plugin,"mailaddrs","view",array("CRM_MailAddr","detailView"));
plugins_register_backend_handler($plugin,"mailaddrs","submit",array("CRM_MailAddr","processSubmit"));
