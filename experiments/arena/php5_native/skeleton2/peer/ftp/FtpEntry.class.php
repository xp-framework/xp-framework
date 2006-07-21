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
   * @purpose  Base class
   */
  class FtpEntry extends Object {
    public
      $name         = '',
      $permissions  = 0,
      $numlinks     = 0,
      $user         = '',
      $group        = '',
      $size         = 0,
      $date         = NULL;

    public
      $_hdl     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   resource hdl default NULL
     */
    public function __construct($name, $hdl= NULL) {
      $this->name= $name;
      $this->_hdl= $hdl;
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
    public function setPermissions($perm) {
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
        throw(new IllegalArgumentException('Expect: string(9) / int, have "'.$perm.'"'));
      }
    }

    /**
     * Get Permissions
     *
     * @access  public
     * @return  int
     */
    public function getPermissions() {
      return $this->permissions;
    }

    /**
     * Set Numlinks
     *
     * @access  public
     * @param   int numlinks
     */
    public function setNumlinks($numlinks) {
      $this->numlinks= $numlinks;
    }

    /**
     * Get Numlinks
     *
     * @access  public
     * @return  int
     */
    public function getNumlinks() {
      return $this->numlinks;
    }

    /**
     * Set User
     *
     * @access  public
     * @param   string user
     */
    public function setUser($user) {
      $this->user= $user;
    }

    /**
     * Get User
     *
     * @access  public
     * @return  string
     */
    public function getUser() {
      return $this->user;
    }

    /**
     * Set Group
     *
     * @access  public
     * @param   string group
     */
    public function setGroup($group) {
      $this->group= $group;
    }

    /**
     * Get Group
     *
     * @access  public
     * @return  string
     */
    public function getGroup() {
      return $this->group;
    }

    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    public function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

    /**
     * Set Date
     *
     * @access  public
     * @param   &util.Date date
     */
    public function setDate(&$date) {
      $this->date= &$date;
    }

    /**
     * Get Date
     *
     * @access  public
     * @return  &util.Date
     */
    public function &getDate() {
      return $this->date;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   mixed name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
  }
?>
