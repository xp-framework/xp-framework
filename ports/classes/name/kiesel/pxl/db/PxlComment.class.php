<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 56677 2007-03-21 14:10:03Z rdoebele $
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table comment, database main
   * (Auto-generated on Sun, 06 May 2007 20:53:06 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class PxlComment extends DataSet {
    public
      $comment_id         = 0,
      $comment_type_id    = 0,
      $page_id            = 0,
      $bz_id              = 0,
      $title              = NULL,
      $body               = NULL,
      $url                = NULL,
      $commented_at       = NULL,
      $author             = NULL,
      $email              = NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('comment');
        $peer->setConnection('pxl');
        $peer->setIdentity('comment_id');
        $peer->setPrimary(array('comment_id'));
        $peer->setTypes(array(
          'comment_id'          => array('%d', FieldType::INT, FALSE),
          'comment_type_id'     => array('%d', FieldType::INT, FALSE),
          'page_id'             => array('%d', FieldType::INT, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE),
          'title'               => array('%s', FieldType::VARCHAR, TRUE),
          'body'                => array('%s', FieldType::TEXT, TRUE),
          'url'                 => array('%s', FieldType::TEXT, TRUE),
          'commented_at'        => array('%s', FieldType::DATETIME, FALSE),
          'author'              => array('%s', FieldType::VARCHAR, TRUE),
          'email'               => array('%s', FieldType::VARCHAR, TRUE)
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
     * Gets an instance of this object by index "i_comment_page"
     * 
     * @param   int page_id
     * @return  name.kiesel.pxl.db.PxlComment[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPage_id($page_id) {
      return self::getPeer()->doSelect(new Criteria(array('page_id', $page_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "pk_comment"
     * 
     * @param   int comment_id
     * @return  name.kiesel.pxl.db.PxlComment entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByComment_id($comment_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('comment_id', $comment_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves comment_id
     *
     * @return  int
     */
    public function getComment_id() {
      return $this->comment_id;
    }
      
    /**
     * Sets comment_id
     *
     * @param   int comment_id
     * @return  int the previous value
     */
    public function setComment_id($comment_id) {
      return $this->_change('comment_id', $comment_id);
    }

    /**
     * Retrieves comment_type_id
     *
     * @return  int
     */
    public function getComment_type_id() {
      return $this->comment_type_id;
    }
      
    /**
     * Sets comment_type_id
     *
     * @param   int comment_type_id
     * @return  int the previous value
     */
    public function setComment_type_id($comment_type_id) {
      return $this->_change('comment_type_id', $comment_type_id);
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
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }
      
    /**
     * Sets title
     *
     * @param   string title
     * @return  string the previous value
     */
    public function setTitle($title) {
      return $this->_change('title', $title);
    }

    /**
     * Retrieves body
     *
     * @return  string
     */
    public function getBody() {
      return $this->body;
    }
      
    /**
     * Sets body
     *
     * @param   string body
     * @return  string the previous value
     */
    public function setBody($body) {
      return $this->_change('body', $body);
    }

    /**
     * Retrieves url
     *
     * @return  string
     */
    public function getUrl() {
      return $this->url;
    }
      
    /**
     * Sets url
     *
     * @param   string url
     * @return  string the previous value
     */
    public function setUrl($url) {
      return $this->_change('url', $url);
    }

    /**
     * Retrieves commented_at
     *
     * @return  util.Date
     */
    public function getCommented_at() {
      return $this->commented_at;
    }
      
    /**
     * Sets commented_at
     *
     * @param   util.Date commented_at
     * @return  util.Date the previous value
     */
    public function setCommented_at($commented_at) {
      return $this->_change('commented_at', $commented_at);
    }

    /**
     * Retrieves author
     *
     * @return  string
     */
    public function getAuthor() {
      return $this->author;
    }
      
    /**
     * Sets author
     *
     * @param   string author
     * @return  string the previous value
     */
    public function setAuthor($author) {
      return $this->_change('author', $author);
    }

    /**
     * Retrieves email
     *
     * @return  string
     */
    public function getEmail() {
      return $this->email;
    }
      
    /**
     * Sets email
     *
     * @param   string email
     * @return  string the previous value
     */
    public function setEmail($email) {
      return $this->_change('email', $email);
    }
  }
?>