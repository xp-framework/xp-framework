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
      ob_start();
      highlight_string('<?php '.str_replace('&#160;', ' ', $this->buffer).'?>');
      $s= ob_get_contents();
      ob_end_clean();

      return strtr($s, array(
        '&nbsp;' => '&#160;', 
        "\r"     => ''
      ));
    }
  }
?>
