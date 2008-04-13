<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.markup.MarkupProcessor');

  /**
   * Delegating processor
   *
   * @purpose  Processor
   */
  abstract class DelegatingProcessor extends MarkupProcessor {
    protected
      $delegate= NULL;

    /**
     * Constructor
     *
     * @param   text.doclet.markup.MarkupProcessor delegate
     */
    public function __construct(MarkupProcessor $delegate) {
      $this->delegate= $delegate;
    }

    /**
     * Return tag name
     *
     * @return  string
     */
    protected abstract function tag();

    /**
     * Initializes the processor.
     *
     * @return  string
     */
    public function initialize() {
      return '</p><'.$this->tag().'><p>';
    }

    /**
     * Process a token
     *
     * @param   string token
     * @return  string
     */
    public function process($token) {
      return $this->delegate->process($token);
    }

    /**
     * Finalize
     *
     * @return  string
     */
    public function finalize() {
      return '</p></'.$this->tag().'><p>';
    }
  }
?>
