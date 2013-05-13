<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'util.Objects');

  /**
   * TestCase for Objects class
   *
   * @see  xp://util.Objects
   */
  class ObjectsTest extends TestCase {

    /**
     * Creates primitives
     *
     * @return  var[]
     */
    public function primitives() {
      return array(
        array(FALSE), array(TRUE),
        array(1), array(0), array(-1), array(LONG_MAX), array(LONG_MIN),
        array(1.0), array(0.5), array(-6.1),
        array('', 'String', "\0")
      );
    }

    /**
     * Creates arrays
     *
     * @return  var[]
     */
    public function arrays() {
      return array(
        array(array()), array(array(1, 2, 3)), array(array(NULL, NULL)),
        array(array(array('Nested'), array('Array')))
      );
    }

    /**
     * Creates maps
     *
     * @return  var[]
     */
    public function maps() {
      return array(
        array(array('one' => 'two'))
      );
    }

    /**
     * Creates objects
     *
     * @return  var[]
     */
    public function objects() {
      return array(
        array($this), array(new Object()),
        array(new String(''), new String('Test'))
      );
    }

    /**
     * Creates values of all types
     *
     * @return  var[]
     */
    public function values() {
      return array_merge(
        array(NULL),
        $this->primitives(),
        $this->arrays(),
        $this->maps(),
        $this->objects()
      );
    }

    /**
     * Filters values() method
     *
     * @param   var exclude
     * @return  var[]
     */
    public function valuesExcept($exclude) {
      return array_filter($this->values(), function($value) use($exclude) {
        return $value[0] !== $exclude;
      });
    }

    #[@test, @values('values')]
    public function value_is_equal_to_self($val) {
      $this->assertTrue(Objects::equal($val, $val));
    }

    #[@test, @values(source= 'valuesExcept', args= array(NULL))]
    public function null_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(NULL, $val));
    }

    #[@test, @values(source= 'valuesExcept', args= array(FALSE)))]
    public function false_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(FALSE, $val));
    }

    #[@test, @values(source= 'valuesExcept', args= array(TRUE)))]
    public function true_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(TRUE, $val));
    }

    #[@test, @values(source= 'values')]
    public function int_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(6100, $val));
    }

    #[@test, @values(source= 'values')]
    public function double_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(6100.0, $val));
    }

    #[@test, @values(source= 'values')]
    public function string_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal('More power', $val));
    }
  
    #[@test, @values(source= 'values')]
    public function array_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(array(4, 5, 6), $val));
    }

    #[@test, @values(source= 'values')]
    public function hash_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(array('color' => 'blue'), $val));
    }

    #[@test, @values(source= 'values')]
    public function object_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(new Object(), $val));
    }

    #[@test, @values(source= 'values')]
    public function string_instance_not_equal_to_other_values($val) {
      $this->assertFalse(Objects::equal(new String('Binford 6100: More Power!'), $val));
    }

    #[@test]
    public function differently_ordered_arrays_not_equal() {
      $this->assertFalse(Objects::equal(
        array(1, 2, 3),
        array(3, 2, 1)
      ));
    }

    #[@test]
    public function differently_ordered_hashes_are_equal() {
      $this->assertTrue(Objects::equal(
        array('price' => 12.99, 'color' => 'blue'),
        array('color' => 'blue', 'price' => 12.99)
      ));
    }

    #[@test]
    public function null_string_without_default() {
      $this->assertEquals('', Objects::stringOf(NULL));
    }

    #[@test]
    public function null_string_with_default() {
      $this->assertEquals('default-value', Objects::stringOf(NULL, 'default-value'));
    }

    #[@test, @values('primitives')]
    public function stringOf_calls_xpStringOf_on_primitives($val) {
      $this->assertEquals(xp::stringOf($val), Objects::stringOf($val));
    }

    #[@test, @values('arrays')]
    public function stringOf_calls_xpStringOf_on_arrays($val) {
      $this->assertEquals(xp::stringOf($val), Objects::stringOf($val));
    }

    #[@test, @values('maps')]
    public function stringOf_calls_xpStringOf_on_maps($val) {
      $this->assertEquals(xp::stringOf($val), Objects::stringOf($val));
    }

    #[@test, @values('objects')]
    public function stringOf_calls_toString_on_objects($val) {
      $this->assertEquals($val->toString(), Objects::stringOf($val));
    }

    #[@test]
    public function null_hash() {
      $this->assertEquals('N;', Objects::hashOf(NULL));
    }

    #[@test, @values('primitives')]
    public function hashOf_calls_serialize_on_primitives($val) {
      $this->assertEquals(serialize($val), Objects::hashOf($val));
    }

    #[@test, @values('arrays')]
    public function hashOf_calls_serialize_on_arrays($val) {
      $this->assertEquals(serialize($val), Objects::hashOf($val));
    }

    #[@test, @values('maps')]
    public function hashOf_calls_serialize_on_maps($val) {
      $this->assertEquals(serialize($val), Objects::hashOf($val));
    }

    #[@test, @values('objects')]
    public function hashOf_calls_hashCode_on_objects($val) {
      $this->assertEquals($val->hashCode(), Objects::hashOf($val));
    }
  }
?>
