<?php namespace net\xp_framework\unittest\remote;

use remote\protocol\Serializer;
use remote\protocol\RemoteInterfaceMapping;
use util\Hashmap;

/**
 * Unit test for Serializer class
 *
 * @see      xp://remote.Serializer
 */
abstract class SerializerTest extends \unittest\TestCase {
  protected $serializer= null;

  /**
   * Unserializes a value from a given serialized representation
   *
   * @param  string $bytes
   * @param  lang.Type $t
   * @return var value
   */
  protected abstract function unserialize($bytes, $t= null, $ctx= array());

  /**
   * Serializes a value and returns a serialized representation
   *
   * @param  var $value
   * @return string bytes
   */
  protected abstract function serialize($value);

  /**
   * Setup testcase
   */
  public function setUp() {
    $this->serializer= new Serializer();
  }

  #[@test]
  public function representationOfNull() {
    $this->assertEquals('N;', $this->serialize(null));
  }

  #[@test]
  public function representationOfShorts() {
    $this->assertEquals('S:300;', $this->serialize(new \lang\types\Short(300)));
    $this->assertEquals('S:-300;', $this->serialize(new \lang\types\Short(-300)));
  }

  #[@test]
  public function representationOfBytes() {
    $this->assertEquals('B:127;', $this->serialize(new \lang\types\Byte(127)));
    $this->assertEquals('B:-128;', $this->serialize(new \lang\types\Byte(-128)));
  }

  #[@test]
  public function representationOfBooleans() {
    $this->assertEquals('b:1;', $this->serialize($var= true));
    $this->assertEquals('b:0;', $this->serialize($var= false));
  }

  #[@test]
  public function representationOfIntegers() {
    $this->assertEquals('i:6100;', $this->serialize($var= 6100));
    $this->assertEquals('i:-6100;', $this->serialize($var= -6100));
  }

  #[@test]
  public function representationOfLongs() {
    $this->assertEquals('l:6100;', $this->serialize(new \lang\types\Long(6100)));
    $this->assertEquals('l:-6100;', $this->serialize(new \lang\types\Long(-6100)));
  }

  #[@test]
  public function representationOfFloats() {
    $this->assertEquals('d:0.1;', $this->serialize($var= 0.1));
    $this->assertEquals('d:-0.1;', $this->serialize($var= -0.1));
  }

  #[@test]
  public function representationOfDoubles() {
    $this->assertEquals('d:0.1;', $this->serialize(new \lang\types\Double(0.1)));
    $this->assertEquals('d:-0.1;', $this->serialize(new \lang\types\Double(-0.1)));
  }

  #[@test]
  public function representationOfString() {
    $this->assertEquals('s:11:"Hello World";', $this->serialize($var= 'Hello World'));
  }

  /**
   * Test serialization of an array containing three integers
   * (1, 2 and 5)
   *
   */
  #[@test]
  public function representationOfIntegerArray() {
    $this->assertEquals(
      'a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}',
      $this->serialize($var= array(1, 2, 5))
    );
  }

  /**
   * Test serialization of an array containing two strings
   * ("More" and "Power")
   *
   */
  #[@test]
  public function representationOfStringArray() {
    $this->assertEquals(
      'a:2:{i:0;s:4:"More";i:1;s:5:"Power";}',
      $this->serialize($var= array('More', 'Power'))
    );
  }

  #[@test]
  public function representationOfDate() {
    $this->assertEquals('T:1122644265;', $this->serialize(new \util\Date(1122644265)));
  }

  #[@test]
  public function representationOfHashmap() {
    $h= new Hashmap();
    $h->put('key', 'value');
    $h->put('number', '6100');

    $this->assertEquals(
      'a:2:{s:3:"key";s:5:"value";s:6:"number";s:4:"6100";}',
      $this->serialize($h)
    );
  }

  #[@test]
  public function representationOfMixedHashmap() {
    $h= new Hashmap();
    $h->put('key', 'value');
    $h->put('number', 6100);

    $this->assertEquals(
      'a:2:{s:3:"key";s:5:"value";s:6:"number";i:6100;}',
      $this->serialize($h)
    );
  }

  /**
   * Test serialization of a generic value object
   *
   * @see     xp://Person
   */
  #[@test]
  public function representationOfValueObject() {
    $this->assertEquals(
      'O:39:"net.xp_framework.unittest.remote.Person":2:{s:2:"id";i:1549;s:4:"name";s:11:"Timm Friebe";}',
      $this->serialize(new Person())
    );
  }

  /**
   * Test serialization of a mapped value object
   *
   * @see     xp://Person
   */
  #[@test]
  public function representationOfMappedValueObject() {
    $this->serializer->mapPackage('remote', \lang\reflect\Package::forName('net.xp_framework.unittest.remote'));
    $this->assertEquals(
      'O:13:"remote.Person":2:{s:2:"id";i:1549;s:4:"name";s:11:"Timm Friebe";}',
      $this->serialize(new Person())
    );
  }

