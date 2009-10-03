<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'xml.Node'
  );

  /**
   * Test XML Node class
   *
   * @see      xp://net.xp_framework.unittest.xml.TreeTest 
   * @purpose  Unit Test
   */
  class NodeTest extends TestCase {
    
    /**
     * Helper method which returns the XML representation of a Node object,
     * trimmed of trailing \ns.
     *
     * @param   xml.Node node
     * @return  string
     */
    protected function sourceOf($node, $mode= INDENT_DEFAULT) {
      return rtrim($node->getSource($mode), "\n");
    }
    
    /**
     * Tests attribute accessors
     *
     * @see     xp://xml.Node#setAttribute
     * @see     xp://xml.Node#getAttribute
     */
    #[@test]
    public function attributeAccessors() {
      $n= new Node('node');
      $n->setAttribute('id', 1);
      $this->assertTrue($n->hasAttribute('id'));
      $this->assertFalse($n->hasAttribute('href'));
      $this->assertEquals(1, $n->getAttribute('id'));
    }

    /**
     * Tests content accessors
     *
     * @see     xp://xml.Node#setContent
     * @see     xp://xml.Node#getContent
     */
    #[@test]
    public function contentAccessors() {
      $content= '"This is interesting", Tom\'s friend said. "It\'s > 4 but < 2!"';
      $n= new Node('node');
      $n->setContent($content);
      $this->assertEquals($content, $n->getContent());
    }
    
    /**
     * Tests name accessors
     *
     * @see     xp://xml.Node#setName
     * @see     xp://xml.Node#getName
     */
    #[@test]
    public function nameAccessors() {
      $n= new Node('node');
      $n->setName('name');
      $this->assertEquals('name', $n->getName());
    }
    
    /**
     * Tests that setContent() will throw an XMLFormatException in case the 
     * content contains illegal characters
     *
     * @see     xp://xml.Node#setContent
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function illegalContent() {
      $n= new Node('node');
      $n->setContent("\0");
    }
    
    /**
     * Tests that addChild() will throw an IllegalArgumentException in case the 
     * passed argument is not a node object
     *
     * @see     xp://xml.Node#addChild
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addingNullChild() {
      $n= new Node('node');
      $n->addChild($child= NULL);
    }

    /**
     * Tests that addChild() will return the child added
     *
     * @see     xp://xml.Node#addChild
     */
    #[@test]
    public function addingReturnsChild() {
      $n= new Node('node');
      $child= new Node('node');
      $this->assertEquals($child, $n->addChild($child));
    }

    /**
     * Tests that withChild() will return the node itself
     *
     * @see     xp://xml.Node#withChild
     */
    #[@test]
    public function withChildReturnsNode() {
      $n= new Node('node');
      $child= new Node('node');
      $this->assertEquals($n, $n->withChild($child));
    }
    
    /**
     * Tests that fromArray() will return an empty node when passed an empty array
     *
     * @see     xp://xml.Node#fromArray
     */
    #[@test]
    public function fromEmptyArray() {
      $this->assertEquals(
        '<node/>', 
        $this->sourceOf(Node::fromArray(array(), 'node'))
      );
    }

    /**
     * Tests fromArray() with an array of two numbers
     *
     * @see     xp://xml.Node#fromArray
     */
    #[@test]
    public function fromNumberArray() {
      $this->assertEquals(
        '<items><item>1</item><item>2</item></items>', 
        $this->sourceOf(Node::fromArray(array(1, 2), 'items'), INDENT_NONE)
      );
    }

    /**
     * Tests fromArray() with an array of characters
     *
     * @see     xp://xml.Node#fromArray
     */
    #[@test]
    public function fromCharacterArray() {
      $this->assertEquals(
        '<characters><character>1</character><character>&amp;</character><character>1</character></characters>', 
        $this->sourceOf(Node::fromArray(array('1', '&', '1'), 'characters'), INDENT_NONE)
      );
    }
    
    /**
     * Tests a node without attributes or content
     *
     */
    #[@test]
    public function sourceOfEmptyNode() {
      $this->assertEquals(
        '<node/>', 
        $this->sourceOf(new Node('node'))
      );
    }

    /**
     * Tests a node with one attribute
     *
     */
    #[@test]
    public function sourceOfNodeWithOneAttribute() {
      $this->assertEquals(
        '<node id="1"/>', 
        $this->sourceOf(new Node('node', NULL, array('id' => 1)))
      );
    }

    /**
     * Tests a node with two attributes
     *
     */
    #[@test]
    public function sourceOfNodeWithTwoAttributes() {
      $this->assertEquals(
        '<node id="2" name="&amp;XML"/>', 
        $this->sourceOf(new Node('node', NULL, array('id' => 2, 'name' => '&XML')))
      );
    }

    /**
     * Tests a node with content. Makes sure escaping of special characters
     * is performed as necessary.
     *
     */
    #[@test]
    public function sourceOfNodeWithContent() {
      $this->assertEquals(
        '<expr>eval(\'1 &lt;&gt; 2 &amp;&amp; \') == &quot;Parse Error&quot;</expr>', 
        $this->sourceOf(new Node('expr', 'eval(\'1 <> 2 && \') == "Parse Error"'))
      );
    }

    /**
     * Tests a node with CDATA content. 
     *
     */
    #[@test]
    public function sourceOfNodeWithCData() {
      $this->assertEquals(
        '<text><![CDATA[Special characters: <>"\'&]]></text>', 
        $this->sourceOf(new Node('text', new CData('Special characters: <>"\'&')))
      );
    }

    /**
     * Tests a node with PCDATA content. 
     *
     */
    #[@test]
    public function sourceOfNodeWithPCData() {
      $this->assertEquals(
        '<text>A <a href="http://xp-framework.net/">link</a> to click on</text>', 
        $this->sourceOf(new Node('text', new PCData('A <a href="http://xp-framework.net/">link</a> to click on')))
      );
    }
  }
?>
