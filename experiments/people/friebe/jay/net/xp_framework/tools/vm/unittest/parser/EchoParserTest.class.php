<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.tools.vm.unittest.ParserTest');

  /**
   * TestCase for echo nodes
   *
   * @purpose  Unittest
   */
  class EchoParserTest extends ParserTest {
    
    /**
     * Tests parsing a simple "Hello, World!" script
     *
     */
    #[@test]
    public function helloWorld() {
      $nodes= $this->parse('echo "Hello World!\n";');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Echo', $nodes[0]);
      $this->assertEquals('"Hello World!\n"', $nodes[0]->args[0]);
    }

    /**
     * Tests parsing an echo statement with multiple args
     *
     */
    #[@test]
    public function multipleArgs() {
      $nodes= $this->parse('echo 1, 2, 3;');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Echo', $nodes[0]);
      $this->assertEquals(3, sizeof($nodes[0]->args));

      foreach ($nodes[0]->args as $i => $arg) {
        $this->assertNode('LongNumber', $arg);
        $this->assertEquals($i+ 1, (int)$arg->value);
      }
    }
  }
?>
