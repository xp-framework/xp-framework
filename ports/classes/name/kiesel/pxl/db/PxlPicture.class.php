<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 56677 2007-03-21 14:10:03Z rdoebele $
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table picture, database main
   * (Auto-generated on Sun, 06 May 2007 13:35:01 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class PxlPicture extends DataSet {
    public
      $picture_id         = 0,
      $page_id            = 0,
      $filename           = '',
      $author_id          = 0;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('picture');
        $peer->setConnection('pxl');
        $peer->setIdentity('picture_id');
        $peer->setPrimary(array('picture_id'));
        $peer->setTypes(array(
          'picture_id'          => array('%d', FieldType::INT, FALSE),
          'page_id'             => array('%d', FieldType::INT, FALSE),
          'filename'            => array('%s', FieldType::VARCHAR, FALSE),
          'author_id'           => array('%d', FieldType::INT, FALSE)
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "i_picture_page"
     * 
     * @param   int page_id
     * @return  name.kiesel.pxl.db.PxlPicture[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPage_id($page_id) {
      return self::getPeer()->doSelect(new Criteria(array('page_id', $page_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "i_picture_pagefile"
     * 
     * @param   int page_id
     * @param   string filename
     * @return  name.kiesel.pxl.db.PxlPicture entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPage_idFilename($page_id, $filename) {
      $r= self::getPeer()->doSelect(new Criteria(
        array('page_id', $page_id, EQUAL),
        array('filename', $filename, EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "pk_picture"
     * 
     * @param   int picture_id
     * @return  name.kiesel.pxl.db.PxlPicture entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPicture_id($picture_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('picture_id', $picture_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves picture_id
     *
     * @return  int
     */
    public function getPicture_id() {
      return $this->picture_id;
    }
      
    /**
     * Sets picture_id
     *
     * @param   int picture_id
     * @return  int the previous value
     */
    public function setPicture_id($picture_id) {
      return $this->_change('picture_id', $picture_id);
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
     * Retrieves filename
     *
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }
      
    /**
     * Sets filename
     *
     * @param   string filename
     * @return  string the previous value
     */
    public function setFilename($filename) {
      return $this->_change('filename', $filename);
    }

    /**
     * Retrieves author_id
     *
     * @return  int
     */
    public function getAuthor_id() {
      return $this->author_id;
    }
      
    /**
     * Sets author_id
     *
     * @param   int author_id
     * @return  int the previous value
     */
    public function setAuthor_id($author_id) {
      return $this->_change('author_id', $author_id);
    }
  }
?>