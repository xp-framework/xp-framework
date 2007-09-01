<?php
/* This class is part of the XP framework
 *
 * $Id: CodeProcessor.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::util::markup;

  ::uses('net.xp_framework.util.markup.MarkupProcessor');

  /**
   * Processes <code> ... </code>
   *
   * @purpose  Processor
   */
  class CodeProcessor extends MarkupProcessor {
    public
      $buffer = '';

    /**
     * Initializes the processor.
     *
     * @return  string
     */
    public function initialize() {
      $this->buffer= '';
      return '';
    }
    
    /**
     * Process a token
     *
     * @param   string token
     * @return  string
     */
    public function process($token) {
      $this->buffer.= $token;
      return '';
    }

    /**
     * Finalizes the processor.
     *
     * @return  string
     */    
    public function finalize() {
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

        $out.= strtr(htmlspecialchars(is_array($token) ? $token[1] : $token), array(
          "\n" => '<br/>',
          "\r" => ''
        ));
      }
      
      return '<code><span>'.substr($out, 8, -5).'</span></code>';
    }
  }
?>
