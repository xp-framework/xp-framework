<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase', 
    'util.Date',
    'util.DateUtil',
    'TestClass'
  );

  /**
   * Tests method invocations
   *
   * @purpose  Unit Test
   */
  class MethodInvocationTest extends TestCase {
    var
      $fixture= NULL;

    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->fixture= &XPClass::forName('TestClass');
    }
    
    /**
     * Verify type / argument match
     *
     * @access  protected
     * @param   string type
     * @param   &mixed arg
     * @return  bool
     */
    function verifyType($type, &$arg) {
    
      // Handle "mixed"
      if (0 == strncmp('mixed', $type, 5)) return TRUE;
    
      // Handle types arrays
      if ('[]' == substr($type, -2)) {
        $componentType= substr($type, 0, -2);
        for ($i= 0, $s= sizeof($arg); $i < $s; $i++) {
          if (!$this->verifyType($componentType, $arg[$i])) return FALSE;
        }
        return TRUE;
      }
      
      // Handle generic arrays
      if (1 == sscanf($type, 'array<%[^>]>', $componentsDeclaration)) {
        $componentTypes= explode(', ', str_replace('&', '', $componentsDeclaration));
        foreach (array_keys($arg) as $k) {
          if (!$this->verifyType($componentTypes[0], $k)) return FALSE;
          if (!$this->verifyType($componentTypes[1], $arg[$k])) return FALSE;
        }
        return TRUE;
      }
      
      // Handle other types
      switch ($type) {
        case 'int': return is_int($arg);
        case 'bool': return is_bool($arg);
        case 'float': return is_float($arg);
        case 'string': return is_string($arg);
        case 'array': return is_array($args);
        default: return NULL === $arg || is($type, $arg);
      }
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string name
     * @param   mixed[] args
     */
    function invoke($name, $args= array()) {
      $method= &$this->fixture->getMethod($name);
      $arguments= $method->getArguments();
      
      // Check how many arguments are required
      $required= sizeof($arguments);
      $numargs= sizeof($args);
      $success= $required == $numargs;
      foreach ($arguments as $pos => $arg) {
        if ('*' == substr($arg->getType(), -1)) {
          $success= $numargs >= $pos;
          break;
        }
      }
      
      // Check number of arguments
      if (!$success) {
        return throw(new IllegalArgumentException(
          'Incorrect number of arguments to '.$name.'(): '.$numargs
        ));
      }
      
      // Check types
      foreach ($arguments as $pos => $arg) {
        if ($this->verifyType($arg->getType(), $args[$pos])) continue;

        return throw(new IllegalArgumentException(
          'Argument #'.$pos.': '.xp::typeOf($args[$pos]).' does not match '.$arg->getType()
        ));
      }

      $method->invoke($this->fixture->newInstance(), $args);
    }

    /**
     * Tests invoking a method without arguments
     *
     * @access  public
     */
    #[@test]
    function noArgsMethod() {
      $this->invoke('toString');
    }

    /**
     * Tests invoking a method without arguments
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function noArgsMethodWithArgument() {
      $this->invoke('toString', array('arg0'));
    }

    /**
     * Tests invoking TestClass::setDate() with a Date instance
     *
     * @access  public
     */
    #[@test]
    function setDateWithDateInstance() {
      $this->invoke('setDate', array(new Date()));
    }

    /**
     * Tests invoking TestClass::setDate() with NULL
     *
     * @access  public
     */
    #[@test]
    function setDateWithNull() {
      $this->invoke('setDate', array(NULL));
    }

    /**
     * Tests invoking TestClass::setDate() with an Object instance
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setDateWithObjectInstance() {
      $this->invoke('setDate', array(new Object()));
    }

    /**
     * Tests invoking TestClass::setDate() with a primitive
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setDateWithPrimitive() {
      $this->invoke('setDate', array('2007-07-07'));
    }

    /**
     * Tests invoking TestClass::add() with two ints
     *
     * @access  public
     */
    #[@test]
    function addWithInts() {
      $this->invoke('add', array(1, 2));
    }

    /**
     * Tests invoking TestClass::add() with two floats
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithFloats() {
      $this->invoke('add', array(1.0, 2.0));
    }

    /**
     * Tests invoking TestClass::add() with two objects
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithObjects() {
      $this->invoke('add', array(new Date(), new Object()));
    }

    /**
     * Tests invoking TestClass::add() with NULLs
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithNulls() {
      $this->invoke('add', array(NULL, NULL));
    }

    /**
     * Tests invoking TestClass::add() with only argument
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithOnlyOneArgument() {
      $this->invoke('add', array(1));
    }

    /**
     * Tests invoking TestClass::add() with too many arguments
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addWithTooManyArguments() {
      $this->invoke('add', array(1, 2, 3));
    }
    
    /**
     * Tests invoking TestClass::setNames() with an array of strings
     *
     * @access  public
     */
    #[@test]
    function setNamesWithStringArray() {
      $this->invoke('setNames', array(array('Timm', 'Alex')));
    }

    /**
     * Tests invoking TestClass::setNames() with an empty array
     *
     * @access  public
     */
    #[@test]
    function setNamesWithEmptyArray() {
      $this->invoke('setNames', array(array()));
    }

    /**
     * Tests invoking TestClass::setNames() with a mixed array
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setNamesWithMixedArray() {
      $this->invoke('setNames', array(array('Timm', FALSE)));
    }

    /**
     * Tests invoking TestClass::setNames() with an associative array
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function setNamesWithAssociativeArray() {
      $this->invoke('setNames', array(array('name' => 'Timm', 'lastname' => 'Friebe')));
    }

    /**
     * Tests invoking TestClass::filter() with an hash
     *
     * @access  public
     */
    #[@test]
    function filterWithEmptyHash() {
      $this->invoke('filter', array(
        array(),
        'isBefore',
        Date::now()
      ));
    }

    /**
     * Tests invoking TestClass::filter() with an hash
     *
     * @access  public
     */
    #[@test]
    function filterWithStringDateHash() {
      $this->invoke('filter', array(
        array(
          'now'       => Date::now(),
          'tomorrow'  => DateUtil::addDays(Date::now(), 1),
          'yesterday' => DateUtil::addDays(Date::now(), -1)
        ),
        'isBefore',
        Date::now()
      ));
    }

    /**
     * Tests invoking TestClass::filter() with an hash
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function filterWithMixedHash() {
      $this->invoke('filter', array(
        array(
          'now'       => Date::now(),
          'tomorrow'  => FALSE
        ),
        'isBefore',
        Date::now()
      ));
    }
    
    /**
     * Tests invoking TestClass::format() with only one argument
     *
     * @access  public
     */
    #[@test]
    function formatWithOneArgument() {
      $this->invoke('format', array('Needs no formatting'));
    }

    /**
     * Tests invoking TestClass::format() with two arguments
     *
     * @access  public
     */
    #[@test]
    function formatWithTwoArguments() {
      $this->invoke('format', array('Hello %s', 'World'));
    }

    /**
     * Tests invoking TestClass::format() with only one argument
     *
     * @access  public
     */
    #[@test]
    function formatWithLotsArguments() {
      $this->invoke('format', array('%s: %s %d %s', 'Hello', 'Timm', 42, 'is the answer'));
    }

    /**
     * Tests invoking TestClass::format() with no arguments
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function formatWithNoArguments() {
      $this->invoke('format');
    }

  }
?>
