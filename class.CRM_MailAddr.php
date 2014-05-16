<?
class CRM_MailAddr extends DBObj {
  protected static $__table="crm_mailaddrs";
  public static $mod="crm";
  public static $sub="mailaddr";
  
  public static $elements=array(
    "crm_customers_id"=>array("title"=>"","mode"=>"string","dbkey"=>"crm_customers_id"),
    "active"=>array("title"=>"","mode"=>"string","dbkey"=>"active"),
    "is_primary"=>array("title"=>"","mode"=>"string","dbkey"=>"is_primary"),
    "addr"=>array("title"=>"","mode"=>"string","dbkey"=>"addr"),
  );
  public static $link_elements=array(
  );
  public static $list_elements=array(
    "crm_customers_id",
    "active",
    "is_primary",
    "addr",
  );
  public static $detail_elements=array(
    "crm_customers_id",
    "active",
    "is_primary",
    "addr",
  );
  public static $edit_elements=array(
    "crm_customers_id",
    "active",
    "is_primary",
    "addr",
  );
  public static $links=array(
  );
  public function processProperty($key) {
    $ret=NULL;
    switch($key) {
    }
    return $ret;
  }
}

plugins_register_backend_handler($plugin,"mailaddr","list",array("CRM_MailAddr","listView"));
plugins_register_backend_handler($plugin,"mailaddr","edit",array("CRM_MailAddr","editView"));
plugins_register_backend_handler($plugin,"mailaddr","view",array("CRM_MailAddr","detailView"));
plugins_register_backend_handler($plugin,"mailaddr","submit",array("CRM_MailAddr","processSubmit"));
