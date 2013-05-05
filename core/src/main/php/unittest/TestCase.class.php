<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.AssertionFailedError',
    'unittest.PrerequisitesNotMetError'
  );

  /**
   * Test case
   *
   * @see      php://assert
   * @purpose  Base class
   */
  class TestCase extends Object {
    public
      $name     = '';
      
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Get this test cases' name
     *
     * @param   bool compound whether to use compound format
     * @return  string
     */
    public function getName($compound= FALSE) {
      return $compound ? $this->getClassName().'::'.$this->name : $this->name;
    }

    /**
     * Fail this test case
     *
     * @param   string reason
     * @param   var actual
     * @param   var expect
     */
    public function fail($reason, $actual, $expect) {
      throw new AssertionFailedError($reason, $actual, $expect);
    }

    /**
     * Skip this test case
     *
     * @param   string reason
     * @param   var[] prerequisites default []
     */
    public function skip($reason, $prerequisites= array()) {
      throw new PrerequisitesNotMetError($reason, $prerequisites= array());
    }
    
    /**
     * Assert that a value is an array. This is TRUE if the given value 
     * is either an array type itself or the wrapper type lang.types.ArrayList
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'is_array'
     */
    public function assertArray($var, $error= 'is_array') {
      if (!is_array($var) && !is('lang.types.ArrayList', $var)) {
        $this->fail($error, xp::typeOf($var), 'array');
      }
    }
    
    /**
     * Assert that a value is an object
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'is_object'
     */
    public function assertObject($var, $error= 'is_object') {
      if (!is_object($var)) {
        $this->fail($error, xp::typeOf($var), 'object');
      }
    }
    
    /**
     * Assert that a value is empty
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'empty'
     * @see     php://empty
     */
    public function assertEmpty($var, $error= 'empty') {
      if (!empty($var)) {
        $this->fail($error, $var, '<empty>');
      }
    }

    /**
     * Assert that a value is not empty
     *
     * @deprecated
     * @param   var var
     * @param   string error default '!empty'
     * @see     php://empty
     */
    public function assertNotEmpty($var, $error= '!empty') {
      if (empty($var)) {
        $this->fail($error, $var, '<not empty>');
      }
    }

    /**
     * Assert that a given object is of a specified class
     *
     * @deprecated Use assertInstanceOf() instead
     * @param   lang.Generic var
     * @param   string name
     * @param   string error default 'typeof'
     */
    public function assertClass($var, $name, $error= 'typeof') {
      if (!($var instanceof Generic)) {
        $this->fail($error, $var, $name);
      }
      if ($var->getClassName() !== $name) {
        $this->fail($error, $var->getClassName(), $name);
      }
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @deprecated Use assertInstanceOf() instead
     * @param   lang.Generic var
     * @param   string name
     * @param   string error default 'instanceof'
     */
    public function assertSubclass($var, $name, $error= 'instanceof') {
      if (!($var instanceof Generic)) {
        $this->fail($error, $var, $name);
      }
      if (!is($name, $var)) {
        $this->fail($error, $name, $var->getClassName());
      }
    }
    
    
    /**
     * Compare two values
     *
     * @param   var a
     * @param   var b
     * @return  bool TRUE if the two values are equal, FALSE otherwise
     */
    protected function _compare($a, $b) {
      if (is_array($a)) {
        if (!is_array($b) || sizeof($a) != sizeof($b)) return FALSE;

        foreach (array_keys($a) as $key) {
          if (!$this->_compare($a[$key], $b[$key])) return FALSE;
        }
        return TRUE;
      }
      
      return $a instanceof Generic ? $a->equals($b) : $a === $b;
    }

    /**
     * Assert that two values are equal
     *
     * @param   var expected
     * @param   var actual
     * @param   string error default 'notequal'
     */
    public function assertEquals($expected, $actual, $error= 'equals') {
      if (!$this->_compare($expected, $actual)) {
        $this->fail($error, $actual, $expected);
      }
    }
    
    /**
     * Assert that two values are not equal
     *
     * @param   var expected
     * @param   var actual
     * @param   string error default 'equal'
     */
    public function assertNotEquals($expected, $actual, $error= '!equals') {
      if ($this->_compare($expected, $actual)) {
        $this->fail($error, $actual, $expected);
      }
    }

    /**
     * Assert that a value is true
     *
     * @param   var var
     * @param   string error default '==='
     */
    public function assertTrue($var, $error= '===') {
      if (TRUE !== $var) {
        $this->fail($error, $var, TRUE);
      }
    }
    
    /**
     * Assert that a value is false
     *
     * @param   var var
     * @param   string error default '==='
     */
    public function assertFalse($var, $error= '===') {
      if (FALSE !== $var) {
        $this->fail($error, $var, FALSE);
      }
    }

    /**
     * Assert that a value's type is null
     *
     * @param   var var
     * @param   string error default '==='
     */
    public function assertNull($var, $error= '===') {
      if (NULL !== $var) {
        $this->fail($error, $var, NULL);
      }
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @param   var type either a type name or a lang.Type instance
     * @param   var var
     * @param   string error default 'instanceof'
     */
    public function assertInstanceOf($type, $var, $error= 'instanceof') {
      if (!($type instanceof Type)) {
        $type= Type::forName($type);
      }
      
      $type->isInstance($var) || $this->fail($error, xp::typeOf($var), $type->getName());
    }
    
    /**
     * Set up this test. Overwrite in subclasses. Throw a 
     * PrerequisitesNotMetError to indicate this case should be
     * skipped.
     *
     * @throws  unittest.PrerequisitesNotMetError
     */
    public function setUp() { }
    
    /**
     * Tear down this test case. Overwrite in subclasses.
     *
     */
    public function tearDown() { }
    
    /**
     * Creates a string representation of this testcase
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->name.'>';
    }

    /**
     * Returns whether an object is equal to this testcase
     *
     * @param   lang.Generic cmp
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->name == $cmp->name;
    }
  }
?>
