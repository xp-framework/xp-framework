<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.xp.Lexer',
    'xp.compiler.syntax.xp.Parser'
  );

  /**
   * TestCase
   *
   */
  class MethodDeclarationTest extends TestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function toStringMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'toString',
        'returns'    => new TypeName('string'),
        'parameters' => NULL,
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public string toString() { }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function equalsMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'equals',
        'returns'    => new TypeName('bool'),
        'parameters' => array(array(
          'name'  => 'cmp',
          'type'  => new TypeName('Object'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public bool equals(Object $cmp) { }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function abstractMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC | MODIFIER_ABSTRACT,
        'annotations'=> NULL,
        'name'       => 'setTrace',
        'returns'    => TypeName::$VOID,
        'parameters' => array(array(
          'name'  => 'cat',
          'type'  => new TypeName('util.log.LogCategory'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => NULL,
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public abstract void setTrace(util.log.LogCategory $cat);
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function interfaceMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'compareTo',
        'returns'    => new TypeName('int'),
        'parameters' => array(array(
          'name'  => 'other',
          'type'  => new TypeName('Object'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('interface Comparable { 
        public int compareTo(Object $other) { }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function staticMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations'=> NULL,
        'name'       => 'loadClass',
        'returns'    => new TypeName('Class', array(new TypeName('T'))),
        'parameters' => array(array(
          'name'  => 'name',
          'type'  => new TypeName('string'),
          'check' => TRUE
        )),
        'throws'     => array(new TypeName('ClassNotFoundException'), new TypeName('SecurityException')),
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Class<T> { 
        public static Class<T> loadClass(string $name) throws ClassNotFoundException, SecurityException { }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function printfMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations' => NULL,
        'name'        => 'printf',
        'returns'     => new TypeName('string'),
        'parameters'  => array(array(
          'name'      => 'format',
          'type'      => new TypeName('string'),
          'check'     => TRUE
        ), array(
          'name'      => 'args',
          'type'      => new TypeName('string'),
          'vararg'    => TRUE,
          'check'     => TRUE
        )), 
        'throws'      => NULL,
        'body'        => array(),
        'extension'   => NULL
      ))), $this->parse('class Format { 
        public static string printf(string $format, string... $args) {
        
        }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function addAllMethod() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'addAll',
        'returns'    => TypeName::$VOID,
        'parameters' => array(array(
          'name'   => 'elements',
          'type'   => new TypeName('T[]'),  // XXX FIXME this is probably not a good representation
          'check'  => TRUE      
        )), 
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class List { 
        public void addAll(T[] $elements) { }
      }'));
    }

    /**
     * Test operator declaration
     *
     */
    #[@test]
    public function plusOperator() {
      $this->assertEquals(array(new OperatorNode(array(
        'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations'=> NULL,
        'symbol'     => '+',
        'returns'    => new TypeName('self'),
        'parameters' => array(array(
          'name'  => 'a',
          'type'  => new TypeName('self'),
          'check' => TRUE
        ), array(
          'name'  => 'b',
          'type'  => new TypeName('self'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => array()
      ))), $this->parse('class Integer { 
        public static self operator + (self $a, self $b) { }
      }'));
    }

    /**
     * Test missing return type yields a parse error
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function missingReturnType() {
      $this->parse('class Broken { public run() { }}');
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function noRuntimeTypeCheck() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'equals',
        'returns'    => new TypeName('bool'),
        'parameters' => array(array(
          'name'  => 'cmp',
          'type'  => new TypeName('Generic'),
          'check' => FALSE
        )),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Test { 
        public bool equals(Generic? $cmp) { }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function mapMethodWithAnnotations() {
      $this->assertEquals(array(new MethodNode(array(
        'modifiers'  => 0,
        'annotations'=> array(
          new AnnotationNode(array(
            'type'        => 'test',
            'parameters'  => array()
          ))
        ),
        'name'       => 'map',
        'returns'    => new TypeName('[:string]'),
        'parameters' => array(), 
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Any { 
        [@test] [:string] map() { }
      }'));
    }
  }
?>
