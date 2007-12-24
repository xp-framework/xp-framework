<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'io.streams.Streams', 'io.streams.MemoryInputStream');

  /**
   * TestCase
   *
   * @see      xp://lang.Primitive
   * @purpose  Unittest
   */
  class PrimitiveTest extends TestCase {

    /**
     * Test string primitive
     *
     */
    #[@test]
    public function stringPrimitive() {
      $this->assertEquals(Primitive::$STRING, Primitive::forName('string'));
    }
  
    /**
     * Test integer primitive
     *
     */
    #[@test]
    public function integerPrimitive() {
      $this->assertEquals(Primitive::$INTEGER, Primitive::forName('integer'));
    }

    /**
     * Test double primitive
     *
     */
    #[@test]
    public function doublePrimitive() {
      $this->assertEquals(Primitive::$DOUBLE, Primitive::forName('double'));
    }

    /**
     * Test boolean primitive
     *
     */
    #[@test]
    public function booleanPrimitive() {
      $this->assertEquals(Primitive::$BOOLEAN, Primitive::forName('boolean'));
    }

    /**
     * Test array primitive
     *
     */
    #[@test]
    public function arrayPrimitive() {
      $this->assertEquals(Primitive::$ARRAY, Primitive::forName('array'));
    }

    /**
     * Test non-primitive passed to forName() raises an exception
     *
     * @see   xp://lang.Primitive#forName
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonPrimitive() {
      Primitive::forName('lang.Object');
    }

    /**
     * Test string is boxed to lang.types.String
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxString() {
      $this->assertEquals(new String('Hello'), Primitive::boxed('Hello'));
    }

    /**
     * Test integer is boxed to lang.types.Integer
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxInteger() {
      $this->assertEquals(new Integer(1), Primitive::boxed(1));
    }

    /**
     * Test double is boxed to lang.types.Double
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxDouble() {
      $this->assertEquals(new Double(1.0), Primitive::boxed(1.0));
    }

    /**
     * Test boolean is boxed to lang.types.Boolean
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxBoolean() {
      $this->assertEquals(new Boolean(TRUE), Primitive::boxed(TRUE), 'true');
      $this->assertEquals(new Boolean(FALSE), Primitive::boxed(FALSE), 'false');
    }

    /**
     * Test arrays are boxed to lang.types.ArrayList
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxArray() {
      $this->assertEquals(new ArrayList(1, 2, 3), Primitive::boxed(array(1, 2, 3)));
    }

    /**
     * Test objects are boxed to themselves
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxObject() {
      $o= new Object();
      $this->assertEquals($o, Primitive::boxed($o));
    }

    /**
     * Test null values are boxed to themselves
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxNull() {
      $this->assertEquals(NULL, Primitive::boxed(NULL));
    }

    /**
     * Test resources cannot be boxed
     *
     * @see   xp://lang.Primitive#boxed
     */
    #[@test]
    public function boxResource() {
      $fd= Streams::readableFd(new MemoryInputStream('test'));
      try {
        Primitive::boxed($fd);
      } catch (IllegalArgumentException $expected) {
        // OK
      } finally(); {
        fclose($fd);    // Necessary, PHP will segfault otherwise
        if ($expected) return;
      }
      $this->fail('Expected exception not caught', NULL, 'lang.IllegalArgumentException');
    }

    /**
     * Test lang.types.String is unboxed to string
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxString() {
      $this->assertEquals('Hello', Primitive::unboxed(new String('Hello')));
    }

    /**
     * Test lang.types.Integer is unboxed to integer
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxInteger() {
      $this->assertEquals(1, Primitive::unboxed(new Integer(1)));
    }

    /**
     * Test lang.types.Double is unboxed to double
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxDouble() {
      $this->assertEquals(1.0, Primitive::unboxed(new Double(1.0)));
    }

    /**
     * Test lang.types.Boolean is unboxed to boolean
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxBoolean() {
      $this->assertEquals(TRUE, Primitive::unboxed(new Boolean(TRUE)), 'true');
      $this->assertEquals(FALSE, Primitive::unboxed(new Boolean(FALSE)), 'false');
    }

    /**
     * Test lang.types.ArrayList is unboxed to array
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxArray() {
      $this->assertEquals(array(1, 2, 3), Primitive::unboxed(new ArrayList(1, 2, 3)));
    }

    /**
     * Test objects cannot be unboxed.
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unboxObject() {
      Primitive::unboxed(new Object());
    }

    /**
     * Test null values are unboxed to themselves
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxNull() {
      $this->assertEquals(NULL, Primitive::unboxed(NULL));
    }

    /**
     * Test primitives values are unboxed to themselves
     *
     * @see   xp://lang.Primitive#unboxed
     */
    #[@test]
    public function unboxPrimitive() {
      $this->assertEquals(1, Primitive::unboxed(1));
    }
  }
?>
