<?php

Namespace Model;

class PHPLdapAdminCentos32 extends BaseLinuxApp {

  // Compatibility
  public $os = array("Linux") ;
  public $linuxType = array("Redhat") ;
  public $distros = array("CentOS") ;
  public $versions = array("5.9", "6.4") ;
  public $architectures = array("32") ;

  // Model Group
  public $modelGroup = array("Default") ;

  public function __construct($params) {
      parent::__construct($params);
      $this->autopilotDefiner = "PHPLdapAdmin";
      $this->installCommands = array("yum install -y haproxy");
      $this->uninstallCommands = array("yum remove -y haproxy");
      $this->programDataFolder = "/opt/PHPLdapAdmin"; // command and app dir name
      $this->programNameMachine = "haproxy"; // command and app dir name
      $this->programNameFriendly = "HA Proxy Server!"; // 12 chars
      $this->programNameInstaller = "HA Proxy Server";
      $this->statusCommand = "haproxy -v" ;
//      $this->versionInstalledCommand = SUDOPREFIX."apt-cache policy httpd" ;
//      $this->versionRecommendedCommand = SUDOPREFIX."apt-cache policy httpd" ;
//      $this->versionLatestCommand = SUDOPREFIX."apt-cache policy httpd" ;
      $this->initialize();
  }

}