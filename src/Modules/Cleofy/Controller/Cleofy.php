<?php

Namespace Controller ;

class Cleofy extends Base {

    private $injectedActions = array();

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        // if we don't have an object, its an array of errors
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }

        $action = $pageVars["route"]["action"];

        if ($action=="help") {
            $helpModel = new \Model\Help();
            $this->content["helpData"] = $helpModel->getHelpData($pageVars["route"]["control"]);
            return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

        if ($action=="standard") {
          $this->content["result"] = $thisModel->askWhetherToCleofy();
          return array ("type"=>"view", "view"=>"cleofy", "pageVars"=>$this->content); }

        else if (in_array($action, array_keys($this->injectedActions))) {
          $extendedModel = new $this->injectedActions[$action]() ;
          $this->content["result"] = $extendedModel->askWhetherToCleofy();
          return array ("type"=>"view", "view"=>"cleofy", "pageVars"=>$this->content);
        }

    }

    public function injectCleofyAction($action, $modelName) {
       $this->injectedActions[] = array($action => $modelName);
    }

}