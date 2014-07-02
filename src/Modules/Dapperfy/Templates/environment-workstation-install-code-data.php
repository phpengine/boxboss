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

                array ( "Logging" => array( "log" => array( "log-message" => "Lets initialize our new download directory as a dapper project"), ) ),
                array ( "Project" => array( "init" => array(), ) , )
                
                array ( "Logging" => array( "log" => array( "log-message" => "Lets create our Host File entry"), ) ),
                array ( "HostEditor" => array( "init" => array(
                  "host-ip" => "<%tpl.php%>dap_apache_vhost_ip</%tpl.php%>",
                  "host-name" => "<%tpl.php%>dap_apache_vhost_url</%tpl.php%>.local",                
                ), ) , )
                                
                array ( "Logging" => array( "log" => array( "log-message" => "Next create our virtual host"), ) ),
                array ( "ApacheVHostEditor" => array( "add" => array (
                    "guess" => true,
                    "vhe-docroot" => "<%tpl.php%>dap_proj_cont_dir</%tpl.php%>",
                    "vhe-url" => "<%tpl.php%>dap_apache_vhost_url</%tpl.php%>",
                    "vhe-ip-port" => "<%tpl.php%>dap_apache_vhost_ip</%tpl.php%>",
                    "vhe-vhost-dir" => "/etc/apache2/sites-available",
                    "vhe-template" => $this->getTemplate(),
                    "vhe-file-ext" => ".conf",
                ), ), ),

                array ( "Logging" => array( "log" => array( "log-message" => "Next ensure our db file configuration is reset to blank" ), ), ),
                array ( "DBConfigure" => array( "reset" => array( "platform" => "<%tpl.php%>dap_db_platform</%tpl.php%>" ), ), ),

                array ( "Logging" => array( "log" => array("log-message" => "Next configure our projects db configuration file"), ) ),
                array ( "DBConfigure" => array( "conf" => array(
					"mysql-admin-user" => "<%tpl.php%>dap_db_admin_user_name</%tpl.php%>",
					"mysql-admin-pass" => "<%tpl.php%>dap_db_admin_user_pass</%tpl.php%>",
                    "mysql-host" => "<%tpl.php%>dap_db_ip_address</%tpl.php%>",
                    "mysql-user" => "<%tpl.php%>dap_db_app_user_name</%tpl.php%>",
                    "mysql-pass" => "<%tpl.php%>dap_db_app_user_pass</%tpl.php%>",
                    "mysql-db" => "<%tpl.php%>dap_db_name</%tpl.php%>",
                    "mysql-platform" => "<%tpl.php%>dap_db_platform</%tpl.php%>",
                ), ) , ) ,

                array ( "Logging" => array( "log" => array( "log-message" => "Now lets drop our current database if it exists"), ) ),
                array ( "DBInstall" => array( "drop" => array(
                    "mysql-admin-user" => "<%tpl.php%>dap_db_admin_user_name</%tpl.php%>",
                    "mysql-admin-pass" => "<%tpl.php%>dap_db_admin_user_pass</%tpl.php%>",
                    "mysql-host" => "<%tpl.php%>dap_db_ip_address</%tpl.php%>",
                    "mysql-user" => "<%tpl.php%>dap_db_app_user_name</%tpl.php%>",
                    "mysql-pass" => "<%tpl.php%>dap_db_app_user_pass</%tpl.php%>",
                    "mysql-db" => "<%tpl.php%>dap_db_name</%tpl.php%>",
                    "mysql-platform" => "<%tpl.php%>dap_db_platform</%tpl.php%>",
                ), ), ),

                array ( "Logging" => array( "log" => array("log-message" => "Now lets install our database"), ), ),
                array ( "DBInstall" => array( "install" => array(
                    "mysql-admin-user" => "<%tpl.php%>dap_db_admin_user_name</%tpl.php%>",
                    "mysql-admin-pass" => "<%tpl.php%>dap_db_admin_user_pass</%tpl.php%>",
                    "mysql-host" => "<%tpl.php%>dap_db_ip_address</%tpl.php%>",
                    "mysql-user" => "<%tpl.php%>dap_db_app_user_name</%tpl.php%>",
                    "mysql-pass" => "<%tpl.php%>dap_db_app_user_pass</%tpl.php%>",
                    "mysql-db" => "<%tpl.php%>dap_db_name</%tpl.php%>",
                    "mysql-platform" => "<%tpl.php%>dap_db_platform</%tpl.php%>"
                ), ), ),
              
				// @todo why version locally
                array ( "Logging" => array( "log" => array( "log-message" => "The application is installed now so lets do our versioning" ), ), ),
                array ( "Version" => array( "latest" => array(
                    "container" => "<%tpl.php%>dap_proj_cont_dir</%tpl.php%>",
                    "limit" => "<%tpl.php%>dap_version_num_revisions</%tpl.php%>"
                ), ), ),

                array ( "Logging" => array( "log" => array( "log-message" => "Now lets restart Apache so we are serving our new application version", ), ), ),
                array ( "ApacheControl" => array( "restart" => array(
                    "guess" => true,
                ), ), ),

                array ( "Logging" => array( "log" => array("log-message" => "Our deployment is done" ), ), ),
	      );

	  }


 // This function will set the vhost template for your Virtual Host
 // You need to call this from your constructor
 private function setVHostTemplate() {
   $this->steps[2]["ApacheVHostEditor"]["virtualHostEditorAdditionTemplateData"] =
  <<<'TEMPLATE'
 NameVirtualHost ****IP ADDRESS****:80
 <VirtualHost ****IP ADDRESS****:80>
   ServerAdmin webmaster@localhost
 	ServerName ****SERVER NAME****
 	DocumentRoot ****WEB ROOT****src
 	<Directory ****WEB ROOT****src>
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
 	 DocumentRoot ****WEB ROOT****src
     SSLEngine on
     SSLCertificateFile /etc/ssl/certs/ssl-cert-snakeoil.pem
     SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
   # SSLCertificateChainFile /etc/apache2/ssl/bundle.crt
 	 <Directory ****WEB ROOT****src>
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
