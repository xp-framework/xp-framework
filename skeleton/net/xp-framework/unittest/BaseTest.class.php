<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class BaseTest extends TestCase {
      
    /**
     * Test prerequisites
     *
     * @access  public
     */
    function testPrerequisites() {
      $this->assertEquals(get_magic_quotes_gpc(), 0, 'magicquotesgpc');
      $this->assertEquals(get_magic_quotes_runtime(), 0, 'magicquotesruntime');
      $this->assertIn(version_compare(phpversion(), '4.2.0'), array(0, 1), 'phpversion');
    }
  }
?>
