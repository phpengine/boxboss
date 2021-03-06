<?php

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

    public function __construct() {
        $this->setSteps();
    }

    /* Steps */
    private function setSteps() {

        $this->steps =
            array(
                array ( "Logging" => array( "log" =>
                    array( "log-message" => "Lets begin invoking Configuration of a Bastion server on environment <%tpl.php%>env_name</%tpl.php%>"),
                ) ),
                array ( "Logging" => array( "log" =>
                    array( "log-message" => "First lets SFTP over our Bastion Server CM Autopilot"),
                ) ),
                array ( "SFTP" => array( "copy" =>
                    array("original-file" => "build/config/ptconfigure/autopilots/<%tpl.php%>env_name</%tpl.php%>-cm-bastion.php" ),
                    array("target-file" => "/tmp/<%tpl.php%>env_name</%tpl.php%>-cm-bastion.php" ),
                    array("environment-name" => "<%tpl.php%>env_name</%tpl.php%>" ),
                ) , ) ,
                array ( "Logging" => array( "log" =>
                    array( "log-message" => "Lets run that autopilot"),
                ) ),
                array ( "Invoke" => array( "data" =>
                    array("ssh-data" => $this->setSSHData() ),
                    array("environment-name" => "<%tpl.php%>env_name</%tpl.php%>" ),
                ) , ) ,
                array ( "Logging" => array( "log" =>
                    array( "log-message" => "Invoking a Bastion server on environment <%tpl.php%>env_name</%tpl.php%> complete"),
                ) ),
            );

    }

    private function setSSHData() {
        $sshData = <<<"SSHDATA"
sudo ptconfigure autopilot execute /tmp/<%tpl.php%>env_name</%tpl.php%>-cm-bastion.php
SSHDATA;
        return $sshData ;
    }

}
