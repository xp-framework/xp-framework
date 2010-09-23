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
  class net·xp_lang·tests·syntax·php·ClassDeclarationTest extends net·xp_lang·tests·syntax·php·ParserTestCase {

    /**
     * Parse class net·xp_lang·tests·syntax·php·source and return statements inside field declaration
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·php·Parser())->parse(new xp·compiler·syntax·php·Lexer($src, '<string:'.$this->name.'>'))->declaration;
    }
  
    /**
     * Test class net·xp_lang·tests·syntax·php·declaration
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
        $this->parse('<?php class Empty { } ?>')
      );
    }

    /**
     * Test class constant declaration
     *
     */
    #[@test]
    public function classConstant() {
      $this->assertEquals(array(new ClassConstantNode(
        'DEBUG',
        TypeName::$VAR,
        new IntegerNode('1')
      )), $this->parse('<?php class Logger { 
        const DEBUG = 1;
      } ?>')->body);
    }

    /**
     * Test class constant declaration
     *
     */
    #[@test]
    public function classConstants() {
      $this->assertEquals(array(
        new ClassConstantNode(
          'DEBUG',
          TypeName::$VAR,
          new IntegerNode('1')
        ), new ClassConstantNode(
          'WARN',
          TypeName::$VAR,
          new IntegerNode('2')
        )
      ), $this->parse('<?php class net·xp_lang·tests·syntax·php·Logger { 
        const DEBUG = 1, WARN  = 2;
      } ?>')->body);
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
        'type'            => new TypeName('var'),
        'initialization'  => new NullNode()
      )), new MethodNode(array(
        'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'annotations' => NULL,
        'name'        => 'getInstance',
        'returns'     => new TypeName('var'),
        'parameters'  => NULL, 
        'throws'      => NULL,
        'body'        => array(),
        'extension'   => NULL
      ))), $this->parse('<?php class net·xp_lang·tests·syntax·php·Logger { 
        private static $instance= null;
        public static function getInstance() { /* ... */ }
      } ?>')->body);
    }
  }
?>
