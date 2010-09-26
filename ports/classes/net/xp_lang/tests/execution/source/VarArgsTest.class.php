<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests varargs
   *
   * @see   http://java.sun.com/j2se/1.5.0/docs/guide/language/varargs.html
   */
  class net·xp_lang·tests·execution·source·VarArgsTest extends ExecutionTest {

    /**
     * Test 
     *
     */
    #[@test]
    public function intArray() {
      $class= $this->define('class', $this->name, NULL, '{
        public int[] $values;
        
        public __construct(int... $values) {
          $this.values= $values;
        }
      }');
      $this->assertEquals(array(1, 2, 3), $class->newInstance(1, 2, 3)->values);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function stringFormat() {
      $class= $this->define('class', $this->name, NULL, '{
        public static string format(string $f, var... $args) {
          return vsprintf($f, $args);
        }
      }', array('import native standard.vsprintf;'));

      $this->assertEquals(
        'Hello World #1',
        $class->getMethod('format')->invoke(NULL, array('Hello %s #%d', 'World', 1))
      );
    }
  }
?>
