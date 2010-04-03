<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.ClassLoader'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.reflect.Constructor
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Field
   */
  class AccessibilityTest extends TestCase {
  
    /**
     * Invoke protected constructor from same class
     *
     */
    #[@test]
    public function invokingProtectedConstructorFromSameClass() {
      $class= ClassLoader::defineClass('ProtectedConstructor', 'lang.Object', array(), '{
        protected function __construct() { }
        
        public static function main(array $args) {
          return XPClass::forName("ProtectedConstructor")->getConstructor()->newInstance(array());
        }
      }');
      $instance= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertInstanceOf($class, $instance);
    }

    /**
     * Invoke protected constructor from child class
     *
     */
    #[@test]
    public function invokingProtectedConstructorFromChildClass() {
      $class= ClassLoader::defineClass('ProtectedConstructorChild', 'ProtectedConstructor', array(), '{
        public static function main(array $args) {
          return XPClass::forName("ProtectedConstructorChild")->getConstructor()->newInstance(array());
        }
      }');
      $instance= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertInstanceOf($class, $instance);
    }

    /**
     * Invoke protected method from same class
     *
     */
    #[@test]
    public function invokingProtectedMethodFromSameClass() {
      $class= ClassLoader::defineClass('ProtectedMethod', 'lang.Object', array(), '{
        protected function target() {
          return $this;
        }
        
        public static function main(array $args) {
          return XPClass::forName("ProtectedMethod")->getMethod("target")->invoke(new self(), array());
        }
      }');
      $instance= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertInstanceOf($class, $instance);
    }

    /**
     * Invoke protected method from parent class
     *
     */
    #[@test]
    public function invokingProtectedMethodFromParentClass() {
      $class= ClassLoader::defineClass('ProtectedMethodParent', 'ProtectedMethod', array(), '{
        public static function main(array $args) {
          return XPClass::forName("ProtectedMethod")->getMethod("target")->invoke(new self(), array());
        }
      }');
      $instance= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertInstanceOf($class, $instance);
    }

    /**
     * Invoke protected method from child class
     *
     */
    #[@test]
    public function invokingProtectedMethodFromChildClass() {
      $class= ClassLoader::defineClass('ProtectedMethodChild', 'ProtectedMethod', array(), '{
        public static function main(array $args) {
          return XPClass::forName("ProtectedMethodChild")->getMethod("target")->invoke(new self(), array());
        }
      }');
      $instance= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertInstanceOf($class, $instance);
    }

    /**
     * Invoke protected method from same class
     *
     */
    #[@test]
    public function invokingProtectedStaticMethodFromSameClass() {
      $class= ClassLoader::defineClass('ProtectedStaticMethod', 'lang.Object', array(), '{
        protected static function target() {
          return "Invoked";
        }
        
        public static function main(array $args) {
          return XPClass::forName("ProtectedStaticMethod")->getMethod("target")->invoke(NULL, array());
        }
      }');
      $value= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertEquals("Invoked", $value);
    }

    /**
     * Read protected field from same class
     *
     */
    #[@test]
    public function protectedMemberFromSameClass() {
      $class= ClassLoader::defineClass('ProtectedMember', 'lang.Object', array(), '{
        protected $target= "Target";
        
        public static function main(array $args) {
          $f= XPClass::forName("ProtectedMember")->getField("target");
          $i= new self();
          $f->set($i, "Modified");
          return $f->get($i);
        }
      }');
      $value= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertEquals("Modified", $value);
    }

    /**
     * Write protected static field from same class
     *
     */
    #[@test]
    public function protectedStaticMemberFromSameClass() {
      $class= ClassLoader::defineClass('ProtectedStaticMember', 'lang.Object', array(), '{
        protected static $target = "Target";
        
        public static function main(array $args) {
          $f= XPClass::forName("ProtectedStaticMember")->getField("target");
          $f->set(NULL, "Modified");
          return $f->get(NULL);
        }
      }');
      $value= $class->getMethod('main')->invoke(NULL, array(array()));
      $this->assertEquals("Modified", $value);
    }
  }
?>
