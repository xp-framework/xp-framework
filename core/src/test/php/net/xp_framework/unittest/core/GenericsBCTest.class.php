<?php namespace net\xp_framework\unittest\core;

/**
 * TestCase for `$__generic`-style generics
 *
 * @see  https://github.com/xp-framework/rfc/issues/193
 */
class GenericsBCTest extends \unittest\TestCase {

  #[@test]
  public function list_of_string() {
    \lang\ClassLoader::defineClass('GenericsBCTest_List', 'lang.Object', array(), '{
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

  #[@test]
  public function map_of_string_to_object() {
    \lang\ClassLoader::defineClass('GenericsBCTest_Map', 'lang.Object', array(), '{
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
