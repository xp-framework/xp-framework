<?php
/* This class is part of the XP framework
 *
 * $Id: BugzillaDuplicates.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::bugzilla::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table duplicates, database bugs
   * (Auto-generated on Wed,  2 Feb 2005 16:40:39 +0100 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaDuplicates extends rdbms::DataSet {
    public
      $dupe_of            = 0,
      $dupe               = 0;

    /**
     * Static initializer
     *
     */
    public static function __static() { 
      with ($peer= ::getPeer()); {
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
     * @return  &rdbms.Peer
     */
    public function getPeer() {
      return ::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @param   int dupe
     * @return  &Duplicates object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByDupe($dupe) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('dupe', $dupe, EQUAL))));
    }

    /**
     * Retrieves dupe_of
     *
     * @return  int
     */
    public function getDupe_of() {
      return $this->dupe_of;
    }
      
    /**
     * Sets dupe_of
     *
     * @param   int dupe_of
     * @return  int the previous value
     */
    public function setDupe_of($dupe_of) {
      return $this->_change('dupe_of', $dupe_of);
    }

    /**
     * Retrieves dupe
     *
     * @return  int
     */
    public function getDupe() {
      return $this->dupe;
    }
      
    /**
     * Sets dupe
     *
     * @param   int dupe
     * @return  int the previous value
     */
    public function setDupe($dupe) {
      return $this->_change('dupe', $dupe);
    }
  }
?>
