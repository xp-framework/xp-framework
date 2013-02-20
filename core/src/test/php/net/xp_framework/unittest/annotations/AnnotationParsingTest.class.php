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
        array(0 => array('hello' => NULL), 1 => array()),
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
        array(0 => array('hello' => 'World'), 1 => array()),
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
        array(0 => array('hello' => 'World=Welt'), 1 => array()),
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
        array(0 => array('hello' => '@World'), 1 => array()),
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
        array(0 => array('hello' => '@hello("World")'), 1 => array()),
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
        array(0 => array('hello' => 'said "he"'), 1 => array()),
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
        array(0 => array('hello' => "said 'he'"), 1 => array()),
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
        array(0 => array('hello' => 'World'), 1 => array()),
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
        array(0 => array('hello' => 'Beck\'s'), 1 => array()),
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
        array(0 => array('hello' => 'said "he"'), 1 => array()),
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
        array(0 => array('hello' => "World\n"), 1 => array()),
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
        array(0 => array('hello' => '@World'), 1 => array()),
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
        array(0 => array('hello' => '@hello(\'World\')'), 1 => array()),
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
        array(0 => array('answer' => 42), 1 => array()),
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
        array(0 => array('version' => 3.5), 1 => array()),
        $this->parse('#[@version(3.5)]')
      );
    }

    /**
     * Tests simple annotation with multiple values
     *
     * @deprecated
     */
    #[@test]
    public function multiValueBackwardsCompatibility() {
      $this->assertEquals(
        array(0 => array('xmlmapping' => array('hw_server', 'server')), 1 => array()),
        $this->parse("#[@xmlmapping('hw_server', 'server')]")
      );
      xp::gc();
    }

    /**
     * Tests simple annotation with multiple values
     *
     * @deprecated
     */
    #[@test]
    public function multiValueBackwardsCompatibilityNoWhitespace() {
      $this->assertEquals(
        array(0 => array('xmlmapping' => array('hw_server', 'server')), 1 => array()),
        $this->parse("#[@xmlmapping('hw_server','server')]")
      );
      xp::gc();
    }

    /**
     * Tests simple annotation with multiple values
     *
     * @deprecated
     */
    #[@test]
    public function multiValueBackwardsCompatibilityMixedValue() {
      $this->assertEquals(
        array(0 => array('xmlmapping' => array('hw_server', TRUE)), 1 => array()),
        $this->parse("#[@xmlmapping('hw_server', TRUE)]")
      );
      xp::gc();
    }

    /**
     * Tests simple annotation with multiple values
     *
     */
    #[@test]
    public function multiValueUsingShortArray() {
      $this->assertEquals(
        array(0 => array('xmlmapping' => array('hw_server', 'server')), 1 => array()),
        $this->parse("#[@xmlmapping(['hw_server', 'server'])]")
      );
    }

    /**
     * Tests simple annotation with an array value
     *
     */
    #[@test]
    public function arrayValue() {
      $this->assertEquals(
        array(0 => array('versions' => array(3.4, 3.5)), 1 => array()),
        $this->parse('#[@versions(array(3.4, 3.5))]')
      );
    }

    /**
     * Tests simple annotation with an array value
     *
     */
    #[@test]
    public function arrayValueWithNestedArray() {
      $this->assertEquals(
        array(0 => array('versions' => array(array(3))), 1 => array()),
        $this->parse('#[@versions(array(array(3)))]')
      );
    }

    /**
     * Tests simple annotation with an array value
     *
     */
    #[@test]
    public function arrayValueWithNestedArrays() {
      $this->assertEquals(
        array(0 => array('versions' => array(array(3), array(4))), 1 => array()),
        $this->parse('#[@versions(array(array(3), array(4)))]')
      );
    }

    /**
     * Tests simple annotation with an array value
     *
     */
    #[@test]
    public function arrayValueWithStringsContainingBraces() {
      $this->assertEquals(
        array(0 => array('versions' => array('(3..4]')), 1 => array()),
        $this->parse('#[@versions(array("(3..4]"))]')
      );
    }

    /**
     * Tests simple annotation with a bool value
     *
     */
    #[@test]
    public function boolValue() {
      $this->assertEquals(
        array(0 => array('supported' => TRUE), 1 => array()),
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
        array(0 => array('config' => array('key' => 'value', 'times' => 5, 'disabled' => FALSE, 'null' => NULL, 'list' => array(1, 2))), 1 => array()), 
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
        array(0 => array('interceptors' => array('classes' => array(
          'net.xp_framework.unittest.core.FirstInterceptor',
          'net.xp_framework.unittest.core.SecondInterceptor',
        ))), 1 => array()),
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
        array(0 => array('fromXml' => array('xpath' => '/parent/child/@attribute')), 1 => array()),
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
        array(0 => array('fromXml' => array('xpath' => '/parent[@attr="value"]/child[@attr1="val1" and @attr2="val2"]')), 1 => array()),
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
        array(0 => array('permission' => 'rn=login, rt=config'), 1 => array()),
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
        array(0 => array('arg' => array('name' => 'verbose', 'short' => 'v')), 1 => array()),
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
        array(0 => array('permission' => array('names' => array('rn=login, rt=config1', 'rn=login, rt=config2'))), 1 => array()),
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
        array(0 => array('test' => NULL, 'ignore' => NULL, 'limit' => array('time' => 0.1, 'memory' => 100)), 1 => array()),
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
        array(0 => array('overloaded' => array('signatures' => array(array('string'), array('string', 'string')))), 1 => array()),
        $this->parse('#[@overloaded(signatures= array(array("string"), array("string", "string")))]')
      );
    }

    /**
     * Test webmethod annotation 
     *
     */
    #[@test]
    public function webMethodWithParameterAnnotations() {
      $this->assertEquals(
        array(
          0 => array('webmethod' => array('verb' => 'GET', 'path' => '/greet/{name}')),
          1 => array('$name' => array('path' => NULL), '$greeting' => array('param' => NULL))
        ),
        $this->parse('#[@webmethod(verb= "GET", path= "/greet/{name}"), @$name: path, @$greeting: param]')
      );
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting @/')]
    public function missingAnnotationAfterCommaAndValue() {
      $this->parse('#[@ignore("Test"), ]');
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting @/')]
    public function missingAnnotationAfterComma() {
      $this->parse('#[@ignore, ]');
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting @/')]
    public function missingAnnotationAfterSecondComma() {
      $this->parse('#[@ignore, @test, ]');
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated or malformed string/')]
    public function unterminatedString() {
      $this->parse('#[@ignore("Test)]');
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated array/')]
    public function unterminatedArray() {
      $this->parse('#[@ignore(array(1]');
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated array/')]
    public function unterminatedArrayKey() {
      $this->parse('#[@ignore(name = array(1]');
    }

    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
    public function malformedArray() {
      $this->parse('#[@ignore(array(1 ,, 2))]');
    }


    /**
     * Test broken annotation
     *
     */
    #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
    public function malformedArrayKey() {
      $this->parse('#[@ignore(name= array(1 ,, 2))]');
    }

    /**
     * Test short array syntax
     *
     */
    #[@test]
    public function shortArraySyntaxAsValue() {
      $this->assertEquals(
        array(0 => array('permissions' => array('rn=login, rt=config', 'rn=admin, rt=config')), 1 => array()),
        $this->parse("#[@permissions(['rn=login, rt=config', 'rn=admin, rt=config'])]")
      );
    }

    /**
     * Test short array syntax
     *
     */
    #[@test]
    public function shortArraySyntaxAsKey() {
      $this->assertEquals(
        array(0 => array('permissions' => array('names' => array('rn=login, rt=config', 'rn=admin, rt=config'))), 1 => array()),
        $this->parse("#[@permissions(names = ['rn=login, rt=config', 'rn=admin, rt=config'])]")
      );
    }

  }
?>
