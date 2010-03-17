<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.Expect',
    'unittest.Limit',
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
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
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
     * Assert that a value is an array. This is TRUE if the given value 
     * is either an array type itself or the wrapper type lang.types.ArrayList
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'notarray'
     */
    public function assertArray($var, $error= 'notarray') {
      if (!is_array($var) && !is('lang.types.ArrayList', $var)) {
        $this->fail($error, xp::typeOf($var), 'array');
      }
    }
    
    /**
     * Assert that a value is an object
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'notobject'
     */
    public function assertObject($var, $error= 'notobject') {
      if (!is_object($var)) {
        $this->fail($error, xp::typeOf($var), 'object');
      }
    }
    
    /**
     * Assert that a value is empty
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'notempty'
     * @see     php://empty
     */
    public function assertEmpty($var, $error= 'notempty') {
      if (!empty($var)) {
        $this->fail($error, $var, '<empty>');
      }
    }

    /**
     * Assert that a value is not empty
     *
     * @deprecated
     * @param   var var
     * @param   string error default 'empty'
     * @see     php://empty
     */
    public function assertNotEmpty($var, $error= 'empty') {
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
     * @param   string error default 'notequal'
     */
    public function assertClass($var, $name, $error= 'notequal') {
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
     * @param   string error default 'notsubclass'
     */
    public function assertSubclass($var, $name, $error= 'notsubclass') {
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
    public function assertEquals($expected, $actual, $error= 'notequal') {
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
    public function assertNotEquals($expected, $actual, $error= 'equal') {
      if ($this->_compare($expected, $actual)) {
        $this->fail($error, $actual, $expected);
      }
    }

    /**
     * Assert that a value is true
     *
     * @param   var var
     * @param   string error default 'nottrue'
     */
    public function assertTrue($var, $error= 'nottrue') {
      if (TRUE !== $var) {
        $this->fail($error, $var, TRUE);
      }
    }
    
    /**
     * Assert that a value is false
     *
     * @param   var var
     * @param   string error default 'notfalse'
     */
    public function assertFalse($var, $error= 'notfalse') {
      if (FALSE !== $var) {
        $this->fail($error, $var, FALSE);
      }
    }

    /**
     * Assert that a value's type is null
     *
     * @param   var var
     * @param   string error default 'notnull'
     */
    public function assertNull($var, $error= 'notnull') {
      if (NULL !== $var) {
        $this->fail($error, $var, NULL);
      }
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @param   var type either a type name or a lang.Type instance
     * @param   var var
     * @param   string error default 'notaninstance'
     */
    public function assertInstanceOf($type, $var, $error= 'notaninstance') {
      if (!($type instanceof Type)) {
        $type= Type::forName($type);
      }
      
      $type->isInstance($var) || $this->fail($error, $type->getName(), xp::typeOf($var));
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
