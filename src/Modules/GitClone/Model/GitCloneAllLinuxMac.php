<?php

Namespace Model;

class GitCloneAllLinuxMac extends Base {

    // Compatibility
    public $os = array("Linux", "Darwin") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    private $projectDirectory;
    private $webServerUser;

    public function checkoutProject(){
        if ($this->askWhetherToDownload() != true) { return false; }
        $this->askForGitCloneTargetRepo();
        $this->doGitCloneCommand();
        if ($this->askAlsoChangePerms() == false ) { return true; }
        $this->setWebServerUser();
        $this->changeNewProjectPermissions();
        $this->changeNewProjectFolderOwner();
        $this->changeToProjectDirectory();
        return true;
    }

    protected function askWhetherToDownload() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Perform a clone/download of files?';
        return self::askYesOrNo($question);
    }

    protected function askForGitCloneTargetRepo() {
        if (isset($this->params["repository-url"])) { return $this->params["repository-url"] ; }
        $question = 'What\'s git repo to clone from?';
        $this->params["repository-url"] = self::askForInput($question, true);
    }

    protected function askAlsoChangePerms() {
        if (isset($this->params["change-owner-permissions"]) && $this->params["change-owner-permissions"]!==true) { return false ; }
        $question = 'Also change permissions/owner?';
        return self::askYesOrNo($question);
    }
// @todo scrap this
//    protected function doGitCloneCommandWithErrorCheck($params){
//        $data = $this->doGitCloneCommand($params);
//        print $data;
//        if ( substr($data, 0, 5) == "error" ) { return false; }
//        return true;
//    }

    // @todo there needs to be a dependency check for git-safe-key module to be installed by ptconfigure
    protected function getGitCommand() {
        if (isset($this->params["private-key"]) && strlen($this->params["private-key"])>0) {
            return 'git-key-safe -i '.$this->params["private-key"] ; }
        else {
            return 'git' ; }
    }

    protected function doGitCloneCommand() {
        $projectOriginRepo = $this->params["repository-url"] ;
        $customCloneFolder = (isset($this->params["custom-clone-dir"])) ? $this->params["custom-clone-dir"] : null ;
        $customBranch = (isset($this->params["custom-branch"])) ? $this->params["custom-branch"] : null ;
        // @todo the git --single-branch option gave errors on centos 6.2, so instead of cloning a single branch I
        // changed it to clone whole repo then switch to specified. works on ubuntu and centos
        // $branchParam = ($customBranch!=null) ? $customBranch.' --single-branch ' : "" ;
        $branchParam = ($customBranch != null) ? '--branch '.escapeshellarg($customBranch).' ' : "" ;
        $command  = $this->getGitCommand().' clone '.$branchParam.escapeshellarg($projectOriginRepo);
        if (isset($customCloneFolder)) { $command .= ' '.escapeshellarg($customCloneFolder); }
        $nameInRepo = substr($projectOriginRepo, strrpos($projectOriginRepo, '/', -1) );
        $this->projectDirectory = (isset($customCloneFolder)) ? $customCloneFolder : $nameInRepo ;
//        var_dump($command);
//        echo $command;
        $rc = self::executeAndGetReturnCode($command, true, true);
        return ($rc["rc"] == 0) ? true : false ;
    }

    protected function dropDirectory(){
        $command  = 'sudo rm -rf '.$this->projectDirectory;
        return self::executeAndOutput($command);
    }

    protected function changeToProjectDirectory(){
        if (file_exists(getcwd().DIRECTORY_SEPARATOR.$this->projectDirectory)) {
            chdir(getcwd().DIRECTORY_SEPARATOR.$this->projectDirectory); }
         else {
             echo "Could not navigate to: ".getcwd().'/'.$this->projectDirectory."\n"; }
        echo "Now in: ".getcwd()."\n\n";
    }

    // @todo make this a param or remove to another module as its not technically a git command
    protected function setWebServerUser(){
        $this->webServerUser = $this->askWebServerUser();
    }

    // @todo make this a param or remove to another module as its not technically a git command
    protected function changeNewProjectPermissions(){
        $command  = 'sudo chmod -R 755 '.getcwd().DIRECTORY_SEPARATOR.$this->projectDirectory;
        self::executeAndOutput($command, "Changing Folder Permissions...");
    }

    // @todo make this a param or remove to another module as its not technically a git command
    protected function changeNewProjectFolderOwner(){
        $command  = 'sudo chown -R '.$this->webServerUser.' '.$this->projectDirectory;
        self::executeAndOutput($command, "Changing Folder Owner...");
    }

    protected function askWebServerUser(){
        $question = 'What user is Apache Web Server running as?';
        if ($this->detectDebianApacheVHostFolderExistence()) {
            if (isset($this->params["guess"])) { return "www-data" ; }
            $question .= ' Guessed ubuntu:www-data - use this?';
            $input = self::askForInput($question);
            return ($input=="") ? "www-data" : $input ;  }
        if ($this->detectRHVHostFolderExistence()) {
            if (isset($this->params["guess"])) { return "apache" ; }
            $question .= ' Guessed Centos/RH:apache - use this?';
            $input = self::askForInput($question);
            return ($input=="") ? "apache" : $input ;  }
        return self::askForInput($question, true);
    }

    protected function detectDebianApacheVHostFolderExistence(){
        return file_exists("/etc/apache2/sites-available");
    }

    protected function detectRHVHostFolderExistence(){
        return file_exists("/etc/httpd/vhosts.d");
    }

}