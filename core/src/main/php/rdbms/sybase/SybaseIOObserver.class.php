<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Logger', 'rdbms.DBObserver');

  /**
   * Observer class to observe a SybaseConnections IO performance.
   *
   * @ext      sybase
   * @purpose  Observe SybaseConnection
   */
  class SybaseIOObserver extends Object implements DBObserver {
    protected
      $messages = array(),
      $queries  = array();

    /**
     * Constrcutor.
     *
     * @param   string argument
     */
    public function __construct($arg) {
      $this->cat= Logger::getInstance()->getCategory($arg);
    }

    /**
     * Sybase message callback.
     *
     * @param   int msgnumber
     * @param   int severity
     * @param   int state
     * @param   int line
     * @param   string text
     * @return  bool handled
     */
    public function _msghandler($msgnumber, $severity, $state, $line, $text) {

      // Filter 'IO statistics'-messages by their msgnumber
      if (in_array($msgnumber, array(3614, 3615))) {
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
     * @param   var argument
     * @return  rdbms.sybase.SybaseIOObserver
     */
    public static function instanceFor($arg) {
      return new SybaseIOObserver($arg);
    }
    
    /**
     * Update the observer. Process new message.
     *
     * @param   var observable
     * @param   var dbevent
     */
    public function update($obs, $arg= NULL) {
      if (!is('rdbms.DBEvent', $arg)) return;
      
      // Passthrough event to appropriate function, if existant
      if (method_exists($this, 'on'.$arg->getName())) {
        call_user_func_array(
          array($this, 'on'.$arg->getName()),
          array($obs, $arg)
        );
      }
    }
    
    /**
     * Process connect events.
     *
     * @param   var observable
     * @param   var dbevent
     */
    public function onConnect($obs, $arg) {
      ini_set('sybct.min_server_severity', 0);
      sybase_set_message_handler(array($this, '_msghandler'), $obs->handle);
      sybase_query('set statistics io on', $obs->handle);
      
      // Reset query- and message-cache
      $this->queries= $this->messages= array();
    }
    
    /**
     * Process query event.
     *
     * @param   var observable
     * @param   var dbevent
     */
    public function onQuery($obs, $arg) {
      
      // Add query to cache
      $this->queries[]= $arg->getArgument();
    }
    
    /**
     * Process end of query event.
     *
     * @param   var observable
     * @param   var dbevent
     */
    public function onQueryEnd($obs, $arg) {
      $this->cat->info($this->getClassName().'::onQueryEnd() Query was:', (sizeof($this->queries) == 1 ? $this->queries[0] : $this->queries));
      $result= $arg->getArgument();
      
      $sc= 0; $reads= 0;
      foreach (array_keys($this->messages) as $idx) {
        $msg= $this->messages[$idx];
        switch ($msg['msgnumber']) {
          case 3615: {
            $split= sscanf($msg['text'], 'Table: %s scan count %d, logical reads: (regular=%d apf=%d total=%d), physical reads: (regular=%d apf=%d total=%d), apf IOs used=%d');
            $this->cat->infof('IO(%s): scan count= %d, logical= %d, physical= %d',
              $split[0],
              $split[1],
              $split[4],
              $split[7]
            );
            
            // Add overall statistics
            $sc+= $split[1];
            $reads+= $split[4] + $split[7];
            
            break;
          }
          
          case 3614: {
            $split= sscanf($msg['text'], 'Total writes for this command: %d');
            if ($split[0] > 0) $this->cat->infof('Write-IO: %d', $split[0]);
            break;
          }
        }
      }
      
      $this->cat->infof('Overall stats for query: scan count= %d, reads= %d', $sc, $reads);
      
      // Retrieve number of rows returned, then calculate average cost of row
      if (1 < ($nrows= sybase_num_rows($result->handle))) {
        $this->cat->infof('Average stats for query: scan count= %0.02f, reads= %0.02f (%d lines)',
          $sc / $nrows,
          $reads / $nrows,
          $nrows
        );
      }

      $this->queries= $this->messages= array();
    }
  } 
?>
