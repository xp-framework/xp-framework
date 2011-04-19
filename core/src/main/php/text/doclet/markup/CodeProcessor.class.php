<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.markup.MarkupProcessor');

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
     * @param   [:string] attributes
     * @return  string
     */
    public function initialize($attributes= array()) {
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
        T_INTERFACE                     => 'keyword',
        T_EXTENDS                       => 'keyword',
        T_IMPLEMENTS                    => 'keyword',
        T_CATCH                         => 'keyword',
        T_THROW                         => 'keyword',
        T_TRY                           => 'keyword',
        T_NEW                           => 'keyword',
        T_FUNCTION                      => 'keyword',
        T_FOR                           => 'keyword',
        T_IF                            => 'keyword',
        T_ELSE                          => 'keyword',
        T_SWITCH                        => 'keyword',
        T_WHILE                         => 'keyword',
        T_FOREACH                       => 'keyword',
        T_RETURN                        => 'keyword',
        
        T_STATIC                        => 'modifier',
        T_ABSTRACT                      => 'modifier',
        T_PUBLIC                        => 'modifier',
        T_PRIVATE                       => 'modifier',
        T_PROTECTED                     => 'modifier',
        T_FINAL                         => 'modifier',
        
        T_DNUMBER                       => 'number',
        T_LNUMBER                       => 'number',
        T_CONSTANT_ENCAPSED_STRING      => 'string',
        
        T_COMMENT                       => 'comment',
        
        '{'                             => 'bracket',
        '}'                             => 'bracket',
        '('                             => 'bracket',
        ')'                             => 'bracket',
      );
      
      // Tokenize buffer
      $tokens= token_get_all('<?php '.trim($this->buffer, "\r\n").'?>');
      if (!is_array($tokens) || xp::errorAt(__FILE__, __LINE__ - 1)) {
        $e= new FormatException('Cannot parse "'.$this->buffer.'"');
        xp::gc(__FILE__);
        throw $e;
      }
      
      // Create HTML
      $current= NULL;
      $out= '';
      for ($i= 1, $s= sizeof($tokens)- 1; $i < $s; $i++) {
        $token= $tokens[$i];
        $class= isset($classes[$token[0]]) ? $classes[$token[0]] : 'default';
        
        // Handle annotations
        if (is_array($token) && T_COMMENT === $token[0] && '#' === $token[1][0]) {
          $class= 'annotation';
        }
        
        if ($current != $class) {
          $out.= '</span><span class="'.$class.'">';
          $current= $class;
        }

        $out.= strtr(htmlspecialchars(is_array($token) ? $token[1] : $token), array(
          "\n" => '<br/>',
          "\r" => ''
        ));
      }
      
      // Skip leading "</span>" (7)
      return '</p><code>'.substr($out, 7).($current ? '</span>' : '').'</code><p>';
    }
  }
?>
