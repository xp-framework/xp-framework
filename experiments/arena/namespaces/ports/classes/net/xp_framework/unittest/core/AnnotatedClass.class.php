<?php
/* This class is part of the XP framework
 *
 * $Id: AnnotatedClass.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  /**
   * Helper class for AnnotationTest
   *
   * @see      xp://net.xp_framework.unittest.core.AnnotationTest
   * @purpose  Helper
   */
  class AnnotatedClass extends lang::Object {

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
    #[@fromXml(xpath= '/parent[@attr="value"]/child[@attr1="val1" and @attr2="val2"')]
    public function complexXPath() { }
  }
?>
