<?php namespace xp\unittest\sources;

/**
 * Source
 */
abstract class AbstractSource extends \lang\Object {

  /**
   * Get all test cases
   *
   * @param   lang.XPClass class
   * @param   var[] arguments
   * @return  unittest.TestCase[]
   */
  public function testCasesInClass(\lang\XPClass $class, $arguments= null) {
  
    // Verify we were actually given a testcase class
    if (!$class->isSubclassOf('unittest.TestCase')) {
      throw new \lang\IllegalArgumentException('Given argument is not a TestCase class ('.\xp::stringOf($class).')');
    }
    
    // Add all tests cases
    $r= array();
    foreach ($class->getMethods() as $m) {
      $m->hasAnnotation('test') && $r[]= $class->getConstructor()->newInstance(array_merge(
        (array)$m->getName(true), 
        (array)$arguments
      ));
    }
    
    // Verify we actually added tests by doing this.
    if (empty($r)) {
      throw new \util\NoSuchElementException('No tests found in '.$class->getName());
    }
    return $r;
  }

  /**
   * Get all test cases
   *
   * @param   var[] arguments
   * @return  unittest.TestCase[]
   */
  public abstract function testCasesWith($arguments);
}
