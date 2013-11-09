<?php namespace net\xp_framework\unittest\xml;
 
use lang\types\String;
use xml\Tree;

/**
 * Test XML Tree class
 *
 * @see   xp://net.xp_framework.unittest.xml.NodeTest 
 */
class TreeTest extends \unittest\TestCase {
  
  /**
   * Helper method which returns the XML representation of a Tree object,
   * trimmed of trailing \ns.
   *
   * @param   xml.Tree tree
   * @return  string
   */
  protected function sourceOf($tree, $mode= INDENT_DEFAULT) {
    return rtrim($tree->getSource($mode), "\n");
  }

  #[@test]
  public function emptyTree() {
    $this->assertEquals(
      '<root/>', 
      $this->sourceOf(new Tree('root'))
    );
  }

  #[@test]
  public function rootMember() {
    with ($t= new Tree('formresult'), $r= $t->root()); {
      $this->assertClass($r, 'xml.Node');
      $this->assertFalse($r->hasChildren());
      $this->assertEmpty($r->getAttributes());
      $this->assertEquals('formresult', $r->getName());
    }
  }

  #[@test]
  public function addChild() {
    $t= new Tree('tests');
    $child= new \xml\Node('test', 'success', array('name' => 'TreeTest'));
    $this->assertEquals($child, $t->addChild($child));
  }

