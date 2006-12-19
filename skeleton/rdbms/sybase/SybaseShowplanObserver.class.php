<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Logger', 'rdbms.DBObserver');

  /**
   * Observer class to observe a SybaseConnections IO
   * optimizer plan
   *
   * @ext      sybase
   * @purpose  Observe SybaseConnection
   */
  class SybaseShowplanObserver extends Object implements DBObserver {

    /**
     * Constructor.
     *
     * @access  protected
     * @param   string argument
     */
    public function __construct($arg) {
      $log= &Logger::getInstance();
      $this->cat= &$log->getCategory($arg);
      $this->messagecodes= array_merge(
        range(3612,3615),
        range(6201,6299),
        range(10201,10299),
        range(302,310)
      );
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
    public function _msghandler($msgnumber, $severity, $state, $line, $text) {

      // Filter 'optimizer'-messages by their msgnumber
      if (in_array($msgnumber, $this->messagecodes)) {
        $this->messages[]= array(
          'msgnumber' => $msgnumber,
          'text'      => rtrim($text)
        );
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
     * @return  &rdbms.sybase.SybaseShowplanObserver
     */
    public static function &instanceFor($arg) {
      return new SybaseShowplanObserver($arg);
    }
    
    /**
     * Update the observer. Process new message.
     *
     * @access  public
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    public function update(&$obs, $arg= NULL) {
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
    public function onConnect(&$obs, &$arg) {
      if (0 <= version_compare(phpversion(), '4.3.5')) {
        ini_set('sybct.min_server_severity', 0);
        sybase_set_message_handler(array(&$this, '_msghandler'), $obs->handle);
      } else {
        $this->cat->warn($this->getClassName().': Unsupported PHP version detected (requires 4.3.5)');
      }
     
      sybase_query('set showplan on', $obs->handle);

      // Reset query- and message-cache
      $this->queries= $this->messages= array();
    }
    
    /**
     * Process query event.
     *
     * @access  protected
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    public function onQuery(&$obs, &$arg) {
      
      // Add query to cache
      $this->queries[]= $arg->getArgument();
    }
    
    /**
     * Process end of query event.
     *
     * @access  protected
     * @param   &mixed observable
     * @param   &mixed dbevent
     */
    public function onQueryEnd(&$obs, &$arg) {
      $this->cat->info($this->getClassName().'::onQueryEnd() Query was:', (sizeof($this->queries) == 1 ? $this->queries[0] : $this->queries));

      $showplan= '';
      foreach (array_keys($this->messages) as $idx) {
        $showplan.= $this->messages[$idx]['text']."\n";
      }
      
      $this->cat->infof("Showplan output is:\n%s", $showplan);
      $this->queries= $this->messages= array();
    }
  } 
?>
