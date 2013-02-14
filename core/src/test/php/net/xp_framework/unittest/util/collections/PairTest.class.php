<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.collections.Pair',
    'lang.types.String'
  );

  /**
   * Test Pair class
   *
   * @see  xp://util.collections.Pair
   */
  class PairTest extends TestCase {

    /**
     * Tests constructor
     */
    #[@test]
    public function can_create() {
      new Pair(NULL, NULL);
    }

    /**
     * Tests key member
     */
    #[@test]
    public function key() {
      $p= new Pair('key', NULL);
      $this->assertEquals('key', $p->key);
    }

    /**
     * Tests value member
     */
    #[@test]
    public function value() {
      $p= new Pair(NULL, 'value');
      $this->assertEquals('value', $p->value);
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function equals_same_instance() {
      $p= new Pair(NULL, NULL);
      $this->assertEquals($p, $p);
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function equals_null_key_null_value() {
      $this->assertEquals(new Pair(NULL, NULL), new Pair(NULL, NULL));
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function equals_primitive_key_null_value() {
      $this->assertEquals(new Pair('key', NULL), new Pair('key', NULL));
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function equals_primitive_key_primitive_value() {
      $this->assertEquals(new Pair('key', 'value'), new Pair('key', 'value'));
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function equals_key_instance_value_instance() {
      $this->assertEquals(
        new Pair(new String('key'), new String('value')),
        new Pair(new String('key'), new String('value'))
      );
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function primitive_key_and_value_not_equal_to_null_key_and_value() {
      $this->assertNotEquals(new Pair('key', 'value'), new Pair(NULL, NULL));
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function instance_key_and_value_not_equal_to_null_key_and_value() {
      $this->assertNotEquals(
        new Pair(new String('key'), new String('value')),
        new Pair(NULL, NULL)
      );
    }

    /**
     * Tests equals() method
     */
    #[@test]
    public function pair_not_equals_to_null() {
      $this->assertNotEquals(new Pair(NULL, NULL), NULL);
    }

    /**
     * Tests hashCode() method
     */
    #[@test]
    public function hashcode_of_nulls_equal() {
      $this->assertEquals(
        create(new Pair(NULL, NULL))->hashCode(),
        create(new Pair(NULL, NULL))->hashCode()
      );
    }

    /**
     * Tests hashCode() method
     */
    #[@test]
    public function hashcode_of_different_keys_not_equal() {
      $this->assertNotEquals(
        create(new Pair(NULL, NULL))->hashCode(),
        create(new Pair('key', NULL))->hashCode()
      );
    }

    /**
     * Tests hashCode() method
     */
    #[@test]
    public function hashcode_of_different_values_not_equal() {
      $this->assertNotEquals(
        create(new Pair(NULL, NULL))->hashCode(),
        create(new Pair(NULL, 'value'))->hashCode()
      );
    }
  }
?>
