<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('io.File');

  /**
   * Will notify after a given amount of occurences
   *
   * Example params parameter for NotifiedException
   * <pre>
   *   'digest' => array(
   *      'after'       => 10,
   *      'mail'        => 'foo@bar.baz',
   *      'subject'     => '[MyTool] An error occurred',
   *      'stor'        => '/tmp/notify.digest'
   *    )
   * </pre>
   */
  class DigestNotifier extends Object {
  
    /**
     * Notify
     *
     * @access  public
     * @param   string messages
     * @param   array params Parameter, associative array, see classdoc example
     * @param   string detail
     * @param   string stack
     * @return  bool success
     */
    function notify($message, $params, $details, $stack) {
      if (empty($params['stor'])) return FALSE;
      
      $f= &new File($params['stor']);
      try(); {
        if (!$f->exists()) {
        
          // Create file
          $f->open(FILE_MODE_WRITE);
          $data= array();
        } else {
        
          // Get data from file
          $f->open(FILE_MODE_READWRITE);
          $data= unserialize($f->read($f->size()));
          $f->rewind();
        }
              
        // Append data and write
        $data[]= array(
          time(),
          $message, 
          $details, 
          $stack
        );
        $f->write(serialize($data));
        $f->close();
      } if (catch('Exception', $e)) {
        return FALSE;
      }

      $result= TRUE;
      
      // Have we reached the number of messages we want to be notified after?
      if (sizeof($data) > $params['after']- 1) {
        $str= '';
        for ($i= 0; $i < sizeof($data); $i++) {
          $str.= sprintf(
            "--%s:\n".
            "  Message: %s\n".
            "  Details: %s\n".
            "  Stack:   %s\n",
            date('r', $data[$i][0]),
            $data[$i][1],
            $data[$i][2],
            $data[$i][3]
          );
        }
        
        // Send mail
        mail(
          $params['mail'],
          $params['subject'].' - digest for '.sizeof($data).' exceptions',
          $str,
          'X-Sender: '.$this->getClassName()
        );

        // Unlink storage
        $result= $f->unlink();
      }

      return $result;
    }
  }
?>
