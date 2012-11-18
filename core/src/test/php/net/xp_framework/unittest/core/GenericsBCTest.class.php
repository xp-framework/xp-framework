<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase for `$__generic`-style generics
   *
   * @see  https://github.com/xp-framework/rfc/issues/193
   */
  class GenericsBCTest extends TestCase {

    /**
     * Test class with one generic parameter
     *
     */
    #[@test]
    public function list_of_string() {
      ClassLoader::defineClass('GenericsBCTest_List', 'lang.Object', array(), '{
        public $__generic;

        public function getClassName() {
          return "List<".$this->__generic[0].">";
        }
      }');
      $this->assertEquals(
        'List<String>',
        create('new GenericsBCTest_List<String>')->getClassName()
      );
    }

    /**
     * Test class with two generic parameters
     *
     */
    #[@test]
    public function map_of_string_to_object() {
      ClassLoader::defineClass('GenericsBCTest_Map', 'lang.Object', array(), '{
        public $__generic;

        public function getClassName() {
          return "Map<".$this->__generic[0].", ".$this->__generic[1].">";
        }
      }');
      $this->assertEquals(
        'Map<String, Object>',
        create('new GenericsBCTest_Map<String, Object>')->getClassName()
      );
    }
  }
?>