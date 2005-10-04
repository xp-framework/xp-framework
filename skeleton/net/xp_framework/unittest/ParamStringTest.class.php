<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'util.cmd.ParamString'
  );

  /**
   * Test framework code
   *
   * @see      xp://util.cmd.ParamString
   * @purpose  Unit Test
   */
  class ParamStringTest extends TestCase {
    
    /**
     * Test short option without value ("flag")
     *
     * @access  public
     */
    #[@test]
    function testShortFlag() {
      $p= &new ParamString(array('-k'));

      $this->assertTrue($p->exists('k'));
      $this->assertNull($p->value('k'));

      delete($p);
    }

    /**
     * Test short option with value
     *
     * @access  public
     */
    #[@test]
    function testShortValue() {
      $p= &new ParamString(array('-d', 'sql'));

      $this->assertTrue($p->exists('d'));
      $this->assertEquals($p->value('d'), 'sql');
      
      delete($p);
    }

    /**
     * Test long option without value ("flag")
     *
     * @access  public
     */
    #[@test]
    function testLongFlag() {
      $p= &new ParamString(array('--verbose'));

      $this->assertTrue($p->exists('verbose'));
      $this->assertNull($p->value('verbose'));

      delete($p);
    }

    /**
     * Test Long option with value
     *
     * @access  public
     */
    #[@test]
    function testLongValue() {
      $p= &new ParamString(array('--level=3'));

      $this->assertTrue($p->exists('level'));
      $this->assertEquals($p->value('level'), '3');

      delete($p);
    }
  }
?>
