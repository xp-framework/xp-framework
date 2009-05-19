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
      $processors = array(),
      $state      = array(
        'pre'   => 'copy',
        'xmp'   => 'copy',
        'code'  => 'code'
      );

    protected static 
      $defaultProcessors = array();

    static function __static() {
      self::$defaultProcessors['default']= new DefaultProcessor();
      self::$defaultProcessors['copy']= new CopyProcessor();
      self::$defaultProcessors['code']= new CodeProcessor();
    }
    
    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->processors= self::$defaultProcessors;
    }

    /**
     * Register a processor
     *
     * @param   string tag
     * @param   text.doclet.markup.MarkupProcessor proc
     */
    public function registerProcessor($tag, MarkupProcessor $proc) {
      with ($unid= $proc->hashCode()); {
        $this->processors[$unid]= $proc;
        $this->state[$tag]= $unid;
      }
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
      if (empty($this->stack)) throw new IllegalStateException('Stack underflow');
      return $this->stack[0];
    }

    /**
     * Retrieve markup for specified text
     *
     * @param   string text
     * @return  string
     */
    public function markupFor($text) {
      $processor= $this->pushProcessor($this->processors['default']);

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
            $attributes= array();
            if (FALSE !== ($p= strpos($tag, ' '))) {
              $at= new StringTokenizer(substr($tag, $p+ 1), ' ');
              while ($at->hasMoreTokens()) {
                $name= $at->nextToken('=');
                $at->nextToken('"');
                $attributes[$name]= $at->nextToken('"');
                $at->nextToken(' ');
              }
              $lookup= strtolower(substr($tag, 0, $p));
            } else {
              $lookup= strtolower($tag);
            }

            if (isset($this->state[$lookup])) {
              $processor= $this->pushProcessor($this->processors[$this->state[$lookup]]);
              $out.= $processor->initialize($attributes);
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
