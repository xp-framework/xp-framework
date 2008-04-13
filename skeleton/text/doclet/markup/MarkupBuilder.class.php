<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'text.StringTokenizer',
    'text.doclet.markup.DefaultProcessor',
    'text.doclet.markup.CopyProcessor',
    'text.doclet.markup.CodeProcessor'
  );

  /**
   * Markup builder based on regular expressions
   *
   * @test     xp://net.xp_framework.unittest.text.doclet.MarkupTest
   * @purpose  Plain text to markup converter
   */
  class MarkupBuilder extends Object {
    public 
      $stack      = array(),
      $state      = array(
        'pre'   => 'copy',
        'xmp'   => 'copy',
        'code'  => 'code'
      );

    public static 
      $processors = array();

    static function __static() {
      self::$processors['default']= new DefaultProcessor();
      self::$processors['copy']= new CopyProcessor();
      self::$processors['code']= new CodeProcessor();
    }

    /**
     * Push a processor onto stack
     *
     * @param   text.doclet.markup.MarkupProcessor proc
     * @return  text.doclet.markup.MarkupProcessor
     */
    public function pushProcessor($proc) {
      array_unshift($this->stack, $proc);
      return $proc;
    }
    
    /**
     * Pop processor off stack
     *
     * @return  text.doclet.markup.MarkupProcessor
     */
    public function popProcessor() {
      array_shift($this->stack);
      return $this->stack[0];
    }

    /**
     * Retrieve markup for specified text
     *
     * @param   string text
     * @return  string
     */
    public function markupFor($text) {
      $processor= $this->pushProcessor(self::$processors['default']);

      $st= new StringTokenizer($text, '<>', $returnDelims= TRUE);
      $out= '';      
      while ($st->hasMoreTokens()) {
        if ('<' == ($token= $st->nextToken())) {
          $tag= $st->nextToken();
          
          // If this is an opening tag and a behaviour is defined for it, switch
          // states and pass control to the processor.
          // If this is a closing tag and behaviour is defined for it, switch back
          // state and return control to the previous processor.
          if (ctype_alnum($tag[0])) {
            $st->nextToken('>');
            $lookup= strtolower($tag);

            if (isset($this->state[$lookup])) {
              $processor= $this->pushProcessor(self::$processors[$this->state[$lookup]]);
              $out.= $processor->initialize();
              continue;
            } else {
              $token= '<'.$tag.'>';
            }
          } else if ('/' == $tag[0] && ctype_alnum($tag[1])) {
            $st->nextToken('>');
            $lookup= ltrim(strtolower($tag), '/');

            if (isset($this->state[$lookup])) {
              $out.= $processor->finalize();
              $processor= $this->popProcessor();
              $st->nextToken("\n");
              continue;
            } else {
              $token= '<'.$tag.'>';
            }
          } else {
            $token= '<'.$tag;
          }
        }
        
        // Console::writeLine($processor->getClass(), ': "', $token, '"');
        $out.= $processor->process($token);
      }
      
      return $out;
    }
  }
?>
