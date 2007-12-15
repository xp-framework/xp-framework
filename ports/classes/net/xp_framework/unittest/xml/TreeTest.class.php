<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'xml.Tree'
  );

  /**
   * Test XML Tree class
   *
   * @see      xp://net.xp_framework.unittest.xml.NodeTest 
   * @purpose  Unit Test
   */
  class TreeTest extends TestCase {
    
    /**
     * Helper method which returns the XML representation of a Tree object,
     * trimmed of trailing \ns.
     *
     * @param   &xml.Tree tree
     * @return  string
     */
    protected function sourceOf($tree, $mode= INDENT_DEFAULT) {
      return rtrim($tree->getSource($mode), "\n");
    }

    /**
     * Tests an empty tree (one with only a root node)
     *
     */
    #[@test]
    public function emptyTree() {
      $this->assertEquals(
        '<root/>', 
        $this->sourceOf(new Tree('root'))
      );
    }

    /**
     * Tests root member
     *
     */
    #[@test]
    public function rootMember() {
      with ($t= new Tree('formresult'), $r= $t->root); {
        $this->assertClass($r, 'xml.Node');
        $this->assertEmpty($r->children);
        $this->assertEmpty($r->attribute);
        $this->assertEquals('formresult', $r->getName());
      }
    }

    /**
     * Tests adding a child
     *
     */
    #[@test]
    public function addChild() {
      $t= new Tree('tests');
      $child= new Node('test', 'success', array('name' => 'TreeTest'));
      $this->assertEquals($child, $t->addChild($child));
    }

    /**
     * Tests fromString
     *
     */
    #[@test]
    public function fromString() {
      $t= Tree::fromString('
        <c:config xmlns:c="http://example.com/cfg/1.0">
          <attribute name="key">value</attribute>
        </c:config>
      ');
      
      with ($r= $t->root); {
        $this->assertEquals('c:config', $r->getName());
        $this->assertTrue($r->hasAttribute('xmlns:c'));
        $this->assertEquals('http://example.com/cfg/1.0', $r->getAttribute('xmlns:c'));
        $this->assertEquals(1, sizeof($r->children));
      }      
      
      with ($c= $t->root->children[0]); {
        $this->assertEquals('attribute', $c->name);
        $this->assertTrue($c->hasAttribute('name'));
        $this->assertEquals('key', $c->getAttribute('name'));
        $this->assertEquals(0, sizeof($c->children));
        $this->assertEquals('value', $c->getContent());
      }
    }
    
    /**
     * Tests xml is parsed to correct encoding
     *
     */
    #[@test]
    public function fromStringEncodingIso88591() {
      $tree= Tree::fromString('<?xml version="1.0" encoding="iso-8859-1"?>
        <document><node>Some umlauts: öäü</node></document>
      ');
      
      $this->assertEquals(1, sizeof($tree->root->children));
      $this->assertEquals('document', $tree->root->getName());
      $this->assertEquals('Some umlauts: öäü', $tree->root->children[0]->getContent());
    }

    /**
     * Tests xml is converted to iso-8859-1
     *
     */
    #[@test]
    public function fromStringEncodingUTF8() {
      $tree= Tree::fromString('<?xml version="1.0" encoding="UTF-8"?>
        <document><node>Some umlauts: Ã¶Ã¤Ã¼</node></document>
      ');
      
      $this->assertEquals(1, sizeof($tree->root->children));
      $this->assertEquals('document', $tree->root->getName());
      $this->assertEquals('Some umlauts: öäü', $tree->root->children[0]->getContent());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function singleElement() {
      $tree= Tree::fromString('<document empty="false">Content</document>');
      $this->assertEquals(0, sizeof($tree->root->children));
      $this->assertEquals('Content', $tree->root->getContent());
      $this->assertEquals('false', $tree->root->getAttribute('empty'));
    }

    /**
     * Tests fromString when given incorrect XML
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function fromNonXmlString() {
      Tree::fromString('@@NO-XML-HERE@@');
    }
  }
?>
