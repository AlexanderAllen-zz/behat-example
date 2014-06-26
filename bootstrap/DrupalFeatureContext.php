<?php
/**
 * @file
 * MinkContext for Behat.
 *
 * @see http://behat.org/
 * @see http://mink.behat.org/
 */

use Behat\Behat\Context\ClosuredContextInterface,
  Behat\Behat\Context\TranslatedContextInterface,
  Behat\Behat\Context\BehatContext,
  Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
  Behat\Gherkin\Node\TableNode;

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

/**
 * Features context.
 */
class DrupalFeatureContext extends Drupal\DrupalExtension\Context\DrupalContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   *
   * @param array $parameters
   *   context parameters (set them up through behat.yml)
   */
  public function __construct(array $parameters) {
    #$this->persist();
  }

  public function persist() {
    $main = $this->getMainContext();

    $drupal = $this->getMainContext()->getSubcontext('drupal_context');
    $data = $this->getMainContext()->getContextData();
    $data->user = $drupal->user;
    $data->users = $drupal->users;
    $data->_drupal = $drupal;
  }

  /**
   * Verify that jQuery is available so we can more easily perform our tests.
   *
   * @Given /^JQuery is available$/
   */
  public function jqueryIsAvailable() {
    $jquery = $this->session->evaluateScript("if(typeof jQuery !== undefined) { return true; } else { return false; }");
    if (!$jquery) {
      throw new PendingException('jQuery is not available!');
    }
  }

  /**
   * Validate JavaScript environment.
   *
   * @Then /^These JavaScript objects are present:$/
   */
  public function theseJavascriptObjectsArePresent(TableNode $table) {
    $session = $this->session;

    $hash = $table->getHash();
    foreach ($hash as $row) {
      $name = array_shift($row);
      $variable = array_shift($row);

      if ($session->evaluateScript("var status = function(v) { return Boolean(v in window); }; status(\"$variable\");")) {
        PHPUnit_Framework_Assert::fail("Variable $name not found.");
      }
    }
  }

  /**
   * @Given /^I am viewing my "(?P<type>[^"]*)" node with the timestamped title "(?P<title>[^"]*)"$/
   */
  public function createMyTimeStampedNode($type, $title) {

    // Attach unique timestamp to nodes, then register title so front-end
    // drivers have access to it.
    $title = $title . ' ' . time();
    $data = $this->getMainContext()->getContextData();
    $data->nodes[] = $title;
    #$data->session = substr(session_name(), 1);

    #$this->getSession()->getCookie(substr(session_name(), 1));

    #$this->getMainContext()->setContextData('current_node', $title);

    // Contexts are re-instantiated between scenarios, save the currently logged
    // in users' credentials to persistent cache before the drupal context
    // gets wiped.
    // @todo move this to a hook instead of a step.
    /*$drupal = $this->getMainContext()->getSubcontext('drupal_context');
    $data = $this->getMainContext()->getContextData();
    $data->user = $drupal->user;
    $data->users = $drupal->users;
    $data->_drupal = $drupal;
    */

    #$fed_mink = $this->getMainContext()->getSubcontext('zombie_context')->getMink();
    #$fed_session = $fed_mink->getSession();



    # Given /^I am viewing my "(?P<type>[^"]*)" node with the title "(?P<title>[^"]*)"$/
    # DrupalFeatureContext::createMyNode()
    return new Given("I am viewing my \"$type\" node with the title \"$title\"");
  }

  /**
   * Tell the web driver to close session on instance destruct.
   */
  public function __destruct() {
    #$this->session->stop();
  }

}

# $_COOKIE