  #[@test]
  public function representationOfEnum() {
    $this->assertEquals(
      'O:37:"net.xp_framework.unittest.remote.Enum":1:{s:4:"name";s:6:"Value1";}',
      $this->serialize(\net\xp_framework\unittest\remote\Enum::$Value1)
    );
  }

  #[@test]
  public function representationOfByes() {
    $this->assertEquals(
      "Y:4:\"\0abc\";",
      $this->serialize(new \lang\types\Bytes(array(0, 'a', 'b', 'c')))
    );
  }

  #[@test]
  public function valueOfInt() {
    $this->assertEquals(
      1,
      $this->unserialize('i:1;')
    );
  }

  #[@test]
  public function valueOfByte() {
    $this->assertEquals(
      new \lang\types\Byte(1),
      $this->unserialize('B:1;')
    );
  }

  #[@test]
  public function valueOfLong() {
    $this->assertEquals(
      new \lang\types\Long(12345),
      $this->unserialize('l:12345;')
    );
  }

  #[@test]
  public function valueOfFloat() {
    $this->assertEquals(
      new \lang\types\Float(1.5),
      $this->unserialize('f:1.5;')
    );
  }

  #[@test]
  public function valueOfDouble() {
    $this->assertEquals(
      1.5,
      $this->unserialize('d:1.5;')
    );
  }

  /**
   * Test deserialization of an integer
   *
   * @see     xp://Person
   */
  #[@test]
  public function valueOfShorts() {
    $this->assertEquals(
      new \lang\types\Short(1),
      $this->unserialize('S:1;')
    );
  }

  #[@test]
  public function valueOfDates() {
    $this->assertEquals(
      new \util\Date(328312800),
      $this->unserialize('T:328312800;')
    );
  }

  #[@test]
  public function valueOfEnum() {
    $obj= $this->unserialize('O:37:"net.xp_framework.unittest.remote.Enum":1:{s:4:"name";s:6:"Value1";};');
    $this->assertEquals(\net\xp_framework\unittest\remote\Enum::$Value1, $obj);
    $this->assertEquals(\net\xp_framework\unittest\remote\Enum::$Value1->ordinal(), $obj->ordinal());
    $this->assertEquals(\net\xp_framework\unittest\remote\Enum::$Value1->name(), $obj->name());
  }

  /**
   * Test deserialization of a class that does not exist will yield an UnknownRemoteObject
   *
   * @see   xp://net.xp_framework.unittest.remote.UnknownRemoteObjectTest
   */
  #[@test]
  public function valueOfUnknownObject() {
    $obj= $this->unserialize('O:40:"net.xp_framework.unittest.remote.Unknown":2:{s:2:"id";i:1549;s:4:"name";s:11:"Timm Friebe";};');
    $this->assertClass($obj, 'remote.UnknownRemoteObject');
    $this->assertEquals('net.xp_framework.unittest.remote.Unknown', $obj->__name);
    $this->assertEquals(1549, $obj->__members['id']);
    $this->assertEquals('Timm Friebe', $obj->__members['name']);
  }

  #[@test]
  public function valueOfException() {
    $exception= $this->unserialize(
      'E:46:"java.lang.reflect.UndeclaredThrowableException":3:{'.
      's:7:"message";s:12:"*** BLAM ***";'.
      's:5:"trace";a:1:{i:0;t:4:{s:4:"file";s:9:"Test.java";s:5:"class";s:4:"Test";s:6:"method";s:4:"main";s:4:"line";i:10;}}'.
      's:5:"cause";N;'.
      '}'
    );
    $this->assertClass($exception, 'remote.ExceptionReference');
    $this->assertEquals('java.lang.reflect.UndeclaredThrowableException', $exception->referencedClassname);
    $this->assertEquals('*** BLAM ***', $exception->getMessage());
    with ($trace= $exception->getStackTrace()); {
      $this->assertEquals(1, sizeof($trace));
      $this->assertClass($trace[0], 'remote.RemoteStackTraceElement');
      $this->assertEquals('Test.java', $trace[0]->file);
      $this->assertEquals('Test', $trace[0]->class);
      $this->assertEquals('main', $trace[0]->method);
      $this->assertEquals(10, $trace[0]->line);
    }
    $this->assertNull($exception->getCause());
  }

