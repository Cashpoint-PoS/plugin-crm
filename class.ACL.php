<?
class ACL extends DBObj{
  protected static $__table="acl";
  public static $mod="user";
  public static $sub="acls";
  
  public static $elements=array(
    "obj_type"=>array("title"=>"Objekttyp","mode"=>"string","dbkey"=>"object_type"),
    "obj_id"=>array("title"=>"Objekt-ID","mode"=>"string","dbkey"=>"object_id"),
    "target_type"=>array("title"=>"Zieltyp","mode"=>"string","dbkey"=>"target_type"),
    "target_id"=>array("title"=>"Ziel-ID","mode"=>"string","dbkey"=>"target_type"),
    "negate"=>array("title"=>"Negativ","mode"=>"select","data"=>array(0=>"Nein",1=>"Ja"),"dbkey"=>"negate"),
    "acl"=>array("title"=>"Rechte","mode"=>"string","dbkey"=>"acl"),
    "obj"=>array("title"=>"Gültig für Objekt(e)","mode"=>"process"),
    "target"=>array("title"=>"Betrifft Benutzer","mode"=>"process"),
  );
  
  public static $list_elements=array(
    "obj","target","negate","acl"
  );
  
  public static $edit_elements=array(
    "obj_type","obj_id","target_type","target_id","negate","acl"
  );
  public static $detail_elements=array(
    "obj","target","negate","acl"
  );
  
  public function processProperty($key) {
    switch($key) {
      case "obj":
        if($this->object_type==="")
          return "Alle Objekte";
        else {
          if($this->object_id===0)
            return "Alle Objekt des Typs ".$this->object_type;
          else
            return "Objekt des Typs ".$this->object_type." mit der ID #".$this->object_id;
        }
      break;
      case "target":
        if($this->target_type==="")
          return "Alle Benutzer und Gruppen";
        elseif($this->target_type==="user")
          return "Benutzer #".$this->target_id." (".(User::getById($this->target_id,false)->displayname).")";
        elseif($this->target_type==="group")
          return "Alle Benutzer der Gruppe ".$this->target_id." (".(Group::getById($this->target_id,false)->name).")";
      break;
      default: return NULL;
    }
    return NULL;
  }
}

plugins_register_backend_handler($plugin,"acls","list",array("ACL","listView"));
plugins_register_backend_handler($plugin,"acls","edit",array("ACL","editView"));
plugins_register_backend_handler($plugin,"acls","view",array("ACL","detailView"));
plugins_register_backend_handler($plugin,"acls","submit",array("ACL","processSubmit"));
