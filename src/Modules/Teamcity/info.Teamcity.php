<?php

Namespace Info;

class TeamcityInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Teamcity - The Jetbrains Build Server";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Teamcity" =>  array_merge(parent::routesAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("teamcity"=>"Teamcity");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module allows you to install Teamcity, the popular Build Server.

  Teamcity, teamcity

        - install
        Installs Teamcity from the Jetbrains distributed native package
        example: ptconfigure teamcity install

HELPDATA;
      return $help ;
    }

}