<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.AssertionFailedError',
    'util.profiling.unittest.PrerequisitesNotMetError',
    'lang.MethodNotImplementedException'
  );

  /**
   * Test case
   *
   * @see      php://assert
   * @purpose  Base class
   */
  class TestCase extends Object {
    var
      $name     = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
      assert_options(ASSERT_ACTIVE, 1);
      assert_options(ASSERT_WARNING, 1);
      assert_options(ASSERT_CALLBACK, array('TestCase', '_fail'));
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Private helper method
     *
     * @model   static
     * @access  private
     * @param   mixed* arg
     * @return  mixed* arg
     */
    function _store() {
      static $store;
      
      if (0 == func_num_args()) {
        return $store;
      }
      $store= func_get_args();
    }

    /**
     * Private helper method
     *
     * @access  private
     * @param   mixed expr
     * @param   string reason
     * @param   mixed actual default NULL
     * @param   mixed expect default NULL
     * @return  mixed expr
     */
    function _test($expr, $reason, $actual= NULL, $expect= NULL) {
      TestCase::_store($reason, $actual, $expect);
      return $expr;
    }
    
    /**
     * Callback for assert
     *
     * @model   static
     * @access  protected
     * @param   string filee
     * @param   int line
     * @param   string code
     */
    function _fail($file, $line, $code) {
      list($reason, $actual, $expect)= TestCase::_store();
      TestCase::fail(
        $reason, 
        substr($code, 12, strpos($code, ', $error')- 12),
        $actual,
        $expect
      );
    }
    
    /**
     * Fail this test case
     *
     * @access  public
     * @param   string reason
     * @param   string code
     * @param   mixed actual
     * @param   mixed expect
     * @return  bool FALSE
     */
    function fail($reason, $code, $actual, $expect) {
      return throw(new AssertionFailedError(
        $reason, 
        $code,
        $actual,
        $expect
      ));
    }
    
    /**
     * Assert that a value's type is boolean
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notbool'
     * @return  bool
     */
    function assertBoolean($var, $error= 'notbool') {
      return assert('$this->_test(is_bool($var), $error, gettype($var))');
    }
    
    /**
     * Assert that a value's type is float
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notfloat'
     * @return  bool
     */
    function assertFloat($var, $error= 'notfloat') {
      return assert('$this->_test(is_float($var), $error, gettype($var), "float")');
    }
    
    /**
     * Assert that a value's type is integer
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notinteger'
     * @return  bool
     */
    function assertInteger($var, $error= 'notinteger') {
      return assert('$this->_test(is_int($var), $error, gettype($var), "integer")');
    }

    /**
     * Assert that a value's type is string
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notstring'
     * @return  bool
     */
    function assertString($var, $error= 'notstring') {
      return assert('$this->_test(is_string($var), $error, gettype($var), "string")');
    }

    /**
     * Assert that a value's type is null
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notnull'
     * @return  bool
     */
    function assertNull($var, $error= 'notnull') {
      return assert('$this->_test(is_null($var), $error, gettype($var), "null")');
    }
    
    /**
     * Assert that a value is an array
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notarray'
     * @return  bool
     */
    function assertArray($var, $error= 'notarray') {
      return assert('$this->_test(is_array($var), $error, gettype($var), "array")');
    }
    
    /**
     * Assert that a value is an object
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notobject'
     * @return  bool
     */
    function assertObject(&$var, $error= 'notobject') {
      return assert('$this->_test(is_object($var), $error, gettype($var), "object")');
    }
    
    /**
     * Assert that a value is empty
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     * @param   string error default 'notempty'
     * @see     php://empty
     */
    function assertEmpty($var, $error= 'notempty') {
      assert('$this->_test(empty($var), $error, $var, "empty(\$var)")');
    }

    /**
     * Assert that a value is not empty
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     * @param   string error default 'empty'
     * @see     php://empty
     */
    function assertNotEmpty($var, $error= 'empty') {
      assert('$this->_test(!empty($var), $error, $var, "!empty(\$var)")');
    }

    /**
     * Assert that two values are equal
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @param   string error default 'notequal'
     * @return  bool
     */
    function assertEquals($a, $b, $error= 'notequal') {
      if (is_a($a, 'Object')) return assert('$this->_test($a->equals($b), $error, $a, $b)');
      return assert('$this->_test($a === $b, $error, $a, $b)');
    }
    
    /**
     * Assert that two values are not equal
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @param   string error default 'equal'
     * @return  bool
     */
    function assertNotEquals($a, $b, $error= 'equal') {
      return assert('$this->_test($a !== $b, $error, $a, $b)');
    }

    /**
     * Assert that a value is true
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'nottrue'
     * @return  bool
     */
    function assertTrue($var, $error= 'nottrue') {
      if ($r= $this->assertBoolean($var, $error)) {
        $r= assert('$this->_test($var === TRUE, $error, $var, TRUE)');
      }
      return $r;
    }
    
    /**
     * Assert that a value is false
     *
     * @access  public
     * @param   mixed var
     * @param   string error default 'notfalse'
     * @return  bool
     */
    function assertFalse($var, $error= 'notfalse') {
      if ($r= $this->assertBoolean($var, $error)) {
        $r= assert('$this->_test($var === FALSE, $error, $var, FALSE)');
      }
      return $r;
    }
    
    /**
     * Assert that a value matches a given pattern
     *
     * @access  public
     * @param   mixed var
     * @param   string pattern
     * @param   string error default 'nomatches'
     * @return  bool
     * @see     php://preg_match
     */
    function assertMatches($var, $pattern, $error= 'nomatches') {
      return assert('$this->_test(preg_match($pattern, $var), $error, $var, $pattern)');
    }

    /**
     * Assert that a string contains a substring
     *
     * @access  public
     * @param   mixed var
     * @param   string needle
     * @param   string error default 'notcontained'
     * @return  bool
     */
    function assertContains($var, $needle, $error= 'notcontained') {
      return assert('$this->_test(strstr($var, $needle), $error, $var, $needle)');
    }
    
    /**
     * Assert that a given object is of a specified class
     *
     * @access  public
     * @param   &lang.Object var
     * @param   string name
     * @param   string error default 'notequal'
     * @return  bool
     */
    function assertClass(&$var, $name, $error= 'notequal') {
      if ($r= $this->assertObject($var, $error)) {
        $r= assert('$this->_test($var->getClassName() === $name, $error, xp::typeOf($var), $name)');
      }
      return $r;
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @access  public
     * @param   &lang.Object var
     * @param   string name
     * @param   string error default 'notsubclass'
     * @return  bool
     */
    function assertSubclass(&$var, $name, $error= 'notsubclass') {
      if ($r= $this->assertObject($var, $error)) {
        $r= assert('$this->_test(is($name, $var), $error, xp::typeOf($var), $name)');
      }
      return $r;
    }
    
    /**
     * Assert that a value is contained in a list
     *
     * @access  public
     * @param   mixed var
     * @param   array list
     * @param   string error default 'notinlist'
     * @return  bool
     */
    function assertIn($var, $list, $error= 'notinlist') {
      return assert('$this->_test(in_array($var, $list, TRUE), $error, $list, $var)');
    }

    /**
     * Set up this test. Overwrite in subclasses. Throw a 
     * PrerequisitesNotMetError to indicate this case should be
     * skipped.
     *
     * @model   abstract
     * @access  public
     * @throws  util.profiling.unittest.PrerequisitesNotMetError
     */
    function setUp() { }
    
    /**
     * Tear down this test case. Overwrite in subclasses.
     *
     * @model   abstract
     * @access  public
     */
    function tearDown() { }
    
    /**
     * Run this test case.
     *
     * @access  public
     * @return  &mixed return value of test method
     * @throws  lang.MethodNotImplementedException
     */
    function &run() {
      $class= &$this->getClass();
      $method= &$class->getMethod($this->name);

      if (!$method) {
        return throw(new MethodNotImplementedException(
          'Method does not exist', $this->name
        ));
      }
      
      $expected= NULL;
      if ($method->hasAnnotation('expect')) {
        try(); {
          $expected= &XPClass::forName($method->getAnnotation('expect'));
        } if (catch('Exception', $e)) {
          return throw($e);
        }
      }
      
      try(); {
        $res= $method->invoke($this, NULL);
      } if (catch('Exception', $e)) {
        
        // Was that an expected exception?
        if ($expected && $expected->isInstance($e)) {
          return TRUE;
        }
        
        return throw($e);
      }
      
      if ($expected) return $this->fail(
        'Expected exception not caught',
        'failedexpect',
        $expected->getClassName(),
        NULL
      );
      
      return $res;
    }
  }
?>
