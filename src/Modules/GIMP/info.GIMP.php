<?php

Namespace Info;

class GIMPInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "GIMP - The Image Editor";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "GIMP" =>  array_merge(parent::routesAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("gimp"=>"GIMP", "Gimp"=>"GIMP");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module allows you to install GIMP, the popular Image Editor
  GIMP, gimp

        - install
        Installs GIMP through apt-get
        example: ptconfigure gimp install

HELPDATA;
      return $help ;
    }

}