  #[@test]
  public function fromString() {
    $t= Tree::fromString('
      <c:config xmlns:c="http://example.com/cfg/1.0">
        <attribute name="key">value</attribute>
      </c:config>
    ');
    
    with ($r= $t->root()); {
      $this->assertEquals('c:config', $r->getName());
      $this->assertTrue($r->hasAttribute('xmlns:c'));
      $this->assertEquals('http://example.com/cfg/1.0', $r->getAttribute('xmlns:c'));
      $this->assertEquals(1, sizeof($r->getChildren()));
    }      
    
    with ($c= $t->root()->nodeAt(0)); {
      $this->assertEquals('attribute', $c->getName());
      $this->assertTrue($c->hasAttribute('name'));
      $this->assertEquals('key', $c->getAttribute('name'));
      $this->assertEquals(0, sizeof($c->getChildren()));
      $this->assertEquals('value', $c->getContent());
    }
  }
  
  #[@test]
  public function fromStringEncodingIso88591() {
    $tree= Tree::fromString('<?xml version="1.0" encoding="ISO-8859-1"?>
      <document><node>Some umlauts: öäü</node></document>
    ');
    
    $this->assertEquals('iso-8859-1', $tree->getEncoding());
    $this->assertEquals(1, sizeof($tree->root()->getChildren()));
    $this->assertEquals('document', $tree->root()->getName());
    $this->assertEquals('Some umlauts: öäü', $tree->root()->nodeAt(0)->getContent());
  }

  #[@test]
  public function fromStringEncodingUTF8() {
    $tree= Tree::fromString('<?xml version="1.0" encoding="UTF-8"?>
      <document><node>Some umlauts: Ã¶Ã¤Ã¼</node></document>
    ');
    
    $this->assertEquals('iso-8859-1', $tree->getEncoding());
    $this->assertEquals(1, sizeof($tree->root()->getChildren()));
    $this->assertEquals('document', $tree->root()->getName());
    $this->assertEquals('Some umlauts: öäü', $tree->root()->nodeAt(0)->getContent());
  }

  #[@test]
  public function singleElement() {
    $tree= Tree::fromString('<document empty="false">Content</document>');
    $this->assertEquals(0, sizeof($tree->root()->getChildren()));
    $this->assertEquals('Content', $tree->root()->getContent());
    $this->assertEquals('false', $tree->root()->getAttribute('empty'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function fromNonXmlString() {
    Tree::fromString('@@NO-XML-HERE@@');
  }

  #[@test]
  public function utf8Encoding() {
    $t= create(new Tree('unicode'))->withEncoding('UTF-8');
    $t->root()->setContent('Hällo');

    $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $t->getDeclaration());
    $this->assertEquals('<unicode>HÃ¤llo</unicode>', $this->sourceOf($t));
  }

  #[@test]
  public function iso88591Encoding() {
    $t= create(new Tree('unicode'))->withEncoding('iso-8859-1');
    $t->root()->setContent('Hällo');

    $this->assertEquals('<?xml version="1.0" encoding="ISO-8859-1"?>', $t->getDeclaration());
    $this->assertEquals('<unicode>Hällo</unicode>', $this->sourceOf($t));
  }

  #[@test]
  public function utf8EncodingWithIso88591StringObject() {
    $t= create(new Tree('unicode'))->withEncoding('UTF-8');
    $t->root()->setContent(new String('Hällo', 'iso-8859-1'));

    $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $t->getDeclaration());
    $this->assertEquals('<unicode>HÃ¤llo</unicode>', $this->sourceOf($t));
  }

  #[@test]
  public function iso88591EncodingWithIso88591StringObject() {
    $t= create(new Tree('unicode'))->withEncoding('iso-8859-1');
    $t->root()->setContent(new String('Hällo', 'iso-8859-1'));

    $this->assertEquals('<?xml version="1.0" encoding="ISO-8859-1"?>', $t->getDeclaration());
    $this->assertEquals('<unicode>Hällo</unicode>', $this->sourceOf($t));
  }

  #[@test]
  public function utf8EncodingWithUtf8StringObject() {
    $t= create(new Tree('unicode'))->withEncoding('UTF-8');
    $t->root()->setContent(new String('HÃ¤llo', 'UTF-8'));

    $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $t->getDeclaration());
    $this->assertEquals('<unicode>HÃ¤llo</unicode>', $this->sourceOf($t));
  }

  #[@test]
  public function iso88591EncodingWithUtf8StringObject() {
    $t= create(new Tree('unicode'))->withEncoding('iso-8859-1');
    $t->root()->setContent(new String('HÃ¤llo', 'UTF-8'));

    $this->assertEquals('<?xml version="1.0" encoding="ISO-8859-1"?>', $t->getDeclaration());
    $this->assertEquals('<unicode>Hällo</unicode>', $this->sourceOf($t));
  }

  #[@test, @ignore('Performance testing')]
  public function performance() {
    $s= microtime(true);
    $t= new Tree();
    for ($i= 0; $i < 100; $i++) {
      $c= $t->addChild(new \xml\Node('child', null, array('id' => $i)));
      for ($j= 0; $j < 100; $j++) {
        $c->addChild(new \xml\Node('elements', str_repeat('x', $j)));
      }
    }
    $l= strlen($t->getSource(INDENT_NONE));
    printf('%d bytes, %.3f seconds', $l, microtime(true) - $s);
  }

  #[@test]
  public function parseIntoUtf8() {
    $tree= new Tree();
    create(new \xml\parser\XMLParser('utf-8'))->withCallback($tree)->parse(trim('
      <?xml version="1.0" encoding="UTF-8"?>
      <document><node>Some umlauts: Ã¶Ã¤Ã¼</node></document>
    '));
    
    $this->assertEquals('utf-8', $tree->getEncoding());
    $this->assertEquals(1, sizeof($tree->root()->getChildren()));
    $this->assertEquals('document', $tree->root()->getName());
    $this->assertEquals('Some umlauts: Ã¶Ã¤Ã¼', $tree->root()->nodeAt(0)->getContent());
  }

  #[@test]
  public function parseIntoIso() {
    $tree= new Tree();
    create(new \xml\parser\XMLParser('iso-8859-1'))->withCallback($tree)->parse(trim('
      <?xml version="1.0" encoding="UTF-8"?>
      <document><node>Some umlauts: Ã¶Ã¤Ã¼</node></document>
    '));
    
    $this->assertEquals('iso-8859-1', $tree->getEncoding());
    $this->assertEquals(1, sizeof($tree->root()->getChildren()));
    $this->assertEquals('document', $tree->root()->getName());
    $this->assertEquals('Some umlauts: öäü', $tree->root()->nodeAt(0)->getContent());
  }
}
