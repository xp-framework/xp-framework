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

    /**
     * Tests $a instanceof $string->type
     *
      */
    #[@test]
    public function variableInstanceOfMember() {
      $nodes= $this->parse('class String { } $a instanceof $string->type;');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('Variable', $nodes[1]->object);
      $this->assertEquals('$a', $nodes[1]->object->name);
      $this->assertNode('ObjectReference', $nodes[1]->type);
      $this->assertNode('Variable', $nodes[1]->type->class);
      $this->assertEquals('$string', $nodes[1]->type->class->name);
      $this->assertNode('Member', $nodes[1]->type->member);
      $this->assertEquals('type', $nodes[1]->type->member->name);
    }

    /**
     * Tests $a instanceof $string->type->name
     *
      */
    #[@test]
    public function variableInstanceOfMemberChain() {
      $nodes= $this->parse('class String { } $a instanceof $string->type->name;');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('Variable', $nodes[1]->object);
      $this->assertEquals('$a', $nodes[1]->object->name);
      $this->assertNode('ObjectReference', $nodes[1]->type);
      $this->assertNode('Variable', $nodes[1]->type->class);
      $this->assertEquals('$string', $nodes[1]->type->class->name);
      $this->assertNode('Member', $nodes[1]->type->member);
      $this->assertEquals('type', $nodes[1]->type->member->name);
      $this->assertEquals(1, sizeof($nodes[1]->type->chain));
      $this->assertNode('ObjectReference', $nodes[1]->type->chain[0]);
      $this->assertNull($nodes[1]->type->chain[0]->class);
      $this->assertNode('Member', $nodes[1]->type->chain[0]->member);
      $this->assertEquals('name', $nodes[1]->type->chain[0]->member->name);
    }

    /**
     * Tests $a instanceof $types[0]
     *
      */
    #[@test]
    public function variableInstanceOfArray() {
      $nodes= $this->parse('class String { } $a instanceof $types[0];');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('Variable', $nodes[1]->object);
      $this->assertEquals('$a', $nodes[1]->object->name);
      $this->assertNode('ArrayAccess', $nodes[1]->type);
      $this->assertNode('Variable', $nodes[1]->type->expression);
      $this->assertEquals('$types', $nodes[1]->type->expression->name);
      $this->assertNode('LongNumber', $nodes[1]->type->offset);
      $this->assertEquals(0, (int)$nodes[1]->type->offset->value);
    }

    /**
     * Tests $a instanceof $types["name"]
     *
      */
    #[@test]
    public function variableInstanceOfHashLookup() {
      $nodes= $this->parse('class String { } $a instanceof $types["name"];');
      $this->assertEquals(2, sizeof($nodes));
      $this->assertNode('InstanceOf', $nodes[1]);
      $this->assertNode('Variable', $nodes[1]->object);
      $this->assertEquals('$a', $nodes[1]->object->name);
      $this->assertNode('ArrayAccess', $nodes[1]->type);
      $this->assertNode('Variable', $nodes[1]->type->expression);
      $this->assertEquals('$types', $nodes[1]->type->expression->name);
      $this->assertEquals('"name"', $nodes[1]->type->offset);
    }
  }
?>
