<?php
/* Tool-Session, die eine Tool-ID für jede Variable mitbringt
 *
 * $Id$
 */

  define("SESS_TOOL",	0);
  define("SESS_KEY",	1);

  /**
   * Session-Klasse
   *
   */
  class ToolSession extends Object {
    var $ID, $Error, $Debug= 0;
    var $tool_id, $changed;
    
    // Hannah-spezifisches
    var $port= 2001;
    var $sess_server= "testsession.schlund.de";
    var $remote;
    var $TTL= 86400;
    var $ss_status;
    var $ss_socket;

    /**
     * Constructor
     */
    function __construct($params= NULL) {
      $this->isNew= ((getenv("SESS")== "")? 1: 0);
      $this->Error= $this->changed= 0;
      $this->ID= ($this->isNew)? 0: getenv('SESS');
      parent::__construct($params);
      
      // Anlegen, falls neue Session
      // Ansonsten auslesen
      $this->logline_text("ENV{SESS}", getenv("SESS"));
      if($this->isNew) {
        $this->logline_text("session", "new");
        $this->remote= $this->sess_server;
      } else {
        $this->logline_text("session", "use $this->ID");
	$this->remote= sprintf(
          "%d.%d.%d.%d",
          hexdec(substr($this->ID, 0, 2)),
	  hexdec(substr($this->ID, 2, 2)),
	  hexdec(substr($this->ID, 4, 2)),
	  hexdec(substr($this->ID, 6, 2))
        );
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function logline_text($key, $val) {
      if(!$this->Debug) return 0;
      LOG::info("ToolSession::$key => $val");
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function urlencode($val) {
      return str_replace("_", "%5F", urlencode($val));
    }
     
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */   
    function connect() {
      $this->ss_connect($this->remote, $this->port);
      if($this->ss_status!= "+OK") {
        $this->Error++;
        return 0;
      }
      
      if($this->isNew) {
        $this->logline_text("create", $this->TTL);
        $this->ss_create($this->TTL);
        if($this->ss_status!= "+OK") return 0;
      }
      return 1;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function destroy() {
      fclose($this->ss_socket);
    }

    /**
     * (Insert method's description here)
     * Session Keys zurückgeben:
     * Defaultwert -1 => nur die des aktuellen Tools
     * 0xFFFF => die aller Tools
     * 
     * @access  
     * @param   
     * @return  
     */    
    function session_keys($tool_id= -1) {
      if($tool_id< 0) $tool_id= $this->tool_id;
      if($tool_id== 0xFFFF) $tool_id= -1;
      $keys= explode(" ", urldecode($this->ss_talk("session_keys ".$this->urlencode($this->ID).' tmp')));
      $ret_keys= array();
      foreach($keys as $i=> $key) {
        $cmp_tool_id= substr($key, 0, 4);
        if($tool_id< 0 || $cmp_tool_id== $tool_id) $ret_keys[$i]= array($cmp_tool_id, substr($key, 4));
      }
      return $ret_keys;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function associate($uid) {
      $command= sprintf("session_associate %s %s",
        $this->urlencode($this->ID),
        $this->urlencode($uid)
      );
      $this->logline_text("command", $command);
      $this->ss_talk($command);
      return ($this->status()== "+OK")? 1: 0;
    } 

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function dissociate() {
      $command= sprintf("session_dissociate %s",
        $this->urlencode($this->ID)
      );
      $this->logline_text("command", $command);
      $this->ss_talk($command);
      return ($this->status()== "+OK")? 1: 0;
    } 

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function is_valid () {
      $this->ss_talk("session_isvalid ".$this->urlencode($this->ID));
      return ($this->status()== "+OK")? 1: 0;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_regPerm($key, $value) {
      $command= sprintf("var_write %s perm %04d%s %s",
        $this->urlencode($this->ID),
        $this->tool_id,
        $this->urlencode($key),
        $this->urlencode(serialize($value))
      );
      $this->logline_text("command", $command);
      $this->ss_talk($command);
      return ($this->status()== "+OK")? 1: 0;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_reg($key, $value) {
      $command= sprintf("var_write %s tmp %04d%s %s",
        $this->urlencode($this->ID),
        $this->tool_id,
        $this->urlencode($key),
        $this->urlencode(serialize($value))
      );
      $this->logline_text("command", $command);
      $this->ss_talk($command);
      return ($this->status()== "+OK")? 1: 0;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_regGlobal($key, $value) {
      $command= sprintf("var_write %s tmp %s %s",
        $this->urlencode($this->ID),
        $this->urlencode($key),
        $this->urlencode(serialize($value))
      );
      $this->logline_text("command", $command);
      $this->ss_talk($command);
      return ($this->status()== "+OK")? 1: 0;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_unreg($key) {
      $this->ss_talk(sprintf("var_delete %s tmp %04d%s",
        $this->urlencode($this->ID),
        $this->tool_id,
        $this->urlencode($key)
      ));
      return ($this->status()== "+OK")? 1: 0;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_unregPerm($key) {
      $this->ss_talk(sprintf("var_delete %s perm %04d%s",
        $this->urlencode($this->ID),
        $this->tool_id,
        $this->urlencode($key)
      ));
      return ($this->status()== "+OK")? 1: 0;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_unreg_group($range) {
      $keys= explode(" ", urldecode($this->ss_talk("session_keys ".$this->urlencode($this->ID)." tmp")));
      foreach($keys as $key) {        
        $match= '/^'.sprintf("%04d%s", $this->tool_id, $range).'.*/';
        if(preg_match($match, $key)) $this->var_unreg(substr($key, 4));
      }
      return $this->changed;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_get($key, $tool_id= -1) {
      if($tool_id< 0) $tool_id= $this->tool_id;
      $command= sprintf("var_read %s tmp %04d%s",
        $this->urlencode($this->ID),
        $tool_id,
        $this->urlencode($key)
      );
      $return= $this->ss_talk($command);
      if (!is_string($return)) {
        LOG::warn($this->getName().'::'.$command.'::'.var_export($return, 1));
        return NULL;
      }
      LOG::info($this->getName().'::'.$command.'::'.var_export($return, 1));
      $return= unserialize(urldecode(trim($return)));
      
      $this->logline_text("var_get $command", $return);
      if(!empty($return)) return $return;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function var_getPerm($key, $tool_id= -1) {
      if($tool_id< 0) $tool_id= $this->tool_id;
      $command= sprintf("var_read %s perm %04d%s",
        $this->urlencode($this->ID),
        $tool_id,
        $this->urlencode($key)
      );
      $return= urldecode(trim($this->ss_talk($command)));
      $this->logline_text("var_getPerm $command", $return);
      if (!empty($return)) return unserialize($return);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
    function var_ref($key, &$var) {
      $var= $this->var_get($key);
      return 1;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function sync($quiet= 0) {
      // Bei Hannah nicht nötig:)
      return 1;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function status() {
      return($this->ss_status);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function kill() {
      $this->logline_text("session_terminate", "{this->ID} $this->ID");
      $this->ss_talk("session_terminate ".$this->urlencode($this->ID));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __destruct() {
      if (isset($this->ss_socket)) fclose($this->ss_socket);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function ss_connect($remote, $port) {
      $this->logline_text('connect ', $remote.':'.$port);
      $socket= fsockopen($remote, $port, $err, $errstr, 2);   // 2 Sekunden Timeout
      if(!$socket) {
        $this->ss_status= "-NOCONN";
      } else {
        $this->ss_socket= $socket;
        $this->ss_status= "+OK";
      }
      $this->logline_text("ss_connect", $this->ss_status);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function ss_create($ttl= 0) {
      if($ttl== 0) $ttl= $this->TTL;
      $this->logline_text("new::ID", "ss_talk session_create $ttl");
      $this->ID= urldecode($this->ss_talk("session_create $ttl"));
      $this->logline_text("new::ID", $this->ID);
      return ($this->status()== "+OK")? 1: 0;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function ss_talk($query) {
      if (!isset($this->ss_socket)) {
        $this->connect();
      }
      if(!isset($this->ss_socket)) return 0;
      
      // Kommunikation
      $this->logline_text("ss_talk.query", $query);
      fputs($this->ss_socket, $query."\n");
      $answer= "";
      while(substr($answer, -1)!= "\n") $answer.= fgets($this->ss_socket, 1024);
      $answer= chop($answer);
      $this->logline_text("ss_talk.answer", $answer);
      
      // Return auswerten
      $status= $answer;
      $rest= "";
      $sp_pos= strpos($answer, " ");
      if(is_int($sp_pos)) {
        $status= substr($answer, 0, $sp_pos);
        $rest= trim(substr($answer, $sp_pos));
      }
      $this->ss_status= $status;
      $this->logline_text("ss_talk.done", $rest);
      
      return $rest;
    }

  }
?>
