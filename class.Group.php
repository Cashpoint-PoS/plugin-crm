<?
class CRM_Group extends DBObj {
  protected static $__table="crm_groups";
  public static $mod="crm";
  public static $sub="groups";
  
  public static $elements=array(
    "name"=>array("title"=>"Name","mode"=>"string","dbkey"=>"name"),
    "description"=>array("title"=>"Beschreibung","mode"=>"string","dbkey"=>"description"),
  );
  
  public static $link_elements=array(
    "name","description"
  );
  public static $list_elements=array(
    "name","description"
  );
  public static $detail_elements=array(
    "name","description"
  );
  public static $edit_elements=array(
    "name","description"
  );
  public static $links=array(
    "CRM_Customer"=>array("title"=>"Mitglieder","table"=>"link_crm_customers_crm_groups"),
  );

}
plugins_register_backend_handler($plugin,"groups","list",array("CRM_Group","listView"));
plugins_register_backend_handler($plugin,"groups","edit",array("CRM_Group","editView"));
plugins_register_backend_handler($plugin,"groups","view",array("CRM_Group","detailView"));
plugins_register_backend_handler($plugin,"groups","submit",array("CRM_Group","processSubmit"));
