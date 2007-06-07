<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.emit.php5.Php5Emitter'
  );

  /**
   * Tests PHP5 emitter
   *
   * @ext      tokenize
   * @purpose  Unit Test
   */
  class AbstractEmitterTest extends TestCase {

    /**
     * Parses a given string source into an AST and returns the emitted PHP5 
     * sourcecode.
     *
     * @param   string source
     * @return  string
     * @throws  lang.FormatException in case errors occur during emitAll() or parse()
     */
    protected function emit($source) {
      $parser= new Parser();
      $nodes= $parser->yyparse(new Lexer($source, '(string)'));
      
      if ($parser->hasErrors()) {
        $message= 'Errors found: {';
        foreach ($parser->getErrors() as $error) {
          $message.= "\n  * ".$error->toString();
        }
        $message.= "\n}";
        throw new FormatException($message);
      }
      
      $emitter= new Php5Emitter();
      $emitter->emitAll($nodes);
      
      if ($emitter->hasErrors()) {
        $message= 'Errors found: {';
        foreach ($emitter->getErrors() as $error) {
          $message.= "\n  * ".$error->toString();
        }
        $message.= "\n}";
        throw new FormatException($message);
      }
      
      return $emitter->getResult();
    }
    
    /**
     * Normalize sourcecode, e.g. replace all whitespace tokens by a 
     * single space, trim others.
     *
     * @param   string source the sourcecode to normalize
     * @return  string the normalized sourcecode
     */
    protected function normalizeSourcecode($source) {
      $tokens= token_get_all($source);
      $return= '';
      foreach ($tokens as $token) {
        $return.= (is_array($token) 
          ? T_WHITESPACE == $token[0] ? ' ' : trim($token[1])
          : $token
        );
      }
      return $return;
    }
    
    /**
     * Helper method that asserts two sourcecodes are equal
     *
     * @param   string expected Expected code w/o prologue and epilogue
     * @param   string emitted emitted sourcecode returned by emit
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSourcecodeEquals($expected, $emitted) {
      if (!is_string($emitted)) return;

      $expected= $this->normalizeSourcecode("<?php\n ".$expected).' ?>';
      $emitted= $this->normalizeSourcecode($emitted);
      $this->assertEquals($expected, $emitted);
    }
  }
?>
