<?php

Namespace Info;

class XvfbInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Xvfb - The Display Manager Solution";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Xvfb" =>  array_merge(parent::routesAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("xvfb"=>"Xvfb");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module allows you to install Xvfb, the popular Virtual Machine Solution.

  Xvfb, xvfb

        - install
        Installs Xvfb through apt-get
        example: ptconfigure xvfb install

HELPDATA;
      return $help ;
    }

}