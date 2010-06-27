<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.ietf.UUID');
  
  define('LOCKTOKEN_PREFIX', 'opaquelocktoken');
  
  /**
   * <quote>
   * A lock token is a type of state token, represented as a URI, which
   * identifies a particular lock.  A lock token is returned by every
   * successful LOCK operation in the lockdiscovery property in the
   * response body, and can also be found through lock discovery on a
   * resource.
   * 
   * Lock token URIs MUST be unique across all resources for all time.
   * This uniqueness constraint allows lock tokens to be submitted across
   * resources and servers without fear of confusion.
   * 
   * This specification provides a lock token URI scheme called
   * opaquelocktoken that meets the uniqueness requirements.  However
   * resources are free to return any URI scheme so long as it meets the
   * uniqueness requirements
   * </quote>
   * 
   * @purpose  Locktocken
   * @see      rfc://2518#6.3
   * @see      http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt
   */
  class OpaqueLockTocken extends Object {
    public
      $uuid= NULL;
    
    /**
     * Constructor
     *
     * @param   org.ietf.UUID uuid
     */
    public function __construct($uuid) {
      $this->uuid= $uuid;
      
    }
    
    /**
     * Create a LockTocken from a string
     *
     * @param   string str
     * @return  util.webdav.LockTocken
     * @throws  lang.FormatException in case the string is not a valid opaquelocktoken
     */
    public static function fromString($str) {
      list($prefix, $uuidstr)= explode(':', $str, 2);
      if (
        (LOCKTOKEN_PREFIX !== $prefix) ||
        (FALSE === ($uuid= UUID::fromString($uuidstr)))
      ) {
        throw new FormatException($str.' is not a valid opaquelocktoken string');
      }
      
      return new OpaqueLockTocken($uuid);
    }
       
    /**
     * Create string representation
     *
     * Examples:
     * <pre>
     * opaquelocktoken:f54e9600-9600-154e-b88a-066e7c6b1eb3
     * opaquelocktoken:faabe080-e080-1aab-a85d-066e72288282
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      return LOCKTOKEN_PREFIX.':'.$this->uuid->toString();
    }
  }
?>
