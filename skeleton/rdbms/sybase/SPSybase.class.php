<?php
/**
 *
 * $Id$
 */
  
  uses(
    'rdbms.SQLException',
    'rdbms.sybase.SybaseDate'
  );
  
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
      $host, 
      $user, 
      $pass, 
      $db, 
      $field_map,
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
     * Konnektieren
     *
     * @access  public
     * @return  resource Datenbank-Handle
     * @throws  SQLException, wenn kein Connect zustande kommt
     */
    function connect() {
      $this->handle= @sybase_connect($this->host, $this->user, $this->pass);
      if (FALSE === $this->handle) return throw(new SQLException(sprintf(
        'Unable to connect to %s@%s',
        $this->user,
        $this->host
      )));
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
      return @sybase_select_db($this->db, $this->handle);
    }
    
    /**
     * Query-Funktion. Connected, falls nötig
     *
     * @access  public
     * @param   string sql  Der abzusetzende SQL-Query-String
     * @return  bool result Query-Ergebnis
     * @throws  SQLException, wenn der Query schiefgeht
     */
    function query($sql) {
    
      // Wenn es keinen Connect gibt, einen herstellen
      if(!$this->handle) {
        $connect= $this->connect();
        if (isset($this->db)) $this->select_db();
      }
      
      $result= sybase_query($sql, $this->handle);
      if (FALSE === $result) throw(new SQLException('Statement failed', $sql));
      return $result;
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
      // $this->_logline_text("seek", "{offset} $offset");
      return sybase_data_seek($query, $offset);
    }
    
    /**
     * Einen Datensatz holen
     *
     * @access  public
     * @param	resource query Queryhandle, z.B. aus query()
     * @return  array Der selektierte Datensatz
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
        
        // Field-Mapping
        if (isset($this->field_map[$key])) $row['map_'.$key]= $this->field_map[$key][$val];

        // Datumsangaben automatisch umwandeln, Format: mon dd yyyy hh:mm AM (or PM)
        // Ist natürlich Pfusch, da ein String genau so aufgebaut sein könnte!
        if (preg_match(
          RE_SYBASE_DATE,
          $val, 
          $regs
        )) {
          $row[$key]= new SybaseDate();
          $row[$key]->fromRegs($regs);
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
     */   
    function &select_ref($sql) {
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
     */   
    function &select($sql) {
      $query= $this->query('select '.preg_replace('/^[\s\t\r\n]*select/i', '', $sql));
      if($query) {
        $result_set= array();
        while($result_set[]= $this->fetch($query)) {};
        unset($result_set[sizeof($result_set)- 1]);
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
      $result= $this->query('update '.preg_replace('/^[\s\t\r\n]*update/i', '', $sql));
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
      $result= $this->query('insert '.preg_replace('/^[\s\t\r\n]*insert/i', '', $sql));
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
      $result= $this->query('delete '.preg_replace('/^[\s\t\r\n]*delete/i', '', $sql));
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
      $qrh= $this->query("select @@IDENTITY");
      if(!$qrh) return 0;
      list($result)= sybase_fetch_row($qrh, $this->handle);
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
      $qrh= $this->query("select @@error", $this->handle);
      if(!$qrh) return 0;
      list($result)= sybase_fetch_row($query);
      return $result;
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
    
    function getHandle() {
      return $this->handle;
    }
    
  } //end::class(Database)
?>
