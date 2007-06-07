<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.tools.vm.unittest.ParserTest');

  /**
   * TestCase for static initializer nodes
   *
   * @purpose  Unittest
   */
  class StaticInitializerParserTest extends ParserTest {
    
    /**
     * Tests an empty static initializer block
     *
     */
    #[@test]
    public function emptyStaticInitializer() {
      $nodes= $this->parse('class Bar { static { } }');
      $this->assertNode('ClassDeclaration', $nodes[0]);
      $this->assertNode('StaticInitializer', $nodes[0]->statements[0]);
      $this->assertArray($nodes[0]->statements[0]->block);
      $this->assertEquals(0, sizeof($nodes[0]->statements[0]->block));
    }

    /**
     * Tests a non-empty static initializer block
     *
     */
    #[@test]
    public function nonEmptyStaticInitializer() {
      $nodes= $this->parse('class Bar { static { echo "Hi"; } }');
      $this->assertNode('ClassDeclaration', $nodes[0]);
      $this->assertNode('StaticInitializer', $nodes[0]->statements[0]);
      $this->assertArray($nodes[0]->statements[0]->block);
      $this->assertEquals(1, sizeof($nodes[0]->statements[0]->block));
      $this->assertNode('Echo', $nodes[0]->statements[0]->block[0]);
    }

    /**
     * Tests static initializer vs. static method
     *
     */
    #[@test]
    public function versusStaticMethod() {
      $nodes= $this->parse('class Bar { static { } static void main($args); }');
      $this->assertNode('ClassDeclaration', $nodes[0]);
      $this->assertNode('StaticInitializer', $nodes[0]->statements[0]);
      $this->assertNode('MethodDeclaration', $nodes[0]->statements[1]);
    }
  }
?>
