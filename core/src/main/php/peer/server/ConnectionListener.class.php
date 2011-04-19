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
   * Connection listener
   *
   * @deprecated Implement peer.protocol.ServerProtocol instead!
   * @see      xp://peer.server.Server#notify
   * @purpose  Abstract base class for listeners
   */
  class ConnectionListener extends Object {
    public
      $server = NULL;

    /**
     * Method to be triggered when a client connects
     *
     * Example:
     * <code>
     * printf(">>> ConnectionListener::connected() @%d\n", getmypid());
     * </code>
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function connected($event) {
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * Example:
     * <code>
     *   printf(                                                
     *     ">>> ConnectionListener::data(%s) @%d\n",            
     *     addcslashes($event->data, [...]), // see addcslashes() manual page
     *     getmypid()                                           
     *   );                                                     
     *   if ('QUIT' == substr($event->data, 0, 4)) {            
     *     $event->stream->close();                             
     *   }                                                      
     * </code>
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function data($event) { 
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * Example:
     * <code>
     * printf(">>> ConnectionListener::disconnected() @%d\n", getmypid());
     * </code>
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function disconnected($event) { 
    }
    
    /**
     * Method to be triggered when a communication error occurs
     *
     * Example:
     * <code>
     * printf(">>> ConnectionListener::error() @%d\n", getmypid());
     * $event->data->printStackTrace();
     * </code>
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function error($event) { 
    }
  
  }
?>
