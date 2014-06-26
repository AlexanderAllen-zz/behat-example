README.txt

Assumptions:

- This project repository IS NOT a Drupal module.
- For convenience, this repository provides a centralized location where to
  store Behat tests for DCMS.
- Behat is a generic PHP library, and is not directly associated with Drupal.
- This repository uses Composer to download all dependencies required for testing.
- In theory tests (this repository) can be placed anywhere in a local filesystem
where access to drush is available.

Functionality:
- Behat (a standalone codebase) uses a number of "drivers" to interact with
  other systems.
- This project uses the "Drupal Extension" D.O. contributed project to
  interact with Drupal.
- A working drush alias is required by Drupal Extension, this alias is
  hardcoded in the YAML file used by Behat (behat.yml).
- All Behat driver configuration is stored in the behat.yml file
- Tests can be run all at once or individually.
- Behat uses "contexts" - PHP classes - to define and configure the rules to
  be executed. This project comes with it's own contexts included (required).
- You can extend the provided contexts at any time if need be.

Tips:
- Behat scans recursively the specified "features" directory.
- Different versions of drupal/drupal-extension require specific versions of
  behat.
- Composer packages sometimes throw themselves into a circular dependency loop -
  an infinite loop caused by the version requirements of each package. To avoid
  this infinite execution loop we use the version alias functionality mentioned
  in: https://getcomposer.org/doc/articles/troubleshooting.md#need-to-override-a-package-version

Installation:
- Assuming the project has been checked out to /home/<yourusername>/behat
- SSH into your virtualized local lamp stack
- Assuming your lamp stack has composer installed
- Change directory into <shared mount>/.../[home/][<yourusername>/]behat
- Run composer update. This should all install all the dependencies listed in
  composer.json.

Starting a test suite:
- behat --ansi --profile dcms-local

Usage:
- Feature files are located in the "features" directory.
- The "features" directory is scanned recursively by behat - you can organize
  your feature in as many folders at it makes sense - they will be found.
- While behat features (features/*.feature files) are written in Gherkin (see
  reference section below), the steps that are actually executed, i.e., the
  actual interaction that happens with Drupal or any other system (doesn't have
  to be Drupal) are written in PHP code.
- Custom steps are defined in custom code, *.php files located in the "boostrap"
  directory. You can write your own steps at any time.
- Some steps are provided by other projects such as the "Drupal Extension".
- Drupal Extension is a Drupal.org project, but is not a module - it simply
  provides a context that you can extend.
- You extend the Drupal Extension-provided context in order to inherit common
  Drupal interaction steps such as: create content, log in, click on things in
  the page (i.e., simulate a Drupal user with different roles).
- This project uses both the Drupal Extension, and custom steps where neccesary.
- What Context is used is normally defined by "profiles" found on the yaml file.
- You can specify which "profile" to use for your tests, and therefore which
  steps are available to your test suites.

Reference/Bibliography:

- DRUPALCON AUSTIN 2014 PRESENTATION

  # Please watch this first if you have never worked with Behat in order to get
  # familiarized!

  https://austin2014.drupal.org/session/user-personas-testing-project-managers-journey-towards-behat

- Gherkin syntax:
  https://github.com/cucumber/cucumber/wiki/Gherkin
  https://github.com/cucumber/cucumber/wiki/Feature-Introduction
  http://pivotallabs.com/well-formed-stories/
  http://docs.behat.org/guides/1.gherkin.html
  http://www.strongandagile.co.uk/index.php/what-makes-a-good-user-story/
- Composer:
  https://getcomposer.org/doc/00-intro.md
  https://getcomposer.org/doc/
- Contexts:
  http://docs.behat.org/guides/7.config.html#Context
  http://docs.behat.org/guides/4.context.html
- behat.yml
  http://docs.behat.org/guides/7.config.html