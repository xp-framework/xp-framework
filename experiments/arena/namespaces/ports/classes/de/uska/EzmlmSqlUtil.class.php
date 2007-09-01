<?php
/* This class is part of the XP framework
 *
 * $Id: EzmlmSqlUtil.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::uska;

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
  class EzmlmSqlUtil extends lang::Object {
    public
      $db=          NULL,
      $database=    '',
      $mailinglist= '';
      
    /**
     * Constructor.
     *
     * @param   string database
     * @param   string mailinglist
     */
    public function __construct($database, $mailinglist) {
      $this->database= $database;
      $this->mailinglist= $mailinglist;
    }
    
    /**
     * Set connection,
     *
     * @param   &rdbms.mysql.MysqlConnection db
     */
    public function setConnection($db) {
      $this->db= $db;
    }
  
    /**
     * Check whether address is subscribed
     *
     * @param   string address
     * @return  bool
     */
    public function isSubscribed($address) {
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
     * @return  string[]
     */
    public function getSubscribers() {
      $q= $this->db->query('
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
     * @param   string address
     */
    public function addSubscriber($address) {
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
     * @param   string address
     * @return  int
     */
    public function removeSubscriber($address) {
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
     * @param   string from
     * @param   string to
     * @return  int count
     */
    public function alterAddress($from, $to) {
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
     * @param   string addr
     * @param   string from
     * @param   string action
     * @param   string who
     */
    public function writeLog($addr, $from, $action, $who= EZMLM_WHO) {
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
