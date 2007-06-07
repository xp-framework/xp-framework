<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer'
  );

  /**
   * Base class for all parser tests
   *
   * @purpose  Unit Test
   */
  abstract class ParserTest extends TestCase {

    /**
     * Parses a given string source into an AST
     *
     * @param   string source
     * @return  net.xp_framework.tools.vm.VNode[]
     */
    protected function parse($source) {
      return create(new Parser())->parse(new Lexer($source, '(string)'));
    }
    
    /**
     * Assertion helper
     *
     * @param   string type unqualified node type
     * @param   net.xp_framework.tools.vm.VNode node
     * @throws  unittest.AssertionFailedError
     */
    protected function assertNode($type, $node) {
      $this->assertClass($node, 'net.xp_framework.tools.vm.nodes.'.$type.'Node');
    }
  }
?>
