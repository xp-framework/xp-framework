<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.Module');

  /**
   * TestCase
   *
   * @see xp://lang.Module
   */
  class ModuleTest extends TestCase {

    /**
     * Test forName()
     *
     */
    #[@test, @expect(class= 'lang.ElementNotFoundException', withMessage= 'No such module non-existant')]
    public function forname_throws_exception_for_non_existant_module() {
      Module::forName('non-existant');
    }

    /**
     * Test getModules()
     *
     */
    #[@test]
    public function getmodules_returns_list_of_modules() {
      $this->assertInstanceOf('lang.Module[]', Module::getModules());
    }
  }
?>
