<?php
/**
 *
 * $Id$
 */
  
  /**
   * Ergänzt bz_id-Felder durch bz_description, Datumsangaben durch unix-Timestamp
   *
   * @purpose Datenbank-Klasse für Schlund-Datenbanken
   * @example sybase.ini
   * @see     http://manuals.sybase.com/
   * @see     http://curry.schlund.de/datenbank/MIGRAENE.html#bearbeitungszustand
   */
  class SPSybase extends Object {
    var 
      $handle= 0,
      $Error= 0,
      $Debug= 0;
      
    var
      $host, $user, $pass, $db, $bz_map;
      
    var 
      $last_affected_rows, 
      $last_num_rows, 
      $transaction;
    
    /**
     * Constructor
     */
    function __construct($params) {
      $this->bz_map= array();
      parent::__construct($params);
      $this->transaction= 0;
      $this->handle= NULL;
      $this->last_insert_id= $this->last_affected_rows= $this->last_num_rows= -1;
    }
    
    /**
     * Debug-Ausgabe
     *
     * @access  private
     * @param	string key
     * @param   variant var
     */
    function _logline_text($key, $var) {
      if(!$this->Debug || !$GLOBALS["stage_server"]) return 0;
      logline_text(get_class($this)."::$key", $var);
    }
    
    /**
     * Konnektieren
     *
     * @access  public
     * @return  bool connected
     */
    function connect() {
      $this->handle= @sybase_connect($this->host, $this->user, $this->pass);
      $this->Error= $this->handle? 0: 1;
      return $this->handle;
    }

    /**
     * Datenbank auswählen
     *
     * @access  public
     * @param   string db default NULL Auszuwählende Datenbank (wenn NULL, $this->db)
     * @return  bool result Datenbank ausgewählt?
     */    
    function select_db($db= NULL) {
      if(!is_null($db)) $this->db= $db;
      $this->_logline_text("select_db", "{db} $this->db");
      return @sybase_select_db($this->db, $this->handle);
    }
    
    /**
     * Query-Funktion. Connected, falls nötig
     *
     * @access  public
     * @param   string sql  Der abzusetzende SQL-Query-String
     * @return  bool result Query-Ergebnis
     */
    function query($sql) {
      // Wenn es keinen Connect gibt, einen herstellen
      if(!$this->handle) {
        $this->_logline_text("connect", $this->user."@".$this->host);
        $connect= $this->connect();
        if (isset($this->db)) $this->select_db($this->db);
        $this->_logline_text('connect.error', $this->Error);
      }
      return sybase_query($sql, $this->handle);
    }
    
    /**
     * Data Seek: Offset innerhalb eines Querys definieren
     *
     * @access  public
     * @param	resource query Queryhandle, z.B. aus query()
     * @param   int offset Der Offset, zu dem gesprungen wird
     * @return  bool Konnte geseekt werden?
     */
    function data_seek($query, $offset) {
      $this->_logline_text("seek", "{offset} $offset");
      return sybase_data_seek($query, $offset);
    }
    
    /**
     * Einen Datensatz holen
     *
     * @access  public
     * @param	resource query Queryhandle, z.B. aus query()
     * @return  array Der selektierte Datensatz
     * @throws  E_SQL
     */
    function &fetch($query) {
      $row= sybase_fetch_array($query);
      if (FALSE === $row) return FALSE;
      
      foreach($row as $key=> $val) {

        // Zahlen aus dem Array rippen
        if(is_int($key)) {
          unset($row[$key]);
          continue;
        }

        // Datumsangaben automatisch umwandeln
        // Default: mon dd yyyy hh:mm AM (or PM)
        if (preg_match('/^([a-zA-Z]{3})[ ]+([0-9]{1,2})[ ]+([0-9]{2,4})[ ]+([0-9]{1,2}):([0-9]{1,2})([A|P]M)$/', substr($val, 0, 23), $regs)) {
          $regs[1]= $GLOBALS["Sybase__monthmapping"][$regs[1]];
          if($regs[6]== "PM" && $regs[4]!= 12) $regs[4]+= 12; // 12 PM => 12:00
          if($regs[6]== "AM" && $regs[4]== 12) $regs[4]= 0;   // 12 AM => 00:00
          $format= "%02d.%02d.%04d";
          if(($regs[4]+ $regs[5])!= 0) $format.= ", %02d:%02d Uhr";
          $row["sdate_$key"]= $val;
          $row["udate_$key"]= mktime($regs[4], $regs[5], 0, $regs[1], $regs[2], $regs[3]);
          $row[$key]= sprintf($format, $regs[2], $regs[1], $regs[3], $regs[4], $regs[5]);
        }

        // BZ-IDs durch ihre Beschreibung ergänzen
        if($key== "bz_id" && !empty($this->bz_map)) {
          $row["bz_descr"]= $this->bz_map[$val];
        }
      }
      
      return $row;
    }
    
    /**
     * Datensätze als assoziativen Array holen
     *
     * @access  public
     * @param	string sql Das SQL
     * @return  array rows Folgende Form (bei Anzahl zurückgegebener Felder):
     *          1) field[0].content => field[0].content
     *          2) field[0].content => field[1].content
     *          3) field[0].content => array(field[1].content, field[2].content, ...)
     * @throws  E_SQL
     */   
    function &select_ref($sql) {
      $this->_logline_text("select_ref", "{SQL} $sql");
      $query= $this->query("select $sql", $this->handle);
      if($query) {
        $result_set= array();
        while($data= sybase_fetch_row($query)) {
          switch (sizeof($data)) {
            case 1: $result_set[$data[0]]= $data[0]; break;
            case 2: $result_set[$data[0]]= $data[1]; break;
            default: $result_set[$data[0]]= array_slice($data, 1);
          }
        }
        $this->_logline_text("result_set", $result_set);
        $this->last_num_rows= sybase_num_rows($query);
        return $result_set;
      }
      return $query;
    }
	 

    /**
     * Select-Wrapper
     *
     * @access  public
     * @param	string sql Das SQL (ohne select)
     * @return  array Alle Rows
     * @throws  E_SQL
     */   
    function &select($sql) {
//	 	preg_replace( "/^[sS][eE][lL][eE][cC][tT] ", "", $sql );
      $this->_logline_text("select", "{SQL} $sql");
      $query= $this->query("select $sql", $this->handle);
      if($query) {
        $result_set= array();
        while($result_set[]= $this->fetch($query)) {};
        unset($result_set[sizeof($result_set)- 1]);
        $this->_logline_text("result_set", $result_set);
        $this->last_num_rows= sybase_num_rows($query);
        return $result_set;
      }
      return $query;
    }
    
    /**
     * Update-Wrapper
     *
     * @access  public
     * @param	string sql Das SQL (ohne update)
     * @return  bool Query-Ergebnis
     */   
    function update($sql) {
      $this->last_affected_rows= -1;
      $this->_logline_text("update", "{SQL} $sql");
      $result= $this->query("update $sql", $this->handle);
      if($result) {
        $this->last_affected_rows= sybase_affected_rows();
      }
      return $result;
    }

    /**
     * Insert-Wrapper
     *
     * @access  public
     * @param	string sql Das SQL (ohne insert)
     * @return  bool result Query-Ergebnis
     */   
	function insert($sql) {
	  $this->last_insert_id= $this->last_affected_rows= -1;
	  $this->_logline_text("insert", "{SQL} $sql");
	  $result= $this->query("insert $sql", $this->handle);
	  if($result) {
		$this->last_affected_rows= sybase_affected_rows();
	  }
	  return $result;
	}
	
    /**
     * Delete-Wrapper
     *
     * @access  public
     * @param	string sql Das SQL (ohne delete)
     * @return  bool result Query-Ergebnis
     */   
    function delete($sql) {
      $this->last_affected_rows= -1;
      $this->_logline_text("delete", "{SQL} $sql");
      $result= $this->query("delete $sql", $this->handle);
      if($result) {
        $this->last_affected_rows= sybase_affected_rows();
      }
      return $result;
    }

    /**
     * Letzten Auto-Identity-Wert holen
     *
     * @access  public
     * @return  int Der Wert von select @@IDENTITY
     */   
    function insert_id() {
      $id= $this->query("select @@IDENTITY", $this->handle);
      if(!$id) {
        $this->_logline_text("insert_id", "{result} ERR");
        return 0;
      }
      list($result)= sybase_fetch_row($id);
      $this->_logline_text("insert_id", "{result} $result");
      return $result;
    }
        
    /**
     * Transaktion beginnen
     *
     * @access  public
     * @param   string name default php_transaction Transaktions-Name
     * @return  int Success
     */   
    function start_tran($name= "php_transaction") {
      $this->transaction= $name;
      $this->_logline_text("transaction_start", $this->transaction);
      return $this->query("begin transaction $this->transaction", $this->handle);
    }
 
    /**
     * Transaktion committen
     *
     * @access  public
     * @return  int Success
     */      
    function commit_tran() {
      $return= $this->query("commit transaction $this->transaction");
      if($return) $this->transaction= 0;
      return $return;
    }

    /**
     * Transaktion rollback'en
     *
     * @access  public
     * @return  int Success
     */          
    function rollback_tran() {
      $return= $this->query("rollback transaction $this->transaction");
      if($return) $this->transaction= 0;
      return $return;    
    }
    
    /**
     * Letzen Fehler zurückgeben
     *
     * @access  public
     * @return  int Der Wert von @@ERROR
     */      
    function get_error() {
      $query= $this->query("select @@error", $this->handle);
      if(!$query) {
        $this->_logline_text("get_error", "{result} ERR");
        return 0;
      }
      list($this->Error)= sybase_fetch_row($query);
      $this->_logline_text("get_error", "{result} $this->Error");
      return $this->Error;
    }
    
    /**
     * Destructor
     */
    function __destruct() {
      parent::__destruct();
      //if($this->handle) sybase_close($this->handle);
    } 
    
    /**
     * Anzahl der vom letzten Select-Query betroffene Rows zurückgeben
     *
     * @access  public
     * @params  resource query Query-Handle
     * @return  int Anzahl Rows
     */      
    function num_rows($query) {
      return sybase_num_rows($query);
    }
    
    /**
     * Deutsche Datums/Uhrzeitangabe für Querys aufarbeiten
     *
     * @access  public
     * @param   string localtime Datums-String 14.12.2002, 11:55
     * @return  string convert(datetime, [...]) für Select
     */      
    function timefromlocale($localtime) {
      if(!preg_match('/^([0-9]+).([0-9]+).([0-9]+) ?([0-9]+)?:?([0-9]+)?$/', $localtime, $regs)) return 0;
      _logline_text($localtime, $regs);
      
      // Stunden und Minuten könnten evtl. fehlen
      if(!isset($regs[4])) $regs[4]= 0;
      if(!isset($regs[5])) $regs[5]= 0;
      $time= mktime($regs[4], $regs[5], 0, $regs[2], $regs[1], $regs[3]);
      return "convert(datetime, '".date("M d Y h:i A", $time)."', 100)";
    }
	 
	 function getHandle() {
	 	return $this->handle;
	 }
  } //end::class(Database)
  
  // Sybase-Datum in deutsches Datumsformat umwandeln
  $GLOBALS["Sybase__monthmapping"]= array(
    "Jan" => 1,
    "Feb" => 2,
    "Mar" => 3,
    "Mrz" => 3,
    "Apr" => 4,
    "May" => 5,
    "Mai" => 5,
    "Jun" => 6,
    "Jul" => 7,
    "Aug" => 8,
    "Sep" => 9,
    "Oct" => 10,
    "Okt" => 10,
    "Nov" => 11,
    "Dec" => 12,
    "Dez" => 12
  );
  
?>
