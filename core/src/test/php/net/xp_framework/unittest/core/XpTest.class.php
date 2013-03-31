<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the <xp> functions
   *
   */
  class XpTest extends TestCase {

    /**
     * Tests xp::version()
     *
     */
    #[@test]
    public function version() {
      $this->assertEquals(3, sscanf(xp::version(), '%d.%d.%d', $series, $major, $minor));
    }

    /**
     * Tests xp::errorAt()
     *
     */
    #[@test]
    public function no_error_here() {
      $this->assertNull(xp::errorAt(__FILE__));
    }

    /**
     * Tests xp::errorAt()
     *
     */
    #[@test]
    public function triggered_error_at_file() {
      trigger_error('Test');
      $this->assertEquals(
        array(__LINE__ - 2 => array('Test' => array('class' => NULL, 'method' => 'trigger_error', 'cnt' => 1))),
        xp::errorAt(__FILE__)
      );
      xp::gc();
    }

    /**
     * Tests xp::errorAt()
     *
     */
    #[@test]
    public function triggered_error_at_file_and_line() {
      trigger_error('Test');
      $this->assertEquals(
        array('Test' => array('class' => NULL, 'method' => 'trigger_error', 'cnt' => 1)),
        xp::errorAt(__FILE__, __LINE__ - 3)
      );
      xp::gc();
    }

    /**
     * Tests xp::gc()
     *
     */
    #[@test]
    public function gc() {
      trigger_error('Test');
      $this->assertEquals(
        array(__FILE__ => array(__LINE__ - 2 => array('Test' => array('class' => NULL, 'method' => 'trigger_error', 'cnt' => 1)))),
        xp::$errors
      );
      xp::gc();
      $this->assertEquals(array(), xp::$errors);
    }

    /**
     * Tests xp::null()
     *
     */
    #[@test]
    public function null() {
      $this->assertEquals('null', get_class(xp::null()));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_int() {
      $this->assertEquals('þint', xp::reflect('int'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_double() {
      $this->assertEquals('þdouble', xp::reflect('double'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_string() {
      $this->assertEquals('þstring', xp::reflect('string'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_bool() {
      $this->assertEquals('þbool', xp::reflect('bool'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_var() {
      $this->assertEquals('var', xp::reflect('var'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_int_array() {
      $this->assertEquals('¦þint', xp::reflect('int[]'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_int_map() {
      $this->assertEquals('»þint', xp::reflect('[:int]'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_generic_list_of_int() {
      $this->assertEquals('List··þint', xp::reflect('List<int>'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_object() {
      $this->assertEquals('Object', xp::reflect('lang.Object'));
    }

    /**
     * Tests xp::reflect()
     *
     */
    #[@test]
    public function reflect_this() {
      $this->assertEquals(__CLASS__, xp::reflect($this->getClassName()));
    }
  }
?>
