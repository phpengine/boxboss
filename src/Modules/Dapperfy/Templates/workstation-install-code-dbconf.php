<?php

/*************************************
*      Generated Autopilot file      *
*     ---------------------------    *
*Autopilot Generated By Dapperstrano *
*     ---------------------------    *
*************************************/

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

    public function __construct() {
	    $this->setSteps();
        $this->setVHostTemplate();
    }

    /* Steps */
    private function setSteps() {

	    $this->steps =
        array(
              array ( "Project" => array(
                    "projectInitializeExecute" => true,
              ) , ) ,
              array ( "HostEditor" => array(
                    "hostEditorAdditionExecute" => true,
                    "hostEditorAdditionIP" => "****dap_apache_vhost_ip****",
                    "hostEditorAdditionURI" => "****dap_apache_vhost_url****.local",
              ) , ) ,
              array ( "VHostEditor" => array(
                    "virtualHostEditorAdditionExecute" => true,
                    "virtualHostEditorAdditionDocRoot" => "****dap_proj_cont_dir****",
                    "virtualHostEditorAdditionURL" => "****dap_apache_vhost_url****.local",
                    "virtualHostEditorAdditionIp" => "****dap_apache_vhost_ip****",
                    "virtualHostEditorAdditionTemplateData" => "",
                    "virtualHostEditorAdditionDirectory" => "/etc/apache2/sites-available",
                    "virtualHostEditorAdditionFileSuffix" => "",
                    "virtualHostEditorAdditionVHostEnable" => true,
                    "virtualHostEditorAdditionSymLinkDirectory" => "/etc/apache2/sites-enabled",
                    "virtualHostEditorAdditionApacheCommand" => "apache2",
              ) , ) ,
              array ( "DBConfigure" => array(
                        "dbResetExecute" => true,
                        "dbResetPlatform" => "****dap_db_platform****",
              ) , ) ,
              array ( "DBConfigure" => array(
                        "dbConfigureExecute" => true,
                        "dbConfigureDBHost" => "****dap_db_ip_address****",
                        "dbConfigureDBUser" => "****dap_db_app_user_name****",
                        "dbConfigureDBPass" => "****dap_db_app_user_pass***",
                        "dbConfigureDBName" => "****dap_db_name***",
                        "dbConfigurePlatform" => "****dap_db_platform****",
              ) , ) ,
	      );

	  }


 // This function will set the vhost template for your Virtual Host
 // You need to call this from your constructor
 private function setVHostTemplate() {
   $this->steps[2]["VHostEditor"]["virtualHostEditorAdditionTemplateData"] =
  <<<'TEMPLATE'
 NameVirtualHost ****IP ADDRESS****:80
 <VirtualHost ****IP ADDRESS****:80>
   ServerAdmin webmaster@localhost
 	ServerName ****SERVER NAME****
 	DocumentRoot ****WEB ROOT****/src
 	<Directory ****WEB ROOT****/src>
 		Options Indexes FollowSymLinks MultiViews
 		AllowOverride All
 		Order allow,deny
 		allow from all
 	</Directory>
   ErrorLog /var/log/apache2/error.log
   CustomLog /var/log/apache2/access.log combined
 </VirtualHost>

 NameVirtualHost ****IP ADDRESS****:443
 <VirtualHost ****IP ADDRESS****:443>
 	 ServerAdmin webmaster@localhost
 	 ServerName ****SERVER NAME****
 	 DocumentRoot ****WEB ROOT****/src
   # SSLEngine on
 	 # SSLCertificateFile /etc/apache2/ssl/ssl.crt
   # SSLCertificateKeyFile /etc/apache2/ssl/ssl.key
   # SSLCertificateChainFile /etc/apache2/ssl/bundle.crt
 	 <Directory ****WEB ROOT****/src>
 		 Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>
  ErrorLog /var/log/apache2/error.log
  CustomLog /var/log/apache2/access.log combined
  </VirtualHost>
TEMPLATE;
}


}
