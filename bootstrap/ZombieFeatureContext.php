<?php
/**
 * @file
 * MinkContext for Behat.
 *
 * Requires a Selenium service running. On virtual setups Selenium runs
 *
 * @see http://behat.org/
 * @see http://mink.behat.org/
 * @see https://github.com/Behat/MinkZombieDriver
 */


use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException,
    Behat\Gherkin\Node\ExampleTableNode;

// Zombie requirements.
use Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer,
    Behat\Mink\Mink,
    Behat\Mink\Session;

/**
 * Features context.
 */
class ZombieFeatureContext extends BehatContext {

  static $roles = array();
  static $environments = array();

  // Singleton for mink.
  static private $_mink = NULL;
  static private $_driver = NULL;

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   *
   * @param array $parameters
   *   context parameters (set them up through behat.yml)
   */
  public function __construct(array $parameters) {

    $this->mink = $this->getMink();
  }

  public function getDriver() {
    if (is_null(self::$_driver)) {
      $host = '127.0.0.1';
      $port  = '8121';
      $node_binary = '/usr/bin/node';
      $server = new ZombieServer($host, $port, $node_binary);
      self::$_driver = new ZombieDriver($server);
    }
    return self::$_driver;
  }

  /**
   * Getter for Mink singleton.
   *
   * @return Mink
   *   Returns a singleton Mink instance.
   */
  public function getMink() {
    if (is_null(self::$_mink)) {
      self::$_mink = new Mink();
    }
    return self::$_mink;
  }

  /**
   * Manages Mink sessions, one at a time.
   */
  protected function createSession($name, $reset = FALSE) {

    $default = $this->mink->getDefaultSessionName();

    // Reset session.
    if ($reset && $default && $this->mink->isSessionStarted($default)) {
      $session = $this->mink->getSession();
      if ($session->isStarted()) {
        $session->stop();
      }
      else {
        $session->reset();
      }
    } else {
      // By default start session.
      $session = new Session($this->getDriver());
      if (!$session->isStarted()) {
        $session->start();
      }
    }

    $this->mink->registerSession($name, $session);
    $this->mink->setDefaultSessionName($name);
    return $session;
  }

  /**
   * Tests the Zombie.js browser functionality.
   *
   * @Then /^Tell the browser to go back$/
   */
  public function tellTheBrowserToGoBack() {
    $this->server->evalJS("browser.window.history.back(); browser.wait(function() { stream.end(); })");
  }

  /**
   * Tests the Zombie.js browser functionality.
   *
   * @Then /^Tell the browser to go forward$/
   */
  public function tellTheBrowserToGoForward() {
    $this->server->evalJS("browser.window.history.forward(); browser.wait(function() { stream.end(); })");
  }

  /**
   * Tests finding an HTML element.
   *
   * @Given /^Print the page title$/
   */
  public function printThePageTitle() {
    $page = $this->session->getPage();
    $elem = $page->find('css', 'title');
    $this->printDebug('Title: ' . $elem->getHtml());
  }

  /**
   * Tests a JavaScript assertion.
   *
   * @Given /^Assert that JavaScript variable "([^"]*)" exists$/
   */
  public function assertThatJavascriptVariableExists($variable) {

    // @TODO VARIABLES ARE ASSERTING IN BOGUS DOMAINS SUCH AS GOOGLE.
    // @TODO Research including node.js require.

    // Verifies that we are able to talk to the loaded DOM via Zombie.
    // Should print... either 'authenticated' or 'anonymous';
    // $ret1 = $this->session->evaluateScript("var status = function() { if (jQuery('body').hasClass('logged-in')) { return 'authenticated'; } else { return 'anonymous'; } }; status();");
    // $this->printDebug('User status: ' . print_r($ret1, TRUE));

    // Use PHPUnit for assertions.
    $ret = $this->mink->getSession()->evaluateScript("var status = function(v) { return Boolean(v in window); }; status(\"$variable\");");
    PHPUnit_Framework_Assert::assertTrue($ret, 'Validate variable ' . $variable);
  }