  /**
   * Test deserialization of an arraylist
   *
   * @see     xp://Person
   */
  #[@test]
  public function valueOfArrayList() {
    $return= $this->unserialize(
      'A:2:{O:39:"net.xp_framework.unittest.remote.Person":2:{s:2:"id";i:1549;s:4:"name";s:11:"Timm Friebe";}s:5:"World";}'
    );
    $this->assertClass($return, 'lang.types.ArrayList');
    $this->assertEquals(2, $return->length);
    $this->assertEquals(new Person(), $return[0]);
    $this->assertEquals('World', $return[1]);
  }

  #[@test]
  public function valueOfBytes() {
    $this->assertEquals(
      new \lang\types\Bytes(array(0, 'a', 'b', 'c')),
      $this->unserialize("Y:4:\"\0abc\";")
    );
  }

  #[@test]
  public function arrayList() {
    $list= $this->unserialize(
      'A:1:{a:2:{s:2:"la";s:2:"la";s:3:"foo";A:2:{a:1:{s:13:"verschachteln";s:7:"istToll";}s:6:"barbar";}}}'
    );
    $this->assertEquals($list, new \lang\types\ArrayList(
      array(
        'la'  => 'la',
        'foo' => new \lang\types\ArrayList(
          array('verschachteln' => 'istToll'),
          'barbar'
        )
      ))
    );
  }

  #[@test]
  public function genericClass() {
    $class= $this->unserialize('C:47:"net.xp_framework.easc.reflect.MethodDescription"');
    $this->assertTrue(is('remote.ClassReference', $class));
    $this->assertEquals("net.xp_framework.easc.reflect.MethodDescription", $class->referencedName());
  }

  #[@test]
  public function genericPackageMappedClass() {
    $this->serializer->mapPackage('net.xp_framework.easc.reflect', \lang\reflect\Package::forName('remote.reflect'));

    $class= $this->unserialize('C:47:"net.xp_framework.easc.reflect.MethodDescription"');
    $this->assertTrue(is('remote.ClassReference', $class));
    $this->assertEquals('remote.reflect.MethodDescription', $class->referencedName());
  }

  #[@test]
  public function remoteInterfaceMapping() {
    $this->serializer->mapPackage('net.xp_framework.easc.beans', \lang\reflect\Package::forName('remote.beans'));
    $this->serializer->mapping('I', new RemoteInterfaceMapping());

    $class= $this->unserialize(
      'I:12036987:{s:41:"net.xp_framework.easc.beans.BeanInterface";}',
      null,
      array('handler' => 'remote.protocol.XPProtocolHandler')
    );

    $this->assertSubclass($class, 'lang.reflect.Proxy');
    $this->assertSubclass($class, 'remote.beans.BeanInterface');
  }

  /**
   * Check serialization through custom class mappings. Check that the serialization
   * is always carried through by the best matching serializer mapping.
   *
   */
  #[@test]
  public function bestMapping() {
    $fooClass= \lang\ClassLoader::defineClass('net.xp_framework.unittest.remote.FooClass', 'lang.Object', null);
    $barClass= \lang\ClassLoader::defineClass('net.xp_framework.unittest.remote.BarClass', 'FooClass', null);
    $bazClass= \lang\ClassLoader::defineClass('net.xp_framework.unittest.remote.BazClass', 'BarClass', null);
    $bazookaClass= \lang\ClassLoader::defineClass('net.xp_framework.unittest.remote.BazookaClass', 'BazClass', null);

    // Both must be serialized with the FOO mapping, because both are Foo or Foo-derived objects.
    $this->serializer->mapping('FOO', newinstance('remote.protocol.SerializerMapping', array(), '{
      function handledClass() { return XPClass::forName("net.xp_framework.unittest.remote.FooClass"); }
      function representationOf($serializer, $value, $context= array()) { return "FOO:"; }
      public function valueOf($serializer, $serialized, $context= array()) { return NULL; }
    }'));
    $this->assertEquals('FOO:', $this->serialize(new FooClass()));
    $this->assertEquals('FOO:', $this->serialize(new BarClass()));
    $this->assertEquals('FOO:', $this->serialize(new BazClass()));

    // Add more concrete mapping for BAR. Foo must still be serialized with FOO, but the BarClass-object
    // has a better matching mapping.
    $this->serializer->mapping('BAR', newinstance('remote.protocol.SerializerMapping', array(), '{
      function handledClass() { return XPClass::forName("net.xp_framework.unittest.remote.BarClass"); }
      function representationOf($serializer, $value, $context= array()) { return "BAR:"; }
      function valueOf($serializer, $serialized, $context= array()) { return NULL; }
    }'));
    $this->assertEquals('FOO:', $this->serialize(new FooClass()));
    $this->assertEquals('BAR:', $this->serialize(new BarClass()));
    $this->assertEquals('BAR:', $this->serialize(new BazClass()));
    $this->assertEquals('BAR:', $this->serialize(new BazookaClass()));
  }
}
