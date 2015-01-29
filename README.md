# Drupal Module: Loft Testing
**Author:** Aaron Klump  <sourcecode@intheloftstudios.com>

## For Development Only
It is recommended to only use this module on development instances of a website, and disable it on production environments.

## Implementation

When testing requires database objects, you should create a separate module called `module_name_test` which is a feature.


### For integration testing
Add the following to the beginning of your `module.test` file

    <?php
    use AKlump\LoftTesting\Simpletest\DrupalWebTestCase;

    class GopFacetsWebTestCase extends DrupalWebTestCase {


## Organizing into subtests
Instead of creating long or multiple test methods you can create subtest methods, which are grouped to in a common instance so they run faster, yet your code is cleaner.  The way it works is that you declare a `testGroup` and then any number of subtest methods that begin with `subtestGroup`. Subtests will be executed in the order they are declared.

    <?php
    public function subtestMyCoolGroupTestName() {
      // ... test goes here
    }

    public function testMyCoolGroup() {
      $this->doSubtests();
    }

## Skipping a `testMethod` -> `_testMethod`
If you have a test method `_test...` that is working well and you want to skip it while developing more tests, and to save execution time, you can disable it simply by preceding the method name with an underscore.  Normally only methods beginning with "test" are called, so naturally this will be skipped over.

The reason for stating the obvious, is because the not-so-obvious functionality this extension class provides.  **All methods that begin with "_test" will automatically be added to the skipped subtests array, and you will see a notice in the results that they were skipped.**  This is so you don't forget to remove that underscore later on.

## Skipping a subtest group
Likewise you may target a subtest group to be skipped explicitly using `skipSubtests()`. Here's what you do:

    public function testMyCoolGroup() {
      $this->skipSubtests();
      $this->doSubtests();
    }

## Nesting subtest groups
Imagine for a minute something like this.  Remember the only requirement for a subtest name is that is begins with 'sub'.  It's only convention that we've been using `__FUNCTION__` as the group name, and with some creativity you could do some cool stuff and cut down the time it takes for your testing.

    public function testMyCoolGroup() {
      $this->doSubtests();
      $this->doSubtests('group2');
      $this->doSubtests('group3');
    }


**Be aware that skipping only occurs if you're calling `doSubtests()`** inside the method body of your test method as per the examples in this document.

## Naming Conventions
### Test one function per subtest...
When testing a single function use the following as a naming convention.  For a function called `_gop_facets_round_robin_default` as a subtest of `testGroup`...

    public function subtestGroup__gop_facets_round_robin_default() {
    
... notice the double underscore, preserving the fact that the function name in drupal begins with an underscore, indicating it's private.

For a function called `gop_facets_list` as a subtest of `testGroup`...

    public function subtestGroup_gop_facets_list() {
    
### Testing other than a function
In this case follow camel case naming conventions for your subtest name.

    public function subtestMultipleTermsShouldBeAdded() {

    
## Complications
### Taxonomy
You can export taxonomy vocabularies in features, but **terms do not get exported**.

### How to: Terms
1. Export the terms using `export_terms.php` from the command line.
1. Argument one is the path to the settings file containing db info.
2. Additional arguments are vocab machine names to export; leave blank for all.
1. Copy the output and paste into the setUp() method of your test.
1. Terms will be created for each test.

### Import to an existing site with Drush 
Outside of the context of tests you can easily use this method to migrate taxonomy terms in a Drupal 7 site...  BE CAREFUL, EACH TIME YOU RUN THIS SCRIPT NEW TERMS WILL BE CREATED AND YOU WILL END UP WITH DUPLICATES.

1. Do the above saving your file anywhere inside your drupal site.
2. Run `drush scr export_terms.php`
3. After confirming the terms were imported, delete the file `export_terms.php`.

### How to: Nodes
Node content cannot be exported in features.
@todo make a node exporter


##Contact
* **In the Loft Studios**
* Aaron Klump - Developer
* PO Box 29294 Bellingham, WA 98228-1294
* _aim_: theloft101
* _skype_: intheloftstudios
* _d.o_: aklump
* <http://www.InTheLoftStudios.com>