<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.util.markup.MarkupProcessor');

  /**
   * Processes <pre> ... </pre>
   *
   * @purpose  Processor
   */
  class CopyProcessor extends MarkupProcessor {
    public
      $patterns= array(
        '#\r#',
        '#\n#',
        '#&(?![a-z0-9\#]+;)#',
      ),
      $replacements= array(
        '',
        '<br/>',
        '&amp;', 
      );

    /**
     * Initializes the processor.
     *
     * @access  public
     * @return  string
     */
    public function initialize() {
      return '<pre>';
    }

    /**
     * Finalizes the processor.
     *
     * @access  public
     * @return  string
     */    
    public function finalize() {
      return '</pre>';
    }

    /**
     * Process
     *
     * @access  public
     * @param   string token
     * @return  string
     */
    public function process($token) {
      return preg_replace($this->patterns, $this->replacements, $token);
    }
  }
?>
