<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.tools.vm.unittest.ParserTest');

  /**
   * TestCase for instanceof nodes
   *
   * @purpose  Unittest
   */
  class InstanceOfParserTest extends ParserTest {
    
    /**
     * Tests $a instanceof Type
     *
      */
    #[@test]
    public function variableInstanceOfName() {
      $nodes= $this->parse('class String { } $a instanceof String;');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('Variable', $nodes[1]->object);
      $this->assertEquals('$a', $nodes[1]->object->name);
      $this->assertNode('ClassReference', $nodes[1]->type);
      $this->assertEquals('String', $nodes[1]->type->name);
    }

    /**
     * Tests $a instanceof $type
     *
      */
    #[@test]
    public function expressionInstanceOfName() {
      $nodes= $this->parse('class String { } get_class($a) instanceof String;');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('FunctionCall', $nodes[1]->object);
      $this->assertEquals('get_class', $nodes[1]->object->name);
      $this->assertNode('ClassReference', $nodes[1]->type);
      $this->assertEquals('String', $nodes[1]->type->name);
    }

    /**
     * Tests $a instanceof $type
     *
      */
    #[@test]
    public function variableInstanceOfVariable() {
      $nodes= $this->parse('class String { } $a instanceof $name;');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('Variable', $nodes[1]->object);
      $this->assertEquals('$a', $nodes[1]->object->name);
      $this->assertNode('Variable', $nodes[1]->type);
      $this->assertEquals('$name', $nodes[1]->type->name);
    }
  }
?>
