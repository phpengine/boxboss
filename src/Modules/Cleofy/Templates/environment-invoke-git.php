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
            array ( "Logging" => array( "log" => array(
                "log-message" => "Lets begin invoking Configuration of a Git SCM Server on environment <%tpl.php%>env_name</%tpl.php%>",
            ), ), ),
            array ( "Logging" => array( "log" => array(
                "log-message" => "First lets SFTP over our Git SCM Server CM Autopilot",
            ), ), ),
            array ( "SFTP" => array( "put" =>  array(
                "source" => getcwd()."/build/config/cleopatra/autopilots/<%tpl.php%>env_name</%tpl.php%>-cm-git.php",
                "target" => "/tmp/<%tpl.php%>env_name</%tpl.php%>-cm-git.php",
                "environment-name" => "<%tpl.php%>env_name</%tpl.php%>",
            ), ), ),
            array ( "Logging" => array( "log" =>array(
                "log-message" => "Lets run that autopilot"
            ), ), ),
            array ( "Invoke" => array( "data" => array(
                "guess" => true,
                "ssh-data" => $this->setSSHData(),
                "environment-name" => "<%tpl.php%>env_name</%tpl.php%>",
            ), ), ),
            array ( "Logging" => array( "log" => array(
                "log-message" => "Invoking Git SCM Server on environment <%tpl.php%>env_name</%tpl.php%> complete",
            ), ), ),
        );

    }

    private function setSSHData() {
        $sshData = <<<"SSHDATA"
sudo cleopatra autopilot install /tmp/<%tpl.php%>env_name</%tpl.php%>-cm-git.php
rm /tmp/<%tpl.php%>env_name</%tpl.php%>-cm-git.php
SSHDATA;
        return $sshData ;
    }

}