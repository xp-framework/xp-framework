<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('PREREQUISITE_LIBRARYMISSING', 'library.missing');
  define('PREREQUISITE_INITFAILED',     'initialization.failed');

  /**
   * Indicates prerequisites have not been met
   *
   * @purpose  Exception
   */
  class PrerequisitesNotMetError extends ChainedException {
    public $prerequisites= array();
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   lang.Throwable cause 
     * @param   array prerequisites default array()
     */
    public function __construct($message, $cause= NULL, $prerequisites= array()) {
      parent::__construct($message, $cause);
      $this->prerequisites= (array)$prerequisites;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        '%s (%s) { prerequisites: [%s] }',
        $this->getClassName(),
        $this->message,
        implode(', ', array_map(array('xp', 'stringOf'), $this->prerequisites))
      );
    }
  }
?>
