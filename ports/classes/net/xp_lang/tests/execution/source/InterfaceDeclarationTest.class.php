<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses(
    'net.xp_lang.tests.execution.source.ExecutionTest', 
    'xp.compiler.checks.RoutinesVerification',
    'xp.compiler.checks.PropertiesVerification',
    'xp.compiler.checks.FieldsVerification'
  );

  /**
   * Tests interface declarations
   *
   */
  class net·xp_lang·tests·execution·source·InterfaceDeclarationTest extends ExecutionTest {

    /**
     * Sets up test case and adds RoutinesVerification check
     *
     */
    public function setUp() {
      parent::setUp();
      $this->check(new RoutinesVerification(), TRUE);
      $this->check(new FieldsVerification(), TRUE);
      $this->check(new PropertiesVerification(), TRUE);
    }
    
    /**
     * Test declaring an interface
     *
     */
    #[@test]
    public function comparableInterface() {
      $class= $this->define('interface', 'Comparable', NULL, '{
        public int compareTo(Generic $in);
      }');
      $this->assertEquals('SourceComparable', $class->getName());
      $this->assertTrue($class->isInterface());
      
      with ($method= $class->getMethod('compareTo')); {
        $this->assertEquals('compareTo', $method->getName());
        $this->assertEquals(MODIFIER_PUBLIC | MODIFIER_ABSTRACT, $method->getModifiers());
        $this->assertEquals(Primitive::$INTEGER, $method->getReturnType());
        
        with ($params= $method->getParameters()); {
          $this->assertEquals(1, sizeof($params));
          $this->assertEquals(XPClass::forName('lang.Generic'), $params[0]->getType());
        }
      }
    }

    /**
     * Test declaring an interface with fields.
     *
     * TODO: This should throw a CompilationException
     */
    #[@test, @expect('lang.FormatException')]
    public function interfacesMayNotHaveFields() {
      $this->define('interface', 'WithField', NULL, '{
        public int $field = 0;
      }');
    }

    /**
     * Test declaring an interface with properties.
     *
     * TODO: This should throw a CompilationException
     */
    #[@test, @expect('lang.FormatException')]
    public function interfacesMayNotHaveProperties() {
      $this->define('interface', 'WithProperty', NULL, '{
        public int property { get { return 0; } }
      }');
    }

    /**
     * Test declaring a method inside an interface with body
     *
     * TODO: This should throw a CompilationException
     */
    #[@test, @expect('lang.FormatException')]
    public function interfaceMethodsMayNotContainBody() {
      $this->define('interface', 'WithMethod', NULL, '{
        public int method() {
          return 0;
        }
      }');
    }

    /**
     * Test a generic interface declaration
     *
     */
    #[@test]
    public function genericInterface() {
      $class= $this->define('interface', 'Filter<T>', NULL, '{ 
        public bool accept(T $element);
      }');
      $this->assertTrue($class->isGenericDefinition());
      $this->assertEquals(array('T'), $class->genericComponents());

      $this->assertEquals(array('params' => 'T'), $class->getMethod('accept')->getAnnotation('generic'));
    }
  }
?>
