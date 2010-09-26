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
  class FieldDeclarationTest extends ParserTestCase {
  
    /**
     * Parse class source and return statements inside field declaration
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
    }

    /**
     * Test field declaration
     *
     */
    #[@test]
    public function publicField() {
      $this->assertEquals(array(new FieldNode(array(
        'modifiers'  => MODIFIER_PUBLIC,
        'annotations'=> NULL,
        'name'       => 'name',
        'type'       => new TypeName('string'),
        'initialization' => NULL,
      ))), $this->parse('class Person { 
        public string $name;
      }'));
    }

    /**
     * Test field declaration
     *
     */
    #[@test]
    public function privateStaticField() {
      $this->assertEquals(array(new FieldNode(array(
        'modifiers'       => MODIFIER_PRIVATE | MODIFIER_STATIC,
        'annotations'     => NULL,
        'name'            => 'instance',
        'type'            => new TypeName('self'),
        'initialization'  => new NullNode()
      ))), $this->parse('class Logger { 
        private static self $instance= null;
      }'));
    }

    /**
     * Test property declaration
     *
     */
    #[@test]
    public function readOnlyProperty() {
      $this->assertEquals(array(
        new FieldNode(array(
          'modifiers'  => MODIFIER_PRIVATE,
          'annotations'=> NULL,
          'name'       => '_name',
          'type'       => new TypeName('string'),
          'initialization' => NULL,
        )),
        new PropertyNode(array(
          'modifiers'  => MODIFIER_PUBLIC,
          'annotations'=> NULL,
          'type'       => new TypeName('string'),
          'name'       => 'name',
          'handlers'   => array(
            'get' => array(
              new ReturnNode(new MemberAccessNode(new VariableNode('this'), '_name'))
            ),
          )
        ))
      ), $this->parse('class Person {
        private string $_name;
        public string name { get { return $this._name; } }
      }'));
    }

    /**
     * Test property declaration
     *
     */
    #[@test]
    public function readWriteProperty() {
      $this->assertEquals(array(
        new FieldNode(array(
          'modifiers'  => MODIFIER_PRIVATE,
          'annotations'=> NULL,
          'name'       => '_name',
          'type'       => new TypeName('string'),
          'initialization' => NULL,
        )),
        new PropertyNode(array(
          'modifiers'  => MODIFIER_PUBLIC,
          'annotations'=> NULL,
          'type'       => new TypeName('string'),
          'name'       => 'name',
          'handlers'   => array(
            'get' => array(
              new ReturnNode(new MemberAccessNode(new VariableNode('this'), '_name'))
            ),
            'set' => array(
              new AssignmentNode(array(
                'variable'   => new MemberAccessNode(new VariableNode('this'), '_name'),
                'expression' => new VariableNode('value'),
                'op'         => '='
              ))
            )
          )
        ))
      ), $this->parse('class Person {
        private string $_name;
        public string name { 
          get { return $this._name; } 
          set { $this._name= $value; }
        }
      }'));
    }

    /**
     * Test indexer declaration
     *
     */
    #[@test]
    public function indexer() {
      $this->assertEquals(array(
        new IndexerNode(array(
          'modifiers'  => MODIFIER_PUBLIC,
          'annotations'=> NULL,
          'type'       => new TypeName('T'),
          'parameter'  => array(
            'name'  => 'offset',
            'type'  => new TypeName('int'),
            'check' => TRUE
          ),
          'handlers'   => array(
            'get'   => NULL,
            'set'   => NULL,
            'isset' => NULL,
            'unset' => NULL
          )
        ))
      ), $this->parse('class ArrayList {
        public T this[int $offset] { 
          get {  } 
          set {  }
          isset {  }
          unset {  }
        }
      }'));
    }
  }
?>
