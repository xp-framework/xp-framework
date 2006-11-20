<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Represent an FTP entry
   *
   * @see      xp://peer.ftp.FtpDir#getEntry
   * @test     xp://net.xp_framework.unittest.peer.FtpRawListTest
   * @purpose  Base class
   */
  class FtpEntry extends Object {
    var
      $name         = '',
      $permissions  = 0,
      $numlinks     = 0,
      $user         = '',
      $group        = '',
      $size         = 0,
      $date         = NULL;

    var
      $_hdl     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   resource hdl default NULL
     */
    function __construct($name, $hdl= NULL) {
      $this->name= $name;
      $this->_hdl= $hdl;
    }

    /**
     * Parse raw listing entry:
     *
     * Example (Un*x):
     * <pre>
     *   drwx---r-t 37 p159995 ftpusers 4096 Apr 4 20:16 .
     *   -rw----r-- 1 p159995 ftpusers 415 May 23 2000 write.html
     * </pre>
     *
     * Example (Windows):
     * <pre>
     *   01-04-06  04:51PM       <DIR>          _db_import
     *   12-23-05  04:49PM                  807 1and1logo.gif
     *   11-08-06  10:04AM                   27 info.txt 
     * </pre>
     *
     * @model   static
     * @access  public
     * @param   string raw
     * @param   resource handle default NULL
     * @return  &peer.ftp.FtpEntry
     */
    function &parseFrom($raw, $handle= NULL) {
      sscanf(
        $raw, 
        '%s %d %s %s %d %s %d %[^ ] %[^$]',
        $permissions,
        $numlinks,
        $user,
        $group,
        $size,
        $month,
        $day,
        $date,
        $filename
      );
      
      if ('d' == $permissions{0}) {
        $e= &new FtpDir($filename, $handle);
      } else {
        $e= &new FtpEntry($filename, $handle);
      }

      $d= &new Date($month.' '.$day.' '.(strstr($date, ':') ? date('Y').' '.$date : $date));

      // Check for "recent" file which are specified "HH:MM" instead
      // of year for the last 6 month (as specified in coreutils/src/ls.c)
      if (strstr($date, ':')) {
        $now= &Date::now();
        if ($d->getMonth() > $now->getMonth()) $d->year--;
      }

      $e->setPermissions(substr($permissions, 1));
      $e->setNumlinks($numlinks);
      $e->setUser($user);
      $e->setGroup($group);
      $e->setSize($size);
      $e->setDate($d);
      return $e;
    }

    /**
     * Set Permissions. Takes either a string or an integer as argument.
     * In case a string is passed, it should have the following form:
     *
     * <pre>
     *   rwxr-xr-x  # 755
     *   rw-r--r--  # 644
     * </pre>
     *
     * @access  public
     * @param   mixed perm
     * @throws  lang.IllegalArgumentException
     */
    function setPermissions($perm) {
      static $m= array('r' => 4, 'w' => 2, 'x' => 1, '-' => 0);

      if (is_string($perm) && 9 == strlen($perm)) {
        $this->permissions= (
          ($m[$perm{0}] | $m[$perm{1}] | $m[$perm{2}]) * 100 +
          ($m[$perm{3}] | $m[$perm{4}] | $m[$perm{5}]) * 10 +
          ($m[$perm{6}] | $m[$perm{7}] | $m[$perm{8}])
        );
      } elseif (is_int($perm)) {
        $this->permissions= $perm;
      } else {
        return throw(new IllegalArgumentException('Expect: string(9) / int, have "'.$perm.'"'));
      }
    }

    /**
     * Get Permissions
     *
     * @access  public
     * @return  int
     */
    function getPermissions() {
      return $this->permissions;
    }

    /**
     * Set Numlinks
     *
     * @access  public
     * @param   int numlinks
     */
    function setNumlinks($numlinks) {
      $this->numlinks= $numlinks;
    }

    /**
     * Get Numlinks
     *
     * @access  public
     * @return  int
     */
    function getNumlinks() {
      return $this->numlinks;
    }

    /**
     * Set User
     *
     * @access  public
     * @param   string user
     */
    function setUser($user) {
      $this->user= $user;
    }

    /**
     * Get User
     *
     * @access  public
     * @return  string
     */
    function getUser() {
      return $this->user;
    }

    /**
     * Set Group
     *
     * @access  public
     * @param   string group
     */
    function setGroup($group) {
      $this->group= $group;
    }

    /**
     * Get Group
     *
     * @access  public
     * @return  string
     */
    function getGroup() {
      return $this->group;
    }

    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    function getSize() {
      return $this->size;
    }

    /**
     * Set Date
     *
     * @access  public
     * @param   &util.Date date
     */
    function setDate(&$date) {
      $this->date= &$date;
    }

    /**
     * Get Date
     *
     * @access  public
     * @return  &util.Date
     */
    function &getDate() {
      return $this->date;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   mixed name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
  }
?>
