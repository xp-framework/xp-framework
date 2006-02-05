<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'util.log.Logger'
  );
  include('chain-function.php');

  /**
   * Tests chain
   *
   * @purpose  TestCase
   */
  class ChainTest extends TestCase {
  
    /**
     * Tests Logger::getInstance()->getCategory();
     *
     * @access  public
     */
    #[@test]
    function loggerCategory() {
      $cat= &chain(Logger::getInstance(), 'getCategory()');
      $this->assertClass($cat, 'util.log.LogCategory');
    }

    /**
     * Tests XPClass::forName($class)->newInstance() will throw an 
     * exception if $class is not existant
     *
     * @access  public
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    function exceptionsBreakChain() {
      chain(XPClass::forName('@@NOTEXISTANTCLASS@@'), 'newInstance()');
    }

    /**
     * Tests NULL doesn't cause fatal errors (e.g. $instance->toString()
     * where $instance is NULL)
     *
     * @access  public
     */
    #[@test, @expect('lang.NullPointerException')]
    function nullThrowsNPE() {
      chain($instance= NULL, 'toString()');
    }
  }
?>
