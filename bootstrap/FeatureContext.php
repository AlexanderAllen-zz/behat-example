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
use Behat\Behat\Context;

// Hooks
use Behat\Behat\Event\SuiteEvent,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Event\FeatureEvent;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext {

  /**
   * Initializes context.
   *
   * Every scenario gets it's own context object.
   *
   * @param array $parameters
   *   context parameters (set them up through behat.yml)
   */
  public function __construct(array $parameters) {

    // Make sure the user's CLI environment has a timezone to avoid warnings.
    // http://www.php.net/manual/en/timezones.php
    date_default_timezone_set('America/New_York');

    if (!empty($parameters)) {
      if (array_key_exists('zombie_context', $parameters)) {
        $this->useContext('zombie_context', new $parameters['zombie_context']($parameters));
      }

      if (array_key_exists('drupal_context', $parameters)) {
        $this->useContext('drupal_context', new $parameters['drupal_context']($parameters));
      }
    }
  }

  /**
   * Implements BeforeSuite hook.
   *
   * @BeforeSuite @ads
   */
  public static function setup(SuiteEvent $event) {
    $test = null;
  }

  /**
   * Implements BeforeFeature hook.
   *
   * @BeforeFeature @ads
   */
  public static function setupFeature(FeatureEvent $event) {
    $test = null;
    // $event->getFeature(), getContextParameters()
  }

  /**
   * Implements BeforeScenario hook.
   *
   * @BeforeScenario @zombie,@ads
   */
  #public static function prepare(ScenarioEvent $event) {
  #  $test = null;
    // $context = $event->getContext();
    // $context->setParentContext($context->getSubcontext('zombie_context'));
  #  $test = null;
  #}

}
