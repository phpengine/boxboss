<?php

Namespace Info;

class NagiosServerInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Nagios Server - Install or remove the Nagios Server";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "NagiosServer" =>  array_merge(parent::routesAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("nagios-server"=>"NagiosServer", "nagiosserver"=>"NagiosServer", "nagios"=>"NagiosServer");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module is part of the Default Distribution and provides you with a method by which you can install Nagios.

  NagiosServer, nagios-server, nagiosserver, nagios

        - install
        Installs Nagios Network Monitoring Server
        example: ptconfigure nagios-server install

HELPDATA;
      return $help ;
    }

}