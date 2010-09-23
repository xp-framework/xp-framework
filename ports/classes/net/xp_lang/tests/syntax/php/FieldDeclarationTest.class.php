<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·FieldDeclarationTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Parse class net·xp_lang·tests·syntax·php·source and return statements inside field declaration
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·php·Parser())->parse(new xp·compiler·syntax·php·Lexer('<?php '.$src.' ?>', '<string:'.$this->name.'>'))->declaration->body;
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
        'type'       => TypeName::$VAR,
        'initialization' => NULL,
      ))), $this->parse('class Person { 
        public $name;
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
        'type'            => TypeName::$VAR,
        'initialization'  => new NullNode()
      ))), $this->parse('class Logger { 
        private static $instance= null;
      }'));
    }
  }
?>
