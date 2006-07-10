<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'remote.protocol.Serializer',
    'net.xp_framework.unittest.remote.Person'
  );

  /**
   * Unit test for Serializer class
   *
   * @see      xp://remote.Serializer
   * @purpose  TestCase
   */
  class SerializerTest extends TestCase {

    /**
     * Setup testcase
     *
     * @access  public
     */
    function setUp() {
      $this->serializer= &new Serializer();
    }
    
    /**
     * Test serialization of NULL
     *
     * @access  public
     */
    #[@test]
    function representationOfNull() {
      $this->assertEquals('N;', $this->serializer->representationOf($var= NULL));
    }

    /**
     * Test serialization of Shorts
     *
     * @access  public
     */
    #[@test]
    function representationOfShorts() {
      $this->assertEquals('S:300;', $this->serializer->representationOf(new Short(300)));
      $this->assertEquals('S:-300;', $this->serializer->representationOf(new Short(-300)));
    }

    /**
     * Test serialization of longs
     *
     * @access  public
     */
    #[@test]
    function representationOfBytes() {
      $this->assertEquals('B:127;', $this->serializer->representationOf(new Byte(127)));
      $this->assertEquals('B:-128;', $this->serializer->representationOf(new Byte(-128)));
    }

    /**
     * Test serialization of booleans
     *
     * @access  public
     */
    #[@test]
    function representationOfBooleans() {
      $this->assertEquals('b:1;', $this->serializer->representationOf($var= TRUE));
      $this->assertEquals('b:0;', $this->serializer->representationOf($var= FALSE));
    }

    /**
     * Test serialization of integers
     *
     * @access  public
     */
    #[@test]
    function representationOfIntegers() {
      $this->assertEquals('i:6100;', $this->serializer->representationOf($var= 6100));
      $this->assertEquals('i:-6100;', $this->serializer->representationOf($var= -6100));
    }

    /**
     * Test serialization of longs
     *
     * @access  public
     */
    #[@test]
    function representationOfLongs() {
      $this->assertEquals('l:6100;', $this->serializer->representationOf(new Long(6100)));
      $this->assertEquals('l:-6100;', $this->serializer->representationOf(new Long(-6100)));
    }

    /**
     * Test serialization of floats
     *
     * @access  public
     */
    #[@test]
    function representationOfFloats() {
      $this->assertEquals('d:0.1;', $this->serializer->representationOf($var= 0.1));
      $this->assertEquals('d:-0.1;', $this->serializer->representationOf($var= -0.1));
    }

    /**
     * Test serialization of doubles
     *
     * @access  public
     */
    #[@test]
    function representationOfDoubles() {
      $this->assertEquals('d:0.1;', $this->serializer->representationOf(new Double(0.1)));
      $this->assertEquals('d:-0.1;', $this->serializer->representationOf(new Double(-0.1)));
    }

    /**
     * Test serialization of the string "Hello World"
     *
     * @access  public
     */
    #[@test]
    function representationOfString() {
      $this->assertEquals('s:11:"Hello World";', $this->serializer->representationOf($var= 'Hello World'));
    }
    
    /**
     * Test serialization of an array containing three integers 
     * (1, 2 and 5)
     *
     * @access  public
     */
    #[@test]
    function representationOfIntegerArray() {
      $this->assertEquals(
        'a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}', 
        $this->serializer->representationOf($var= array(1, 2, 5))
      );
    }
    
    /**
     * Test serialization of an array containing two strings 
     * ("More" and "Power")
     *
     * @access  public
     */
    #[@test]
    function representationOfStringArray() {
      $this->assertEquals(
        'a:2:{i:0;s:4:"More";i:1;s:5:"Power";}', 
        $this->serializer->representationOf($var= array('More', 'Power'))
      );
    }
    
    /**
     * Test serialization of a date object
     *
     * @access  public
     */
    #[@test]
    function representationOfDate() {
      $this->assertEquals('T:1122644265;', $this->serializer->representationOf(new Date(1122644265)));
    }

    /**
     * Test serialization of a hashmap
     *
     * @access  public
     */
    #[@test]
    function representationOfHashmap() {
      $h= &new Hashmap();
      $h->put('key', 'value');
      $h->put('number', '6100');

      $this->assertEquals(
        'a:2:{s:3:"key";s:5:"value";s:6:"number";s:4:"6100";}', 
        $this->serializer->representationOf($h)
      );
    }

    /**
     * Test serialization of a hashmap with mixed values
     *
     * @access  public
     */
    #[@test]
    function representationOfMixedHashmap() {
      $h= &new Hashmap();
      $h->put('key', 'value');
      $h->put('number', 6100);

      $this->assertEquals(
        'a:2:{s:3:"key";s:5:"value";s:6:"number";i:6100;}', 
        $this->serializer->representationOf($h)
      );
    }
    
    /**
     * Test serialization of a generic value object
     *
     * @access  public
     * @see     xp://Person
     */
    #[@test]
    function representationOfValueObject() {
      $this->assertEquals(
        'O:39:"net.xp_framework.unittest.remote.Person":2:{s:2:"id";i:1549;s:4:"name";s:11:"Timm Friebe";}',
        $this->serializer->representationOf(new Person())
      );
    }

    /**
     * Test deserialization of an integer
     *
     * @access  public
     * @see     xp://Person
     */
    #[@test]
    function valueOfInt() {
      $this->assertEquals(
        1,
        $this->serializer->valueOf('i:1;')
      );
    }

    /**
     * Test deserialization of an integer
     *
     * @access  public
     * @see     xp://Person
     */
    #[@test]
    function valueOfShorts() {
      $this->assertEquals(
        new Short(1),
        $this->serializer->valueOf('S:1;')
      );
    }
    
    /**
     * Test deserialization of an integer
     *
     * @access  public
     * @see     xp://Person
     */
    #[@test]
    function valueOfArrayList() {
      $return= &$this->serializer->valueOf("A:2:{O:6:\"Person\":2:{s:2:\"id\";i:1549;s:4:\"name\";s:11:\"Timm Friebe\";}s:5:\"World\";}");
      $this->assertClass($return, 'lang.types.ArrayList');
      $this->assertEquals(2, sizeof($return->values));
      $this->assertEquals(new Person(), $return->values[0]);
      $this->assertEquals('World', $return->values[1]);
    }

    /**
     * Assert 
     *
     * @access  public
     */
    #[@test]
    function arrayList() {
      $list= $this->serializer->valueOf(
        'A:1:{a:2:{s:2:"la";s:2:"la";s:3:"foo";A:2:{a:1:{s:13:"verschachteln";s:7:"istToll";}s:6:"barbar";}}}',
        $context
      );
      $this->assertEquals($list, new ArrayList(array(
        array(
          'la'  => 'la',
          'foo' => new ArrayList(array(
            array('verschachteln' => 'istToll'),
            'barbar'
          ))
        ))
      ));
    }
    
    /**
     * Check serialization through custom class mappings. Check that the serialization
     * is always carried through by the best matching serializer mapping.
     *
     * @access  public
     */
    #[@test]
    function bestMapping() {
      $cl= &ClassLoader::getDefault();
      $fooClass= &$cl->defineClass('net.xp_framework.unittest.remote.FooClass', 'class FooClass extends Object { }');
      $barClass= &$cl->defineClass('net.xp_framework.unittest.remote.BarClass', 'class BarClass extends FooClass { }');
      
      $fooHandler= &$cl->defineClass('net.xp_framework.unittest.remote.FooHandler', 'class FooHandler extends Object {
        function &handledClass() { return XPClass::forName("net.xp_framework.unittest.remote.FooClass"); }
        function representationOf(&$serializer, &$var, $ctx) { return "FOO:"; }
        function &valueOf(&$serializer, $serialized, &$length, $context) { return NULL; }
      } implements("net/xp_framework/unittest/remote/FooHandler.class.php", "remote.protocol.SerializerMapping");');
      
      $barHandler= &$cl->defineClass('net.xp_framework.unittest.remote.BarHandler', 'class BarHandler extends Object {
        function &handledClass() { return XPClass::forName("net.xp_framework.unittest.remote.BarClass"); }
        function representationOf(&$serializer, &$var, $ctx) { return "BAR:"; }
        function &valueOf(&$serializer, $serialized, &$length, $context) { return NULL; }
      } implements("net/xp_framework/unittest/remote/BarHandler.class.php", "remote.protocol.SerializerMapping");');
      
      
      // Both must be serialized with the FOO mapping, because both are Foo or Foo-derived objects.
      $this->serializer->mapping('FOO', $fooHandler->newInstance());
      $this->assertEquals('FOO:', $this->serializer->representationOf(new FooClass()));
      $this->assertEquals('FOO:', $this->serializer->representationOf(new BarClass()));
      
      // Add more concrete mapping for BAR. Foo must still be serialized with FOO, but the BarClass-object
      // has a better matching mapping.
      $this->serializer->mapping('BAR', $barHandler->newInstance());
      $this->assertEquals('FOO:', $this->serializer->representationOf(new FooClass()));
      $this->assertEquals('BAR:', $this->serializer->representationOf(new BarClass()));
    }
  }
?>
