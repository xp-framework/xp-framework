<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests using native classes
   *
   */
  class net·xp_lang·tests·execution·source·NativeClassUsageTest extends ExecutionTest {
  
    /**
     * Test PHP's ReflectionClass
     *
     */
    #[@test]
    public function reflectionClass() {
      $this->assertEquals(
        'ReflectionClass', 
        $this->run('$r= new php.reflection.ReflectionClass("ReflectionClass"); return $r.getName();')
      );
    }
  }
?>