  /**
   * @Given /^the user role "([^"]*)"$/
   */
  public function theUserRole($role) {
    // The SESSION cookie is hashed by Drupal, and $_SESSION is not available,
    // so we use the 'logged-in' body class that appears on standard Drupal
    // themes.
    #$authenticated = $this->session->evaluateScript("if (jQuery('body').hasClass('logged-in')) { return 'authenticated'; } else { return 'anonymous'; } ");
    #if ($authenticated !== $role) {
    #  PHPUnit_Framework_Assert::fail("User role is '$authenticated', expected '$role'");
    #}

    $this->createSession($role);
    return;


    if (!isset(self::$roles[$role])) {

      // Only start forking for a second user.
      if(empty(self::$roles)) {

       # $this->mink->registerSession()

        $js = <<<JS
var {$role} = browser;
JS;
      } else {
        $js = <<<JS
var {$role} = browser.fork();
JS;
      }

      isset($this->getMainContext()->server);

     # $this->mink->getSession()



     # $this->session->evaluateScript($js);

      self::$roles[$role] = $role;

    }
  }

  /**
   * @Transform /^table:environment,drush alias,uri$/
   */
  public function castEnvironmentsTable(ExampleTableNode $table) {
    $enviroments = array();
    foreach ($table->getHash() as $hash) {
      $enviroments[$hash['environment']] = $hash;
    }
    return $enviroments;
  }

  /**
   * @Given /^The following environments:$/
   */
  public function theFollowingEnvironments(array $table) {

    $this->_environments = $table;
  }

  /**
   * @Transform /^table:name,variable$/
   */
  public function castVariablesTable(ExampleTableNode $table) {
    $variables = array();
    foreach ($table->getHash() as $hash) {
      $variables[$hash['name']] = $hash;
    }
    return $variables;
  }

  /**
   * @Then /^these variables must exist:$/
   */
  public function theseVariablesMustExist($variables) {
    $session = $this->mink->getSession();

    foreach ($this->_environments as $env => $hash) {
      // @todo If uri is available from drush, fetch it from there.
      PHPUnit_Framework_Assert::assertNotFalse(filter_var($hash['uri'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED), 'Validate URL ' . $hash['uri']);

      // Creating a node.js instance for every environment we want to verify
      // might be overkill, but at least we can open/close the browser window.
      $session->getDriver()->reset();


      $session->visit($hash['uri']);

      PHPUnit_Framework_Assert::assertNotFalse($session->evaluateScript('browser.success'), 'HTTP Status Response');

      //$this->printDebug(print_r($session->getCurrentUrl(), TRUE));
      //$this->printDebug(print_r($session->getResponseHeaders(), TRUE));
      //$this->printDebug($session->evaluateScript('browser.statusCode'));
      // $this->server->evalJS('browser.location.toString()', 'json');

      foreach ($variables as $name => $varhash) {
        $this->assertThatJavascriptVariableExists($varhash['variable']);
      }
    }
  }

  /**
   * @Given /^I am viewing the background node$/
   */
  public function iAmViewingTheBackgroundNode() {

    $drupal = $this->getMainContext()->getSubcontext('drupal_context');

    #$data = $this->getMainContext()->getContextData(); #current_node

    $session = $this->createSession('default');
    $path = $drupal->locatePath('node/' . $drupal->nodes[0]->nid);
    $session->visit($path);

    $this->printDebug(print_r($session->getCurrentUrl(), TRUE));
    $this->printDebug(print_r($session->getResponseHeaders(), TRUE));
    $this->printDebug($session->evaluateScript('browser.statusCode'));

  }

  /**
   * @When /^I attach the file "([^"]*)"$/
   */
  public function iAttachTheFile($arg1) {
    throw new PendingException();
  }

  /**
   * @When /^I click on "([^"]*)"$/
   */
  public function iClickOn($arg1) {
    throw new PendingException();
  }

  /**
   * Destroy sessions.
   *
   * Since every scenario gets it's own context object, it's easier to halt all
   * sessions in progress than recycling them.
   */
  public function __destruct() {
    $this->mink->stopSessions();
  }

}
