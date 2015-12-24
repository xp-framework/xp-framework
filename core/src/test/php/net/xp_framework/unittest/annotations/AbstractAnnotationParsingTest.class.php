<?php namespace net\xp_framework\unittest\annotations;

use lang\reflect\ClassParser;

/**
 * Base class for parent access tests
 */
abstract class AbstractAnnotationParsingTest extends \unittest\TestCase {
  const PARENTS_CONSTANT = 'constant';
  public static $parentsExposed = 'exposed';
  protected static $parentsHidden = 'hidden';
  private static $parentsInternal = 'internal';

  /**
   * Helper
   *
   * @param   string input
   * @return  [:var]
   */
  protected function parse($input) {
    $parser= new ClassParser();
    return $parser->parseAnnotations($input, $this->getClassName(), array(
      'Namespaced' => 'net.xp_framework.unittest.annotations.fixture.Namespaced'
    ));
  }
}
