<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('EVENT_CONNECTED',     'connected');
  define('EVENT_DATA',          'data');
  define('EVENT_ERROR',         'error');
  define('EVENT_DISCONNECTED',  'disconnected');

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class ConnectionListener extends Object {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function connected($event) {
      printf(">>> ConnectionListener::connected()\n");
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function data($event) { 
      printf(
        ">>> ConnectionListener::data(%s) @%d\n", 
        addcslashes($event->data, "\0..\37!@\177..\377"),
        getmypid()
      );
      if ('QUIT' == substr($event->data, 0, 4)) {
        $event->stream->close();
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function disconnected($event) { 
      printf(">>> ConnectionListener::disconnected() @%d\n", getmypid());
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function error($event) { 
      printf(">>> ConnectionListener::error()\n");
      $event->data->printStackTrace();
    }
  
  }
?>
