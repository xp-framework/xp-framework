<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests with statement
   *
   */
  class net·xp_lang·tests·execution·source·WithTest extends ExecutionTest {
    
    /**
     * Test
     *
     */
    #[@test]
    public function oneAssignment() {
      $this->assertEquals('child', $this->run('with ($n= new xml.Node("root").addChild(new xml.Node("child"))) { 
        return $n.getName(); 
      }'));
    }
  }
?>
