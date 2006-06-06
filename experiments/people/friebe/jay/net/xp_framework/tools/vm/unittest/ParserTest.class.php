<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer'
  );

  /**
   * Tests AST parser
   *
   * @purpose  Unit Test
   */
  class ParserTest extends TestCase {

    /**
     * Parses a given string source into an AST
     *
     * @access  protected
     * @param   string source
     * @return  net.xp_framework.tools.vm.VNode[]
     */
    function &parse($source) {
      $parser= &new Parser();
      return $parser->yyparse(new Lexer($source, '(string)'));
    }

    /**
     * Tests parsing a simple "Hello, World!" script
     *
     * @access  public
     */
    #[@test]
    function helloWorld() {
      $nodes= $this->parse('echo "Hello World!\n";');
      $this->assertEquals(1, sizeof($nodes)) &&
      $this->assertClass($nodes[0], 'net.xp_framework.tools.vm.nodes.EchoNode') &&
      $this->assertEquals('"Hello World!\n"', $nodes[0]->args[0]);
    }
  }
?>
