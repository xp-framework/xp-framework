<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
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
     */
    #[@test]
    public function shortFlag() {
      $p= new ParamString(array('-k'));

      $this->assertTrue($p->exists('k'));
      $this->assertNull($p->value('k'));
    }

    /**
     * Test short option with value
     *
     */
    #[@test]
    public function shortValue() {
      $p= new ParamString(array('-d', 'sql'));

      $this->assertTrue($p->exists('d'));
      $this->assertEquals('sql', $p->value('d'));
    }

    /**
     * Test long option without value ("flag")
     *
     */
    #[@test]
    public function longFlag() {
      $p= new ParamString(array('--verbose'));

      $this->assertTrue($p->exists('verbose'));
      $this->assertNull($p->value('verbose'));
    }

    /**
     * Test Long option with value
     *
     */
    #[@test]
    public function longValue() {
      $p= new ParamString(array('--level=3'));

      $this->assertTrue($p->exists('level'));
      $this->assertEquals('3', $p->value('level'));
    }

    /**
     * Test Long option with value
     *
     */
    #[@test]
    public function longValueShortGivenDefault() {
      $p= new ParamString(array('-l', '3'));

      $this->assertTrue($p->exists('level'));
      $this->assertEquals('3', $p->value('level'));
    }

    /**
     * Test Long option with value
     *
     */
    #[@test]
    public function longValueShortGiven() {
      $p= new ParamString(array('-L', '3', '-l', 'FAIL'));

      $this->assertTrue($p->exists('level', 'L'));
      $this->assertEquals('3', $p->value('level', 'L'));
    }

    /**
     * Test positional query
     *
     */
    #[@test]
    public function positional() {
      $p= new ParamString(array('That is a realm'));
      
      $this->assertTrue($p->exists(0));
      $this->assertEquals('That is a realm', $p->value(0));
    }

    /**
     * Test exists() method
     *
     */
    #[@test]
    public function existance() {
      $p= new ParamString(array('a', 'b'));
      
      $this->assertTrue($p->exists(0));
      $this->assertTrue($p->exists(1));
      $this->assertFalse($p->exists(2));
    }

    /**
     * Test positional query
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonExistantPositional() {
      create(new ParamString(array('a')))->value(1);
    }

    /**
     * Test positional query
     *
     */
    #[@test]
    public function nonExistantPositionalWithDefault() {
      $this->assertEquals(
        'Default', 
        create(new ParamString(array('--verbose')))->value(1, NULL, 'Default')
      );
    }

    /**
     * Test named query
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonExistantNamed() {
      create(new ParamString(array('--verbose')))->value('name');
    }

    /**
     * Test named query
     *
     */
    #[@test]
    public function nonExistantNamedWithDefault() {
      $this->assertEquals(
        'Default', 
        create(new ParamString(array('--verbose')))->value('name', 'n', 'Default')
      );
    }
    
    /**
     * Test long option with whitespace in value
     *
     */
    #[@test]
    public function whitespaceInParameter() {
      $p= new ParamString(array('--realm=That is a realm'));
      
      $this->assertTrue($p->exists('realm'));
      $this->assertEquals('That is a realm', $p->value('realm'));
    }
  }
?>
