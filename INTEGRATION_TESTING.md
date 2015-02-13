# Integration Testing Drupal 7

## Quickstart: Step 1

**Integration testing in Drupal is tricky and often relies on database configurations that would not be present unless you provide them to your test, i.e., nodes, fields, taxonomy, etc.  The easiest way to provide these elements is to use the [Features](https://www.drupal.org/project/features) module and export the node types, taxonomy, etc that your tests will rely on for passing.  UNDERSTAND THIS AND SAVE YOURSELF LOTS OF WASTED EFFORT!**


1. Create a feature-based module called `tests/{{ my_module_name }}_test_feature` if appropriate.
1. Create a file called `tests/{{ my_module_name }}.test`.
1. Copy into this file the contents of `examples/my_module_name.test`.
1. Replace `{{ my_module_name }}`, `{{ MyModuleName }}` and `{{ My Module Name }}`.
1. Add the following line to your module's info file:

        files[] = tests/{{ my_module_name }}.test

1. Enable both [Loft Testing](http://www.intheloftstudios.com/packages/drupal/loft_testing) and [the Simpletest module](https://www.drupal.org/documentation/modules/simpletest).
1. Write some tests.
1. Run your tests from `admin/config/development/testing` and enjoy!

## Why Loft Testing?

### Subtests
In `DrupalWebTestCase`, each test method in the class gets a clean installation of the drupal database.  That means that you have to code all tests that share that single instance into one test method, or wait for lots of Drupal instances to be installed.  I didn't like this so I added the concept of subtests.

Subtests share the same drupal instance but allow you to organize your code in much smaller chunks for better readability and maintenance.

### PhpUnit methods
I was tired of keeping strait Simpletest methods and PhpUnit method names and preferring the former, wrote aliases into Loft Testing so I could write using PhpUnit methods.  There, no more learning two testing vocabularies.

