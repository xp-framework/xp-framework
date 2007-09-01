<?php
/* This class is part of the XP framework
 *
 * $Id: TreeTest.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace net::xp_framework::unittest::xml;
 
  ::uses(
    'unittest.TestCase',
    'xml.Tree'
  );

  /**
   * Test XML Tree class
   *
   * @see      xp://net.xp_framework.unittest.xml.NodeTest 
   * @purpose  Unit Test
   */
  class TreeTest extends unittest::TestCase {
    
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
        $this->sourceOf(new xml::Tree('root'))
      );
    }

    /**
     * Tests root member
     *
     */
    #[@test]
    public function rootMember() {
      ::with ($t= new xml::Tree('formresult'), $r= $t->root); {
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
      $t= new xml::Tree('tests');
      $child= new xml::Node('test', 'success', array('name' => 'TreeTest'));
      $this->assertEquals($child, $t->addChild($child));
    }

    /**
     * Tests fromString
     *
     */
    #[@test]
    public function fromString() {
      $t= xml::Tree::fromString('
        <c:config xmlns:c="http://example.com/cfg/1.0">
          <attribute name="key">value</attribute>
        </c:config>
      ');
      
      ::with ($r= $t->root); {
        $this->assertEquals('c:config', $r->getName());
        $this->assertTrue($r->hasAttribute('xmlns:c'));
        $this->assertEquals('http://example.com/cfg/1.0', $r->getAttribute('xmlns:c'));
        $this->assertEquals(1, sizeof($r->children));
      }      
      
      ::with ($c= $t->root->children[0]); {
        $this->assertEquals('attribute', $c->name);
        $this->assertTrue($c->hasAttribute('name'));
        $this->assertEquals('key', $c->getAttribute('name'));
        $this->assertEquals(0, sizeof($c->children));
        $this->assertEquals('value', $c->getContent());
      }
    }

    /**
     * Tests fromString when given incorrect XML
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function fromNonXmlString() {
      xml::Tree::fromString('@@NO-XML-HERE@@');
    }
  }
?>
