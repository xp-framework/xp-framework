<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpListIterator');

  /**
   * List of entries on an FTP server
   *
   * @see      xp://peer.ftp.FtpDir#entries
   * @purpose  List object
   */
  class FtpEntryList extends Object implements IteratorAggregate {
    protected
      $connection   = NULL,
      $list         = array();

    /**
     * Constructor
     *
     * @param   string[] list
     * @param   peer.ftp.FtpConnection connection
     */
    public function __construct(array $list, FtpConnection $connection) {
      $this->list= $list;
      $this->connection= $connection;
    }
    
    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      return new FtpListIterator($this->list, $this->connection);
    }

    /**
     * Returns the number of elements in this list.
     *
     * @return  int
     */
    public function size() {
      return sizeof($this->list) - 2;     // XXX what happens if "." and ".." are not returned by the FTP server?
    }

    /**
     * Tests if this list has no elements.
     *
     * @return  bool
     */
    public function isEmpty() {
      return 0 === $this->size();
    }
  
    /**
     * Returns all elements in this list as an array.
     *
     * @return  peer.ftp.FtpEntry[] an array of all entries
     * @throws  lang.FormatException in case an entry cannot be parsed
     */
    public function asArray() {
      static $dotdirs= array('.', '..');

      for ($i= 0, $r= array(), $s= sizeof($this->list); $i < $s; $i++) {
        $e= $this->connection->parser->entryFrom($this->list[$i], $this->connection);
        in_array($e->getName(), $dotdirs) || $r[]= $e;
      }
      return $r;
    }

    /**
     * Creates a string representation of this list
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->size().' entries)@'.xp::stringOf($this->list);
    }
  }
?>
