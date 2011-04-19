<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.unittest.sources';

  /**
   * Source
   *
   * @purpose  Abstract base class
   */
  abstract class xp·unittest·sources·AbstractSource extends Object {

    /**
     * Get all test cases
     *
     * @param   lang.XPClass class
     * @param   var[] arguments
     * @return  unittest.TestCase[]
     */
    public function testCasesInClass(XPClass $class, $arguments= NULL) {
    
      // Verify we were actually given a testcase class
      if (!$class->isSubclassOf('unittest.TestCase')) {
        throw new IllegalArgumentException('Given argument is not a TestCase class ('.xp::stringOf($class).')');
      }
      
      // Add all tests cases
      $r= array();
      foreach ($class->getMethods() as $m) {
        $m->hasAnnotation('test') && $r[]= $class->getConstructor()->newInstance(array_merge(
          (array)$m->getName(TRUE), 
          (array)$arguments
        ));
      }
      
      // Verify we actually added tests by doing this.
      if (empty($r)) {
        throw new NoSuchElementException('No tests found in '.$class->getName());
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
?>
