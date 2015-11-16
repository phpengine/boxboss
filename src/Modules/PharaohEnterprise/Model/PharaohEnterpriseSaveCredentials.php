<?php

Namespace Model;

class PharaohEnterpriseSaveCredentials extends BaseLinuxApp {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("SaveCredentials") ;

    protected $username ;
    protected $apiKey ;

    public function __construct($params) {
        parent::__construct($params);
        $this->programNameMachine = "PharaohEnterprise"; // command and app dir name
        $this->programNameFriendly = "PT Enterprise"; // 12 chars
        $this->programNameInstaller = "Pharaoh Enterprise - upgrade from open source to Enterprise";
        $this->statusCommand = "httpd -v" ;
        $this->versionInstalledCommand = SUDOPREFIX.'git log -n 1 --pretty=format:"%H"' ;
        $this->versionRecommendedCommand = SUDOPREFIX.'git log -n 1 --pretty=format:"%H"' ;
        $this->versionLatestCommand = SUDOPREFIX.'git log -n 1 --pretty=format:"%H"' ;
        $this->initialize();
    }

    public function setInstallCommands() {
        $ray =
            array(
                array("method"=> array("object" => $this, "method" => "initialiseEnterprise", "params" => array()) ),
                array("method"=> array("object" => $this, "method" => "saveCredentials", "params" => array()) ),
            ) ;
        $this->installCommands = $ray ;
    }

    protected function initialiseEnterprise() {
        $this->username = $this->askForPharaohEnterpriseUsername();
        $this->apiKey = $this->askForPharaohEnterpriseAPIKey();
    }

    protected function askForPharaohEnterpriseAPIKey(){
        if (isset($this->params["api-key"])) { return $this->params["api-key"] ; }
        $appVar = \Model\AppConfig::getAppVariable("pharaoh-enterprise-api-key") ;
        if ($appVar != null) {
            $question = 'Use Application saved Pharaoh Enterprise API Key?';
            if (self::askYesOrNo($question, true) == true) { return $appVar ; } }
        $question = 'Enter Pharaoh Enterprise API Key';
        return self::askForInput($question, true);
    }

    protected function askForPharaohEnterpriseUsername(){
        if (isset($this->params["user-name"])) { return $this->params["user-name"] ; }
        $appVar = \Model\AppConfig::getAppVariable("pharaoh-enterprise-user-name") ;
        if ($appVar != null) {
            $question = 'Use Application saved Pharaoh Enterprise User Name?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter Pharaoh Enterprise User Name';
        return self::askForInput($question, true);
    }

    public function saveCredentials() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Storing Pharaoh Enterprise credentials...", $this->getModuleName()) ;
        \Model\AppConfig::setAppVariable("pharaoh-enterprise-user-name", $this->username);
        \Model\AppConfig::setAppVariable("pharaoh-enterprise-api-key", $this->apiKey) ;
        return true ;
    }


}