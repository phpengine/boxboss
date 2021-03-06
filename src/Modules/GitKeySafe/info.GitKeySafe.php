<?php

Namespace Info;

// @todo the idea for the installed script is from http://alvinabad.wordpress.com/2013/03/23/how-to-specify-an-ssh-key-file-with-the-git-command/
// @todo ^email that guy
class GitKeySafeInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Git Key-Safe - Install a script for git to allow specifying ssh keys during commands";

    public function __construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array( "GitKeySafe" =>  array_merge(parent::routesAvailable(), array("config", "configure", "install") ) );
    }

    public function routeAliases() {
        return array("git-key-safe"=>"GitKeySafe", "gitkeysafe"=>"GitKeySafe");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This module installs Git Key-Safe Server to the program git-key-safe

  GitKeySafe, git-key-safe, gitkeysafe

        - install
        Installs Git Key-Safe Server
        example: ptconfigure gitkeysafe install

        script example: git-safe-key -i /path/to/key clone http://git.com/repo.git

HELPDATA;
      return $help ;
    }

}