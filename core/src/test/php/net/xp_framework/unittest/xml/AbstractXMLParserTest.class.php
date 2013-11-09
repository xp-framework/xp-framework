<?php namespace net\xp_framework\unittest\xml;

use unittest\TestCase;
use xml\parser\XMLParser;
use xml\parser\ParserCallback;
use xml\parser\InputSource;

/**
 * TestCase
 *
 * @see      xp://xml.parser.ParserCallback
 * @see      xp://xml.parser.XMLParser
 */
abstract class AbstractXMLParserTest extends TestCase {
  const NAME = 0;
  const ATTR = 1;
  const CHLD = 2;
  
  protected $parser= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->parser= new XMLParser();
  }
  
  /**
   * Tears down test case
   */
  public function tearDown() {
    delete($this->parser);
  }
  
  /**
   * Returns an XML document by prepending the XML declaration to 
   * the given string and returning it.
   *
   * @param   string str
   * @param   bool decl default TRUE
   * @return  xml.parser.InputSource XML the source XML
   */
  protected abstract function source($str, $decl= true);
  
  /**
   * Creates a new callback
   *
   * @return  xml.parser.ParserCallback
   */
  protected function newCallback() {
    return newinstance('xml.parser.ParserCallback', array(), '{
      protected
        $pointer  = array();
        
      public
        $tree     = NULL,
        $elements = array(),
        $encoding = NULL;
        
      public function onStartElement($parser, $name, $attrs) {
        $this->elements[]= $name;
        array_unshift($this->pointer, array($name, $attrs, array()));
      }

      public function onEndElement($parser, $name) {
        $e= array_shift($this->pointer);
        if (empty($this->pointer)) {
          $this->tree= $e;
        } else {
          $this->pointer[0][\net\xp_framework\unittest\xml\AbstractXMLParserTest::CHLD][]= $e;
        }
      }

      public function onCData($parser, $cdata) {
        $this->pointer[0][\net\xp_framework\unittest\xml\AbstractXMLParserTest::CHLD][]= trim($cdata);
      }

      public function onDefault($parser, $data) {
        $this->pointer[0][\net\xp_framework\unittest\xml\AbstractXMLParserTest::CHLD][]= trim($data);
      }

      public function onBegin($instance) {
        $this->encoding= $instance->getEncoding();
      }

      public function onError($instance, $exception) {
      }

      public function onFinish($instance) {
      }
    }');
  }

  #[@test]
  public function withoutDeclaration() {
    $this->assertTrue($this->parser->parse($this->source('<root/>', true)));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function emptyString() {
    $this->parser->parse($this->source('', false));
  }
  
  #[@test]
  public function withDeclaration() {
    $this->assertTrue($this->parser->parse($this->source('<root/>')));
  }

  #[@test]
  public function tree() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('<book>
      <author><name>Timm</name></author>
      <chapter id="1">
        <title>Introduction</title>
        <paragraph>
          This is where it all started.
        </paragraph>
      </chapter>
    </book>'));
    $this->assertEquals('book', $callback->tree[self::NAME]);
    $this->assertEquals(array(), $callback->tree[self::ATTR]);
    
    with ($author= $callback->tree[self::CHLD][1]); {
      $this->assertEquals('author', $author[self::NAME]);
      $this->assertEquals(array(), $author[self::ATTR]);
    
      with ($name= $author[self::CHLD][0]); {
        $this->assertEquals('name', $name[self::NAME]);
        $this->assertEquals(array(), $name[self::ATTR]);
        $this->assertEquals(array('Timm'), $name[self::CHLD]);
      }
    }

    with ($chapter= $callback->tree[self::CHLD][3]); {
      $this->assertEquals('chapter', $chapter[self::NAME]);
      $this->assertEquals(array('id' => '1'), $chapter[self::ATTR]);

      with ($title= $chapter[self::CHLD][1]); {
        $this->assertEquals('title', $title[self::NAME]);
        $this->assertEquals(array(), $title[self::ATTR]);
        $this->assertEquals(array('Introduction'), $title[self::CHLD]);
      }

      with ($paragraph= $chapter[self::CHLD][3]); {
        $this->assertEquals('paragraph', $paragraph[self::NAME]);
        $this->assertEquals(array(), $paragraph[self::ATTR]);
        $this->assertEquals(array('This is where it all started.'), $paragraph[self::CHLD]);
      }
    }
  }

  #[@test]
  public function reusable() {
    for ($i= 0; $i < 4; $i++) {
      $callback= $this->newCallback();
      $this->parser->setCallback($callback);
      $this->parser->parse($this->source('<run id="'.$i.'"/>'));
      $this->assertEquals(
        array('run', array('id' => (string)$i), array()), 
        $callback->tree, 
        'Run #'.$i
      );
    }
  }

  #[@test]
  public function errorOccursLate() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    try {
      $this->parser->parse($this->source('<doc><h1>Title</h1><p>Text</p><img></doc>'));
      $this->fail('Parsed without problems', null, 'xml.XMLFormatException');
    } catch (\xml\XMLFormatException $expected) {
      $this->assertEquals(null, $callback->tree, 'Tree only set if entire doc parsed');

      $this->assertEquals(4, sizeof($callback->elements));
      $this->assertEquals('doc', $callback->elements[0]);
      $this->assertEquals('h1', $callback->elements[1]);
      $this->assertEquals('p', $callback->elements[2]);
      $this->assertEquals('img', $callback->elements[3]);
    }
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function withoutRoot() {
    $this->parser->parse($this->source(''));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function unclosedTag() {
    $this->parser->parse($this->source('<a>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function unclosedAttribute() {
    $this->parser->parse($this->source('<a href="http://>Click</a>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function unclosedComment() {
    $this->parser->parse($this->source('<doc><!-- Comment</doc>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function incorrectlyClosedComment() {
    $this->parser->parse($this->source('<doc><!-- Comment ></doc>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function malformedComment() {
    $this->parser->parse($this->source('<doc><! Comment --></doc>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function unclosedProcessingInstruction() {
    $this->parser->parse($this->source('<doc><?php echo "1"; </doc>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function attributeRedefinition() {
    $this->parser->parse($this->source('<a id="1" id="2"/>'));
  }

  #[@test]
  public function quotesInsideAttributes() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('<n id="\'1\'" t=\'"_new"\' q="&apos;&quot;"/>'));
    $this->assertEquals(
      array('n', array('id' => "'1'", 't' => '"_new"', 'q' => '\'"'), array()),
      $callback->tree
    );
  }

  #[@test]
  public function greaterSignInAttribute() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('<a id=">"/>'));
    $this->assertEquals(
      array('a', array('id' => '>'), array()),
      $callback->tree
    );
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function smallerSignInAttribute() {
    $this->parser->parse($this->source('<a id="<"/>'));
  }

  #[@test]
  public function cdataSection() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <doc>CDATA [<![CDATA[ <&> ]]>]</doc>
    '));
    $this->assertEquals(array(
      'doc', array(), array(
        'CDATA [', '<&>', ']'
      )
    ), $callback->tree);
  }

  #[@test]
  public function processingInstruction() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('<doc><?php echo "1"; ?></doc>'));

    $this->assertEquals(array(
      'doc', array(), array(
        '<?php echo "1"; ?>'
      )
    ), $callback->tree);
  }

  #[@test]
  public function comment() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('<doc><!-- Comment --></doc>'));

    $this->assertEquals(array(
      'doc', array(), array(
        '<!-- Comment -->'
      )
    ), $callback->tree);
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function nestedCdata() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <doc><![CDATA[ <![CDATA[ ]]> ]]></doc>
    '));
  }

  #[@test]
  public function predefinedEntities() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <doc>&quot;3 &lt; 5 &apos;&amp;&apos; 5 &gt; 3&quot;</doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array(
        '"', '3', '<', '5', "'", '&', "'", '5', '>', '3', '"'
      )
    ), $callback->tree);
  }

  #[@test]
  public function hexEntity() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <doc>&#169; 2001-2009 the XP team</doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array(
        '©', '2001-2009 the XP team'
      )
    ), $callback->tree);
  }

  #[@test]
  public function iso88591Conversion() {
    $this->parser->setEncoding('iso-8859-1');
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <doc>The Ã¼bercoder returns</doc>
    '));

    $this->assertEquals('iso-8859-1', $callback->encoding);
    $this->assertEquals(array(
      'doc', array(), array(
        'The', 'übercoder returns'
      )
    ), $callback->tree);
  }

  #[@test]
  public function utf8Conversion() {
    $this->parser->setEncoding('utf-8');
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <doc>The Ã¼bercoder returns</doc>
    '));
    
    $this->assertEquals('utf-8', $callback->encoding);
    $this->assertEquals(array(
      'doc', array(), array(
        'The', 'Ã¼bercoder returns'
      )
    ), $callback->tree);
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function undeclaredEntity() {
    $this->parser->parse($this->source('<doc>&nbsp;</doc>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function undeclaredEntityInAttribute() {
    $this->parser->parse($this->source('<doc><a href="&nbsp;"/></doc>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function doubleRoot() {
    $this->parser->parse($this->source('<doc/><doc/>'));
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function docTypeWithoutContent() {
    $this->parser->parse($this->source('<!DOCTYPE doc ]>'));
  }

  #[@test]
  public function characterEntity() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <!DOCTYPE doc [ <!ENTITY copy "&#169;"> ]>
      <doc>Copyright: &copy;</doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array(
        'Copyright:', '&copy;'
      )
    ), $callback->tree);
  }

  #[@test]
  public function entity() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <!DOCTYPE doc [ <!ENTITY copyright "2009 The XP team"> ]>
      <doc>Copyright: &copyright;</doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array(
        'Copyright:', '&copyright;'
      )
    ), $callback->tree);
  }

  #[@test]
  public function entityInAttribute() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <!DOCTYPE doc [ <!ENTITY copyright "2009 The XP team"> ]>
      <doc><book copyright="Copyright &copyright;"/></doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array(
        array('book', array('copyright' => 'Copyright 2009 The XP team'), array()),
      )
    ), $callback->tree);
  }

  #[@test]
  public function entityExpansion() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <!DOCTYPE doc [ 
        <!ENTITY year "2009"> 
        <!ENTITY copyright "&year; The XP team"> 
      ]>
      <doc><book copyright="Copyright &copyright;"/></doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array(
        array('book', array('copyright' => 'Copyright 2009 The XP team'), array()),
      )
    ), $callback->tree);
  }

  #[@test]
  public function externalEntity() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <!DOCTYPE doc [ <!ENTITY copyright SYSTEM "http://xp-framework.net/copyright.txt" > ]>
      <doc>Copyright: &copyright;</doc>
    '));

    $this->assertEquals(array(
      'doc', array(), array('Copyright:'),
    ), $callback->tree);
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function externalEntityInAttribute() {
    $callback= $this->newCallback();
    $this->parser->setCallback($callback);
    $this->parser->parse($this->source('
      <!DOCTYPE doc [ <!ENTITY copyright SYSTEM "http://xp-framework.net/copyright.txt" > ]>
      <doc><book copyright="Copyright &copyright;"/></doc>
    '));
  }
}
