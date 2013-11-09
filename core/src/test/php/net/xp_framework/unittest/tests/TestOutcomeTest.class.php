<?php namespace net\xp_framework\unittest\tests;
 
use unittest\TestSuite;
use unittest\TestExpectationMet;
use unittest\TestAssertionFailed;
use unittest\TestError;
use unittest\TestPrerequisitesNotMet;
use unittest\TestNotRun;
use unittest\TestWarning;

/**
 * Test TestOutcome implementations
 *
 * @see      xp://unittest.TestOutcome
 */
class TestOutcomeTest extends \unittest\TestCase {

  /**
   * Creates fixtures
   *
   * @return unittest.TestCase[]
   */
  public function fixtures() {
    return array(
      array($this, ''),
      array(new \unittest\TestVariation($this, array('v')), '("v")')
    );
  }

  /**
   * Assertion helper
   *
   * @param  string expected format string, %s will be replaced by compound name
   * @param  unittest.TestOutcome outcome 
   * @throws unittest.AssertionFailedError
   */
  protected function assertStringRepresentation($expected, $outcome, $variant) {
    $this->assertEquals(
      sprintf($expected, $this->getClassName().'::'.$this->getName().$variant),
      $outcome->toString()
    );
  }

  #[@test, @values('fixtures')]
  public function string_representation_of_TestExpectationMet($test, $variant) {
    $this->assertStringRepresentation(
      'unittest.TestExpectationMet(test= %s, time= 0.000 seconds)',
      new TestExpectationMet($test, 0.0), $variant
    );
  }

  #[@test, @values('fixtures')]
  public function string_representation_of_TestAssertionFailed($test, $variant) {
    $assert= new \unittest\AssertionFailedError('Not equal', 1, 2);
    $this->assertStringRepresentation(
      "unittest.TestAssertionFailed(test= %s, time= 0.000 seconds) {\n  ".\xp::stringOf($assert, '  ')."\n }",
      new TestAssertionFailed($test, $assert, 0.0), $variant
    );
  }

  #[@test, @values('fixtures')]
  public function string_representation_of_TestError($test, $variant) {
    $error= new \lang\Error('Out of memory');
    $this->assertStringRepresentation(
      "unittest.TestError(test= %s, time= 0.000 seconds) {\n  ".\xp::stringOf($error, '  ')."\n }",
      new TestError($test, $error, 0.0), $variant
    );
  }

  #[@test, @values('fixtures')]
  public function string_representation_of_TestPrerequisitesNotMet($test, $variant) {
    $prerequisites= new \unittest\PrerequisitesNotMetError('Initialization failed');
    $this->assertStringRepresentation(
      "unittest.TestPrerequisitesNotMet(test= %s, time= 0.000 seconds) {\n  ".\xp::stringOf($prerequisites, '  ')."\n }",
      new TestPrerequisitesNotMet($test, $prerequisites, 0.0), $variant
    );
  }

  #[@test, @values('fixtures')]
  public function string_representation_of_TestNotRun($test, $variant) {
    $this->assertStringRepresentation(
      "unittest.TestNotRun(test= %s, time= 0.000 seconds) {\n  \"Ignored\"\n }",
      new TestNotRun($test, 'Ignored', 0.0), $variant
    );
  }

  #[@test, @values('fixtures')]
  public function string_representation_of_TestWarning($test, $variant) {
    $this->assertStringRepresentation(
      "unittest.TestWarning(test= %s, time= 0.000 seconds) {\n  [\n    0 => \"Could not open file\"\n  ]\n }",
      new TestWarning($test, array('Could not open file'), 0.0), $variant
    );
  }
}
