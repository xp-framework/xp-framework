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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  bool Success
     */
    function notify($message, $params, $details, $stack) {
      return mail(
        $params,
        $message,
        $details."\n*** Stack Trace: ***\n".$stack,
        'X-Sender: '.$this->getName()
      );
    }
  }
?>
