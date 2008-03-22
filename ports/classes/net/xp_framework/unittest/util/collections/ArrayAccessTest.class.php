<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String',
    'util.collections.HashTable',
    'util.collections.HashSet',
    'util.collections.Vector'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ArrayAccessTest extends TestCase {

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function hashTableReadElement() {
      $c= new HashTable();
      $world= new String('world');
      $c->put(new String('hello'), $world);
      $this->assertEquals($world, $c[new String('hello')]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function hashTableReadNonExistantElement() {
      $c= new HashTable();
      $this->assertEquals(NULL, $c[new String('hello')]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableReadIllegalElement() {
      $c= new HashTable();
      $c[STDIN];
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function hashTableWriteElement() {
      $c= new HashTable();
      $world= new String('world');
      $c[new String('hello')]= $world;
      $this->assertEquals($world, $c->get(new String('hello')));
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableWriteIllegalKey() {
      $c= new HashTable();
      $c[STDIN]= new String('Hello');
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableWriteIllegalValue() {
      $c= new HashTable();
      $c[new String('hello')]= 'scalar';
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function hashTableTestElement() {
      $c= new HashTable();
      $c->put(new String('hello'), new String('world'));
      $this->assertTrue(isset($c[new String('hello')]));
      $this->assertFalse(isset($c[new String('world')]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function hashTableRemoveElement() {
      $c= new HashTable();
      $c->put(new String('hello'), new String('world'));
      $this->assertTrue(isset($c[new String('hello')]));
      unset($c[new String('hello')]);
      $this->assertFalse(isset($c[new String('hello')]));
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function vectorReadElement() {
      $v= new Vector();
      $world= new String('world');
      $v->add($world);
      $this->assertEquals($world, $v[0]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorReadNonExistantElement() {
      $v= new Vector();
      $v[0];
    }

    /**
     * Tests array access operator is overloaded for adding
     *
     */
    #[@test]
    public function vectorAddElement() {
      $v= new Vector();
      $world= new String('world');
      $v[]= $world;
      $this->assertEquals($world, $v[0]);
    }
    
    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function vectorWriteElement() {
      $v= new Vector(array(new String('hello')));
      $world= new String('world');
      $v[0]= $world;
      $this->assertEquals($world, $v[0]);
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorWriteElementBeyondBoundsKey() {
      $v= new Vector();
      $v[0]= new String('world');
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorWriteElementNegativeKey() {
      $v= new Vector();
      $v[-1]= new String('world');
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function vectorTestElement() {
      $v= new Vector();
      $v[]= new String('world');
      $this->assertTrue(isset($v[0]));
      $this->assertFalse(isset($v[1]));
      $this->assertFalse(isset($v[-1]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function vectorRemoveElement() {
      $v= new Vector();
      $v[]= new String('world');
      unset($v[0]);
      $this->assertFalse(isset($v[0]));
    }

    /**
     * Tests Vector is usable in foreach()
     *
     */
    #[@test]
    public function vectorIsUsableInForeach() {
      $values= array(new String('hello'), new String('world'));
      foreach (new Vector($values) as $i => $value) {
        $this->assertEquals($values[$i], $value);
      }
      $this->assertEquals(sizeof($values)- 1, $i);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringReadChar() {
      $s= new String('Hello');
      $this->assertEquals(new Character('H'), $s[0]);
      $this->assertEquals(new Character('e'), $s[1]);
      $this->assertEquals(new Character('l'), $s[2]);
      $this->assertEquals(new Character('l'), $s[3]);
      $this->assertEquals(new Character('o'), $s[4]);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringReadBeyondOffset() {
      $s= new String('Hello');
      $s[5];
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringReadNegativeOffset() {
      $s= new String('Hello');
      $s[-1];
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringReadUtfChar() {
      $s= new String('Übercoder');
      $this->assertEquals(new Character('Ü'), $s[0]);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringWriteChar() {
      $s= new String('Übercoder');
      $s[0]= 'U';
      $this->assertEquals(new String('Ubercoder'), $s);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringWriteUtfChar() {
      $s= new String('Ubercoder');
      $s[0]= 'Ü';
      $this->assertEquals(new String('Übercoder'), $s);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringWriteMoreThanOneChar() {
      $s= new String('Hallo');
      $s[0]= 'Halli H';   // Hoping somehow this would become "Halli Hallo":)
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringWriteBeyondOffset() {
      $s= new String('Hello');
      $s[5]= 's';
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringWriteNegativeOffset() {
      $s= new String('Hello');
      $s[-1]= "\x00";
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringAppend() {
      $s= new String('Hello');
      $s[]= ' ';   // use concat() instead
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringTestChar() {
      $s= new String('Übercoder');
      $this->assertTrue(isset($s[0]));
      $this->assertTrue(isset($s[$s->length()- 1]));
      $this->assertFalse(isset($s[$s->length()]));
      $this->assertFalse(isset($s[-1]));
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringRemoveChar() {
      $s= new String('Übercoder');
      unset($s[0]);
      $this->assertEquals(new String('bercoder'), $s);
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetAddElement() {
      $s= new HashSet();
      $s[]= new String('X');
      $this->assertTrue($s->contains(new String('X')));
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashSetWriteElement() {
      $s= new HashSet();
      $s[0]= new String('X');
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashSetReadElement() {
      $s= new HashSet();
      $s[]= new String('X');
      $x= $s[0];
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetTestElement() {
      $s= new HashSet();
      $this->assertFalse(isset($s[new String('X')]));
      $s[]= new String('X');
      $this->assertTrue(isset($s[new String('X')]));
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetRemoveElement() {
      $s= new HashSet();
      $s[]= new String('X');
      unset($s[new String('X')]);
      $this->assertFalse(isset($s[new String('X')]));
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetUsableInForeach() {
      $s= new HashSet();
      $s->addAll(array(new String('0'), new String('1'), new String('2')));
      foreach ($s as $i => $element) {
        $this->assertEquals(new String($i), $element);
      }
    }
  }
?>
