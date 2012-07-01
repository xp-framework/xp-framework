<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'unittest.TestSuite'
  );

  /**
   * Tests @beforeClass and @afterClass methods
   *
   * @see   xp://unittest.TestSuite
   */
  abstract class BeforeAndAfterClassTest extends TestCase {
    protected $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    /**
     * Runs a test and returns the outcome
     *
     * @param   unittest.TestCase test
     * @return  unittest.TestOutcome
     */
    protected abstract function runTest($test);

    /**
     * Tests @beforeClass methods
     *
     */
    #[@test]
    public function beforeClassMethodIsExecuted() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{
        public static $initialized= FALSE;

        #[@beforeClass]
        public static function prepareTestData() {
          self::$initialized= TRUE;
        }

        #[@test]
        public function fixture() { }
      }');
      $this->suite->runTest($t);
      $this->assertEquals(TRUE, $t->getClass()->getField('initialized')->get(NULL));
    }

    /**
     * Tests @beforeClass methods
     *
     */
    #[@test]
    public function exceptionInBeforeClassSkipsTest() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{

        #[@beforeClass]
        public static function prepareTestData() {
          throw new IllegalStateException("Test data not available");
        }

        #[@test]
        public function fixture() { 
          $this->fail("Will not be run");
        }
      }');
      $r= $this->suite->runTest($t)->outComeOf($t);
      $this->assertInstanceOf('unittest.TestSkipped', $r);
      $this->assertInstanceOf('unittest.PrerequisitesNotMetError', $r->reason);
      $this->assertEquals('Exception in beforeClass method prepareTestData', $r->reason->getMessage());
    }

    /**
     * Tests @beforeClass methods
     *
     */
    #[@test]
    public function failedPrerequisiteInBeforeClassSkipsTest() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{

        #[@beforeClass]
        public static function prepareTestData() {
          throw new PrerequisitesNotMetError("Test data not available", NULL, array("data"));
        }

        #[@test]
        public function fixture() { 
          $this->fail("Will not be run");
        }
      }');
      $r= $this->suite->runTest($t)->outComeOf($t);
      $this->assertInstanceOf('unittest.TestSkipped', $r);
      $this->assertInstanceOf('unittest.PrerequisitesNotMetError', $r->reason);
      $this->assertEquals('Test data not available', $r->reason->getMessage());
    }

    /**
     * Tests @beforeClass methods
     *
     */
    #[@test]
    public function afterClassMethodIsExecuted() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{
        public static $finalized= FALSE;

        #[@afterClass]
        public static function deleteTestData() {
          self::$finalized= TRUE;
        }

        #[@test]
        public function fixture() { }
      }');
      $this->suite->runTest($t);
      $this->assertEquals(TRUE, $t->getClass()->getField('finalized')->get(NULL));
    }

    /**
     * Tests multiple @beforeClass methods
     *
     */
    #[@test]
    public function allBeforeClassMethodsAreExecuted() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{
        public static $initialized= array();

        #[@beforeClass]
        public static function prepareTestData() {
          self::$initialized[]= "data";
        }

        #[@beforeClass]
        public static function connectToDatabase() {
          self::$initialized[]= "conn";
        }

        #[@test]
        public function fixture() { }
      }');
      $this->suite->runTest($t);
      $this->assertEquals(array('data', 'conn'), $t->getClass()->getField('initialized')->get(NULL));
    }

    /**
     * Tests multiple @afterClass methods
     *
     */
    #[@test]
    public function allAfterClassMethodsAreExecuted() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{
        public static $finalized= array();

        #[@beforeClass]
        public static function disconnectFromDatabase() {
          self::$finalized[]= "conn";
        }

        #[@beforeClass]
        public static function deleteTestData() {
          self::$finalized[]= "data";
        }

        #[@test]
        public function fixture() { }
      }');
      $this->suite->runTest($t);
      $this->assertEquals(array('conn', 'data'), $t->getClass()->getField('finalized')->get(NULL));
    }

    /**
     * Tests @beforeClass and @afterClass methods
     *
     */
    #[@test]
    public function afterClassMethodIsNotExecutedWhenPrerequisitesFail() {
      $t= newinstance('unittest.TestCase', array('fixture'), '{
        public static $finalized= FALSE;

        #[@beforeClass]
        public static function prepareTestData() {
          throw new PrerequisitesNotMetError("Test data not available", NULL, array("data"));
        }

        #[@afterClass]
        public static function deleteTestData() {
          self::$finalized= TRUE;
        }

        #[@test]
        public function fixture() { }
      }');
      $this->suite->runTest($t);
      $this->assertEquals(FALSE, $t->getClass()->getField('finalized')->get(NULL));
    }
  }
?>
