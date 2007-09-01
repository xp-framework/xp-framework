<?php
/* This class is part of the XP framework
 *
 * $Id: CopyProcessor.class.php 9228 2007-01-10 17:19:07Z friebe $
 */

  namespace text::doclet::markup;

  uses('text.doclet.markup.MarkupProcessor');

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
     * @return  string
     */
    public function initialize() {
      return '<pre>';
    }

    /**
     * Finalizes the processor.
     *
     * @return  string
     */    
    public function finalize() {
      return '</pre>';
    }

    /**
     * Process
     *
     * @param   string token
     * @return  string
     */
    public function process($token) {
      return preg_replace($this->patterns, $this->replacements, $token);
    }
  }
?>
