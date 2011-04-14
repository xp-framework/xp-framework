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
   * Test TestCase class special methods cannot be overwritten as test methods
   *
   * @see      xp://unittest.TestSuite
   */
  class SpecialMethodsTest extends TestCase {
    protected $suite= NULL;
      
    /**
     * Setup method. Creates a new test suite.
     *
     */
    public function setUp() {
      $this->suite= new TestSuite();
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stateUnchanged() {
      $test= newinstance('unittest.TestCase', array('irrelevant'), '{
        #[@test]
        public function irrelevant() { }

        #[@test]
        public function tearDown() { }
      }');
      
      try {
        $this->suite->addTestClass($test->getClass());
        $this->fail('Expected exception not caught', NULL, 'lang.IllegalStateException');
      } catch (IllegalStateException $expected) {
        $this->assertEquals(0, $this->suite->numTests(), 'Number of test may not have changed');
      }
    }
    
    /**
     * Returns a testcase with setUp() as test method
     *
     * @return  unittest.TestCase
     */
    protected function setUpCase() {
      return newinstance('unittest.TestCase', array('setUp'), '{
        #[@test]
        public function setUp() { }
      }');
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Cannot override/')]
    public function setUpMethodMayNotBeATestInAddTestClass() {
      $this->suite->addTestClass($this->setUpCase()->getClass());
    }

    /**
     * Test
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Cannot override/')]
    public function setUpMethodMayNotBeATestInAddTest() {
      $this->suite->addTest($this->setUpCase());
    }

    /**
     * Returns a testcase with tearDown() as test method
     *
     * @return  unittest.TestCase
     */
    protected function tearDownCase() {
      return newinstance('unittest.TestCase', array('tearDown'), '{
        #[@test]
        public function tearDown() { }
      }');
    }

    /**
     * Test
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Cannot override/')]
    public function tearDownMethodMayNotBeATestInAddTestClass() {
      $this->suite->addTestClass($this->tearDownCase()->getClass());
    }

    /**
     * Test
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Cannot override/')]
    public function tearDownMethodMayNotBeATestInAddTest() {
      $this->suite->addTest($this->tearDownCase());
    }

    /**
     * Returns a testcase with getName() as test method
     *
     * @return  unittest.TestCase
     */
    protected function getNameCase() {
      return newinstance('unittest.TestCase', array('getName'), '{
        #[@test]
        public function getName() { }
      }');
    }
 
    /**
     * Test
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Cannot override/')]
    public function getNameMethodMayNotBeATestInAddTestClass() {
      $this->suite->addTestClass($this->getNameCase()->getClass());
    }

    /**
     * Test
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/Cannot override/')]
    public function getNameMethodMayNotBeATestInAddTest() {
      $this->suite->addTest($this->getNameCase());
    }
  }
?>
