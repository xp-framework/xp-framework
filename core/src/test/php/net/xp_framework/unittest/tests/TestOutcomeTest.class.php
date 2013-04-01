<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'unittest.TestSuite',
    'unittest.TestExpectationMet',
    'unittest.TestAssertionFailed',
    'unittest.TestError',
    'unittest.TestPrerequisitesNotMet',
    'unittest.TestNotRun',
    'unittest.TestWarning'
  );

  /**
   * Test TestOutcome implementations
   *
   * @see      xp://unittest.TestOutcome
   */
  class TestOutcomeTest extends TestCase {

    /**
     * Assertion helper
     *
     * @param  string expected format string, %s will be replaced by compound name
     * @param  unittest.TestOutcome outcome 
     * @throws unittest.AssertionFailedError
     */
    protected function assertStringRepresentation($expected, $outcome) {
      $this->assertEquals(
        sprintf($expected, $this->getClassName().'::'.$this->getName()),
        $outcome->toString()
      );
    }

    /**
     * Tests TestExpectationMet
     */    
    #[@test]
    public function string_representation_of_TestExpectationMet() {
      $this->assertStringRepresentation(
        'unittest.TestExpectationMet(test= %s, time= 0.000 seconds)',
        new TestExpectationMet($this, 0.0)
      );
    }

    /**
     * Tests TestAssertionFailed
     */    
    #[@test]
    public function string_representation_of_TestAssertionFailed() {
      $assert= new AssertionFailedError('Not equal', 1, 2);
      $this->assertStringRepresentation(
        "unittest.TestAssertionFailed(test= %s, time= 0.000 seconds) {\n  ".xp::stringOf($assert, '  ')."\n }",
        new TestAssertionFailed($this, $assert, 0.0)
      );
    }

    /**
     * Tests TestError
     */    
    #[@test]
    public function string_representation_of_TestError() {
      $error= new Error('Out of memory');
      $this->assertStringRepresentation(
        "unittest.TestError(test= %s, time= 0.000 seconds) {\n  ".xp::stringOf($error, '  ')."\n }",
        new TestError($this, $error, 0.0)
      );
    }

    /**
     * Tests TestPrerequisitesNotMet
     */    
    #[@test]
    public function string_representation_of_TestPrerequisitesNotMet() {
      $prerequisites= new PrerequisitesNotMetError('Initialization failed');
      $this->assertStringRepresentation(
        "unittest.TestPrerequisitesNotMet(test= %s, time= 0.000 seconds) {\n  ".xp::stringOf($prerequisites, '  ')."\n }",
        new TestPrerequisitesNotMet($this, $prerequisites, 0.0)
      );
    }

    /**
     * Tests TestNotRun
     */    
    #[@test]
    public function string_representation_of_TestNotRun() {
      $this->assertStringRepresentation(
        "unittest.TestNotRun(test= %s, time= 0.000 seconds) {\n  \"Ignored\"\n }",
        new TestNotRun($this, 'Ignored', 0.0)
      );
    }

    /**
     * Tests TestWarning
     */    
    #[@test]
    public function string_representation_of_TestWarning() {
      $this->assertStringRepresentation(
        "unittest.TestWarning(test= %s, time= 0.000 seconds) {\n  [\n    0 => \"Could not open file\"\n  ]\n }",
        new TestWarning($this, array('Could not open file'), 0.0)
      );
    }
  }
?>
