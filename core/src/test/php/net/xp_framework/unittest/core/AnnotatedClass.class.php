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

  }
?>
