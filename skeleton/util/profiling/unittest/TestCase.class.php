<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.AssertionFailedError'
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
      assert_options(ASSERT_CALLBACK, array('TestCase', 'fail'));
      parent::__construct();
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
    function store() {
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
     * @return  mixed expr
     */
    function test($expr, $reason, $actual= NULL) {
      TestCase::store($reason, $actual);
      return $expr;
    }
    
    /**
     * Callback for assert
     *
     * @model   static
     * @access  magic
     * @param   string filee
     * @param   int line
     * @param   string code
     */
    function fail($file, $line, $code) {
      list($reason, $actual)= TestCase::store();
      throw(new AssertionFailedError(
        $reason, 
        $actual, 
        substr($code, 12, strpos($code, '"')- 14)
      ));
    }
    
    /**
     * Assert that a value's type is boolean
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertBoolean($var) {
      return assert('$this->test(is_bool($var), "notbool", gettype($var))');
    }
    
    /**
     * Assert that a value's type is float
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertFloat($var) {
      return assert('$this->test(is_float($var), "notfloat", gettype($var))');
    }
    
    /**
     * Assert that a value's type is integer
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertInteger($var) {
      return assert('$this->test(is_int($var), "notint", gettype($var))');
    }

    /**
     * Assert that a value's type is string
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertString($var) {
      return assert('$this->test(is_string($var, "notstring", gettype($var)))');
    }

    /**
     * Assert that a value's type is null
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertNull($var) {
      return assert('$this->test(is_null($var), "notnull", gettype($var))');
    }
    
    /**
     * Assert that a value is an array
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertArray($var) {
      return assert('$this->test(is_array($var), "notarray", gettype($var))');
    }
    
    /**
     * Assert that a value is an object
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertObject(&$var) {
      return assert('$this->test(is_object($var), "notobject", gettype($var))');
    }
    
    /**
     * Assert that a value is empty
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     * @see     php://empty
     */
    function assertEmpty($var) {
      assert('$this->test(empty($var), "notempty", $var)');
    }

    /**
     * Assert that a value is not empty
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     * @see     php://empty
     */
    function assertNotEmpty($var) {
      assert('$this->test(!empty($var), "empty", $var)');
    }

    /**
     * Assert that two values are equal
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    function assertEquals($a, $b) {
      return assert('$this->test($a === $b, "notequal", array($a, $b))');
    }
    
    /**
     * Assert that two values are not equal
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    function assertNotEquals($a, $b) {
      return assert('$this->test($a !== $b, "equal", array($a, $b))');
    }

    /**
     * Assert that a value is true
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertTrue($var) {
      if ($r= $this->assertBoolean($var)) {
        $r= assert('$this->test($var === TRUE, "nottrue", $var)');
      }
      return $r;
    }
    
    /**
     * Assert that a value is false
     *
     * @access  public
     * @param   mixed var
     * @return  bool
     */
    function assertFalse($var) {
      if ($r= $this->assertBoolean($var)) {
        $r= assert('$this->test($var === FALSE, "notfalse", $var)');
      }
      return $r;
    }
    
    /**
     * Assert that a value matches a given pattern
     *
     * @access  public
     * @param   mixed var
     * @param   string pattern
     * @return  bool
     * @see     php://preg_match
     */
    function assertMatches($var, $pattern) {
      return assert('$this->test(preg_match($var, $pattern), "nomatches", array($var, $pattern))');
    }
    
    /**
     * Assert that a given object is of a specified class
     *
     * @access  public
     * @param   &lang.Object var
     * @param   string name
     * @return  bool
     */
    function assertClass(&$var, $name) {
      if ($r= $this->assertObject($var)) {
        $r= assert('$this->test($var->getClassName() === $name, "notequal", $var->getClassName())');
      }
      return $r;
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @access  public
     * @param   &lang.Object var
     * @param   string name
     * @return  bool
     */
    function assertSubclass(&$var, $name) {
      if ($r= $this->assertObject($var)) {
        $c= array_search($name, $GLOBALS['php_class_names']);
        $r= assert('$this->test(is_a($var, $c), "notsubclass", $name)');
      }
      return $r;
    }

    /**
     * Set up this test. Overwrite in subclasses.
     *
     * @model   abstract
     * @access  public
     * @return  mixed anything except NULL to indicate this test should be skipped
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
     * @return  &net.xp-framework.util.TestResult either a TestFailure or a TestSuccess
     */
    function &run() {
      return call_user_func(array(&$this, $this->name));
    }
  }
?>
