<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');

  /**
   * Base class for all FTP entries
   *
   * @see      xp://peer.ftp.FtpDir
   * @see      xp://peer.ftp.FtpFile
   * @test     xp://net.xp_framework.unittest.peer.FtpRawListTest
   * @purpose  Abstract base class
   */
  abstract class FtpEntry extends Object {
    protected
      $connection   = NULL,
      $name         = '',
      $permissions  = 0,
      $numlinks     = 0,
      $user         = '',
      $group        = '',
      $size         = 0,
      $date         = NULL;
    
    /**
     * Constructor
     *
     * @param   string name
     * @param   peer.ftp.FtpConnection connection
     */
    public function __construct($name, FtpConnection $connection) {
      $this->name= $name;
      $this->connection= $connection;
    }
    
    /**
     * Get connection
     *
     * @return  peer.ftp.FtpConnection 
     */
    public function getConnection() {
      return $this->connection;
    }

    /**
     * Checks whether this entry exists.
     *
     * @return  bool TRUE if the file exists, FALSE otherwise
     * @throws  io.IOException in case of an I/O error
     */
    public function exists() {
      return ftp_size($this->connection->handle, $this->name) != -1;
    }

    /**
     * Rename this entry
     *
     * @param   string to the new name
     * @throws  io.IOException in case of an I/O error
     */
    public function rename($to) {
      if (!ftp_rename($this->connection->handle, $this->name, $to)) {
        throw new IOException('Could not rename '.$this->name.' to '.$to);
      }
    }

    /**
     * Change this entry's permissions
     *
     * @param   int to the new permissions
     * @throws  io.IOException in case of an I/O error
     */
    public function changePermissions($to) {
      if (!ftp_chmod($this->connection->handle, $this->name, $to)) {
        throw new IOException('Could not change '.$this->name.'\'s permissions to '.$to);
      }
    }

    /**
     * Delete this entry
     *
     * @throws  io.IOException in case of an I/O error
     */
    public abstract function delete();

    /**
     * Set Permissions. Takes either a string or an integer as argument.
     * In case a string is passed, it should have the following form:
     *
     * <pre>
     *   rwxr-xr-x  # 755
     *   rw-r--r--  # 644
     * </pre>
     *
     * @param   mixed perm
     * @throws  lang.IllegalArgumentException
     */
    public function setPermissions($perm) {
      static $m= array('r' => 4, 'w' => 2, 'x' => 1, '-' => 0);

      if (is_string($perm) && 9 == strlen($perm)) {
        $this->permissions= (
          ($m[$perm{0}] | $m[$perm{1}] | $m[$perm{2}]) * 100 +
          ($m[$perm{3}] | $m[$perm{4}] | $m[$perm{5}]) * 10 +
          ($m[$perm{6}] | $m[$perm{7}] | $m[$perm{8}])
        );
      } else if (is_int($perm)) {
        $this->permissions= $perm;
      } else {
        throw new IllegalArgumentException('Expected either a string(9) or int, have '.xp::stringOf($perm));
      }
    }

    /**
     * Get Permissions
     *
     * @return  int
     */
    public function getPermissions() {
      return $this->permissions;
    }

    /**
     * Set Numlinks
     *
     * @param   int numlinks
     */
    public function setNumlinks($numlinks) {
      $this->numlinks= $numlinks;
    }

    /**
     * Get Numlinks
     *
     * @return  int
     */
    public function getNumlinks() {
      return $this->numlinks;
    }

    /**
     * Set User
     *
     * @param   string user
     */
    public function setUser($user) {
      $this->user= $user;
    }

    /**
     * Get User
     *
     * @return  string
     */
    public function getUser() {
      return $this->user;
    }

    /**
     * Set Group
     *
     * @param   string group
     */
    public function setGroup($group) {
      $this->group= $group;
    }

    /**
     * Get Group
     *
     * @return  string
     */
    public function getGroup() {
      return $this->group;
    }

    /**
     * Set Size
     *
     * @param   int size
     */
    public function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

    /**
     * Set Date
     *
     * @param   util.Date date
     */
    public function setDate(Date $date) {
      $this->date= $date;
    }

    /**
     * Get Date
     *
     * @return  util.Date
     */
    public function getDate() {
      return $this->date;
    }

    /**
     * Set Name
     *
     * @param   mixed name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(name= %s) {\n".
        "  [permissions ] %d\n".
        "  [numlinks    ] %d\n".
        "  [user        ] %s\n".
        "  [group       ] %s\n".
        "  [size        ] %d\n".
        "  [date        ] %s\n".
        "}",
        $this->getClassName(),
        $this->name,
        $this->permissions,
        $this->numlinks,
        $this->user,
        $this->group,
        $this->size,
        xp::stringOf($this->date)
      );
    }
  }
?>
