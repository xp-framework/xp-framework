<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'text.StringTokenizer',
    'net.xp-framework.util.markup.DefaultProcessor',
    'net.xp-framework.util.markup.CopyProcessor',
    'net.xp-framework.util.markup.CodeProcessor'
  );

  /**
   * Markup builder based on regular expressions
   *
   * @purpose  Plain text to markup converter
   */
  class MarkupBuilder extends Object {
    var 
      $stack= array();

    /**
     * Push a processor onto stack
     *
     * @access  protected
     * @param   &net.xp-framework.util.markup.MarkupProcessor proc
     * @return  &net.xp-framework.util.markup.MarkupProcessor
     */
    function &pushProcessor(&$proc) {
      array_unshift($this->stack, $proc);
      return $proc;
    }
    
    /**
     * Pop processor off stack
     *
     * @access  protected
     * @return  &net.xp-framework.util.markup.MarkupProcessor
     */
    function &popProcessor() {
      return array_pop($this->stack);
    }

    /**
     * Retrieve markup for specified text
     *
     * @access  public
     * @param   string text
     * @return  string
     */
    function markupFor($text) {
      static $processors= array();
      static $state= array(
        'pre'   => 'copy',
        'code'  => 'code'
      );

      if (!$processors) {
        $processors['default']= &new DefaultProcessor();
        $processors['copy']= &new CopyProcessor();
        $processors['code']= &new CodeProcessor();
      }
      
      $processor= &$this->pushProcessor($processors['default']);

      $st= &new StringTokenizer($text, '<>', $returnDelims= TRUE);
      $out= '';      
      while ($st->hasMoreTokens()) {
        if ('<' == ($token= $st->nextToken())) {
          
          // Found beginning of tag
          $tag= $st->nextToken('>');
          $st->nextToken('>');

          // See if we have a processor state push/pop operation
          $lookup= strtolower($tag);
          if (isset($state[$lookup])) {
            $processor= &$this->pushProcessor($processors[$state[$lookup]]);
            $out.= $processor->initialize();
          } elseif (isset($state[ltrim($lookup, '/')])) {
            $out.= $processor->finalize();
            $processor= &$this->popProcessor();
          } else {
            $out.= '<'.$tag.'>';
          }

          continue;
        }
        
        $out.= $processor->process($token);
      }
      
      return $out;
    }
  }
?>
