<?php

Namespace Model;

class PingAnyOS extends BaseLinuxApp {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;
    protected $targets ;
    protected $interval ;
    protected $maxWait ;
    protected $actionsToMethods = array(
        "once" => "performPingOnce",
        "ten" => "performTenPings",
        "until-responding" => "performPingsUntil",
    ) ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Ping";
        $this->programDataFolder = "";
        $this->programNameMachine = "ping"; // command and app dir name
        $this->programNameFriendly = "!Ping!!"; // 12 chars
        $this->programNameInstaller = "Ping";
        $this->initialize();
    }

    protected function performPingOnce() {
        $libDir = str_replace("Model", "Libraries", dirname(__FILE__) ) ;
        require_once ("{$libDir}".DS."JJG".DS."Ping.php") ;
        $this->setTarget();
        return $this->doOnePing();
    }

    protected function performTenPings() {
        $libDir = str_replace("Model", "Libraries", dirname(__FILE__) ) ;
        require_once ("{$libDir}".DS."JJG".DS."Ping.php") ;
        $this->setTarget();
        $this->setInterval();
        return $this->doTenPings();
    }

    protected function performPingsUntil() {
        $libDir = str_replace("Model", "Libraries", dirname(__FILE__) ) ;
        require_once ("{$libDir}".DS."JJG".DS."Ping.php") ;
        $this->setTarget();
        $this->setInterval();
        $this->setMaxWait();
        return $this->doPingsUntil();
    }

    protected function setTarget($target = null) {
        $ges = $this->getEnvironmentIfSet() ;
        if ($ges !== false) {
            $this->targets = $this->getEnvironmentServerTargets($ges); }
        else if (isset($target) && is_array($target)) {
            $this->targets = $target; }
        else if (isset($target) && !is_array($target)) {
            $this->targets = array($target); }
        else if (isset($this->params["targets"])) {
            $this->targets = explode(',', $this->params["targets"] ) ; }
        else if (isset($this->params["target"])) {
            $this->targets = explode(',', $this->params["target"] ) ; }
        else {
            $this->targets = self::askForInput("Enter Target: ", true); }
    }

    protected function getEnvironmentIfSet() {
        if (isset($this->params["environment-name"])) {
            return $this->getEnvironment($this->params["environment-name"]); }
        else if (isset($this->params["env"])) {
            $this->params["environment-name"] = $this->params["env"] ;
            return $this->getEnvironment($this->params["environment-name"]) ; }
        else {
            return false; }
    }

    protected function getEnvironment($envToFind) {
        $envs = \Model\AppConfig::getProjectVariable("environments");
        foreach ($envs as $env) {
            if ($env["any-app"]["gen_env_name"] == $envToFind) {
                return $env ; } }
        return false ;
    }

    protected function getEnvironmentServerTargets($env) {
        $stargets = array() ;
        foreach ($env["servers"] as $server) {
            $target_types = array("target", "target_public", "target_private") ;
            foreach ($target_types as $target_type) {
                if (isset($server[$target_type])) {
                    $stargets[] = $server[$target_type] ;
                    continue 1 ; } } }
        return (count($stargets) > 0 ) ? $stargets : false ;
    }

    protected function setInterval() {
        if (isset($this->params["interval"])) {
            $this->interval = $this->params["interval"]; }
        else if (isset($this->params["guess"])) {
            $this->interval = "2" ; }
        else {
            $this->interval = self::askForInput("Enter Interval: ", true); }
    }

    protected function setMaxWait() {
        if (isset($this->params["max-wait"])) {
            $this->maxWait = $this->params["max-wait"]; }
        else if (isset($this->params["guess"])) {
            $this->maxWait = "60" ; }
        else {
            $this->maxWait = self::askForInput("Enter Max Wait Time: ", true); }
    }

    private function doOnePing() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $statuses = array() ;
        $c = count($this->targets) ;
        $logging->log("Attempting to Ping {$c} Host/s", $this->getModuleName()) ;
        for ($target_count = 0; $target_count < count($this->targets) ; $target_count++) {
            $logging->log("Attempting to Ping Host {$this->targets[$target_count]} once", $this->getModuleName()) ;
            try {
                $ping = new \JJG\Ping($this->targets[$target_count]);
                $latency = $ping->ping();
                if ($latency !== false) {
                    $logging->log('Ping Latency is ' . $latency . ' ms', $this->getModuleName()) ;
                    $statuses[$target_count] = true; }
                else {
                    $logging->log("Ping Host {$this->targets[$target_count]} could not be reached.", $this->getModuleName()) ;
                    $statuses[$target_count] = false; } }
            catch (\Exception $e) {
                $logging->log('Failed to execute the Ping Library: '.$e->getMessage(), $this->getModuleName(), LOG_FAILURE_EXIT_CODE) ; } }
        return (in_array(false, $statuses)) ? false : true ;
    }

    protected function doTenPings() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $statuses = array() ;
        $c = count($this->targets) ;
        $logging->log("Attempting to Ping {$c} Host/s", $this->getModuleName()) ;
        for ($target_count = 0; $target_count < count($this->targets) ; $target_count++) {
            $logging->log("Attempting to Ping Host {$this->targets[$target_count]} ten times", $this->getModuleName()) ;
            try {
                for ($i = 0; $i < 10; $i ++) {
                    $ping = new \JJG\Ping($this->targets[$target_count]);
                    $latency = $ping->ping();
                    if ($latency !== false) {
                        $logging->log('Ping Latency is ' . $latency . ' ms', $this->getModuleName()) ;
                        $foundSuccess = true; }
                    else {
                        $time = $i * $this->interval ;
                        $logging->log("Ping Host {$this->targets[$target_count]} could not be reached after $i iterations and $time seconds", $this->getModuleName()) ; }
                    sleep($this->interval) ; }
                if (isset ($foundSuccess) && $foundSuccess==true) {
                    $statuses[$target_count] = true ; }
                else {
                    $statuses[$target_count] = false ; } }
            catch (\Exception $e) {
                $logging->log('Failed to execute the Ping Library: '.$e->getMessage(), $this->getModuleName(), LOG_FAILURE_EXIT_CODE) ; } }
        return (in_array(false, $statuses)) ? false : true ;
    }

    protected function doPingsUntil() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $totalTime = 0 ;
        $i = 0;
        while ($totalTime < $this->maxWait) {
            foreach ($this->targets as $target) {
                $ping = new \JJG\Ping($target);
                $latency = $ping->ping();
                if ($latency !== false) {
                    $logging->log('Ping Latency is ' . $latency . ' ms') ;
                    return true ; }
                else {
                    $logging->log("Ping Host {$target} could not be reached after $i iterations and $totalTime seconds") ; }
                sleep($this->interval) ;
                $totalTime = $totalTime + $this->interval ;
                $i++; } }
        return false ;
    }

}