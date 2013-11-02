<?php

Namespace Model;

class NginxSBEditor extends Base {

    private $ServerBlockTemplate;
    private $docRoot;
    private $url;
    private $fileSuffix;
    private $ServerBlockIp;
    private $ServerBlockForDeletion;
    private $ServerBlockEnabledDir;
    private $NginxCommand;
    private $ServerBlockDir = '/etc/nginx/sites-available' ; // no trailing slash
    private $ServerBlockTemplateDir;
    private $ServerBlockDefaultTemplates;

    public function __construct() {
      $this->setServerBlockDefaultTemplates();
    }

    public function askWhetherToListServerBlock() {
        return $this->performServerBlockListing();
    }

    public function askWhetherToCreateServerBlock() {
        return $this->performServerBlockCreation();
    }

    public function askWhetherToDeleteServerBlock() {
        return $this->performServerBlockDeletion();
    }

    private function performServerBlockListing() {
        $this->ServerBlockDir = $this->askForServerBlockDirectory();
        $this->ServerBlockEnabledDir = $this->askForServerBlockEnabledDirectory();
        $this->listAllServerBlocks();
        return true;
    }

    private function performServerBlockCreation() {
        if ( !$this->askForServerBlockEntry() ) { return false; }
        $this->docRoot = $this->askForDocRoot();
        $this->url = $this->askForHostURL();
        $this->ServerBlockIp = $this->askForServerBlockIp();
        $this->ServerBlockTemplateDir = $this->askForServerBlockTemplateDirectory();
        $this->selectServerBlockTemplate();
        $this->processServerBlock();
        if ( !$this->checkServerBlockOkay() ) { return false; }
        $this->ServerBlockDir = $this->askForServerBlockDirectory();
        $this->attemptServerBlockWrite();
        if ( $this->askForEnableServerBlock() ) {
            $this->ServerBlockEnabledDir = $this->askForServerBlockEnabledDirectory();
            $this->enableServerBlock(); }
        $this->NginxCommand = $this->askForNginxCommand();
        $this->restartNginx();
        return true;
    }

    private function performServerBlockDeletion(){
        if ( !$this->askForServerBlockDeletion() ) { return false; }
        echo "Deleting ServerBlock\n";
        $this->ServerBlockDir = $this->askForServerBlockDirectory();
        $this->ServerBlockForDeletion = $this->selectServerBlockInProjectOrFS();
        if ( self::areYouSure("Definitely delete ServerBlock?") == false ) {
          return false; }
        if ( $this->askForDisableServerBlock() ) {
            $this->ServerBlockEnabledDir = $this->askForServerBlockEnabledDirectory();
            $this->disableServerBlock(); }
        $this->attemptServerBlockDeletion();
        $this->NginxCommand = $this->askForNginxCommand();
        $this->restartNginx();
        return true;
    }

    public function runAutoPilot($autoPilot) {
        $this->runAutoPilotServerBlockDeletion($autoPilot);
        $this->runAutoPilotServerBlockCreation($autoPilot);
        return true;
    }

    public function runAutoPilotServerBlockCreation($autoPilot){
        if ( !isset($autoPilot["serverBlockEditorAdditionExecute"]) ||
          $autoPilot["serverBlockEditorAdditionExecute"] == false ) { return false; }
        echo "Creating ServerBlock\n";
        $this->docRoot = $autoPilot["serverBlockEditorAdditionDocRoot"];
        $this->url = $autoPilot["serverBlockEditorAdditionURL"];
        $this->ServerBlockIp = $autoPilot["serverBlockEditorAdditionIp"];
        if ($autoPilot["serverBlockEditorAdditionTemplateData"] != null) {
            echo "Using autopilot ServerBlock\n";
            $this->ServerBlockTemplate = $autoPilot["serverBlockEditorAdditionTemplateData"]; }
        else { echo "Using default ServerBlock\n";
            $this->setServerBlockDefaultTemplate(); }
        $this->processServerBlock();
        $this->ServerBlockDir = $autoPilot["serverBlockEditorAdditionDirectory"];
        $this->attemptServerBlockWrite($autoPilot["serverBlockEditorAdditionFileSuffix"]);
        if ( $autoPilot["serverBlockEditorAdditionServerBlockEnable"]==true ) {
            $this->enableServerBlock($autoPilot["serverBlockEditorAdditionSymLinkDirectory"]); }
        $this->restartNginx();
        return true;
    }

