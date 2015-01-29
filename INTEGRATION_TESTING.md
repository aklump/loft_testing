# Integration Testing Drupal 7

## Quickstart: Step 1
1. Create a file called `tests/{{ my_module_name }}.test`.
1. Copy into this file the contents of `examples/my_module_name.test`.
1. Replace `{{ my_module_name }}`, `{{ MyModuleName }}` and `{{ My Module Name }}`.
1. Add the following line to your module's info file:

        files[] = tests/{{ my_module_name }}.test

1. Enable both [Loft Testing](http://www.intheloftstudios.com/packages/drupal/loft_testing) and [the Simpletest module](https://www.drupal.org/documentation/modules/simpletest).
1. Write some tests.
1. Run your tests from `admin/config/development/testing` and enjoy!
