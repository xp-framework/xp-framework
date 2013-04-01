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
   * Test values annotation
   *
   * @see  xp://unittest.TestSuite
   */
  class ValuesTest extends TestCase {
    protected $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    /**
     * Tests inline value source
     *
     */
    #[@test]
    public function inline_value_source() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $values= array();

        #[@test, @values(array(1, 2, 3))]
        public function fixture($value) {
          $this->values[]= $value;
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array(1, 2, 3), $test->values);
    }

    /**
     * Tests local value source
     *
     */
    #[@test]
    public function local_value_source() {
      $test= newinstance('unittest.TestCase', array('fixture'), '{
        public $values= array();

        public function values() {
          return array(1, 2, 3);
        }

        #[@test, @values("values")]
        public function fixture($value) {
          $this->values[]= $value;
        }
      }');
      $this->suite->runTest($test);
      $this->assertEquals(array(1, 2, 3), $test->values);
    }    
  }
?>