    public function runAutoPilotServerBlockDeletion($autoPilot) {
        if ( !isset($autoPilot["serverBlockEditorDeletionExecute"]) ||
          $autoPilot["serverBlockEditorDeletionExecute"] == false) { return false; }
        $this->ServerBlockDir = $autoPilot["serverBlockEditorDeletionDirectory"] ;
        $this->ServerBlockForDeletion = (is_array( $autoPilot["serverBlockEditorDeletionTarget"]) ) ?
          $autoPilot["serverBlockEditorDeletionTarget"] : array( $autoPilot["serverBlockEditorDeletionTarget"] );
        if ( $autoPilot["serverBlockEditorDeletionServerBlockDisable"]==true ) {
            $this->disableServerBlock($autoPilot["serverBlockEditorDeletionSymLinkDirectory"]); }
        $this->attemptServerBlockDeletion();
        $this->restartNginx();
        return true;
    }

    private function askForServerBlockEntry() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Do you want to add a ServerBlock?';
        return self::askYesOrNo($question);
    }

    private function askForServerBlockDeletion() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Do you want to delete ServerBlock/s?';
        return self::askYesOrNo($question);
    }

    private function askForEnableServerBlock() {
        if (isset($this->params["guess"]) && $this->params["guess"]==true) {  return true ; }
        $question = 'Do you want to enable this ServerBlock?';
        return self::askYesOrNo($question);
    }

    private function askForDisableServerBlock() {
        if (isset($this->params["guess"]) && $this->params["guess"]==true) { return true ; }
        $question = 'Do you want to disable this ServerBlock?';
        return self::askYesOrNo($question);
    }

    private function askForDocRoot() {
      $question = 'What\'s the document root? Enter nothing for '.getcwd();
      $input = self::askForInput($question);
      return ($input=="") ? getcwd() : $input ;
    }

    private function askForHostURL() {
        $question = 'What URL do you want to add as server name?';
        return self::askForInput($question, true);
    }

    private function askForServerBlockIp() {
        $question = 'What IP:Port should be set? Enter nothing for 127.0.0.1:80';
        $input = self::askForInput($question) ;
        return ($input=="") ? '127.0.0.1:80' : $input ;
    }

    private function checkServerBlockOkay(){
        $question = 'Please check ServerBlock: '.$this->ServerBlockTemplate."\n\nIs this Okay?";
        return self::askYesOrNo($question);
    }

    private function askForServerBlockDirectory(){
        $question = 'What is your ServerBlock directory?';
        if ($this->detectNginxServerBlockFolderExistence()) { $question .= ' Found "/etc/nginx/sites-available" - Enter nothing to use this';
            $input = self::askForInput($question);
            return ($input=="") ? $this->ServerBlockDir : $input ;  }
        return self::askForInput($question, true);
    }

    private function askForServerBlockEnabledDirectory(){
        $question = 'What is your Enabled/Available/Symlink ServerBlock directory?';
        if ($this->detectServerBlockEnabledFolderExistence()) { $question .= ' Found "/etc/nginx/sites-enabled" - Enter nothing to use this';
            $input = self::askForInput($question);
            return ($input=="") ? $this->ServerBlockDir : $input ;  }
        return self::askForInput($question, true);
    }

    private function askForServerBlockTemplateDirectory(){
        $question = 'What is your ServerBlock Template directory? Enter nothing for default templates';
        if ($this->detectServerBlockTemplateFolderExistence()) {
            $question .= ' Found "'.$this->docRoot.'/build/config/dapperstrano/server-blocks" - Enter nothing to use this';
            $input = self::askForInput($question);
            return ($input=="") ? $this->ServerBlockTemplateDir : $input ;  }
        else {
          $input = self::askForInput($question);
          return ($input=="") ? $this->ServerBlockTemplateDir : $input ;
        }
    }

    private function detectNginxServerBlockFolderExistence(){
        return file_exists("/etc/nginx/sites-available");
    }

    private function detectServerBlockEnabledFolderExistence(){
        return file_exists("/etc/nginx/sites-enabled");
    }

    private function detectServerBlockTemplateFolderExistence(){
        return file_exists( $this->ServerBlockTemplateDir = $this->docRoot."/build/config/dapperstrano/server-blocks");
    }

    private function attemptServerBlockWrite($serverBlockEditorAdditionFileSuffix=null){
        $this->createServerBlock();
        $this->moveServerBlockAsRoot($serverBlockEditorAdditionFileSuffix);
        $this->writeServerBlockToProjectFile($serverBlockEditorAdditionFileSuffix);
    }

    private function attemptServerBlockDeletion(){
        $this->deleteServerBlockAsRoot();
        $this->deleteServerBlockFromProjectFile();
    }

    private function processServerBlock() {
        $replacements =  array('****WEB ROOT****'=>$this->docRoot,
            '****SERVER NAME****'=>$this->url, '****IP ADDRESS****'=>$this->ServerBlockIp);
        $this->ServerBlockTemplate = strtr($this->ServerBlockTemplate, $replacements);
    }

    private function createServerBlock() {
        $tmpDir = $this->baseTempDir.'/ServerBlocktemp/';
        if (!file_exists($tmpDir)) {mkdir ($tmpDir, 0777, true);}
        return file_put_contents($tmpDir.'/'.$this->url, $this->ServerBlockTemplate);
    }

    private function moveServerBlockAsRoot($serverBlockEditorAdditionFileSuffix=null){
        $command = 'sudo mv '.$this->baseTempDir.'/ServerBlocktemp/'.$this->url.' '.$this->ServerBlockDir.'/'.$this->url.$serverBlockEditorAdditionFileSuffix;
        return self::executeAndOutput($command);
    }

    private function deleteServerBlockAsRoot(){
        foreach ($this->ServerBlockForDeletion as $ServerBlock) {
            $command = 'sudo rm -f '.$this->ServerBlockDir.'/'.$ServerBlock;
            self::executeAndOutput($command, "ServerBlock $ServerBlock Deleted  if existed"); }
        return true;
    }

    private function writeServerBlockToProjectFile($serverBlockEditorAdditionFileSuffix=null){
        if ($this->checkIsDHProject()){
            \Model\AppConfig::setProjectVariable("server-blocks", $this->url.$serverBlockEditorAdditionFileSuffix); }
    }

    private function deleteServerBlockFromProjectFile(){
        if ($this->checkIsDHProject()){
            $allProjectServerBlocks = \Model\AppConfig::getProjectVariable("server-blocks");
            for ($i = 0; $i<=count($allProjectServerBlocks) ; $i++ ) {
                if (isset($allProjectServerBlocks[$i]) && in_array($allProjectServerBlocks[$i], $this->ServerBlockForDeletion)) {
                    unset($allProjectServerBlocks[$i]); } }
            \Model\AppConfig::setProjectVariable("server-blocks", $allProjectServerBlocks); }
    }

    private function enableServerBlock($NginxSBEditorAdditionSymLinkDirectory=null){
        $command = 'nginx_ensite '.$this->url;
        return self::executeAndOutput($command, "a2ensite $this->url done");
    }

    private function disableServerBlock(){
        foreach ($this->ServerBlockForDeletion as $ServerBlock) {
            $command = 'nginx_dissite '.$ServerBlock;
            self::executeAndOutput($command, "nginx_dissite $ServerBlock done"); }
        return true;
    }

    private function restartNginx(){
        echo "Restarting Nginx...\n";
        $command = "sudo service nginx restart";
        return self::executeAndOutput($command);
    }

    private function checkIsDHProject() {
        return file_exists('dhproj');
    }

    private function listAllServerBlocks() {
        $projResults = ($this->checkIsDHProject()) ? \Model\AppConfig::getProjectVariable("server-blocks") : array() ;
        $enabledResults = scandir($this->ServerBlockEnabledDir);
        $otherResults = scandir($this->ServerBlockDir);
        $question = "Current Installed ServerBlocks:\n";
        $i1 = $i2 = $i3 = 0;
        $availableServerBlocks = array();
        if (count($projResults)>0) {
            $question .= "--- Project Server Blocks: ---\n";
            foreach ($projResults as $result) {
                $question .= "($i1) $result\n";
                $i1++;
                $availableServerBlocks[] = $result;} }
        if (count($enabledResults)>0) {
            $question .= "--- Enabled Server Blocks: ---\n";
            foreach ($otherResults as $result) {
                if ($result === '.' or $result === '..') continue;
                $question .= "($i1) $result\n";
                $i1++;
                $availableServerBlocks[] = $result;} }
        if (count($otherResults)>0) {
            $question .= "--- All Available Server Blocks: ---\n";
            foreach ($otherResults as $result) {
                if ($result === '.' or $result === '..') continue;
                $question .= "($i1) $result\n";
                $i1++;
                $availableServerBlocks[] = $result;} }
        echo $question;
    }

    private function selectServerBlockInProjectOrFS(){
        $projResults = ($this->checkIsDHProject())
          ? \Model\AppConfig::getProjectVariable("server-blocks")
          : array() ;
        $otherResults = scandir($this->ServerBlockDir);
        $question = "Please Choose ServerBlock for Deletion:\n";
        $i1 = $i2 = 0;
        $availableServerBlocks = array();
        if (count($projResults)>0) {
            $question .= "--- Project Server Blocks: ---\n";
            foreach ($projResults as $result) {
                $question .= "($i1) $result\n";
                $i1++;
                $availableServerBlocks[] = $result;} }
        if (count($otherResults)>0) {
            $question .= "--- All Server Blocks: ---\n";
            foreach ($otherResults as $result) {
                if ($result === '.' or $result === '..') continue;
                $question .= "($i1) $result\n";
                $i1++;
                $availableServerBlocks[] = $result;} }
        $validChoice = false;
        while ($validChoice == false) {
            if ($i2>0) { $question = "That's not a valid option, ".$question; }
            $input = self::askForInput($question) ;
            if ( array_key_exists($input, $availableServerBlocks) ){
                $validChoice = true;}
            $i2++; }
        return array($availableServerBlocks[$input]) ;
    }

    private function selectServerBlockTemplate(){
        $ServerBlockTemplateResults = (is_array($this->ServerBlockTemplateDir) &&
        count($this->ServerBlockTemplateDir)>0)
          ? scandir($this->ServerBlockTemplateDir)
          : array() ;
        $question = "Please Choose ServerBlock Template: \n";
        $i1 = $i2 = 0;
        $availableServerBlockTemplates = array();
        $question .= "--- Default Server Block Templates: ---\n";
        foreach ($this->ServerBlockDefaultTemplates as $title => $data) {
          $question .= "($i1) $title\n";
          $i1++;
          $availableServerBlockTemplates[] = $title; }
        if (count($ServerBlockTemplateResults)>0) {
            $question .= "--- Server Block Templates in Project: ---\n";
            foreach ($ServerBlockTemplateResults as $result) {
                if ($result === '.' or $result === '..') continue;
                $question .= "($i1) $result\n";
                $i1++;
                $availableServerBlockTemplates[] = $result;} }
        $validChoice = false;
        while ($validChoice == false) {
            if ($i2==1) { $question = "That's not a valid option, ".$question; }
            $input = self::askForInput($question) ;
            if (array_key_exists($input, $availableServerBlockTemplates) ){
                $validChoice = true;}
            $i2++; }
        if (array_key_exists($availableServerBlockTemplates[$input], $this->ServerBlockDefaultTemplates) ) {
          $this->ServerBlockTemplate
            = $this->ServerBlockDefaultTemplates[$availableServerBlockTemplates[$input]];
          return ; }
      $this->ServerBlockTemplate = file_get_contents($this->ServerBlockTemplateDir . '/' .
        $availableServerBlockTemplates[$input]);
    }

    private function setServerBlockDefaultTemplates() {

      $template1 = <<<'TEMPLATE1'
server {
        listen   ****IP ADDRESS**** ; ## listen for ipv4; this line is default and implied
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6

        root ****WEB ROOT**** ;
        index index.html index.htm index.php;

        # Make site accessible from http://localhost/
        server_name ****SERVER NAME**** ;
}

TEMPLATE1;

      $template2 = <<<'TEMPLATE2'
server {
        listen   ****IP ADDRESS**** ; ## listen for ipv4; this line is default and implied
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6

        root ****WEB ROOT****/src ;
        index index.html index.htm index.php;

        # Make site accessible from http://localhost/
        server_name ****SERVER NAME**** ;
}
TEMPLATE2;

      $template3 = <<<'TEMPLATE3'

server {
        listen   ****IP ADDRESS**** ; ## listen for ipv4; this line is default and implied
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6

        root ****WEB ROOT****/web ;
        index index.html index.htm index.php;

        # Make site accessible from http://localhost/
        server_name ****SERVER NAME**** ;
}
TEMPLATE3;

        $template4 = <<<'TEMPLATE4'
server {
        listen   ****IP ADDRESS**** ; ## listen for ipv4; this line is default and implied
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6

        root ****WEB ROOT****/www ;
        index index.html index.htm index.php;

        # Make site accessible from http://localhost/
        server_name ****SERVER NAME**** ;
}
TEMPLATE4;

        $template5 = <<<'TEMPLATE5'
server {
        listen   ****IP ADDRESS**** ; ## listen for ipv4; this line is default and implied
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6

        root ****WEB ROOT****/docroot ;
        index index.html index.htm index.php;

        # Make site accessible from http://localhost/
        server_name ****SERVER NAME**** ;
}
TEMPLATE5;

    $this->ServerBlockDefaultTemplates = array(
      "docroot-no-suffix" => $template1,
      "docroot-src-sfx" => $template2,
      "docroot-web-suffix" => $template3,
      "docroot-www-suffix" => $template4,
      "docroot-docroot-suffix" => $template5
    );

    }

}