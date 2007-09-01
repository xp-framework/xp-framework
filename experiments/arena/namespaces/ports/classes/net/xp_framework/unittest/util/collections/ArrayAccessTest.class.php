<?php
/* This class is part of the XP framework
 *
 * $Id: ArrayAccessTest.class.php 10173 2007-04-29 17:12:00Z friebe $ 
 */

  namespace net::xp_framework::unittest::util::collections;

  ::uses(
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
  class ArrayAccessTest extends unittest::TestCase {

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function hashTableReadElement() {
      $c= new util::collections::HashTable();
      $world= new lang::types::String('world');
      $c->put(new lang::types::String('hello'), $world);
      $this->assertEquals($world, $c[new lang::types::String('hello')]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function hashTableReadNonExistantElement() {
      $c= new util::collections::HashTable();
      $this->assertEquals(NULL, $c[new lang::types::String('hello')]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableReadIllegalElement() {
      $c= new util::collections::HashTable();
      $c[STDIN];
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function hashTableWriteElement() {
      $c= new util::collections::HashTable();
      $world= new lang::types::String('world');
      $c[new lang::types::String('hello')]= $world;
      $this->assertEquals($world, $c->get(new lang::types::String('hello')));
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableWriteIllegalKey() {
      $c= new util::collections::HashTable();
      $c[STDIN]= new lang::types::String('Hello');
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableWriteIllegalValue() {
      $c= new util::collections::HashTable();
      $c[new lang::types::String('hello')]= 'scalar';
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function hashTableTestElement() {
      $c= new util::collections::HashTable();
      $c->put(new lang::types::String('hello'), new lang::types::String('world'));
      $this->assertTrue(isset($c[new lang::types::String('hello')]));
      $this->assertFalse(isset($c[new lang::types::String('world')]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function hashTableRemoveElement() {
      $c= new util::collections::HashTable();
      $c->put(new lang::types::String('hello'), new lang::types::String('world'));
      $this->assertTrue(isset($c[new lang::types::String('hello')]));
      unset($c[new lang::types::String('hello')]);
      $this->assertFalse(isset($c[new lang::types::String('hello')]));
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function vectorReadElement() {
      $v= new util::collections::Vector();
      $world= new lang::types::String('world');
      $v->add($world);
      $this->assertEquals($world, $v[0]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorReadNonExistantElement() {
      $v= new util::collections::Vector();
      $v[0];
    }

    /**
     * Tests array access operator is overloaded for adding
     *
     */
    #[@test]
    public function vectorAddElement() {
      $v= new util::collections::Vector();
      $world= new lang::types::String('world');
      $v[]= $world;
      $this->assertEquals($world, $v[0]);
    }
    
    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function vectorWriteElement() {
      $v= new util::collections::Vector(array(new lang::types::String('hello')));
      $world= new lang::types::String('world');
      $v[0]= $world;
      $this->assertEquals($world, $v[0]);
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorWriteElementBeyondBoundsKey() {
      $v= new util::collections::Vector();
      $v[0]= new lang::types::String('world');
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorWriteElementNegativeKey() {
      $v= new util::collections::Vector();
      $v[-1]= new lang::types::String('world');
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function vectorTestElement() {
      $v= new util::collections::Vector();
      $v[]= new lang::types::String('world');
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
      $v= new util::collections::Vector();
      $v[]= new lang::types::String('world');
      unset($v[0]);
      $this->assertFalse(isset($v[0]));
    }

    /**
     * Tests Vector is usable in foreach()
     *
     */
    #[@test]
    public function vectorIsUsableInForeach() {
      $values= array(new lang::types::String('hello'), new lang::types::String('world'));
      foreach (new util::collections::Vector($values) as $i => $value) {
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
      $s= new lang::types::String('Hello');
      $this->assertEquals(new lang::types::Character('H'), $s[0]);
      $this->assertEquals(new lang::types::Character('e'), $s[1]);
      $this->assertEquals(new lang::types::Character('l'), $s[2]);
      $this->assertEquals(new lang::types::Character('l'), $s[3]);
      $this->assertEquals(new lang::types::Character('o'), $s[4]);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringReadBeyondOffset() {
      $s= new lang::types::String('Hello');
      $s[5];
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringReadNegativeOffset() {
      $s= new lang::types::String('Hello');
      $s[-1];
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringReadUtfChar() {
      $s= new lang::types::String('Übercoder');
      $this->assertEquals(new lang::types::Character('Ü'), $s[0]);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringWriteChar() {
      $s= new lang::types::String('Übercoder');
      $s[0]= 'U';
      $this->assertEquals(new lang::types::String('Ubercoder'), $s);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringWriteUtfChar() {
      $s= new lang::types::String('Ubercoder');
      $s[0]= 'Ü';
      $this->assertEquals(new lang::types::String('Übercoder'), $s);
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringWriteMoreThanOneChar() {
      $s= new lang::types::String('Hallo');
      $s[0]= 'Halli H';   // Hoping somehow this would become "Halli Hallo":)
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringWriteBeyondOffset() {
      $s= new lang::types::String('Hello');
      $s[5]= 's';
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function stringWriteNegativeOffset() {
      $s= new lang::types::String('Hello');
      $s[-1]= "\x00";
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringAppend() {
      $s= new lang::types::String('Hello');
      $s[]= ' ';   // use concat() instead
    }

    /**
     * Tests string class array access operator overloading
     *
     */
    #[@test]
    public function stringTestChar() {
      $s= new lang::types::String('Übercoder');
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
      $s= new lang::types::String('Übercoder');
      unset($s[0]);
      $this->assertEquals(new lang::types::String('bercoder'), $s);
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetAddElement() {
      $s= new util::collections::HashSet();
      $s[]= new lang::types::String('X');
      $this->assertTrue($s->contains(new lang::types::String('X')));
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashSetWriteElement() {
      $s= new util::collections::HashSet();
      $s[0]= new lang::types::String('X');
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashSetReadElement() {
      $s= new util::collections::HashSet();
      $s[]= new lang::types::String('X');
      $x= $s[0];
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetTestElement() {
      $s= new util::collections::HashSet();
      $this->assertFalse(isset($s[new lang::types::String('X')]));
      $s[]= new lang::types::String('X');
      $this->assertTrue(isset($s[new lang::types::String('X')]));
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetRemoveElement() {
      $s= new util::collections::HashSet();
      $s[]= new lang::types::String('X');
      unset($s[new lang::types::String('X')]);
      $this->assertFalse(isset($s[new lang::types::String('X')]));
    }

    /**
     * Tests hashset array access operator overloading
     *
     */
    #[@test]
    public function hashSetUsableInForeach() {
      $s= new util::collections::HashSet();
      $s->addAll(array(new lang::types::String('0'), new lang::types::String('1'), new lang::types::String('2')));
      foreach ($s as $i => $element) {
        $this->assertEquals(new lang::types::String($i), $element);
      }
    }
  }
?>
