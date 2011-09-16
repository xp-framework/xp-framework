<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Helper class for AnnotationTest
   *
   * @see      xp://net.xp_framework.unittest.core.AnnotationTest
   * @purpose  Helper
   */
  class AnnotatedClass extends Object {

    /**
     * Method annotated with one simple annotation
     *
     */
    #[@simple]
    public function simple() { }

    /**
     * Method annotated with more than one annotation
     *
     */
    #[@one, @two, @three]
    public function multiple() { }

    /**
     * Method annotated with an annotation with a string value
     *
     */
    #[@strval('String value')]
    public function stringValue() { }

    /**
     * Method annotated with an annotation with a hash value containing one
     * key/value pair
     *
     */
    #[@config(key = 'value')]
    public function keyValuePair() { }

    /**
     * Unittest method annotated with @test, @ignore and @limit
     *
     */
    #[@test, @ignore, @limit(time = 0.1, memory = 100)]
    public function testMethod() { }

    /**
     * Method annotated with an annotation with a hash value containing 
     * multiple key/value pairs
     *
     */
    #[@config(key = 'value', times= 5, disabled= FALSE, null = NULL, list= array(1, 2))]
    public function keyValuePairs() { }

    /**
     * Method annotated with a multi-line annotation
     *
     */
    #[@interceptors(classes= array(
    #  'net.xp_framework.unittest.core.FirstInterceptor',
    #  'net.xp_framework.unittest.core.SecondInterceptor',
    #))]
    public function multiLine() { }

    /**
     * Method annotated with a simple xpath expression
     *
     */
    #[@fromXml(xpath= '/parent/child/@attribute')]
    public function simpleXPath() { }

    /**
     * Method annotated with a complex xpath expression
     *
     */
    #[@fromXml(xpath= '/parent[@attr="value"]/child[@attr1="val1" and @attr2="val2"]')]
    public function complexXPath() { }

    /**
     * Method annotated with a string default containing "=" signs
     *
     * @see   http://bugs.xp-framework.net/show_bug.cgi?id=38
     */
    #[@permission('rn=login, rt=config')]
    public function stringWithEqualSigns() { }
    
    /**
     * Method annotated with a string, w/o whitespace in assignment
     * 
     */
    #[@arg(name= 'verbose', short='v')]
    public function stringAssignedWithoutWhitespace() {}

    /**
     * Method annotated with multiple values which contains equal signs
     *
     */
    #[@permission(names= array('rn=login, rt=config1', 'rn=login, rt=config2'))]
    public function multipleValuesWithStringsAndEqualSigns() { }

    /**
     * Method annotated with multiple string values
     *
     */
    #[@permission('rn=login, rt=config1', 'rn=login, rt=config2')]
    public function multipleStringValues() { }

    /**
     * Method annotated with multiple values but without using the 'array' keyword
     *
     */
    #[@function(arguments= ('arg1', 'arg2'))]
    public function multipleWithoutArrayKeyword() { }
  }
?>
