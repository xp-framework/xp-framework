<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Observer class to observe a SybaseConnections IO
   * performance.
   *
   * @ext      sybase
   * @purpose  Observe SybaseConnection
   */
  class SybaseIOObserver extends Object {

    /**
     * Constrcutor.
     *
     * @access  protected
     * @param   string argument
     */
    function __construct($arg) {
      $log= &Logger::getInstance();
      $this->cat= &$log->getCategory($arg);
    }

    /**
     * Sybase message callback.
     *
     * @access  magic
     * @param   int msgnumber
     * @param   int severity
     * @param   int state
     * @param   int line
     * @param   string text
     * @return  bool handled
     */
    function _msghandler($msgnumber, $severity, $state, $line, $text) {

      // Filter 'IO statistics'-messages by their msgnumber
      if (in_array($msgnumber, array(3614, 3615))) {
        $this->messages[]= rtrim($text);
      }
      
      // Indicate we did not process the message
      return FALSE;
    }

    /**
     * Retrieves an instance.
     *
     * @model   static
     * @access  public
     * @param   mixed argument
     * @return  &rdbms.sybase.SybaseIOObserver
     */
    function &instanceFor($arg) {
      return new SybaseIOObserver($arg);
    }
    
    /**
     * Update the observer. Process new message.
     *
     * @access  public
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    function update(&$obs, $arg= NULL) {
      if (!is('rdbms.DBEvent', $arg)) return;
      
      // Passthrough event to appropriate function, if existant
      if (method_exists($this, 'on'.$arg->getName())) {
        call_user_func_array(
          array(&$this, 'on'.$arg->getName()),
          array(&$obs, &$arg)
        );
      }
    }
    
    /**
     * Process connect events.
     *
     * @access  protected
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    function onConnect(&$obs, &$arg) {
      $this->cat->debug(__FUNCTION__);
      
      if (0 <= version_compare(phpversion(), '4.3.5')) {
        ini_set('sybct.min_server_severity', 0);
        sybase_set_message_handler(array(&$this, '_msghandler'), $obs->handle);
      } else {
        $this->cat->warn($this->getClassName().': Unsupported PHP version detected (requires 4.3.5)');
      }
      
      sybase_query('set statistics io on', $obs->handle);
      $this->queries= $this->messages= array();
    }
    
    /**
     * Process query event.
     *
     * @access  protected
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    function onQuery(&$obs, &$arg) {
      $this->cat->debug(__FUNCTION__);
      
      $this->queries[]= $arg->getArgument();
    }
    
    /**
     * Process end of query event.
     *
     * @access  protected
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    function onQueryEnd(&$obs, &$arg) {
      $this->cat->info($this->getClassName(), '::', __FUNCTION__, 'Query was:', $this->queries);
      
      foreach ($this->messages as $msg) {
        $this->cat->info('Measured IO: '.$msg);
      }
      
      $this->queries= $this->messages= array();
    }

  } implements (__FILE__, 'rdbms.DBObserver');
?>
