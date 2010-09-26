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
  class OperatorTest extends TestCase {
  
    /**
     * Parse operator source and return statements inside this operator.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
    }

    /**
     * Test operator declaration
     *
     */
    #[@test]
    public function concatOperator() {
      $this->assertEquals(array(new OperatorNode(array(
        'modifiers'  => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations'=> NULL,
        'name'       => '',
        'symbol'     => '~',
        'returns'    => new TypeName('self'),
        'parameters' => array(
          array('name' => 'self', 'type' => new TypeName('self'), 'check' => TRUE),
          array('name' => 'arg', 'type' => TypeName::$VAR, 'check' => TRUE),
        ),
        'throws'     => NULL,
        'body'       => array()
      ))), $this->parse('class String { 
        public static self operator ~(self $self, var $arg) { }
      }'));
    }
  }
?>
