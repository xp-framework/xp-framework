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
  }
?>
