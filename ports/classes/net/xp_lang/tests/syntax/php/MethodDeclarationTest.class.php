<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.php.Lexer',
    'xp.compiler.syntax.php.Parser'
  );

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·MethodDeclarationTest extends TestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·php·Parser())->parse(new xp·compiler·syntax·php·Lexer('<?php '.$src.'?>', '<string:'.$this->name.'>'))->declaration->body;
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
        'returns'    => new TypeName('var'),
        'parameters' => NULL,
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public function toString() { }
      }'));
    }

    /**
     * Test method declaration
     *
     */
    #[@test]
    public function toStringMethodWithReturnType() {
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
        public function toString() : string { }
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
        'returns'    => new TypeName('var'),
        'parameters' => array(array(
          'name'  => 'cmp',
          'type'  => new TypeName('Object'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public function equals(Object $cmp) { }
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
        'returns'    => new TypeName('var'),
        'parameters' => array(array(
          'name'  => 'cat',
          'type'  => new TypeName('LogCategory'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => NULL,
        'extension'  => NULL
      ))), $this->parse('class Null { 
        public abstract function setTrace(LogCategory $cat);
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
        'returns'    => new TypeName('var'),
        'parameters' => array(array(
          'name'  => 'other',
          'type'  => new TypeName('Object'),
          'check' => TRUE
        )),
        'throws'     => NULL,
        'body'       => NULL,
        'extension'  => NULL
      ))), $this->parse('interface Comparable { 
        public function compareTo(Object $other);
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
        'returns'    => new TypeName('var'),
        'parameters' => array(array(
          'name'   => 'elements',
          'type'   => new TypeName('var[]'),
          'check'  => TRUE      
        )), 
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class List { 
        public function addAll(array $elements) { }
      }'));
    }

    /**
     * Test missing return type yields a parse error
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function missingFunctionKeyword() {
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
        'returns'    => new TypeName('var'),
        'parameters' => array(array(
          'name'  => 'cmp',
          'type'  => new TypeName('var'),
          'check' => FALSE
        )),
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Test { 
        public function equals($cmp) { }
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
        'returns'    => new TypeName('var'),
        'parameters' => array(), 
        'throws'     => NULL,
        'body'       => array(),
        'extension'  => NULL
      ))), $this->parse('class Any { 
        #[@test]
        function map() { }
      }'));
    }
  }
?>
