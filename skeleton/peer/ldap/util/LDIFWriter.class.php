<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Write LDAP entries to LDIF format
   *
   * Example:
   * <code>
   *   uses(
   *     'io.File',
   *     'peer.ldap.LDAPClient',
   *     'peer.ldap.util.LDIFWriter',
   *     'peer.URL'
   *   );
   *   
   *   // {{{ main
   *   $dsn= &new URL('ldap://my.ldap.host:389/uid=friebe,ou=People,o=XP,c=DE');
   *   $dn= substr($dsn->getPath(), 1);
   *   
   *   $l= &new LDAPClient($dsn->getHost(), $dsn->getPort(389));
   *   $writer= &new LDIFWriter(new File('php://stdout'));
   *   
   *   try(); {
   *     $l->connect();
   *     $l->bind($dsn->getUser(NULL), $dsn->getPassword(NULL));
   *     
   *     $writer->initialize();
   *     $writer->write($l->read(new LDAPEntry($dn)));
   *   } if (catch('Exception', $e)) {
   *     fputs(STDERR, $e->getStackTrace());
   *   }
   *   
   *   $l->close();
   *   $writer->finish();
   *   // }}}
   * </code>
   *
   * @purpose  LDIF Writer
   */
  class LDIFWriter extends Object {
    public
      $stream       = NULL;
      
    /**
     * Constructor
     *
     * @param   io.Stream stream
     */
    public function __construct($stream) {
      $this->stream= $stream;
      
    }
    
    /**
     * Initialize this writer
     *
     * @param   string mode default STREAM_MODE_WRITE
     * @return  bool success
     */
    public function initialize($mode= STREAM_MODE_WRITE) {
      return $this->stream->open($mode);
    }
    
    /**
     * Write an entry
     *
     * @param   peer.ldap.LDAPEntry entry
     * @throws  lang.IllegalArgumentException in case the parameter is not an LDAPEntry object
     */
    public function write($entry) {
      if (!is('LDAPEntry', $entry)) {
        throw(new IllegalArgumentException(
          'Parameter entry is expected to be a peer.ldap.LDAPEntry object (given: '.xp::typeOf($entry).')'
        ));
      }
      $this->stream->write(sprintf("dn: %s\n", $entry->getDN()));
      foreach (array_keys($entry->attributes) as $key) {
        if ('dn' == $key) continue;
        for ($i= 0, $s= sizeof($entry->attributes[$key]); $i < $s; $i++) {
          $this->stream->write(sprintf("%s: %s\n", $key, $entry->attributes[$key][$i]));
        }
      }
      $this->stream->write("\n");
    }
  }
?>
