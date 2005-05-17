<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp-framework.util.markup.MarkupProcessor');

  /**
   * Processes <code> ... </code>
   *
   * @purpose  Processor
   */
  class CodeProcessor extends MarkupProcessor {
    var
      $buffer = '';

    /**
     * Initializes the processor.
     *
     * @access  public
     * @return  string
     */
    function initialize() {
      $this->buffer= '';
      return '';
    }
    
    /**
     * Process a token
     *
     * @access  public
     * @param   string token
     * @return  string
     */
    function process($token) {
      $this->buffer.= $token;
      return '';
    }

    /**
     * Finalizes the processor.
     *
     * @access  public
     * @return  string
     */    
    function finalize() {
      static $classes= array(
        T_VARIABLE                      => 'variable',
        T_CLASS                         => 'keyword',
        T_FUNCTION                      => 'keyword',
        T_NEW                           => 'keyword',
        T_STATIC                        => 'keyword',
        T_CONSTANT_ENCAPSED_STRING      => 'string',
        T_COMMENT                       => 'comment',
        '{'                             => 'bracket',
        '}'                             => 'bracket',
        '('                             => 'bracket',
        ')'                             => 'bracket',
      );

      $current= 'default';
      $out= '';
      foreach (token_get_all('<?php '.$this->buffer.'?>') as $token) {
        $class= (isset($classes[$token[0]]) 
          ? $classes[$token[0]]
          : 'default'
        );
        if ($current != $class) {
          $out.= '</span><span class="'.$class.'">';
          $current= $class;
        }

        $out.= str_replace("\n", '<br/>', htmlspecialchars(is_array($token) ? $token[1] : $token));
      }
      
      return '<code><span>'.substr($out, 8, -5).'</span></code>';
    }
  }
?>
