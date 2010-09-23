<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.php.Lexer',
    'xp.compiler.syntax.php.Parser',
    'xp.compiler.ast.Node'
  );

  /**
   * Base class net·xp_lang·tests·syntax·php·for all other parser test cases.
   *
   */
  abstract class net·xp_lang·tests·syntax·php·ParserTestCase extends TestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      try {
        return create(new xp·compiler·syntax·php·Parser())->parse(new xp·compiler·syntax·php·Lexer('<?php class net·xp_lang·tests·syntax·php·Container {
          public function method() {
            '.$src.'
          }
        } ?>', '<string:'.$this->name.'>'))->declaration->body[0]->body;
      } catch (ParseException $e) {
        throw $e->getCause();
      }
    }
  }
?>
