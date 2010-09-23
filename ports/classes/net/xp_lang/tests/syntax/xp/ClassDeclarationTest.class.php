<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class ClassDeclarationTest extends ParserTestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer($src, '<string:'.$this->name.'>'))->declaration;
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function emtpyClass() {
      $this->assertEquals(
        new ClassNode(
          0,                          // Modifiers
          NULL,                       // Annotations
          new TypeName('Empty'),      // Name
          NULL,                       // Parent
          array(),                    // Implements
          NULL                        // Body
        ), 
        $this->parse('class Empty { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function classWithStaticInitializer() {
      $this->assertEquals(
        new ClassNode(
          0,                          // Modifiers
          NULL,                       // Annotations
          new TypeName('Driver'),     // Name
          NULL,                       // Parent
          array(),                    // Implements
          array(
            new StaticInitializerNode(array(
            ))
          )
        ), 
        $this->parse('class Driver { static { } }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function annotatedClass() {
      $this->assertEquals(
        new ClassNode(
          0,
          array(new AnnotationNode(array(
            'type'       => 'Deprecated'
          ))),
          new TypeName('Empty'),
          NULL,
          array(),
          NULL
        ), 
        $this->parse('[@Deprecated] class Empty { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function classWithParentClass() {
      $this->assertEquals(
        new ClassNode(
          0,
          NULL,
          new TypeName('Class'),
          new TypeName('lang.Object'),
          array(),
          NULL
        ), 
        $this->parse('class Class extends lang.Object { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function classWithInterface() {
      $this->assertEquals(
        new ClassNode(
          0,
          NULL,
          new TypeName('HttpConnection'),
          NULL,
          array(new TypeName('Traceable')),
          NULL
        ), 
        $this->parse('class HttpConnection implements Traceable { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function classWithInterfaces() {
      $this->assertEquals(
        new ClassNode(
          0,
          NULL,
          new TypeName('Math'),
          NULL,
          array(new TypeName('util.Observer'), new TypeName('Traceable')),
          NULL
        ), 
        $this->parse('class Math implements util.Observer, Traceable { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function classWithParentClassAndInterface() {
      $this->assertEquals(
        new ClassNode(
          0,
          NULL,
          new TypeName('Integer'),
          new TypeName('Number'),
          array(new TypeName('Observer')),
          NULL
        ), 
        $this->parse('class Integer extends Number implements Observer { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function publicClass() {
      $this->assertEquals(
        new ClassNode(
          MODIFIER_PUBLIC,
          NULL,
          new TypeName('Class'),
          NULL,
          array(),
          NULL
        ), 
        $this->parse('public class Class { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function abstractClass() {
      $this->assertEquals(
        new ClassNode(
          MODIFIER_PUBLIC | MODIFIER_ABSTRACT,
          NULL,
          new TypeName('Base'),
          NULL,
          array(),
          NULL
        ), 
        $this->parse('public abstract class Base { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function genericClass() {
      $this->assertEquals(
        new ClassNode(
          0,
          NULL,
          new TypeName('Class', array(new TypeName('T'))),
          NULL,
          array(),
          NULL
        ), 
        $this->parse('class Class<T> { }')
      );
    }

    /**
     * Test class declaration
     *
     */
    #[@test]
    public function hashTableClass() {
      $this->assertEquals(
        new ClassNode(
          0,
          NULL,
          new TypeName('HashTable', array(new TypeName('K'), new TypeName('V'))),
          NULL,
          array(new TypeName('Map', array(new TypeName('K'), new TypeName('V')))),
          NULL
        ), 
        $this->parse('class HashTable<K, V> implements Map<K, V> { }')
      );
    }

    /**
     * Test interface declaration
     *
     */
    #[@test]
    public function emtpyInterface() {
      $this->assertEquals(
        new InterfaceNode(
          0,
          NULL,
          new TypeName('Empty'),
          NULL,
          NULL
        ), 
        $this->parse('interface Empty { }')
      );
    }

    /**
     * Test interface declaration
     *
     */
    #[@test]
    public function genericInterface() {
      $this->assertEquals(
        new InterfaceNode(
          0,
          NULL,
          new TypeName('Filter', array(new TypeName('T'))),
          NULL,
          NULL
        ), 
        $this->parse('interface Filter<T> { }')
      );
    }

    /**
     * Test interface declaration
     *
     */
    #[@test]
    public function twoComponentGenericInterface() {
      $this->assertEquals(
        new InterfaceNode(
          0,
          NULL,
          new TypeName('Map', array(new TypeName('K'), new TypeName('V'))),
          NULL,
          NULL
        ), 
        $this->parse('interface Map<K, V> { }')
      );
    }

    /**
     * Test interface declaration
     *
     */
    #[@test]
    public function interfaceWithParent() {
      $this->assertEquals(
        new InterfaceNode(
          0,
          NULL,
          new TypeName('Debuggable'),
          array(new TypeName('util.log.Traceable')),
          NULL
        ), 
        $this->parse('interface Debuggable extends util.log.Traceable { }')
      );
    }

    /**
     * Test interface declaration
     *
     */
    #[@test]
    public function interfaceWithParents() {
      $this->assertEquals(
        new InterfaceNode(
          0,
          NULL,
          new TypeName('Debuggable'),
          array(new TypeName('Traceable'), new TypeName('Observer', array(new TypeName('T')))),
          NULL
        ), 
        $this->parse('interface Debuggable extends Traceable, Observer<T> { }')
      );
    }

    /**
     * Test array type cannot be used as class name
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noArrayTypeAsClassName() {
      $this->parse('class int[] { }');
    }

    /**
     * Test array type cannot be used as enum name
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noArrayTypeAsEnumName() {
      $this->parse('enum int[] { }');
    }

    /**
     * Test array type cannot be used as interface name
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noArrayTypeAsInterfaceName() {
      $this->parse('interface int[] { }');
    }

    /**
     * Test field declaration
     *
     */
    #[@test]
    public function methodAndField() {
      $this->assertEquals(array(new FieldNode(array(
        'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
        'annotations'     => NULL,
        'name'            => 'instance',
        'type'            => new TypeName('self'),
        'initialization'  => new NullNode()
      )), new MethodNode(array(
        'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations' => NULL,
        'name'        => 'getInstance',
        'returns'     => new TypeName('self'),
        'parameters'  => NULL, 
        'throws'      => NULL,
        'body'        => array(),
        'extension'   => NULL
      ))), $this->parse('class Logger { 
        private static self $instance= null;
        public static self getInstance() { /* ... */ }
      }')->body);
    }

    /**
     * Test field declaration
     *
     */
    #[@test]
    public function fieldAndMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations' => NULL,
        'name'        => 'getInstance',
        'returns'     => new TypeName('self'),
        'parameters'  => NULL, 
        'throws'      => NULL,
        'body'        => array(),
        'extension'   => NULL
      )), new FieldNode(array(
        'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
        'annotations'     => NULL,
        'name'            => 'instance',
        'type'            => new TypeName('self'),
        'initialization'  => new NullNode()
      ))), $this->parse('class Logger { 
        public static self getInstance() { /* ... */ }
        private static self $instance= null;
      }')->body);
    }
  }
?>
