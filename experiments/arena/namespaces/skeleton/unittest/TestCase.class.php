<?php
/* This class is part of the XP framework
 *
 * $Id: TestCase.class.php 11008 2007-09-01 16:00:01Z friebe $ 
 */

  namespace unittest;

  ::uses(
    'util.profiling.Timer',
    'unittest.AssertionFailedError',
    'unittest.PrerequisitesNotMetError',
    'lang.MethodNotImplementedException'
  );

  /**
   * Test case
   *
   * @see      php://assert
   * @purpose  Base class
   */
  class TestCase extends lang::Object {
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
     * @param   mixed actual
     * @param   mixed expect
     * @return  bool FALSE
     */
    public function fail($reason, $actual, $expect) {
      throw(new AssertionFailedError(
        $reason, 
        $actual,
        $expect
      ));
      return FALSE;
    }
    
    /**
     * Assert that a value's type is null
     *
     * @param   mixed var
     * @param   string error default 'notnull'
     * @return  bool
     */
    public function assertNull($var, $error= 'notnull') {
      if (NULL !== $var) {
        return $this->fail($error, $var, NULL);
      }
      return TRUE;
    }
    
    /**
     * Assert that a value is an array. This is TRUE if the given value 
     * is either an array type itself or the wrapper type lang.types.ArrayList
     *
     * @param   mixed var
     * @param   string error default 'notarray'
     * @return  bool
     */
    public function assertArray($var, $error= 'notarray') {
      if (!is_array($var) && !::is('lang.types.ArrayList', $var)) {
        return $this->fail($error, 'array', ::xp::typeOf($var));
      }
      return TRUE;
    }
    
    /**
     * Assert that a value is an object
     *
     * @param   mixed var
     * @param   string error default 'notobject'
     * @return  bool
     */
    public function assertObject($var, $error= 'notobject') {
      if (!is_object($var)) {
        return $this->fail($error, 'object', ::xp::typeOf($var));
      }
      return TRUE;
    }
    
    /**
     * Assert that a value is empty
     *
     * @param   mixed var
     * @return  bool
     * @param   string error default 'notempty'
     * @see     php://empty
     */
    public function assertEmpty($var, $error= 'notempty') {
      if (!empty($var)) {
        return $this->fail($error, '<empty>', $var);
      }
      return TRUE;
    }

    /**
     * Assert that a value is not empty
     *
     * @param   mixed var
     * @return  bool
     * @param   string error default 'empty'
     * @see     php://empty
     */
    public function assertNotEmpty($var, $error= 'empty') {
      if (empty($var)) {
        return $this->fail($error, '<not empty>', $var);
      }
      return TRUE;
    }
    
    /**
     * Compare two values
     *
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    protected function _compare($a, $b) {
      if (is_array($a)) {
        if (!is_array($b) || sizeof($a) != sizeof($b)) return FALSE;

        foreach (array_keys($a) as $key) {
          if (!$this->_compare($a[$key], $b[$key])) return FALSE;
        }
        return TRUE;
      } 
      
      return $a instanceof lang::Generic ? $a->equals($b) : $a === $b;
    }

    /**
     * Assert that two values are equal
     *
     * @param   mixed expected
     * @param   mixed actual
     * @param   string error default 'notequal'
     * @return  bool
     */
    public function assertEquals($expected, $actual, $error= 'notequal') {
      if (!$this->_compare($expected, $actual)) {
        return $this->fail($error, $actual, $expected);
      }
      return TRUE;
    }
    
    /**
     * Assert that two values are not equal
     *
     * @param   mixed expected
     * @param   mixed actual
     * @param   string error default 'equal'
     * @return  bool
     */
    public function assertNotEquals($expected, $actual, $error= 'equal') {
      if ($this->_compare($expected, $actual)) {
        return $this->fail($error, $actual, $expected);
      }
      return TRUE;
    }

    /**
     * Assert that a value is true
     *
     * @param   mixed var
     * @param   string error default 'nottrue'
     * @return  bool
     */
    public function assertTrue($var, $error= 'nottrue') {
      if (TRUE !== $var) {
        return $this->fail($error, $var, TRUE);
      }
      return TRUE;
    }
    
    /**
     * Assert that a value is false
     *
     * @param   mixed var
     * @param   string error default 'notfalse'
     * @return  bool
     */
    public function assertFalse($var, $error= 'notfalse') {
      if (FALSE !== $var) {
        return $this->fail($error, $var, FALSE);
      }
      return TRUE;
    }
    
    /**
     * Assert that a given object is of a specified class
     *
     * @param   lang.Generic var
     * @param   string name
     * @param   string error default 'notequal'
     * @return  bool
     */
    public function assertClass($var, $name, $error= 'notequal') {
      if (!($var instanceof lang::Generic)) {
        return $this->fail($error, $var, $error);
      }
      if ($var->getClassName() !== $name) {
        return $this->fail($error, $var->getClassName(), $name);
      }
      return TRUE;
    }

    /**
     * Assert that a given object is a subclass of a specified class
     *
     * @param   lang.Generic var
     * @param   string name
     * @param   string error default 'notsubclass'
     * @return  bool
     */
    public function assertSubclass($var, $name, $error= 'notsubclass') {
      if (!($var instanceof lang::Generic)) {
        return $this->fail($error, $var, $error);
      }
      if (!::is($name, $var)) {
        return $this->fail($error, $name, $var->getClassName());
      }
      return TRUE;
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
     * Run this test case.
     *
     * @param   unittest.TestResult result
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    public function run($result) {
      $method= $this->getClass()->getMethod($this->name);

      if (!$method) {
        throw(new lang::MethodNotImplementedException(
          'Method does not exist', $this->name
        ));
      }

      // Check for @expect
      $expected= NULL;
      if ($method->hasAnnotation('expect')) {
        try {
          $expected= lang::XPClass::forName($method->getAnnotation('expect'));
        } catch (::Exception $e) {
          throw($e);
        }
      }
      
      // Check for @limit
      $eta= 0;
      if ($method->hasAnnotation('limit')) {
        $eta= $method->getAnnotation('limit', 'time');
      }

      $timer= new util::profiling::Timer();
      $timer->start();

      // Setup test
      try {
        $this->setUp();
      } catch (PrerequisitesNotMetError $e) {
        $timer->stop();
        $result->setSkipped($this, $e, $timer->elapsedTime());
        return FALSE;
      } catch (AssertionFailedError $e) {
        $timer->stop();
        $result->setFailed($this, $e, $timer->elapsedTime());
        return FALSE;
      }

      // Run test
      try {
        $method->invoke($this, NULL);
      } catch (lang::reflect::TargetInvocationException $t) {
        $timer->stop();
        $e= $t->getCause();

        // Was that an expected exception?
        if ($expected && $expected->isInstance($e)) {
          $r= (!$eta || $timer->elapsedTime() <= $eta 
            ? $result->setSucceeded($this, $timer->elapsedTime())
            : $result->setFailed($this, new AssertionFailedError('Timeout', sprintf('%.3f', $timer->elapsedTime()), sprintf('%.3f', $eta)), $timer->elapsedTime())
          );
          $this->tearDown();
          ::xp::gc();
          return $r;
        }

        $result->setFailed($this, $e, $timer->elapsedTime());
        $this->tearDown();
        return FALSE;
      }

      $timer->stop();
      $this->tearDown();

      // Check expected exception
      if ($expected) {
        $e= new AssertionFailedError(
          'Expected exception not caught',
          (isset($e) && $e instanceof lang::XPException ? $e->getClassName() : NULL),
          $method->getAnnotation('expect')
        );
        $result->setFailed($this, $e, $timer->elapsedTime());
        return FALSE;
      }
      
      $r= (!$eta || $timer->elapsedTime() <= $eta 
        ? $result->setSucceeded($this, $timer->elapsedTime())
        : $result->setFailed($this, new AssertionFailedError('Timeout', sprintf('%.3f', $timer->elapsedTime()), sprintf('%.3f', $eta)), $timer->elapsedTime())
      );
      return $r;
    }
    
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
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->name == $cmp->name;
    }
  }
?>
