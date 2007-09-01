<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 56677 2007-03-21 14:10:03Z rdoebele $
 */

  namespace name::kiesel::pxl::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table tag, database main
   * (Auto-generated on Sun, 06 May 2007 20:53:06 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class PxlTag extends rdbms::DataSet {
    public
      $page_id            = 0,
      $tag                = '';

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('tag');
        $peer->setConnection('pxl');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'page_id'             => array('%d', ::INT, FALSE),
          'tag'                 => array('%s', ::VARCHAR, FALSE)
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return ::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "i_tag_page"
     * 
     * @param   int page_id
     * @return  name.kiesel.pxl.db.PxlTag[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPage_id($page_id) {
      return self::getPeer()->doSelect(new (array('page_id', $page_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "i_tag_tag"
     * 
     * @param   string tag
     * @return  name.kiesel.pxl.db.PxlTag[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTag($tag) {
      return self::getPeer()->doSelect(new (array('tag', $tag, EQUAL)));
    }

    /**
     * Retrieves page_id
     *
     * @return  int
     */
    public function getPage_id() {
      return $this->page_id;
    }
      
    /**
     * Sets page_id
     *
     * @param   int page_id
     * @return  int the previous value
     */
    public function setPage_id($page_id) {
      return $this->_change('page_id', $page_id);
    }

    /**
     * Retrieves tag
     *
     * @return  string
     */
    public function getTag() {
      return $this->tag;
    }
      
    /**
     * Sets tag
     *
     * @param   string tag
     * @return  string the previous value
     */
    public function setTag($tag) {
      return $this->_change('tag', $tag);
    }
  }
?>