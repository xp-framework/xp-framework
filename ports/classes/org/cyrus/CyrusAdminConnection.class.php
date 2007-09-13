<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Traceable');

  /**
   * Cyrus admin connection class. Used to perform 
   * administrative tasks on a Cyrus IMAP server.
   *
   * @see      http://cyrusimap.web.cmu.edu/imapd/specs.html
   * @purpose  Admin interface
   */
  class CyrusAdminConnection extends Object implements Traceable {
    public
      $cat      = NULL;

    protected
      $_sock    = NULL,
      $tag      = 1;
    
    /**
     * Constructor
     *
     * @param   string host
     * @param   int port default 143
     */
    public function __construct($host, $port= 143) {
      $this->_sock= new Socket($host, $port);
    }
    
    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }    
    
    /**
     * Connect to server
     *
     * @param   int timeout default 2
     */
    public function connect($timeout= 2) {
      $this->_sock->connect($timeout);
      
      // Read welcome line
      $line= $this->_response();
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function disconnect() {
      if (!$this->_sock->isConnected()) return;
      $this->_sock->close();
    }
    
    /**
     * Wrapper that reads the response from the remote host, returning
     * it into an array if not specified otherwise.
     *
     * Stops reading at one of the terminals "OK", "NO" or "BAD".
     *
     * @param   bool discard default FALSE
     * @param   bool error default TRUE
     * @return  string[]
     * @throws  lang.FormatException in case "NO" occurs
     */
    protected function _response() {
      $lines= array();
      do {
        $line= $this->_sock->readLine();
        $this->cat && $this->cat->debug('<<<', $line);
        
        if (preg_match('#^\S OK#', $line)) break;
        if (preg_match('#^\S NO#', $line)) throw new FormatException('Negative response: '.$line);
        if (preg_match('#^\S BAD#', $line)) throw new FormatException('Negative response: '.$line);
        
        $lines[]= $line;
      } while (!$this->_sock->eof());

      return $lines;
    }
    
    /**
     * Wrapper that sends a command to the remote host.
     *
     * @param   string format
     * @param   mixed* args
     * @return  bool success
     */
    protected function _sendcmd() {
      $a= func_get_args();
      $cmd= $this->tag().' '.vsprintf(array_shift($a), $a);
      $this->cat && $this->cat->debug('>>>', $cmd);
      return $this->_sock->write($cmd."\r\n");
    }
    
    /**
     * Calculate next tag for connection
     *
     * @return  string
     */
    protected function tag() {
      return $this->tag++;
    }
    
    /**
     * Request server capabilities
     *
     * Sends a <tt>CAPABILITY</tt> command. Response looks like:
     * <pre>* CAPABILITY IMAP4 IMAP4rev1 ACL QUOTA LITERAL+ MAILBOX-REFERRALS</pre>
     *
     * @return  string
     */
    public function capability() {
      $this->_sendcmd('CAPABILITY');
      return current($this->_response());
    }
    
    /**
     * Log in with specified credentials
     * 
     * Client sends:
     * <pre>
     *   1 LOGIN username password
     * </pre>
     *
     * Server responds:
     * <pre>
     *   1 OK User logged in
     * </pre>
     *
     * @param   string username   
     * @param   string password
     * @param   lang.FormatException when login fails
     */
    public function login($username, $password) {
      $this->_sendcmd('LOGIN %s %s', $username, $password);
      $this->_response();
    }
    
    /**
     * Sends NOOP
     *
     * Client sends:
     * <pre>
     *   1 NOOP
     * </pre>
     *
     * Server responds:
     * <pre>
     *   1 OK Completed
     * </pre>
     *
     */
    public function noop() {
      $this->_sendcmd('NOOP');
      $this->_response();
    }
    
    /**
     * Request quota for an user
     * 
     * Client sends:
     * <pre>
     *   4 GETQUOTA user.kiesel
     * </pre>
     *
     * Server responds:
     * <pre>
     *   * QUOTA user.kiesel (STORAGE 1 1024000)
     *   1 OK Completed
     * </pre>
     *
     * @param   string username
     * @return  int quota
     */
    public function getQuota($username) {
      $this->_sendcmd('GETQUOTA user.%s', $username);
      $lines= $this->_response();
      if (sscanf(current($lines), '* QUOTA user.%[^ ] (STORAGE %[^ ] %[^)])',
        $user,
        $storage,
        $quota
      ) != 3) throw new FormatException('Cannot parse quota response');
      return $quota;
    }
    
    /**
     * Set quota for an user
     *
     * Client sends:
     * <pre>
     *   6 SETQUOTA user.notexist (STORAGE 2048000)
     * </pre>
     *
     * Server responds:
     * <pre>
     *   6 OK Completed
     * </pre>
     *   
     * @param   string username
     * @param   int quota
     */
    public function setQuota($username, $quota) {
      $this->_sendcmd('SETQUOTA user.%s (STORAGE %d)', $username, $quota);
      $this->_response();
      return TRUE;
    }
    
    /**
     * Create an user
     *
     * Client sends:
     * <pre>
     *   4 CREATE user.notexist
     * </pre>
     *
     * Server responds:
     * <pre>
     *   4 OK Completed
     * </pre>
     *   
     * @param   string username
     */
    public function createUser($username) {
      $this->_sendcmd('CREATE user.%s', $username);
      $this->_response();
      return TRUE;
    }
    
    /**
     * Delete an user
     *
     * Client sends:
     * <pre>
     *   4 DELETE user.notexist
     * </pre>
     *
     * Server responds:
     * <pre>
     *   4 OK Completed
     * </pre>
     *   
     * @param   string username
     */
    public function deleteUser($username) {
      $this->_sendcmd('DELETE user.%s', $username);
      $this->_response();
      return TRUE;
    }
    
    /**
     * Retrieve ACL for user
     *
     * Client sends:
     * <pre>
     *   4 GETACL user.notexist
     * </pre>
     *
     * Server responds:
     * <pre>
     *   
     * </pre>
     *   
     * @see     rfc://4314
     * @param   string username
     * @return  string[]
     */
    public function getACLFor($username) {
      $this->_sendcmd('GETACL user.%s', $username);
      return $this->_response();
    }
    
    /**
     * Set ACL for user on a mailbox
     *
     * Client sends:
     * <pre>
     *   4 SETACL user.notexist notexist lrswipcda
     * </pre>
     *
     * Server responds:
     * <pre>
     *   4 OK Completed
     * </pre>
     *
     * The ACL string can contain the following chars:
     * <ul>
     * <li>l - lookup (mailbox is visible to LIST/LSUB commands, SUBSCRIBE
     *     mailbox)</li>
     * <li>r - read (SELECT the mailbox, perform STATUS)</li>
     * <li>s - keep seen/unseen information across sessions (set or clear
     *     \SEEN flag via STORE, also set \SEEN during APPEND/COPY/
     *     FETCH BODY[...])</li>
     * <li>w - write (set or clear flags other than \SEEN and \DELETED via
     *     STORE, also set them during APPEND/COPY)</li>
     * <li>i - insert (perform APPEND, COPY into mailbox)</li>
     * <li>p - post (send mail to submission address for mailbox,
     *     not enforced by IMAP4 itself)</li>
     * <li>k - create mailboxes (CREATE new sub-mailboxes in any
     *     implementation-defined hierarchy, parent mailbox for the new
     *     mailbox name in RENAME)</li>
     * <li>x - delete mailbox (DELETE mailbox, old mailbox name in RENAME)</li>
     * <li>t - delete messages (set or clear \DELETED flag via STORE, set
     *     \DELETED flag during APPEND/COPY)</li>
     * <li>e - perform EXPUNGE and expunge as a part of CLOSE</li>
     * <li>a - administer (perform SETACL/DELETEACL/GETACL/LISTRIGHTS)</li>
     *
     * @see     rfc://4314
     * @param   string username
     * @param   string mailbox
     * @param   string acl
     */
    public function setACL($username, $mailbox, $acl) {
      $this->_sendcmd('SETACL user.%s %s %s', $mailbox, $username, $acl);
      $this->_response();
    }
    
    /**
     * Closes the current session
     *
     * Client sends:
     * <pre>
     *   8 LOGOUT
     * </pre>
     *
     * Server then closes the connection.
     *
     */
    public function logout() {
      $this->_sendcmd('LOGOUT');
    }
  }
?>
