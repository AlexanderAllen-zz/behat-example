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