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
   * @model    abstract
   * @see      xp://peer.server.Server#notify
   * @purpose  Abstract base class for listeners
   * @experimental
   */
  class ConnectionListener extends Object {
  
    /**
     * Method to be triggered when a client connects
     *
     * Example:
     * <code>
     * printf(">>> ConnectionListener::connected() @%d\n", getmypid());
     * </code>
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function connected(ConnectionEvent $event) {
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
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function data(ConnectionEvent $event) { 
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * Example:
     * <code>
     * printf(">>> ConnectionListener::disconnected() @%d\n", getmypid());
     * </code>
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function disconnected(ConnectionEvent $event) { 
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
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function error(ConnectionEvent $event) { 
    }
  
  }
?>
