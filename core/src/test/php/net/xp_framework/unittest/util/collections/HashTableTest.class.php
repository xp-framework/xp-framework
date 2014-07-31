<?php namespace net\xp_framework\unittest\util\collections;
 
use unittest\TestCase;
use util\collections\HashTable;
use util\collections\Pair;
use lang\types\String;
use lang\types\Integer;
use lang\types\Double;

/**
 * Test HashTable class
 *
 * @see   xp://util.collections.HashTable
 */
class HashTableTest extends TestCase {

  /** @return var[] */
  protected function variations() {
    return array(
      array(new HashTable(), array(
        new Pair('color', 'pink'),
        new Pair('price', null)
      )),
      array(new HashTable(), array(
        new Pair(new String('color'), new String('green')),
        new Pair(new String('price'), new Double(12.99))
      )),
      array(create('new util.collections.HashTable<string, lang.types.String>'), array(
        new Pair('hello', new String('World')),
        new Pair('hallo', new String('Welt'))
      )),
      array(create('new util.collections.HashTable<lang.types.Integer, string[]>'), array(
        new Pair(new Integer(1), array('one', 'eins')),
        new Pair(new Integer(2), array('two', 'zwei'))
      )),
      array(create('new util.collections.HashTable<int[], var>'), array(
        new Pair(array(1, 2), 3),
        new Pair(array(0, -1), 'Test')
      ))
    );
  }

  #@test, @values('variations')]
  public function can_create($fixture, $pairs) {
    // Intentionally empty
  }

  #[@test, @values('variations')]
  public function map_is_initially_empty($fixture, $pairs) {
    $this->assertTrue($fixture->isEmpty());
  }

  #[@test, @values('variations')]
  public function map_size_is_initially_zero($fixture, $pairs) {
    $this->assertEquals(0, $fixture->size());
  }

  #[@test, @values('variations')]
  public function put($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
  }

  #[@test, @values('variations')]
  public function array_access_for_writing($fixture, $pairs) {
    $fixture[$pairs[0]->key]= $pairs[0]->value;
  }

  #[@test, @values('variations')]
  public function put_returns_previously_value($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertEquals($pairs[0]->value, $fixture->put($pairs[0]->key, $pairs[1]->value));
  }

  #[@test, @values('variations')]
  public function map_no_longer_empty_after_put($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertFalse($fixture->isEmpty());
  }

