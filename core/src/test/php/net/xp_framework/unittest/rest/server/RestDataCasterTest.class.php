<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.server.RestDataCaster'
  );
  
  /**
   * Test data casting
   *
   */
  class RestDataCasterTest extends TestCase {
    private
      $sut= NULL;
    
    protected static
      $arrayThree= NULL,
      $arrayThreeHash= NULL,
      $arrayTwoHash= NULL,
      $arrayNullField= NULL,
      $stdClassThree= NULL,
      $objPrivateFields= NULL,
      $objThreeFields= NULL,
      $objThreeTypedFields= NULL,
      $objThreeMethods= NULL,
      $objTwoFields= NULL,
      $objNullField= NULL;
    
    /**
     * Before class setup
     * 
     */
    #[@beforeClass]
    public static function beforeClass() {
      self::$arrayThree= array('1', '2', '3');
      self::$arrayThreeHash= array('one' => '1', 'two' => '2', 'three' => '3');
      self::$arrayTwoHash= array('one' => '1', 'two' => '2');
      self::$arrayNullField= array('nullField' => null);
      
      self::$stdClassThree= new stdClass();
      self::$stdClassThree->one= '1';
      self::$stdClassThree->two= '2';
      self::$stdClassThree->three= '3';
      
      self::$objPrivateFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        protected $three= "3";
      }');
      self::$objTwoFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
      }');
      self::$objThreeFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        public $three= "3";
        public function equals($o) { return $this->one == $o->one && $this->two == $o->two && $this->three == $o->three; }
      }');
      self::$objThreeTypedFields= newinstance('lang.Object', array(), '{
        public $one= "1";
        public $two= "2";
        #[@type(\''.self::$objTwoFields->getClassName().'\')]
        public $three= "3";
      }');
      self::$objThreeMethods= newinstance('lang.Object', array(), '{
        protected $one= "1";
        protected $two= "2";
        protected $three= "3";
        
        public function getOne() { return $this->one; }
        public function setOne($v) { $this->one= $v; }
        public function getTwo() { return $this->two; }
        public function setTwo($v) { $this->two= $v; }
        public function getThree() { return $this->three; }
        public function setThree($v) { $this->three= $v; }
      }');
      self::$objNullField= newinstance('lang.Object', array(), '{
        public $nullField= NULL;
      }');
    }
    
    /**
     * Set up
     */
    public function setUp() {
      $this->sut= new RestDataCaster();
    }
    
    /**
     * Test simplify primitives
     * 
     */
    #[@test]
    public function simplifyPrimitives() {
      $this->assertEquals(1, $this->sut->simple(1));
      $this->assertEquals('test', $this->sut->simple('test'));
      $this->assertTrue($this->sut->simple(TRUE));
    }

    /**
     * Test simplify primitives object
     * 
     */
    #[@test]
    public function simplifyPrimitivesObject() {
      $this->assertEquals(1, $this->sut->simple(new Integer(1)));
      $this->assertEquals('test', $this->sut->simple(new String('test')));
      $this->assertEquals(TRUE, $this->sut->simple(new Boolean(TRUE)));
    }
    
    /**
     * Test simplify array
     * 
     */
    #[@test]
    public function simplifyArray() {
      $this->assertEquals(self::$arrayThree, $this->sut->simple(self::$arrayThree));
    }

    /**
     * Test simplify array type
     *
     */
    #[@test]
    public function simplifyArrayType() {
      $this->assertEquals(array(
        self::$arrayThreeHash, self::$arrayThreeHash
      ), $this->sut->simple(
        array(self::$objThreeFields, self::$objThreeFields)
      ));
    }
    
    /**
     * Test simplify hashmap
     * 
     */
    #[@test]
    public function simplifyHashmap() {
      $this->assertEquals(self::$arrayThreeHash, $this->sut->simple(new Hashmap(self::$arrayThreeHash)));
    }
    
    /**
     * Test simplify stdclass
     * 
     */
    #[@test]
    public function simplifyStdclass() {
      $this->assertEquals(self::$arrayThreeHash, $this->sut->simple(self::$stdClassThree));
    }
    
    /**
     * Test simplify Object with public fields
     * 
     */
    #[@test]
    public function simplifyObjectWithPublicFields() {
      $this->assertEquals(self::$arrayThreeHash, $this->sut->simple(self::$objThreeFields));
    }
    
    /**
     * Test simplify Object with private fields but accessor functions
     * 
     */
    #[@test]
    public function simplifyObjectWithPrivateFieldsButPublicMethods() {
      $this->assertEquals(self::$arrayThreeHash, $this->sut->simple(self::$objThreeMethods));
    }
    
    /**
     * Test simplify Object with Object inside
     * 
     */
    #[@test]
    public function simplifyObjectWithObjectInside() {
      $instance= clone self::$objThreeFields;
      $instance->three= self::$objTwoFields;
      
      $result= self::$arrayThreeHash;
      $result['three']= self::$arrayTwoHash;
      
      $this->assertEquals($result, $this->sut->simple($instance));
    }
    
    /**
     * Test simplify Object ignoring private fields
     * 
     */
    #[@test]
    public function simplifyObjectIgnoringPrivateFields() {
      $this->assertEquals(self::$arrayTwoHash, $this->sut->simple(self::$objPrivateFields));
    }
    
    #[@test]
    public function simple_should_ignore_public_null_fields_by_default() {
      $this->sut->setIgnoreNullFields(FALSE);
      $this->assertEquals(self::$arrayNullField, $this->sut->simple(self::$objNullField));
    }
    
    #[@test]
    public function simple_should_ignore_public_null_fields_if_defined_so() {
      
      $this->assertEquals(array(), $this->sut->simple(self::$objNullField));
    }
    /**
     * Test complexify primitives
     * 
     */
    #[@test]
    public function complexifyPrimitives() {
      $this->assertEquals(1, $this->sut->complex(1, XPClass::forName('lang.types.Integer')));
      $this->assertEquals('test', $this->sut->complex('test', XPClass::forName('lang.types.String')));
      $this->assertTrue($this->sut->simple(TRUE, XPClass::forName('lang.types.Boolean')));
    }
    
    /**
     * Test complexify primitives object
     * 
     */
    #[@test]
    public function complexifyPrimitivesObject() {
      $this->assertEquals(1, $this->sut->complex(1, Primitive::$INT));
      $this->assertEquals('test', $this->sut->complex('test', Primitive::$STRING));
      $this->assertEquals(TRUE, $this->sut->complex(TRUE, Primitive::$BOOLEAN));
    }
    
    /**
     * Test complexify wrong data types to primitive type
     * 
     */
    #[@test]
    public function complexifyToPrimitiveTypesFails() {
      $casts= array(
        array(Primitive::$INT, array()),
        array(Primitive::$INT, new Object()),
        array(Primitive::$INT, '1.3'),
        array(Primitive::$INT, 'test'),
        
        array(Primitive::$BOOLEAN, array()),
        array(Primitive::$BOOLEAN, new Object()),
        
        array(Primitive::$STRING, array()),
        array(Primitive::$STRING, new Object()),
      );
      
      foreach ($casts as $cast) {
        try {
          $this->sut->complex($cast[1], $cast[0]);
          
          throw new IllegalStateException('Cast '.xp::typeOf($cast[1]).' to '.$cast[0]->getName().' should throw exception');
        } catch (ClassCastException $e) {
          // Ignore this, because it's assumed and OK
        }
      }
    }
    
    /**
     * Test complexify wrong data types to lang.Object
     * 
     */
    #[@test]
    public function complexifyToObjectFails() {
      $casts= array(1, TRUE, 'test');
      
      foreach ($casts as $cast) {
        try {
          $this->sut->complex($cast, XPClass::forName('lang.Object'));
          
          throw new IllegalStateException('Cast '.xp::typeOf($cast).' to lang.Object should throw exception');
        } catch (ClassCastException $e) {
          // Ignore this, because it's assumed and OK
        }
      }
    }
    
    /**
     * Test complexify array
     * 
     */
    #[@test]
    public function complexifyArray() {
      $this->assertEquals(self::$arrayThree, $this->sut->complex(self::$arrayThree, XPClass::forName('lang.types.ArrayList')));
    }

    /**
     * Test complexify array type
     *
     */
    #[@test]
    public function complexifyArrayType() {
      $this->assertEquals(array(
        self::$objThreeFields, self::$objThreeFields
      ), $this->sut->complex(
        array((array)self::$objThreeFields, (array)self::$objThreeFields),
        Type::forName(self::$objThreeFields->getClassName().'[]')
      ));
    }
    
    /**
     * Test complexify hashmap
     * 
     */
    #[@test]
    public function complexifyHashmap() {
      $this->assertEquals(new Hashmap(self::$arrayThreeHash), $this->sut->complex(self::$arrayThreeHash, XPClass::forName('util.Hashmap')));
    }
    
    /**
     * Test complexify stdclass
     * 
     */
    #[@test]
    public function complexifyStdclass() {
      $casted= $this->sut->complex(self::$arrayThreeHash, new Type('php.stdClass'));
      
      $this->assertEquals((array)self::$stdClassThree, (array)$casted);
    }
    
    /**
     * Test complexify Object with public fields
     * 
     */
    #[@test]
    public function complexifyObjectWithPublicFields() {
      $casted= $this->sut->complex(self::$arrayThreeHash, self::$objThreeFields->getClass());
      
      $this->assertEquals('1', $casted->one);
      $this->assertEquals('2', $casted->two);
      $this->assertEquals('3', $casted->three);
    }
    
    /**
     * Test complexify Object with private fields but accessor functions
     * 
     */
    #[@test]
    public function complexifyObjectWithPrivateFieldsButPublicMethods() {
      $casted= $this->sut->complex(self::$arrayThreeHash, self::$objThreeMethods->getClass());
      
      $this->assertEquals('1', $casted->getOne());
      $this->assertEquals('2', $casted->getTwo());
      $this->assertEquals('3', $casted->getThree());
    }
    
    /**
     * Test complexify array with array inside
     * 
     */
    #[@test]
    public function complexifyObjectWithObjectInside() {
      $instance= clone self::$objThreeFields;
      $instance->three= self::$objTwoFields;
      
      $result= self::$arrayThreeHash;
      $result['three']= self::$arrayTwoHash;
      
      $casted= $this->sut->complex($result, self::$objThreeTypedFields->getClass());
      
      $this->assertInstanceOf(self::$objThreeTypedFields->getClassName(), $casted);
      $this->assertInstanceOf(self::$objTwoFields->getClassName(), $casted->three);
    }
    
    /**
     * Test complexify array list with wrong data
     * 
     */
    #[@test, @expect('lang.ClassCastException')]
    public function complexifyArrayListWithWrongData() {
      $this->sut->complex(1, XPClass::forName('lang.types.ArrayList'));
    }
    
    /**
     * Test complexify hash map with wrong data
     * 
     */
    #[@test, @expect('lang.ClassCastException')]
    public function complexifyHashmapWithWrongData() {
      $this->sut->complex(1, XPClass::forName('util.Hashmap'));
    }
    
    /**
     * Test complexify stdClass with wrong data
     * 
     */
    #[@test, @expect('lang.ClassCastException')]
    public function complexifyStdClassWithWrongData() {
      $this->sut->complex(1, new Type('php.stdClass'));
    }
    
    /**
     * Test complexify with wrong target type
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function complexifyWithWrongType() {
      $this->sut->complex(1, new Object());
    }
  }
?>
