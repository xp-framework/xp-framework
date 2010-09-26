<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests operator overloading functionality at runtime
   *
   */
  class net·xp_lang·tests·execution·source·OperatorOverloadingTest extends ExecutionTest {
    
    /**
     * Test
     *
     */
    #[@test]
    public function sprintf() {
      $this->assertEquals('Hello World', $this->run(
        '$s= new StringBuffer("Hello %s") % "World"; return $s.getBytes();',
        array('import net.xp_lang.tests.execution.source.StringBuffer;')
      ));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function concatOverloading() {
      $this->assertEquals('HelloWorld', $this->run(
        '$s= new StringBuffer("Hello") ~ "World"; return $s.getBytes();',
        array('import net.xp_lang.tests.execution.source.StringBuffer;')
      ));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function concatEqualsOverloading() {
      $this->assertEquals('HelloWorld', $this->run(
        '$s= new StringBuffer("Hello"); $s~= "World"; return $s.getBytes();',
        array('import net.xp_lang.tests.execution.source.StringBuffer;')
      ));
    }
  }
?>
