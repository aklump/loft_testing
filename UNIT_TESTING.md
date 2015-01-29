# Unit Testing Drupal 7: with PhpUnit
Write PhpUnit tests in a Drupal bootstrapped environment!

## Quickstart: Step 1
1. Make sure you're using `sites/default/settings.php` (see Known Issues).
1. In your module, in a folder `tests/phpunit` create a new file for some unit tests.
1. Copy into this file the contents of `examples/PhpUnitTest.php`.
1. Replace `{{ my_module_name }}` and `{{ MyModuleName }}`.
1. _(You may want to break your tests up into multiple files, in which case you can adjust the stub accordingly.)_
1. Add any dependent module names to `parent::setUp` in the `setUp` method.
1. **Correct the path to `loft_testing/includes/bootstrap.inc` in the `require_once` statement, based on the installation location of your module and Loft Testing module.**
1. Write some tests and enjoy!

## Enable PhpUnit testing via the Drupal UI: Step 2

**Warning: You must always enable Loft Testing when you enable Simpletest, if you follow the instructions in step two, otherwise you'll get something like the following error when you visit the drupal testing page:**

    Fatal error: Class 'AKlump\LoftTesting\Simpletest\PhpUnitTestProxy' not found in ... phpunit.test on line 19

1. Create a file called `tests/{{ my_module_name }}.phpunit.test`.
1. Copy the contents of `examples/my_module_name.phpunit.test`.
1. Replace `{{ my_module_name }}`, `{{ MyModuleName }}` and `{{ My Module Name }}`.
1. Add the following line to your module's info file:

        files[] = tests/{{ my_module_name }}.phpunit.test

1. Make sure you have the correct folder name in, where your tests are written.

        $this->proxyAddPath(dirname(__FILE__) . '/phpunit');

1. Be sure to enable both the Loft Testing and Simpletest modules, as declared above!

## Running your tests
1. You may run the tests from the command line using `phpunit` or,
1. You may use the Drupal testing UI at `admin/config/development/testing` if you've enabled the [simpletest module](https://www.drupal.org/documentation/modules/simpletest) and completed step 2 (above).

## Bootstrapping Drupal
To be able to unit test where drupal functions are used, you will have to do a minimal bootstrap of Drupal.  Fortunately this is provided when you include a single file at the top of your phpunit test file. That file is included with this module at `includes/bootstrap.inc`.

## Known Issues
* If you are not using `sites/default/settings.php` then this doesn't work yet.
* If your phpunit executable is not located at `usr/local/bin/phpunit` you will need to set it in the setUp method of each proxy file using `proxySetExec`, e.g., `examples/my_module_name.phpunit.test` for info.  Refer to the following example:
    
        public function setUp() {
          // Define the system path to phpunit.
          $this->proxySetExec('/path/to/phpunit');
          // Add one or more directories where phpunit should be run
          $this->proxyAddPath(dirname(__FILE__) . '/phpunit');
          parent::setUp();
        }        

## An example file
  Refer to the example file `examples/PhpUnitTest.php` for more info.

### PHP Fatal error:  require_once(): Failed opening required 'DRUPAL_ROOT/includes/bootstrap.inc'
In some cases (i.e., using symbolic links) the drupal root cannot be automatically detected and will need to be explicitly defined in your phpunit test; again, see `examples/PhpUnitTest.php` for more info.  You're looking for this line:

    define('DRUPAL_ROOT', '/Library/Projects/website/public_html');

### Integrating with the Drupal testing UI

    use AKlump\LoftTesting\Simpletest\PhpUnitTestProxy as DrupalUnitTestCase;

    class FhAppsPhpUnitTestProxyServerV3 extends DrupalUnitTestCase {

## Avoid DrupalUnitTest and Simpletest for unit testing
In general do not use `DrupalUnitTest`, instead us PhpUnit (see above).  But for integration testing, use the simpletest module.


## PhpUnit Test Proxy
### Known Issues
* Does not work with mamp unless you make a modification to: `/Applications/MAMP/Library/bin/envvars`; you need to comment out the lines in that file, then restart MAMP, e.g., 

        #DYLD_LIBRARY_PATH="/Applications/MAMP/Library/lib:$DYLD_LIBRARY_PATH"
        #export DYLD_LIBRARY_PATH