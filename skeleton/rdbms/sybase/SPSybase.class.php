<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  
  uses(
    'rdbms.SQLException',
    'util.Date',
    'util.log.Logger'
  );
  
  /**
   * Sybase connection class
   *
   * @purpose  Sybase connection
   * @ext      sybase
   * @see      http://manuals.sybase.com/
   */
  class SPSybase extends Object {
    var 
      $handle= NULL,
      $host, 
      $user, 
      $pass, 
      $db, 
      $field_map= array(),
      $last_affected_rows= -1, 
      $last_num_rows= -1,
      $transaction= 0;
   
    // Do buffered queries? Default: TRUE
    var
      $_buffering= TRUE;
   
    // Logger
    var
      $log;
	  
    var
      $fields	= NULL,
      $lengths	= NULL;
 
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct($params) {
      $l= &Logger::getInstance();
      $this->log= &$l->getCategory($this->getClassName());
      parent::__construct($params);
    }
    
    /**
     * Konnektieren
     *
     * @access  public
     * @return  resource Datenbank-Handle
     * @throws  SQLException, wenn kein Connect zustande kommt
     */
    function connect() {
      $this->handle= sybase_connect($this->host, $this->user, $this->pass);
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
     * Private Helper-Funktion, ermöglicht solcherlei Aufrufe
     *
     * <pre>
     * $qrh= $dbo->query('
     *   select 
     *     domain_id 
     *   from 
     *     domain 
     *   where 
     *     domainname = %s 
     *     and adminc= %d
     *     and ext_vertragpos= %s
     *   ', 
     *   'thekid.de', 
     *   2828822,
     *   NULL
     * );
     * </pre>
     *
     * => Um das Quoten von Strings muss sich nicht mehr gekümmert werden!
     * => NULL wird automatisch zu NULL (bei String-Feldern)
     *
     * @access  private
     * @param   array args Die Argumente
     */
    function prepare($args) {
      $sql= $args[0];
      if (sizeof($args)<= 1) return $sql;
      $j= 0;    
      $sql= $tok= strtok($sql, '%');
      while (++$j && $tok= strtok('%')) {
        $arg= (is_object($args[$j]) && method_exists($args[$j], 'toString') 
          ? $args[$j]->toString()
          : $args[$j]
        );
        switch ($tok{0}) {
          case 'd': $sql.= ($arg === NULL ? 'NULL' : intval($arg)).substr($tok, 1); break;
          case 'c': $sql.= ($arg === NULL ? 'NULL' : $arg).substr($tok, 1); break;
          case 's': $sql.= ($arg === NULL ? 'NULL' : '"'.str_replace('"', '""', $arg).'"').substr($tok, 1); break;
          case 'l': $sql.= ($arg === NULL ? 'NULL' : '"'.str_replace('"', '""', $arg).'%"').substr($tok, 1); break;
          case 'u': $sql.= ($arg === NULL ? 'NULL' : '"'.date ('Y-m-d h:iA', $arg).'"').substr($tok, 1); break;
          case 'f': $sql.= ($arg === NULL ? 'NULL' : floatval ($arg)).substr($tok, 1); break;
          default: $sql.= '%'.$tok; $j--;
        }
      }
      return $sql;
    }
    
    function setBuffering($b) {
      $this->_buffering= $b;
    }
    
    /**
     * Query-Funktion. Connected, falls nötig
     *
     * @access  public
     * @param   string sql Der abzusetzende SQL-Query-String
     * @return  bool result Query-Ergebnis
     * @throws  SQLException, wenn der Query schiefgeht
     */
    function query() {
      $args= func_get_args();
      $this->lengths= $this->fields= NULL;
	  
      $sql= $this->prepare($args);

      // Wenn es keinen Connect gibt, einen herstellen
      if(!$this->handle) {
        $connect= $this->connect();
        if (isset($this->db)) $this->select_db();
      }
      
      $this->log->info('Sybase::'.$sql);
      if ($this->_buffering) {
        $result= sybase_query($sql, $this->handle);
      } else {
        $result= sybase_unbuffered_query($sql, $this->handle, SYBASE_USE_RESULT);
      }
      if (FALSE === $result) {
        return throw(new SQLException('statement failed', $sql));
      }
      
      // Feldtypen herausfinden
      $i= -1;
      $this->lengths= $this->fields= array();
      while (++$i < @sybase_num_fields($result)) {
        $field= sybase_fetch_field($result, $i);
        $this->fields[$field->name]= $field->type;
	$this->lengths[$field->name]= $field->max_length;
      }
      
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
        
        // FALSE ==> NULL
        $this->log->debug($key.' is NULL ?', ($val === FALSE) ? 'yes' : 'no');
        if ($val === FALSE || $val === NULL) {
          $row[$key]= NULL;
          continue;
        }
        
        // Field-Mapping
        if (isset($this->field_map[$key])) $row['map_'.$key]= $this->field_map[$key][$val];
                
        // Datumsangaben automatisch umwandeln
        switch ($this->fields[$key]) {
          case 'datetime': 
            $row[$key]= new Date($val); 
            break;
            
          case 'bit':
          case 'int': 
            settype($row[$key], 'integer'); 
            break;
            
          case 'real':
            if (is_string($val) && strstr($val, '.')) {
              settype($row[$key], 'double'); 
            } else {
              settype($row[$key], 'integer');
            }
            break;
        }
      }
      
      // $this->log->debug($row);
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
    function &select_ref() {
      $args= func_get_args();
      $query= $this->query('select '.preg_replace('/^[\s\t\r\n]*select/i', '', $this->prepare($args)));
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
    function &select() {
      $args= func_get_args();
      $query= $this->query('select '.preg_replace('/^[\s\t\r\n]*select/i', '', $this->prepare($args)));
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
    function update() {
      $args= func_get_args();
      $this->last_affected_rows= -1;
      $result= $this->query('update '.preg_replace('/^[\s\t\r\n]*update/i', '', $this->prepare($args)));
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
    function insert() {
      $args= func_get_args();
      $this->last_insert_id= $this->last_affected_rows= -1;
      $result= $this->query('insert '.preg_replace('/^[\s\t\r\n]*insert/i', '', $this->prepare($args)));
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
    function delete() {
      $args= func_get_args();
      $this->last_affected_rows= -1;
      $result= $this->query('delete '.preg_replace('/^[\s\t\r\n]*delete/i', '', $this->prepare($args)));
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
      if (FALSE === $qrh= $this->query("select @@IDENTITY")) return FALSE;
      list($result)= sybase_fetch_row($qrh);
      return $result;
    }

    
    /**
     * Letzen Fehler zurückgeben
     *
     * @access  public
     * @return  int Der Wert von @@ERROR
     */      
    function get_error() {
      if (FALSE === $qrh= $this->query("select @@ERROR", $this->handle)) return FALSE;
      list($result)= sybase_fetch_row($qrh);
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
     * Gibt den Handle (resource id #x) der Funktion sybase_connect() zurück
     *
     * @return  resource Datenbankhandle
     */
    function getHandle() {
      return $this->handle;
    }
    
  } //end::class(Database)
?>
