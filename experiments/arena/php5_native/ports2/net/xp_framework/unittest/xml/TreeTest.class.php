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
     * @access  protected
     * @param   &xml.Tree tree
     * @return  string
     */
    public function sourceOf(&$tree, $mode= INDENT_DEFAULT) {
      return rtrim($tree->getSource($mode), "\n");
    }

    /**
     * Tests an empty tree (one with only a root node)
     *
     * @access  public
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
     * @access  public
     */
    #[@test]
    public function rootMember() {
      with ($t= new Tree('formresult'), $r= &$t->root); {
        $this->assertClass($r, 'xml.Node');
        $this->assertEmpty($r->children);
        $this->assertEmpty($r->attribute);
        $this->assertEquals('formresult', $r->getName());
      }
    }

    /**
     * Tests adding a child
     *
     * @access  public
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
     * @access  public
     */
    #[@test]
    public function fromString() {
      $t= &Tree::fromString('
        <c:config xmlns:c="http://example.com/cfg/1.0">
          <attribute name="key">value</attribute>
        </c:config>
      ');
      
      with ($r= &$t->root); {
        $this->assertEquals('c:config', $r->getName());
        $this->assertTrue($r->hasAttribute('xmlns:c'));
        $this->assertEquals('http://example.com/cfg/1.0', $r->getAttribute('xmlns:c'));
        $this->assertEquals(1, sizeof($r->children));
      }      
      
      with ($c= &$t->root->children[0]); {
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
     * @access  public
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function fromNonXmlString() {
      Tree::fromString('@@NO-XML-HERE@@');
    }
  }
?>
