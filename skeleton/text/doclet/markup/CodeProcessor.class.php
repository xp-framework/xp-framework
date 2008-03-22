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

      $current= 'default';
      $out= '';
      
      // Tokenize buffer
      $tokens= token_get_all('<?php '.trim($this->buffer, "\r\n").'?>');
      if (!is_array($tokens) || xp::errorAt(__FILE__, __LINE__ - 1)) {
        throw new FormatException('Cannot parse "'.$this->buffer.'"');
      }
      
      // Create HTML
      foreach ($tokens as $token) {
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
      
      return '</p><pre class="code"><span>'.substr($out, 9, -5).'</span></pre><p>';
    }
  }
?>
