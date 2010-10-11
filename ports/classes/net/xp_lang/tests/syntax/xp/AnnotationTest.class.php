<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.xp';

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·xp·AnnotationTest extends ParserTestCase {
  
    /**
     * Parse method annotations and return annotations
     *
     * @param   string annotations
     * @return  xp.compiler.Node[]
     */
    protected function parseMethodWithAnnotations($annotations) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer('abstract class Container {
        '.$annotations.'
        public abstract void method();
      }', '<string:'.$this->name.'>'))->declaration->body[0]->annotations;
    }

    /**
     * Test no annotation
     *
     */
    #[@test]
    public function noAnnotation() {
      $this->assertEquals(NULL, $this->parseMethodWithAnnotations(''));
    }

    /**
     * Test simple annotation (Test)
     *
     */
    #[@test]
    public function simpleAnnotation() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Test'
      ))), $this->parseMethodWithAnnotations('[@Test]'));
    }

    /**
     * Test simple annotation (Test)
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function simpleAnnotationWithBrackets() {
      $this->parseMethodWithAnnotations('[@Test()]');
    }

    /**
     * Test annotation with default value (Expect("lang.IllegalArgumentException"))
     *
     */
    #[@test]
    public function annotationWithStringValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Expect',
        'parameters'    => array('default' => new StringNode('lang.IllegalArgumentException'))
      ))), $this->parseMethodWithAnnotations('[@Expect("lang.IllegalArgumentException")]'));
    }

    /**
     * Test annotation with default value (Limit(5)))
     *
     */
    #[@test]
    public function annotationWithIntegerValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Limit',
        'parameters'    => array('default' => new IntegerNode('5'))
      ))), $this->parseMethodWithAnnotations('[@Limit(5)]'));
    }

    /**
     * Test annotation with default value (Limit(0x5)))
     *
     */
    #[@test]
    public function annotationWithHexValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Limit',
        'parameters'    => array('default' => new HexNode('0x5'))
      ))), $this->parseMethodWithAnnotations('[@Limit(0x5)]'));
    }

    /**
     * Test annotation with default value (Limit(5.0)))
     *
     */
    #[@test]
    public function annotationWithDecimalValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Limit',
        'parameters'    => array('default' => new DecimalNode('5.0'))
      ))), $this->parseMethodWithAnnotations('[@Limit(5.0)]'));
    }

    /**
     * Test annotation with default value (Limit(null)))
     *
     */
    #[@test]
    public function annotationWithNullValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Limit',
        'parameters'    => array('default' => new NullNode())
      ))), $this->parseMethodWithAnnotations('[@Limit(null)]'));
    }

    /**
     * Test annotation with default value (Limit(true)))
     *
     */
    #[@test]
    public function annotationWithTrueValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Limit',
        'parameters'    => array('default' => new BooleanNode(TRUE))
      ))), $this->parseMethodWithAnnotations('[@Limit(true)]'));
    }

    /**
     * Test annotation with default value (Limit(false)))
     *
     */
    #[@test]
    public function annotationWithFalseValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Limit',
        'parameters'    => array('default' => new BooleanNode(FALSE))
      ))), $this->parseMethodWithAnnotations('[@Limit(false)]'));
    }

    /**
     * Test annotation with default value (Restrict(["Admin", "Root"]))
     *
     */
    #[@test]
    public function annotationWithArrayValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Restrict',
        'parameters'    => array('default' => new ArrayNode(array(
          'values'        => array(
            new StringNode('Admin'),
            new StringNode('Root'),
          ),
          'type'          => NULL
        )))
      ))), $this->parseMethodWithAnnotations('[@Restrict(["Admin", "Root"])]'));
    }

    /**
     * Test annotation with default value (Restrict([Role : "Root"]))
     *
     */
    #[@test]
    public function annotationWithMapValue() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Restrict',
        'parameters'    => array('default' => new MapNode(array(
          'elements'      => array(array(
            new StringNode('Role'),
            new StringNode('Root'),
          )),
          'type'          => NULL
        )))
      ))), $this->parseMethodWithAnnotations('[@Restrict([Role : "Root"])]'));
    }

    /**
     * Test annotation with key/value pairs (Expect(classes = [...], code = 503))
     *
     */
    #[@test]
    public function annotationWithValues() {
      $this->assertEquals(array(new AnnotationNode(array(
        'type'          => 'Expect',
        'parameters'    => array(
          'classes' => new ArrayNode(array(
            'values'        => array(
              new StringNode('lang.IllegalArgumentException'),
              new StringNode('lang.IllegalAccessException'),
            ),
            'type'          => NULL
          )),
          'code'    => new IntegerNode('503'),
        )))
      ), $this->parseMethodWithAnnotations('[@Expect(
        classes = ["lang.IllegalArgumentException", "lang.IllegalAccessException"],
        code    = 503
      )]'));
    }

    /**
     * Test multiple annotations (WebMethod, Deprecated)
     *
     */
    #[@test]
    public function multipleAnnotations() {
      $this->assertEquals(array(
        new AnnotationNode(array('type' => 'WebMethod')),
        new AnnotationNode(array('type' => 'Deprecated')),
      ), $this->parseMethodWithAnnotations('[@WebMethod, @Deprecated]'));
    }
  }
?>
