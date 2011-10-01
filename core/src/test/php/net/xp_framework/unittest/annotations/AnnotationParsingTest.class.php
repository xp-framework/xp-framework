<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the XP Framework's annotation parsing implementation
   *
   * @see     rfc://0016
   * @see     xp://lang.XPClass#parseAnnotations
   * @see     http://bugs.xp-framework.net/show_bug.cgi?id=38
   * @see     https://github.com/xp-framework/xp-framework/issues/14
   * @see     https://github.com/xp-framework/xp-framework/pull/56
   * @see     https://gist.github.com/1240769
   */
  class AnnotationParsingTest extends TestCase {
  
    /**
     * Helper
     *
     * @param   string input
     * @return  [:var]
     */
    protected function parse($input) {
      return XPClass::parseAnnotations($input, $this->getClassName());
    }

    /**
     * Tests simple annotation without a value
     *
     */
    #[@test]
    public function noValue() {
      $this->assertEquals(
        array('hello' => NULL),
        $this->parse("#[@hello]")
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function sqStringValue() {
      $this->assertEquals(
        array('hello' => 'World'),
        $this->parse("#[@hello('World')]")
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function sqStringValueWithEqualsSign() {
      $this->assertEquals(
        array('hello' => 'World=Welt'),
        $this->parse("#[@hello('World=Welt')]")
      );
    }

    /**
     * Test string with at sign inside
     *
     */
    #[@test]
    public function sqStringValueWithAtSign() {
      $this->assertEquals(
        array('hello' => '@World'),
        $this->parse("#[@hello('@World')]")
      );
    }

    /**
     * Test string with an annotation inside a string
     *
     */
    #[@test]
    public function sqStringValueWithAnnotation() {
      $this->assertEquals(
        array('hello' => '@hello("World")'),
        $this->parse("#[@hello('@hello(\"World\")')]")
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function sqStringValueWithDoubleQuotes() {
      $this->assertEquals(
        array('hello' => 'said "he"'),
        $this->parse("#[@hello('said \"he\"')]")
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function sqStringValueWithEscapedSingleQuotes() {
      $this->assertEquals(
        array('hello' => "said 'he'"),
        $this->parse("#[@hello('said \'he\'')]")
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function dqStringValue() {
      $this->assertEquals(
        array('hello' => 'World'),
        $this->parse('#[@hello("World")]')
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function dqStringValueWithSingleQuote() {
      $this->assertEquals(
        array('hello' => 'Beck\'s'),
        $this->parse('#[@hello("Beck\'s")]')
      );
    }

    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function dqStringValueWithEscapedDoubleQuotes() {
      $this->assertEquals(
        array('hello' => 'said "he"'),
        $this->parse('#[@hello("said \"he\"")]')
      );
    }
    /**
     * Tests simple annotation with string value
     *
     */
    #[@test]
    public function dqStringValueWithEscapeSequence() {
      $this->assertEquals(
        array('hello' => "World\n"),
        $this->parse('#[@hello("World\n")]')
      );
    }

    /**
     * Test string with at sign inside
     *
     */
    #[@test]
    public function dqStringValueWithAtSign() {
      $this->assertEquals(
        array('hello' => '@World'),
        $this->parse('#[@hello("@World")]')
      );
    }

    /**
     * Test string with an annotation inside a string
     *
     */
    #[@test]
    public function dqStringValueWithAnnotation() {
      $this->assertEquals(
        array('hello' => '@hello(\'World\')'),
        $this->parse('#[@hello("@hello(\'World\')")]')
      );
    }

    /**
     * Tests simple annotation with an int value
     *
     */
    #[@test]
    public function intValue() {
      $this->assertEquals(
        array('answer' => 42),
        $this->parse('#[@answer(42)]')
      );
    }

    /**
     * Tests simple annotation with a double value
     *
     */
    #[@test]
    public function doubleValue() {
      $this->assertEquals(
        array('version' => 3.5),
        $this->parse('#[@version(3.5)]')
      );
    }

    /**
     * Tests simple annotation with a bool value
     *
     */
    #[@test]
    public function boolValue() {
      $this->assertEquals(
        array('supported' => TRUE),
        $this->parse('#[@supported(TRUE)]')
      );
    }

    /**
     * Tests different value types
     *
     */
    #[@test]
    public function keyValuePairsAnnotationValue() {
      $this->assertEquals(
        array('config' => array('key' => 'value', 'times' => 5, 'disabled' => FALSE, 'null' => NULL, 'list' => array(1, 2))), 
        $this->parse("#[@config(key = 'value', times= 5, disabled= FALSE, null = NULL, list= array(1, 2))]")
      );
    }

    /**
     * Tests multi-line annotations
     *
     */
    #[@test]
    public function multiLineAnnotation() {
      $this->assertEquals(
        array('interceptors' => array('classes' => array(
          'net.xp_framework.unittest.core.FirstInterceptor',
          'net.xp_framework.unittest.core.SecondInterceptor',
        ))),
        $this->parse("
          #[@interceptors(classes= array(
            'net.xp_framework.unittest.core.FirstInterceptor',
            'net.xp_framework.unittest.core.SecondInterceptor',
          ))]
        ")
      );
    }

    /**
     * Tests simple xpath annotations
     *
     */
    #[@test]
    public function simpleXPathAnnotation() {
      $this->assertEquals(
        array('fromXml' => array('xpath' => '/parent/child/@attribute')),
        $this->parse("#[@fromXml(xpath= '/parent/child/@attribute')]")
      );
    }

    /**
     * Tests complex xpath annotations
     *
     */
    #[@test]
    public function complexXPathAnnotation() {
      $this->assertEquals(
        array('fromXml' => array('xpath' => '/parent[@attr="value"]/child[@attr1="val1" and @attr2="val2"]')),
        $this->parse("#[@fromXml(xpath= '/parent[@attr=\"value\"]/child[@attr1=\"val1\" and @attr2=\"val2\"]')]")
      );
    }

    /**
     * Tests string default with "="
     *
     */
    #[@test]
    public function stringWithEqualSigns() {
      $this->assertEquals(
        array('permission' => 'rn=login, rt=config'),
        $this->parse("#[@permission('rn=login, rt=config')]")
      );
    }

    /**
     * Test string assignment without whitespace is parsed correctly.
     *
     */
    #[@test]
    public function stringAssignedWithoutWhitespace() {
      $this->assertEquals(
        array('arg' => array('name' => 'verbose', 'short' => 'v')),
        $this->parse("#[@arg(name= 'verbose', short='v')]")
      );
    }

    /**
     * Test annotation with mulitple values containing equal signs
     * is parsed correctly.
     *
     */
    #[@test]
    public function multipleValuesWithStringsAndEqualSigns() {
      $this->assertEquals(
        array('permission' => array('names' => array('rn=login, rt=config1', 'rn=login, rt=config2'))),
        $this->parse("#[@permission(names= array('rn=login, rt=config1', 'rn=login, rt=config2'))]")
      );
    }

    /**
     * Test unittest annotations
     *
     * @see   xp://unittest.TestCase
     */
    #[@test]
    public function unittestAnnotation() {
      $this->assertEquals(
        array('test' => NULL, 'ignore' => NULL, 'limit' => array('time' => 0.1, 'memory' => 100)),
        $this->parse("#[@test, @ignore, @limit(time = 0.1, memory = 100)]")
      );
    }

    /**
     * Test overloaded annotations
     *
     * @see   xp://lang.reflect.Proxy
     */
    #[@test]
    public function overloadedAnnotation() {
      $this->assertEquals(
        array('overloaded' => array('signatures' => array(array('string'), array('string', 'string')))),
        $this->parse('#[@overloaded(signatures= array(array("string"), array("string", "string")))]')
      );
    }
  }
?>
