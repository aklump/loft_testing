#!/bin/bash
#
# Call this for inside a drupal site
# 
echo "`tput setaf 3`Call the green line AFTER APPENDING the rest of the path to the settings.php file`tput op`"
echo "`tput setaf 2`phpm /Library/Packages/php/loft_testing/simpletest/export_terms.php ${PWD}`tput op`"
find . -name settings*php
echo