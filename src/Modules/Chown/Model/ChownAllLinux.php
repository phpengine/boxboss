<?php

Namespace Model;

class ChownAllLinux extends Base {

    // Compatibility
    public $os = array("Linux" ,"Darwin") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function askWhetherToChown() {
        return $this->performChown();
    }

    public function performChown() {
        if ($this->askForChownExecute() != true) { return false; }
        $dirPath = $this->getDirectoryPath() ;
        // @todo this looks very reliable
        $this->doChown($dirPath) ;
        return true;
    }

    private function doChown($dirPath) {
        $recursive = (isset($this->params["recursive"])) ? "-R " : "" ;
        $owner = $this->getOwner() ;
        $comm = "chown $recursive{$owner} $dirPath" ;
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Executing $comm", $this->getModuleName());
        self::executeAndOutput($comm) ;
    }

    private function askForChownExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Chown files?';
        return self::askYesOrNo($question);
    }

    private function getDirectoryPath(){
        if (isset($this->params["path"])) { return $this->params["path"] ; }
        else { $question = "Enter ownership change path:"; }
        $input = self::askForInput($question, true) ;
        return $input ;
    }

    private function getOwner(){
        if (isset($this->params["user"])) { return $this->params["user"] ; }
        else { $question = "Enter ownership user:"; }
        $input = self::askForInput($question, true) ;
        return $input ;
    }

}