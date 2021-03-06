<?php

Namespace Info;

class PhakeInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Phake - The PHP task creation tool (Make/Rake)";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Phake" =>  parent::routesAvailable() );
    }

    public function routeAliases() {
      return array("phake"=>"Phake");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module allows you to install or update Phake.

  Phake, phake

        - install
        Installs the latest version of phake
        example: ptconfigure phake install

        - ensure
        Installs the latest version of phake, only if a version is not installed
        example: ptconfigure phake ensure

HELPDATA;
      return $help ;
    }

}