  #[@test, @values('variations')]
  public function map_size_no_longer_zero_after_put($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertEquals(1, $fixture->size());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function put_illegal_type_in_key() {
    create('new util.collections.HashTable<string, lang.types.String>')->put(5, new String('hello'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function put_illegal_type_in_value() {
    create('new util.collections.HashTable<string, lang.types.String>')->put('hello', new Integer(1));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function put_raises_when_using_null_for_string_instance() {
    create('new util.collections.HashTable<string, lang.types.String>')->put('test', null);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function put_raises_when_using_null_for_arrays() {
    create('new util.collections.HashTable<string, var[]>')->put('test', null);
  }

  #[@test, @values('variations')]
  public function get_returns_null_when_key_does_not_exist($fixture, $pairs) {
    $this->assertNull($fixture->get($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function get_returns_previously_put_element($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertEquals($pairs[0]->value, $fixture->get($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function array_access_for_reading_non_existant($fixture, $pairs) {
    $this->assertNull($fixture[$pairs[0]->key]);
  }

  #[@test, @values('variations')]
  public function array_access_for_reading($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertEquals($pairs[0]->value, $fixture[$pairs[0]->key]);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function get_illegal_type_in_argument() {
    create('new util.collections.HashTable<lang.types.String, lang.types.String>')->get(new Integer(1));
  }

  #[@test, @values('variations')]
  public function containsKey_returns_false_when_element_does_not_exist($fixture, $pairs) {
    $this->assertFalse($fixture->containsKey($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function containsKey_returns_true_when_element_exists($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertTrue($fixture->containsKey($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function array_access_for_testing_non_existant($fixture, $pairs) {
    $this->assertFalse(isset($fixture[$pairs[0]->key]));
  }

  #[@test, @values('variations')]
  public function array_access_for_testing($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertTrue(isset($fixture[$pairs[0]->key]));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function containsKey_illegal_type_in_argument() {
    create('new util.collections.HashTable<lang.types.String, lang.types.String>')->containsKey(new Integer(1));
  }

  #[@test, @values('variations')]
  public function containsValue_returns_false_when_element_does_not_exist($fixture, $pairs) {
    $this->assertFalse($fixture->containsValue($pairs[0]->value));
  }

  #[@test, @values('variations')]
  public function containsValue_returns_true_when_element_exists($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertTrue($fixture->containsValue($pairs[0]->value));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function containsValue_illegal_type_in_argument() {
    create('new util.collections.HashTable<lang.types.String, lang.types.String>')->containsValue(new Integer(1));
  }

  #[@test, @values('variations')]
  public function remove_returns_previously_value($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertEquals($pairs[0]->value, $fixture->remove($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function remove_previously_put_element($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->remove($pairs[0]->key);
    $this->assertFalse($fixture->containsKey($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function array_access_for_removing($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    unset($fixture[$pairs[0]->key]);
    $this->assertFalse($fixture->containsKey($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function remove_non_existant_element($fixture, $pairs) {
    $fixture->remove($pairs[0]->key);
    $this->assertFalse($fixture->containsKey($pairs[0]->key));
  }

  #[@test, @values('variations')]
  public function array_access_for_removing_non_existant_element($fixture, $pairs) {
    unset($fixture[$pairs[0]->key]);
    $this->assertFalse($fixture->containsKey($pairs[0]->key));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function remove_illegal_type_in_argument() {
    create('new util.collections.HashTable<lang.types.String, lang.types.String>')->remove(new Integer(1));
  }

  #[@test, @values('variations')]
  public function equals_its_clone($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertEquals($fixture, clone $fixture);
  }

  #[@test, @values('variations')]
  public function equals_its_clone_when_empty($fixture, $pairs) {
    $this->assertEquals($fixture, clone $fixture);
  }

  #[@test, @values('variations')]
  public function does_not_equal_empty_map($fixture, $pairs) {
    $other= clone $fixture;
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $this->assertNotEquals($fixture, $other);
  }

  #[@test, @values('variations')]
  public function does_not_equal_map_with_different_elements($fixture, $pairs) {
    $other= clone $fixture;
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $other->put($pairs[1]->key, $pairs[1]->value);
    $this->assertNotEquals($fixture, $other);
  }

  #[@test, @values('variations')]
  public function clear($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->clear();
    $this->assertTrue($fixture->isEmpty());
  }

  #[@test, @values('variations')]
  public function keys_returns_empty_array_for_empty_map($fixture, $pairs) {
    $this->assertEquals(array(), $fixture->keys());
  }

  #[@test, @values('variations')]
  public function keys_returns_array_of_added_keys($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->put($pairs[1]->key, $pairs[1]->value);
    $this->assertEquals(array($pairs[0]->key, $pairs[1]->key), $fixture->keys());
  }

  #[@test, @values('variations')]
  public function values_returns_empty_array_for_empty_map($fixture, $pairs) {
    $this->assertEquals(array(), $fixture->values());
  }

  #[@test, @values('variations')]
  public function values_returns_array_of_added_values($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->put($pairs[1]->key, $pairs[1]->value);
    $this->assertEquals(array($pairs[0]->value, $pairs[1]->value), $fixture->values());
  }

  #[@test, @values('variations')]
  public function can_be_used_in_foreach($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->put($pairs[1]->key, $pairs[1]->value);
    $iterated= array();
    foreach ($fixture as $pair) {
      $iterated[]= $pair;
    }
    $this->assertEquals(array($pairs[0], $pairs[1]), $iterated);
  }

  #[@test, @values('variations')]
  public function can_be_used_in_foreach_with_empty_map($fixture, $pairs) {
    $iterated= array();
    foreach ($fixture as $pair) {
      $iterated[]= $pair;
    }
    $this->assertEquals(array(), $iterated);
  }

  #[@test, @values('variations')]
  public function iteration_invoked_twice($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->put($pairs[1]->key, $pairs[1]->value);
    $iterated= array();
    foreach ($fixture as $pair) {
      $iterated[]= $pair;
    }
    foreach ($fixture as $pair) {
      $iterated[]= $pair;
    }
    $this->assertEquals(array($pairs[0], $pairs[1], $pairs[0], $pairs[1]), $iterated);
  }

  #[@test, @values('variations')]
  public function second_iteration_with_break_statement($fixture, $pairs) {
    $fixture->put($pairs[0]->key, $pairs[0]->value);
    $fixture->put($pairs[1]->key, $pairs[1]->value);
    $iterated= array();
    foreach ($fixture as $pair) {
      $iterated[]= $pair;
    }
    foreach ($fixture as $pair) {
      break;
    }
    $this->assertEquals(array($pairs[0], $pairs[1]), $iterated);
  }

  #[@test]
  public function string_representation_of_empty_map() {
    $this->assertEquals(
      'util.collections.HashTable[0] { }',
      create(new HashTable())->toString()
    );
  }

  #[@test]
  public function string_representation_of_map_with_contents() {
    $fixture= new HashTable();
    $fixture->put('hello', 'World');
    $fixture->put('hallo', 'Welt');
    $this->assertEquals(
      "util.collections.HashTable[2] {\n".
      "  \"hello\" => \"World\",\n".
      "  \"hallo\" => \"Welt\"\n".
      "}",
      $fixture->toString()
    );
  }

  #[@test]
  public function string_representation_of_generic_map() {
    $this->assertEquals(
      'util.collections.HashTable<string,lang.types.String>[0] { }',
      create('new util.collections.HashTable<string, lang.types.String>')->toString()
    );
  }
}
