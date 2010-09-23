<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.xp.Lexer',
    'xp.compiler.syntax.xp.Parser',
    'xp.compiler.ast.Node'
  );

  /**
   * Base class for all other parser test cases.
   *
   */
  abstract class ParserTestCase extends TestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      try {
        return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer('class Container {
          public void method() {
            '.$src.'
          }
        }', '<string:'.$this->name.'>'))->declaration->body[0]->body;
      } catch (ParseException $e) {
        throw $e->getCause();
      }
    }
  }
?>
