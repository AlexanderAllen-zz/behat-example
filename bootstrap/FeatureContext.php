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
    Behat\Behat\Event\FeatureEvent,
    Behat\Behat\Event\StepEvent;

// Fork.
use Behat\Mink\Exception\ElementNotFoundException;

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
   * Singleton repository for sharing data between contexts.
   *
   * @var stdClass A free-form generic class.
   */
  private static $_contextData;

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

      $this->ouput_directory = $parameters['output_directory'];
      $this->behat_directory = $parameters['behat_directory'];
    }
  }

  /**
   * Creates a persistent singleton array used for inter-context communication.
   *
   * @return array
   *   Returns a singleton array.
   */
  public function getContextData() {
    if (is_null(self::$_contextData)) {
      self::$_contextData = new stdClass();
      self::$_contextData->nodes = array();
    }
    return self::$_contextData;
  }

  public function setContextData($name = '', $value = '') {
    $data = self::getContextData();
    $data->{$name} = $value;
  }

  /**
   * Implements BeforeSuite hook.
   *
   * @BeforeSuite @ads
   */
  public static function setup(SuiteEvent $event) {
    // Hook stub.
  }

  /**
   * Implements BeforeFeature hook.
   *
   * @BeforeFeature @ads
   */
  public static function setupFeature(FeatureEvent $event) {
    // Hook stub.
  }

  /**
   * Implements BeforeScenario hook.
   *
   * @BeforeScenario @zombie,@ads
   */
  public static function prepare(ScenarioEvent $event) {
    // Hook stub.
  }

  /**
   * Implements AfterStep hook.
   *
   * @BeforeStep
   */
  public function beforeStep(StepEvent $event) {
    // Debugging stub.
    // Alter condition to break into steps that are giving you trouble.
    if ($event->getStep()->getText() == 'I follow "Save"') {
      $debug = NULL;

      $drupal = $this->getSubcontext('drupal_context');
      $html = $drupal->getMink()->getSession()->getPage()->getContent();
      $step = preg_replace('([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})', '', $event->getStep()->getText());
      file_put_contents($this->ouput_directory . '/' . $step . '-' . time() .'.html', $html);
    }
  }

  /**
   * Implements AfterStep hook.
   *
   * @AfterStep
   */
  public function after(StepEvent $event) {
    // Intercept failed steps and take a screenshot of them.
    if ($event->getResult() != StepEvent::PASSED) {

      $drupal = $this->getSubcontext('drupal_context');
      $html = $drupal->getMink()->getSession()->getPage()->getContent();
      $step = preg_replace('([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})', '', $event->getStep()->getText());
      file_put_contents($this->ouput_directory . '/' . $step . '-' . time() .'.html', $html);

      $this->printDebug('Printed debug HTML to: ' . $this->ouput_directory . '/' . $step . '-' . time() .'.html');
    }
  }

  /**
   * Clicks link with specified id|title|alt|text.
   *
   * Original reference: \Behat\MinkExtension\Context\MinkContext::clickLink
   *
   * @When /^I click on XPath "([^"]*)"$/
   */
  public function clickOnXpath($xpath) {

    $session = $this->getSubcontext('drupal_context')->getMink()->getSession();

    #$page = $session->getPage();

   # $this->getSession()->getPage()->clickLink($link);

   # $page->clickLink($link);

    #$this->getSession()->getDriver()->click($this->getXpath());
    #$xpath = '';
    // Returns Behat\Mink\Driver\GoutteDriver

    $selector = new \Behat\Mink\Selector\CssSelector();
    $handler  = new \Behat\Mink\Selector\SelectorsHandler();

    $xpath2 = $handler->selectorToXpath('xpath', $xpath);

    # $nodes = $this->getCrawler()->filterXPath($xpath
    $driver = $session->getDriver();
    # $driver->getCrawler()->filterXPath($xpath); # crawler is private =(

    // \Behat\Mink\Driver\BrowserKitDriver::click
    // \Symfony\Component\DomCrawler\Crawler::filterXPath

    # $this->client->click($node->link());

    # Needs a node, but both getCrawler() and getCrawlerNode() are private.
    #$session->getDriver()->getClient()->click($node->link());

    // Forked \Behat\Mink\Driver\BrowserKitDriver in order to make two private
    // methods public:
    // \Behat\Mink\Driver\BrowserKitDriver::getCrawler()
    // \Behat\Mink\Driver\BrowserKitDriver::getCrawlerNode()
    //
    // Code below is copied from \Behat\Mink\Driver\BrowserKitDriver::click()
    if (!count($nodes = $session->getDriver()->getCrawler()->filterXPath($xpath))) {
      throw new ElementNotFoundException(
        $session, 'link or button', 'xpath', $xpath
      ); #$xpath == "//div[@id='control-panel']/descendant::button[@id='save' and @class='form-submit']"
    }
    $node = $nodes->eq(0);

    // Skip type check in \Behat\Mink\Driver\BrowserKitDriver::click!
    // $type = $session->getDriver()->getCrawlerNode($node)->nodeName;

    // Cannot do this "a" style click() on a "button", will throw an Exception:
    // "Unable to click on a "button" tag."
    #$session->getDriver()->getClient()->click($node->link());

    // Fails when clicking on a "button" without a parent "form"!
    # $session->getDriver()->click($xpath);



    // Using form logic from \Behat\Mink\Driver\BrowserKitDriver::click().
    // Throws LogicException:
    // "The selected node does not have a form ancestor."
    // Looks like we're trapped in a no-exit, dark gang alley at night...
    //
    // vendor/symfony/browser-kit/Symfony/Component/BrowserKit/Client.php
    $client = $session->getDriver()->getClient();
    $form   = $node->form();

    // There is no form.
    #$formId = $this->getFormNodeId($form->getFormNode());
    #if (isset($this->forms[$formId])) {
    #  $this->mergeForms($form, $this->forms[$formId]);
    #}

    // remove empty file fields from request
    foreach ($form->getFiles() as $name => $field) {
      if (empty($field['name']) && empty($field['tmp_name'])) {
        $form->remove($name);
      }
    }

    # browser.cookies().set("session", "123");


    $client->submit($form);


  }

  /**
   * @Then /^a match is found for xpath query "([^"]*)"$/
   */
  public function assertXpathQuery($xpath) {
    $session = $this->getSubcontext('drupal_context')->getMink()->getSession();

    // Code below is copied from \Behat\Mink\Driver\BrowserKitDriver::click()
    if (!count($nodes = $session->getDriver()->getCrawler()->filterXPath($xpath))) {
      throw new ElementNotFoundException(
        $session, 'element', 'xpath', $xpath
      ); #$xpath == "//div[@id='control-panel']/descendant::button[@id='save' and @class='form-submit']"
    }
  }

}
