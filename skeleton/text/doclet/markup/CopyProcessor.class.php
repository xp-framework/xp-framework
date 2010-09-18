<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.markup.MarkupProcessor');

  /**
   * Processes <pre> ... </pre>
   *
   * @purpose  Processor
   */
  class CopyProcessor extends MarkupProcessor {
    public
      $patterns= array(
        '#<#',
        '#>#',
        '#\r#',
        '#&(?![a-z0-9\#]+;)#',
      ),
      $replacements= array(
        '&lt;',
        '&gt;',
        '',
        '&amp;', 
      );

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
     * Process
     *
     * @param   string token
     * @return  string
     */
    public function finalize() {
      return '</p><pre>'.preg_replace($this->patterns, $this->replacements, trim($this->buffer, "\r\n")).'</pre><p>';
    }
  }
?>
