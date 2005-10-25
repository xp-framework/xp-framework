<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // Defines for log
  define('EZMLM_ADD',     '+');
  define('EZMLM_DEL',     '-');
  define('EZMLM_MOD',     '%');
  
  define('EZMLM_WHO',     'w');

  /**
   * Helper class to maintain sql based ezmlm mailinglists.
   *
   * @see      http://ezmlm.org
   * @purpose  Maintain mailing list subscriptions
   */
  class EzmlmSqlUtil extends Object {
    var
      $db=          NULL,
      $database=    '',
      $mailinglist= '';
      
    /**
     * Constructor.
     *
     * @access  public
     * @param   string database
     * @param   string mailinglist
     */
    function __construct($database, $mailinglist) {
      $this->database= $database;
      $this->mailinglist= $mailinglist;
    }
    
    /**
     * Set connection,
     *
     * @access  public
     * @param   &rdbms.mysql.MysqlConnection db
     */
    function setConnection(&$db) {
      $this->db= &$db;
    }
  
    /**
     * Check whether address is subscribed
     *
     * @access  public
     * @param   string address
     * @return  bool
     */
    function isSubscribed($address) {
      $s= $this->db->select('
          1
        from
          %c.%c
        where address= %s
          and hash between 1 and 52
        ',
        $this->database,
        $this->mailinglist,
        $address
      );
      
      return (bool)sizeof($s);
    }
    
    /**
     * Get list of subscribers
     *
     * @access  public
     * @return  string[]
     */
    function getSubscribers() {
      $q= &$this->db->query('
        select
          address
        from
          %c.%c
        where hash between 1 and 52
        ',
        $this->database,
        $this->mailinglist
      );
      $s= array();
      while ($q && $r= $q->next('address')) { $s[]= $r; }
      return $s;
    }
    
    /**
     * Add subscriber
     *
     * @access  public
     * @param   string address
     */
    function addSubscriber($address) {
      $this->db->insert('
        into %c.%c (
          hash,
          address
        ) values (
          %d,
          %s
        )',
        $this->database,
        $this->mailinglist,
        (int)rand(1, 52),
        $address
      );
      $this->writeLog($address, '', EZMLM_ADD);
    }
    
    /**
     * Remove subscriber
     *
     * @access  public
     * @param   string address
     * @return  int
     */
    function removeSubscriber($address) {
      $cnt= $this->db->delete('
        from 
          %c.%c
        where address= %s
        ',
        $this->database,
        $this->mailinglist,
        $address
      );
      $cnt && $this->writeLog($address, '', EZMLM_DEL);
      return $cnt;
    }
    
    /**
     * Modify address
     *
     * @access  public
     * @param   string from
     * @param   string to
     * @return  int count
     */
    function alterAddress($from, $to) {
      if (!$this->isSubscribed($from)) return FALSE;
      $cnt= $this->db->update('
        %c.%c
        set
          address= %s
        where address= %s
        ',
        $this->database,
        $this->mailinglist,
        $to,
        $from
      );
      $cnt && $this->writeLog($to, $from, EZMLM_MOD);
      return $cnt;
    }
    
    /**
     * Write log information
     *
     * @access  protected
     * @param   string addr
     * @param   string from
     * @param   string action
     * @param   string who
     */
    function writeLog($addr, $from, $action, $who= EZMLM_WHO) {
      $this->db->insert('
        %c.%c_slog (
          tai,
          address,
          fromline,
          edir,
          etype
        ) values (
          now(),
          %s,
          %s,
          %s,
          %s
        )',
        $this->database,
        $this->mailinglist,
        $addr,
        $from,
        $action,
        $who
      );
    }    
  }
?>
