<?php namespace net\xp_framework\unittest\core\types;

use unittest\TestCase;
use lang\types\ArrayList;
use lang\IndexOutOfBoundsException;

/**
 * Tests the ArrayList class
 *
 * @see  xp://lang.types.ArrayList
 */
class ArrayListTest extends TestCase {

  #[@test]
  public function a_newly_created_arraylist_has_zero_length() {
    $this->assertEquals(0, create(new ArrayList())->length);
  }

  #[@test]
  public function a_newly_created_arraylist_is_empty() {
    $this->assertEquals(0, sizeof(create(new ArrayList())->values));
  }

  #[@test]
  public function two_newly_created_arraylists_are_equal() {
    $this->assertEquals(new ArrayList(), new ArrayList());
  }

  #[@test]
  public function is_usable_in_foreach() {
    foreach (new ArrayList(0, 1, 2) as $i => $value) {
      $this->assertEquals($i, $value);
    }
    $this->assertEquals(2, $i);
  }

  #[@test]
  public function is_usable_in_for() {
    for ($l= new ArrayList(0, 1, 2), $i= 0; $i < $l->length; $i++) {
      $this->assertEquals($i, $l[$i]);
    }
    $this->assertEquals(3, $i);
  }

  #[@test]
  public function is_usable_in_nested_foreach() {
    $r= '';
    foreach (new ArrayList(new ArrayList(1, 2, 3), new ArrayList(4, 5, 6)) as $i => $value) {
      foreach ($value as $j => $v) {
        $r.= $i.'.'.$j.':'.$v.', ';
      }
    }
    $this->assertEquals('0.0:1, 0.1:2, 0.2:3, 1.0:4, 1.1:5, 1.2:6', substr($r, 0, -2));
  }

  #[@test]
  public function inner_iteration() {
    $a= new ArrayList(1, 2, 3);
    $r= array();
    foreach ($a as $vo) {
      foreach ($a as $vi) {
        $r[]= $vi;
      }
    }
    $this->assertEquals(array(1, 2, 3, 1, 2, 3, 1, 2, 3), $r);
  }

  #[@test, @values([0, 1, 2])]
  public function array_access_operator_is_overloaded($value) {
    $c= new ArrayList(1, 2, 3);
    $this->assertEquals($value + 1, $c[$value]);
  }

  #[@test]
  public function array_access_operator_allows_reassigning() {
    $c= new ArrayList(1, 2, 3);
    $c[0]= 4;
    $this->assertEquals(4, $c[0]);
  }

  #[@test]
  public function array_access_operator_allows_modifying() {
    $c= new ArrayList(1, 2, 3);
    $c[2]+= 1;    // $c[2]++ does NOT work due to a bug in PHP
    $this->assertEquals(4, $c[2]);
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function reading_non_existant_element_raises_an_exception() {
    $c= new ArrayList();
    $c[0];
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function adding_an_element_raises_an_exception() {
    $c= new ArrayList();
    $c[]= 4;
  }

  #[@test, @values([0, 4]), @expect('lang.IndexOutOfBoundsException')]
  public function adding_an_element_by_supplying_nonexistant_offset_raises_an_exception($offset) {
    $c= new ArrayList();
    $c[$offset]= 4;
  }

  #[@test, @expect('lang.IndexOutOfBoundsException')]
  public function negative_key_raises_an_exception() {
    $c= new ArrayList();
    $c[-1]= 4;
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function key_of_incorrect_type_raises_an_exception() {
    $c= new ArrayList(1, 2, 3);
    $c['foo']= 4;
  }

  #[@test]
  public function newInstance_size() {
    $a= ArrayList::newInstance(4);
    $this->assertEquals(4, $a->length);
    $a[0]= 1;
    $a[1]= 2;
    $a[2]= 3;
    $a[3]= 4;
    try {
      $a[4]= 5;
      $this->fail('Should not be able to add a fifth element');
    } catch (IndexOutOfBoundsException $expected) { }
    $this->assertEquals(4, $a->length);
  }

  #[@test]
  public function newInstance_array() {
    $a= ArrayList::newInstance(array(1, 2, 3, 4));
    $this->assertEquals(4, $a->length);
    $this->assertEquals(1, $a[0]);
    $this->assertEquals(2, $a[1]);
    $this->assertEquals(3, $a[2]);
    $this->assertEquals(4, $a[3]);
  }

  #[@test, @values([0, 1, 2])]
  public function array_isset_operator_returns_true_for_existing_offsets($offset) {
    $c= new ArrayList(1, 2, 3);
    $this->assertTrue(isset($c[$offset]));
  }

  #[@test, @values([3, 4, -1])]
  public function array_isset_operator_returns_false_for_offsets_oob($offset) {
    $c= new ArrayList(1, 2, 3);
    $this->assertFalse(isset($c[$offset]));
  }

  #[@test, @values([0, 1, 2, 3, 4, -1]), @expect('lang.IllegalArgumentException')]
  public function array_unset_operator_is_overloaded() {
    $c= new ArrayList(1, 2, 3);
    unset($c[0]);
  }

  #[@test]
  public function arraylist_is_intact_after_unset() {
    $c= new ArrayList(1, 2, 3);
    try {
      unset($c[0]);
    } catch (\lang\IllegalArgumentException $expected) { }

    $this->assertEquals(new ArrayList(1, 2, 3), $c);
  }

  #[@test, @values([1, 2, 3])]
  public function int_contained_in_list_of_ints($value) {
    $this->assertTrue(create(new ArrayList(1, 2, 3))->contains($value));
  }

  #[@test, @values([0, -1, '1', 1.0, true, false, null])]
  public function values_not_contained_in_list_of_ints($value) {
    $this->assertFalse(create(new ArrayList(1, 2, 3))->contains($value));
  }

  #[@test, @values([1, -1, 0, '', false, null])]
  public function an_empty_list_does_not_contain_anything($value) {
    $this->assertFalse(create(new ArrayList())->contains($value));
  }

  #[@test]
  public function a_list_of_an_object_contains_the_given_object() {
    $o= new \lang\Object();
    $this->assertTrue(create(new ArrayList($o))->contains($o));
  }

  #[@test]
  public function a_list_of_an_object_does_not_contain_null() {
    $this->assertFalse(create(new ArrayList(new \lang\Object()))->contains(null));
  }

  #[@test]
  public function a_list_of_strings_does_not_contain_an_object() {
    $this->assertFalse(create(new ArrayList('T', 'e', 's', 't'))->contains(new \lang\Object()));
  }
}
