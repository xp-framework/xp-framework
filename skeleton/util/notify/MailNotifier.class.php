<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * (Insert method's description here)
   *
   * @return  
   */
  class MailNotifier extends Object {
  
    /**
     * Notify
     *
     * @access  public
     * @param   string messages
     * @param   string params mailaddress
     * @param   string detail
     * @param   string stack
     * @return  bool success
     */
    function notify($message, $params, $details, $stack) {
      return mail(
        $params,
        $message,
        $details."\n\n*** Stack Trace: ***\n".$stack,
        'X-Sender: '.$this->getClassName()
      );
    }
  }
?>
