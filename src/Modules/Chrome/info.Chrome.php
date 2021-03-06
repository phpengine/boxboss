<?php

Namespace Info;

class ChromeInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Chrome - Install or remove Chrome";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Chrome" =>  array_merge(parent::routesAvailable(), array() ) );
    }

    public function routeAliases() {
        return array("chrome"=>"Chrome");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module is part of the Default Distribution and provides you  with a method by which you can install Chrome from your package
  manager

  Chrome, chrome

        - install
        Installs Chrome
        example: ptconfigure chrome install

HELPDATA;
      return $help ;
    }

}