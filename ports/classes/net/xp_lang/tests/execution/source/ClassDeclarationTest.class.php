<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests class declarations
   *
   */
  class net·xp_lang·tests·execution·source·ClassDeclarationTest extends ExecutionTest {
    
    /**
     * Test declaring a class
     *
     */
    #[@test]
    public function echoClass() {
      $class= $this->define('class', 'EchoClass', NULL, '{
        public static string[] echoArgs(string[] $args) {
          return $args;
        }
      }');
      $this->assertEquals('SourceEchoClass', $class->getName());
      $this->assertFalse($class->isInterface());
      $this->assertFalse($class->isEnum());
      
      with ($method= $class->getMethod('echoArgs')); {
        $this->assertEquals('echoArgs', $method->getName());
        $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $method->getModifiers());
        $this->assertEquals(ArrayType::forName('string[]'), $method->getReturnType());
        
        with ($params= $method->getParameters()); {
          $this->assertEquals(1, sizeof($params));
          $this->assertEquals(ArrayType::forName('string[]'), $params[0]->getType());
        }
        
        $in= array('Hello', 'World');
        $this->assertEquals($in, $method->invoke(NULL, array($in)));
      }
    }

    /**
     * Test declaring a class
     *
     */
    #[@test]
    public function genericClass() {
      $class= $this->define('class', 'ListOf<T>', NULL, '{
        protected T[] $elements;
        
        public __construct(T?... $initial) {
          $this.elements= $initial;
        }
        
        public T add(T? $element) {
          $this.elements[]= $element;
          return $element;
        }
        
        public T[] elements() {
          return $this.elements;
        }
        
        public static void test(string[] $args) {
          $l= new self<string>("Ciao", "Salut");
          foreach ($arg in $args) {
            $l.add($arg);
          }
          return $l;
        }
      }');
      
      $this->assertTrue($class->isGenericDefinition());
      $this->assertEquals(array('T'), $class->genericComponents('generic'));
      $this->assertEquals(array('params' => 'T', 'return' => 'T'), $class->getMethod('add')->getAnnotation('generic'));
      $this->assertEquals(array('return' => 'T[]'), $class->getMethod('elements')->getAnnotation('generic'));
      $this->assertEquals(
        array('Ciao', 'Salut', 'Hello', 'Hallo', 'Hola'),
        $class->getMethod('test')->invoke(NULL, array(array('Hello', 'Hallo', 'Hola')))->elements()
      );
    }

    /**
     * Test declaring a class
     *
     */
    #[@test]
    public function classInsidePackage() {
      $class= $this->define('class', 'ClassInPackage', NULL, '{ }', array('package demo;'));
      $this->assertEquals('demo.SourceClassInPackage', $class->getName());
      $this->assertEquals('SourceClassInPackage', xp::reflect($class->getName()));
    }

    /**
     * Test declaring a class
     *
     */
    #[@test]
    public function packageClassInsidePackage() {
      $class= $this->define('package class', 'PackageClassInPackage', NULL, '{ }', array('package demo;'));
      $this->assertEquals('demo.SourcePackageClassInPackage', $class->getName());
      $this->assertEquals('demo·SourcePackageClassInPackage', xp::reflect($class->getName()));
    }

    /**
     * Test declaring an interface
     *
     */
    #[@test]
    public function serializableInterface() {
      $class= $this->define('interface', 'Paintable', NULL, '{
        public void paint(Generic $canvas);
      }');
      $this->assertEquals('SourcePaintable', $class->getName());
      $this->assertTrue($class->isInterface());
      $this->assertFalse($class->isEnum());
      
      with ($method= $class->getMethod('paint')); {
        $this->assertEquals('paint', $method->getName());
        $this->assertEquals(MODIFIER_PUBLIC | MODIFIER_ABSTRACT, $method->getModifiers());
        $this->assertEquals(Type::$VOID, $method->getReturnType());
        
        with ($params= $method->getParameters()); {
          $this->assertEquals(1, sizeof($params));
          $this->assertEquals(XPClass::forName('lang.Generic'), $params[0]->getType());
        }
      }
    }

    /**
     * Test static initializer block
     *
     */
    #[@test]
    public function staticInitializer() {
      $class= $this->define('class', 'StaticInitializer', NULL, '{
        public static self $instance;
        
        static {
          self::$instance= new self();
        }
      }');
      $this->assertInstanceOf($class, $class->getField('instance')->get(NULL));
    }

    /**
     * Test class constants
     *
     */
    #[@test]
    public function classConstants() {
      $class= $this->define('class', 'ExecutionTestConstants', NULL, '{
        const int THRESHHOLD= 5;
        const string NAME= "Timm";
        const double TIMEOUT= 0.5;
        const bool PHP= FALSE;
        const var NOTHING = NULL;
      }');
      
      $this->assertEquals(5, $class->_reflect->getConstant('THRESHHOLD'));
      $this->assertEquals('Timm', $class->_reflect->getConstant('NAME'));
      $this->assertEquals(0.5, $class->_reflect->getConstant('TIMEOUT'));
      $this->assertEquals(FALSE, $class->_reflect->getConstant('PHP'));
      $this->assertEquals(NULL, $class->_reflect->getConstant('NOTHING'));
    }

    /**
     * Test static member initialization to complex expressions.
     *
     */
    #[@test]
    public function staticMemberInitialization() {
      $class= $this->define('class', $this->name, NULL, '{
        public static XPClass $arrayClass = lang.types.ArrayList::class;
      }');
      $this->assertInstanceOf('lang.XPClass', $class->getField('arrayClass')->get(NULL));
    }

    /**
     * Test member initialization to complex expressions.
     *
     */
    #[@test]
    public function memberInitialization() {
      $class= $this->define('class', $this->name, NULL, '{
        public lang.types.ArrayList $elements = lang.types.ArrayList::class.newInstance(1, 2, 3);
      }');
      
      with ($instance= $class->newInstance(), $elements= $class->getField('elements')->get($instance)); {
        $this->assertInstanceOf('lang.types.ArrayList', $elements);
        $this->assertEquals(new ArrayList(1, 2, 3), $elements);
      }
    }

    /**
     * Test member initialization to complex expressions.
     *
     */
    #[@test]
    public function memberInitializationWithParent() {
      $class= $this->define('class', $this->name, 'unittest.TestCase', '{
        public lang.types.ArrayList $elements = lang.types.ArrayList::class.newInstance(1, 2, 3);
      }');
      
      with ($instance= $class->newInstance($this->name)); {
        $this->assertEquals($this->name, $instance->getName());
        $elements= $class->getField('elements')->get($instance);
        $this->assertInstanceOf('lang.types.ArrayList', $elements);
        $this->assertEquals(new ArrayList(1, 2, 3), $elements);
      }
    }

    /**
     * Test class annotations
     *
     */
    #[@test]
    public function classAnnotation() {
      $fixture= $this->define('class', 'AnnotationsFor'.$this->name, NULL, '{ }', array('[@fixture]'));
      $this->assertTrue($fixture->hasAnnotation('fixture'));
      $this->assertEquals(NULL, $fixture->getAnnotation('fixture'));
    }

    /**
     * Test interface annotations
     *
     */
    #[@test]
    public function interfaceAnnotation() {
      $fixture= $this->define('interface', 'AnnotationsFor'.$this->name, NULL, '{ }', array('[@fixture]'));
      $this->assertTrue($fixture->hasAnnotation('fixture'));
      $this->assertEquals(NULL, $fixture->getAnnotation('fixture'));
    }

    /**
     * Test interface annotations
     *
     */
    #[@test]
    public function enumAnnotation() {
      $fixture= $this->define('enum', 'AnnotationsFor'.$this->name, NULL, '{ }', array('[@fixture]'));
      $this->assertTrue($fixture->hasAnnotation('fixture'));
      $this->assertEquals(NULL, $fixture->getAnnotation('fixture'));
    }
  }
?>
