<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest', 'lang.Enum', 'xp.compiler.checks.RoutinesVerification');

  /**
   * Tests class declarations
   *
   */
  class net·xp_lang·tests·execution·source·ExtensionMethodsTest extends ExecutionTest {

    /**
     * Sets up test case and adds IsAsssignale check
     *
     */
    public function setUp() {
      parent::setUp();
      $this->check(new RoutinesVerification(), TRUE);
    }

    /**
     * Test extending a class
     *
     */
    #[@test]
    public function classExtension() {
      $class= $this->define('class', 'ClassExtension', NULL, '{
        public static lang.reflect.Method[] methodsNamed(this lang.XPClass $class, text.regex.Pattern $pattern) {
          $r= new lang.reflect.Method[] { };
          foreach ($method in $class.getMethods()) {
            if ($pattern.matches($method.getName())) $r[]= $method;
          }
          return $r;
        }
        
        public lang.reflect.Method runMethod() {
          return self::class.methodsNamed(text.regex.Pattern::compile("run"))[0];
        }
      }');
      $this->assertEquals(
        $class->getMethod('runMethod'), 
        $class->newInstance()->runMethod()
      );
    }

    /**
     * Test extending a primitive
     *
     */
    #[@test]
    public function stringExtension() {
      $class= $this->define('class', 'StringExtension', NULL, '{
        public static bool equal(this string $in, string $cmp, bool $strict) {
          return $strict ? $in === $cmp : $in == $cmp;
        }
        
        public bool run(string $cmp) {
          return "hello".equal($cmp, true);
        }
      }');
      $instance= $class->newInstance();
      $this->assertFalse($instance->run('world'));
      $this->assertTrue($instance->run('hello'));
    }
 
    /**
     * Test extending an array
     *
     */
    #[@test]
    public function arrayExtension() {
      $class= $this->define('class', 'MethodExtension', NULL, '{
        protected static string[] names(this lang.reflect.Method[] $methods) {
          $r= [];
          foreach ($method in $methods) {
            $r[]= $method.getName();
          }
          return $r;
        }
        
        public bool run(XPClass $class) {
          return $class.getMethods().names();
        }
      }');
      $instance= $class->newInstance();
      $this->assertEquals(
        array('hashCode', 'equals', 'getClassName', 'getClass', 'toString'),
        $instance->run(XPClass::forName('lang.Object'))
      );
    }

    /**
     * Test extending an array
     *
     */
    #[@test]
    public function arrayOfSubclassExtension() {
      $class= $this->define('class', 'ObjectExtension', NULL, '{
        protected static string[] hashCodes(this Object[] $objects) {
          $r= [];
          foreach ($object in $objects) {
            $r[]= $object.hashCode();
          }
          return $r;
        }
        
        public bool run(XPClass[] $classes) {
          return $classes.hashCodes();
        }
      }');
      $instance= $class->newInstance();
      $this->assertEquals(
        array('XPClass:lang.Object', 'XPClass:lang.Generic'),
        $instance->run(array(XPClass::forName('lang.Object'), XPClass::forName('lang.Generic')))
      );
    }

    /**
     * Test extending a map
     *
     */
    #[@test]
    public function mapExtension() {
      $class= $this->define('class', 'MapExtension', NULL, '{
        protected static string[] keys(this [:string] $map) {
          $r= [];
          foreach ($key, $value in $map) {
            $r[]= $key;
          }
          return $r;
        }
        
        public bool run([:string] $map) {
          return $map.keys();
        }
      }');
      $instance= $class->newInstance();
      $this->assertEquals(
        array('color', 'name', 'model'),
        $instance->run(array('color' => 'black', 'name' => 'Camera', 'model' => '500'))
      );
    }

    /**
     * Test extending a map
     *
     */
    #[@test]
    public function mapOfSubclassExtension() {
      $class= $this->define('class', 'ObjectMapExtension', NULL, '{
        protected static Object[] values(this [:Object] $map) {
          $r= [];
          foreach ($value in $map) {
            $r[]= $value;
          }
          return $r;
        }
        
        public bool run([:XPClass] $map) {
          return $map.values();
        }
      }');
      $instance= $class->newInstance();
      $this->assertEquals(
        array(XPClass::forName('lang.Object'), $this->getClass()),
        $instance->run(array('object' => XPClass::forName('lang.Object'), 'self' => $this->getClass()))
      );
    }

    /**
     * Test extension methods do not apply if not imported
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= 'Call to undefined method lang.XPClass::fieldsNamed() from scope SourceExtensionDoesNotApplyIfNotImported·0')]
    public function extensionDoesNotApplyIfNotImported() {
      $this->run('return self::class.fieldsNamed(text.regex.Pattern::compile("_.*"));');
    }

    /**
     * Test extension methods do not apply if not imported
     *
     */
    #[@test, @expect(class= 'lang.Error', withMessage= 'Call to undefined method lang.XPClass::fieldsNamed() from scope SourceExtensionDoesNotApplyIfOnlyUsed·0')]
    public function extensionDoesNotApplyIfOnlyUsed() {
      $class= $this->define('class', 'ClassFieldExtension1', NULL, '{
        public static lang.reflect.Field[] fieldsNamed(this lang.XPClass $class, text.regex.Pattern $pattern) {
          throw new IllegalStateException("Unreachable");
        }
      }', array('package demo;'));
      $this->run('return '.$class->getName().'::class.fieldsNamed(text.regex.Pattern::compile("_.*"));');
    }

    /**
     * Test extension methods do not apply if not imported
     *
     */
    #[@test]
    public function extensionApplies() {
      $class= $this->define('class', 'ClassFieldExtension2', NULL, '{
        public static lang.reflect.Field[] fieldsNamed(this lang.XPClass $class, text.regex.Pattern $pattern) {
          $r= new lang.reflect.Field[] { };
          foreach ($field in $class.getFields()) {
            if ($pattern.matches($field.getName())) $r[]= $field;
          }
          return $r;
        }
      }', array('package demo;'));

      $r= $this->run(
        'return '.$class->getName().'::class.fieldsNamed(text.regex.Pattern::compile("_.*"));', 
        array('import '.$class->getName().';')
      );
    }

    /**
     * Test extension methods must be static
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function nonStaticMethod() {
      $this->define('class', 'StringIncorrectExtension', NULL, '{
        public bool equal(this string $in, string $cmp, bool $strict) {
          return $strict ? $in === $cmp : $in == $cmp;
        }
        
        public bool run(string $cmp) {
          return "hello".equal($cmp, true);
        }
      }');
    }
  }
?>
