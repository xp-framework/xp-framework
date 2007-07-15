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
    protected
      $declaredClasses= array();

    /**
     * Declare a class
     *
     * @param   string name
     */
    protected function declareClass($name, $parent= NULL) {
      $qualified= strtr($name, '.', '·');
      $this->declaredClasses[$qualified]= array();
      $parent && $this->declaredClasses[$qualified][0]= strtr($parent, '.', '·');
    }

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
      foreach ($this->declaredClasses as $class => $decl) {
        $emitter->context['classes'][$class]= $decl;
      }

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
     * Helper method that asserts two sourcecodes are equal
     *
     * @param   string expected Expected code w/o prologue and epilogue
     * @param   string emitted emitted sourcecode returned by emit
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSourcecodeEquals($expected, $emitted) {
      $tx= token_get_all('<?php '.$expected.' ?>');
      $te= token_get_all($emitted);
      
      for ($ea= $xa= array(), $offset= 1, $e= NULL, $i= 1, $s= sizeof($tx); $i < $s; $i++) {
        if (is_array($tx[$i])) {
          $ea[]= is_array($te[$i+ $offset]) ? token_name($te[$i+ $offset][0]).':'.addcslashes($te[$i+ $offset][1], "\0..\17") : 'T_NONE:'.$te[$i+ $offset];
          $xa[]= token_name($tx[$i][0]).':'.addcslashes($tx[$i][1], "\0..\17");
          
          if (T_WHITESPACE === $tx[$i][0]) {
          
            // For whitespace, ignore any amount
            if (T_WHITESPACE === $te[$i+ $offset][0]) continue;
            
            // If emitted does not have whitespace but expected does, ignore
            $offset--;
          } else if (T_DOC_COMMENT == $tx[$i][0]) {

            // Normalize doc comments
            $nx= preg_replace('/\n\s+\*/', '*', $tx[$i][1]);
            $ne= preg_replace('/\n\s+\*/', '*', $te[$i+ $offset][1]);
            
            if ($nx === $ne) continue;
            $e= $nx;
            $h= $ne;
            break;
          } else { 
          
            // Compare values
            if ($tx[$i][1] === $te[$i+ $offset][1]) continue;
            $e= $tx[$i][1];
            $h= $te[$i+ $offset][1];
            break;
          }
        } else {
          $ea[]= is_array($te[$i+ $offset]) ? token_name($te[$i+ $offset][0]).':'.addcslashes($te[$i+ $offset][1], "\0..\17") : 'T_NONE:'.$te[$i+ $offset];
          $xa[]= 'T_NONE:'.$tx[$i];
        
          // Compare tokens
          if ($tx[$i] === $te[$i+ $offset]) continue;
          $e= $tx[$i];
          $h= is_array($te[$i+ $offset]) ? token_name($te[$i+ $offset][0]) : $te[$i+ $offset];
          break;
        }
      }
      
      $e && $this->fail('Want '.$e.', have '.$h, array_slice($ea, -5, 5, TRUE), array_slice($xa, -5, 5, TRUE));
    }
  }
?>
