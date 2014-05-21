<?
class CRM_MailCampaign extends DBObj {
  protected static $__table="crm_campaigns";
  public static $mod="crm";
  public static $sub="mailcampaigns";
  
  public static $elements=array(
  "status"=>array("title"=>"Status","mode"=>"select","dbkey"=>"status","data"=>array("In Bearbeitung","Sende Testmails","Sende Produktivmails","Abgeschlossen","Beendet")),
"name"=>array("title"=>"Name","mode"=>"string","dbkey"=>"name"),
"mail_templates_id"=>array("title"=>"Mailvorlage","mode"=>"one2many","dbkey"=>"mail_templates_id","data"=>"Mail_Template"),
"testgroup_id"=>array("title"=>"ID Testgruppe","mode"=>"one2many","dbkey"=>"testgroup_id","data"=>"CRM_Group"),
"prodgroup_id"=>array("title"=>"ID Produktiv-Gruppe","mode"=>"one2many","dbkey"=>"prodgroup_id","data"=>"CRM_Group"),
"mail_sendaccounts_id"=>array("title"=>"Absenderkonto","mode"=>"one2many","dbkey"=>"mail_sendaccounts_id","data"=>"Mail_SendAccount"),
  );
  public static $link_elements=array(
  );
  public static $list_elements=array(
  "status",
"name",
"mail_templates_id",
"mail_sendaccounts_id",
  );
  public static $detail_elements=array(
  "status",
"name",
"mail_templates_id",
"testgroup_id",
"prodgroup_id",
"mail_sendaccounts_id",
  );
  public static $edit_elements=array(
  "status",
"name",
"mail_templates_id",
"testgroup_id",
"prodgroup_id",
"mail_sendaccounts_id",
  );
  public static $links=array(
  );
  public function processProperty($key) {
    $ret=NULL;
    switch($key) {
    }
    return $ret;
  }
  private function sendMails($group) {
  	$log="";
    $customers=$group->getLinkedObjects("CRM_Customer","link_crm_customers_crm_groups");
    $template=Mail_Template::getById($this->mail_templates_id);
    foreach($customers as $customer) {
      $customer=$customer->obj;
      $addrs=CRM_MailAddr::getByOwner($customer);
      if(sizeof($addrs)==0) {
        $log.=sprintf("Customer %d has no email addrs\n",$customer->id);
        continue;
      }
      $log.=sprintf("Customer %d has %d possible email addrs\n",$customer->id,sizeof($addrs));
      $eligible=array();
      foreach($addrs as $addr) {
        if($addr->active!=1) {
          $log.=sprintf("Customer %d email %d (%s) marked inactive\n",$customer->id,$addr->id,$addr->addr);
          continue;
        }
        if($addr->newsletter_ok!=1) {
          $log.=sprintf("Customer %d email %d (%s) marked no-newsletter\n",$customer->id,$addr->id,$addr->addr);
          continue;
        }
        $eligible[]=$addr;
      }
      $primary=null;
      if(sizeof($eligible)==0) {
        $log.=sprintf("Customer %d has no eligible email addrs\n",$customer->id);
        continue;
      } else if(sizeof($eligible)==1) {
        $primary=$eligible[0];
      } else {
        $primary=$eligible[0];
        foreach($eligible as $addr) {
          if($addr->is_primary==1)
            $primary=$addr;
        }
      }
      $log.=sprintf("Customer %d chose addr %d (%s)\n",$customer->id,$primary->id,$primary->addr);
      $mail=$template->applyAndCreate($customer);
      $mail->rcpt_to=$primary->addr;
      $mail->mail_sendaccounts_id=$this->mail_sendaccounts_id;
      $mail->commit();
      $log.=sprintf("Customer %d committed mail object with id %d\n",$customer->id,$mail->id);
      $job=Mail_Job::fromScratch();
      $job->mail_mails_id=$mail->id;
      $job->status=0;
      $job->commit();
      $log.=sprintf("Customer %d committed job with id %d\n",$customer->id,$job->id);
    }
  	return $log;
  }
  public function specialAction($action) {
  	$log="";
  	switch($action) {
  		case "sendtestmails":
  			$group=CRM_Group::getById($this->testgroup_id);
				$log.=$this->sendMails($group);
  		break;
  		case "sendprodmails":
  			$group=CRM_Group::getById($this->prodgroup_id);
				$log.=$this->sendMails($group);
  		break;
  	}
  	return $log;
  }
}

plugins_register_backend_handler($plugin,"mailcampaigns","list",array("CRM_MailCampaign","listView"));
plugins_register_backend_handler($plugin,"mailcampaigns","edit",array("CRM_MailCampaign","editView"));
plugins_register_backend_handler($plugin,"mailcampaigns","view",array("CRM_MailCampaign","detailView"));
plugins_register_backend_handler($plugin,"mailcampaigns","submit",array("CRM_MailCampaign","processSubmit"));
plugins_register_backend_handler($plugin,"mailcampaigns","specialAction",array("CRM_MailCampaign","processSpecialAction"));