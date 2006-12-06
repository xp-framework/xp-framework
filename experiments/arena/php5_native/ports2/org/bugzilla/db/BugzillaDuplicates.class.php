<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table duplicates, database bugs
   * (Auto-generated on Wed,  2 Feb 2005 16:40:39 +0100 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaDuplicates extends DataSet {
    public
      $dupe_of            = 0,
      $dupe               = 0;

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() { 
      with ($peer= &BugzillaDuplicates::getPeer()); {
        $peer->setTable('duplicates');
        $peer->setConnection('bugzilla');
        $peer->setPrimary(array('dupe'));
        $peer->setTypes(array(
          'dupe_of'             => '%d',
          'dupe'                => '%d'
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @access  public
     * @return  &rdbms.Peer
     */
    public function &getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @access  static
     * @param   int dupe
     * @return  &Duplicates object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getByDupe($dupe) {
      $peer= &BugzillaDuplicates::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('dupe', $dupe, EQUAL))));
    }

    /**
     * Retrieves dupe_of
     *
     * @access  public
     * @return  int
     */
    public function getDupe_of() {
      return $this->dupe_of;
    }
      
    /**
     * Sets dupe_of
     *
     * @access  public
     * @param   int dupe_of
     * @return  int the previous value
     */
    public function setDupe_of($dupe_of) {
      return $this->_change('dupe_of', $dupe_of);
    }

    /**
     * Retrieves dupe
     *
     * @access  public
     * @return  int
     */
    public function getDupe() {
      return $this->dupe;
    }
      
    /**
     * Sets dupe
     *
     * @access  public
     * @param   int dupe
     * @return  int the previous value
     */
    public function setDupe($dupe) {
      return $this->_change('dupe', $dupe);
    }
  }
?>
