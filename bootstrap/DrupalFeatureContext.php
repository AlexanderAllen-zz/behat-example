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
   * Tell the web driver to close session on instance destruct.
   */
  public function __destruct() {
    #$this->session->stop();
  }

}
