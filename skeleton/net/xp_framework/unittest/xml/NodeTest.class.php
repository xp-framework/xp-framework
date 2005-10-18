<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'xml.Node'
  );

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class NodeTest extends TestCase {
    
    /**
     * Helper method which returns the XML representation of a Node object,
     * trimmed of trailing \ns.
     *
     * @access  protected
     * @param   &xml.Node node
     * @return  string
     */
    function sourceOf(&$node) {
      return rtrim($node->getSource(INDENT_DEFAULT), "\n");
    }
    
    /**
     * Tests attribute accessors
     *
     * @see     xp://xml.Node#setAttribute
     * @see     xp://xml.Node#getAttribute
     * @access  public
     */
    #[@test]
    function attributeAccessors() {
      $n= &new Node();
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
     * @access  public
     */
    #[@test]
    function contentAccessors() {
      $content= '"This is interesting", Tom\'s friend said. "It\'s > 4 but < 2!"';
      $n= &new Node();
      $n->setContent($content);
      $this->assertEquals($content, $n->getContent());
    }
    
    /**
     * Tests name accessors
     *
     * @see     xp://xml.Node#setName
     * @see     xp://xml.Node#getName
     * @access  public
     */
    #[@test]
    function nameAccessors() {
      $n= &new Node();
      $n->setName('name');
      $this->assertEquals('name', $n->getName());
    }
    
    /**
     * Tests that setContent() will throw an XMLFormatException in case the 
     * content contains illegal characters
     *
     * @see     xp://xml.Node#setContent
     * @access  public
     */
    #[@test, @expect('xml.XMLFormatException')]
    function illegalContent() {
      $n= &new Node();
      $n->setContent("\0");
    }
    
    /**
     * Tests that addChild() will throw an IllegalArgumentException in case the 
     * passed argument is not a node object
     *
     * @see     xp://xml.Node#addChild
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addingNullChild() {
      $n= &new Node();
      $n->addChild($child= NULL);
    }

    /**
     * Tests that addChild() will return the child added
     *
     * @see     xp://xml.Node#addChild
     * @access  public
     */
    #[@test]
    function addingReturnsChild() {
      $n= &new Node();
      $child= &new Node();
      $this->assertEquals($child, $n->addChild($child));
    }
    
    /**
     * Tests a node without attributes or content
     *
     * @access  public
     */
    #[@test]
    function sourceOfEmptyNode() {
      $this->assertEquals(
        '<node/>', 
        $this->sourceOf(new Node('node'))
      );
    }

    /**
     * Tests a node with one attribute
     *
     * @access  public
     */
    #[@test]
    function sourceOfNodeWithOneAttribute() {
      $this->assertEquals(
        '<node id="1"/>', 
        $this->sourceOf(new Node('node', NULL, array('id' => 1)))
      );
    }

    /**
     * Tests a node with two attributes
     *
     * @access  public
     */
    #[@test]
    function sourceOfNodeWithTwoAttributes() {
      $this->assertEquals(
        '<node id="2" name="&amp;XML"/>', 
        $this->sourceOf(new Node('node', NULL, array('id' => 2, 'name' => '&XML')))
      );
    }

    /**
     * Tests a node with content. Makes sure escaping of special characters
     * is performed as necessary.
     *
     * @access  public
     */
    #[@test]
    function sourceOfNodeWithContent() {
      $this->assertEquals(
        '<expr>eval(\'1 &lt;&gt; 2 &amp;&amp; \') == &quot;Parse Error&quot;</expr>', 
        $this->sourceOf(new Node('expr', 'eval(\'1 <> 2 && \') == "Parse Error"'))
      );
    }

    /**
     * Tests a node with CDATA content. 
     *
     * @access  public
     */
    #[@test]
    function sourceOfNodeWithCData() {
      $this->assertEquals(
        '<text><![CDATA[Special characters: <>"\'&]]></text>', 
        $this->sourceOf(new Node('text', new CData('Special characters: <>"\'&')))
      );
    }
  }
?>
