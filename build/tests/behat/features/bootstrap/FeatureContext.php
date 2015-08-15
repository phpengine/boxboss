<?php

use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;

/**
 * Behat context class.
 */

class FeatureContext extends MinkContext
{

    private $output;

    /**
     * Initializes context. Every scenario gets it's own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(Array $parameters) {
        $this->setup();
        $this->useContext('session_control', new \NoActionsContext()) ;
        $this->useContext('default_page_checks', new \AnyModuleActionsContext()) ;
        $this->useContext('login', new \LoginContext()) ;
    }

    private function setup() {
        $bd = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).DIRECTORY_SEPARATOR ;
        try {
            require_once ($bd.'src'.DIRECTORY_SEPARATOR. 'AutoLoad.php') ;
            $autoLoader = new \Core\autoLoader();
            $autoLoader->launch(); }
        catch (\Exception $e) {
            echo "Setup cant load autoloader\n" ;
            echo 'Message: ' .$e->getMessage(); }
        try {
            require_once ($bd.'src'.DIRECTORY_SEPARATOR. 'Constants.php') ; }
        catch (\Exception $e) {
            echo "Setup cant load constants\n" ;
            echo 'Message: ' .$e->getMessage(); }
    }

}