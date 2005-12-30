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
      $this->assertEquals('sql', $p->value('d'));
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
      $this->assertEquals('3', $p->value('level'));
    }
    
    /**
     * Test long option with whitespace in value
     *
     * @access  public
     */
    #[@test]
    function whitespaceInParameter() {
      $p= &new ParamString(array('--realm=That is a realm'));
      
      $this->assertTrue($p->exists('realm'));
      $this->assertEquals('That is a realm', $p->value('realm'));
    }
  }
?>
