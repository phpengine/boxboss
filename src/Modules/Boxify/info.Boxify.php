<?php

Namespace Info;

class BoxifyInfo extends CleopatraBase {

  public $hidden = false;

  public $name = "Boxify Wrapper - Create Cloud Instances";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "Boxify" =>  array_merge(parent::routesAvailable(), array("box-add", "box-destroy", "box-remove", "list-papyrus") ) );
  }

  public function routeAliases() {
    return array("boxify"=>"Boxify");
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to Boxify a Box Management wrapper.

  Boxify, boxify

        - box-add
        Installs a Box through a cloud provider
        example: cleopatra boxify box-add --environment-name="staging" --server-prefix="my-app-staging" --provider="DigitalOcean" --image-id="3101045" --size-id="66" --region-id="2"

        - box-remove
        Removes a Box from the papyrus
        example: cleopatra boxify box-remove --environment-name="staging" --environment-version="5.0" --provider="apt-get"

        - box-destroy
        Removes a Box from both papyrus and the cloud provider
        example: cleopatra boxify box-destroy --environment-name="staging" --server-prefix="" --provider="apt-get"

        - list-papyrus
        List all servers in papyrus, or those of a particular environment
        example: cleopatra boxify list-papyrus --yes
        example: cleopatra boxify list-papyrus --yes --environment-name="staging"

  A environment manager wrapper that will allow you to install environments on any system

HELPDATA;
    return $help ;
  }

}