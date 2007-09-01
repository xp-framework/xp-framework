<?php
/* This class is part of the XP framework
 *
 * $Id: Article.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::news;

  /**
   * Represent an Article
   *
   * @purpose  Base class
   */
  class Article extends lang::Object {
    public
      $articleId        = NULL,
      $messageId        = NULL,
      $header           = array(),
      $body             = '';

    /**
     * Constructor
     *
     * @param   int articleId
     */
    public function __construct($articleId, $messageId) {
      $this->articleId= $articleId;
      $this->messageId= $messageId;
    }

    /**
     * Set ArticleId
     *
     * @param   string articleId
     */
    public function setArticleId($articleId) {
      $this->articleId= $articleId;
    }

    /**
     * Get ArticleId
     *
     * @return  string
     */
    public function getArticleId() {
      return $this->articleId;
    }

    /**
     * Set MessageId
     *
     * @param   int messageId
     */
    public function setMessageId($messageId) {
      $this->messageId= $messageId;
    }

    /**
     * Get MessageId
     *
     * @return  int
     */
    public function getMessageId() {
      return $this->messageId;
    }

    /**
     * Set Header
     *
     * @param   mixed[] header
     */
    public function setHeader($name, $value) {
      $this->header[$name]= $value;
    }

    /**
     * Get Headers
     *
     * @return  mixed[]
     */
    public function getHeaders() {
      return $this->header;
    }

    /**
     * Get Headers
     *
     * @param   string name
     * @return  string value
     */
    public function getHeader($name) {
      return $this->header[$name];
    }

    /**
     * Set Body
     *
     * @param   string body
     */
    public function setBody($body) {
      $this->body= $body;
    }

    /**
     * Get Body
     *
     * @return  string
     */
    public function getBody() {
      return $this->body;
    }
    
    
    /**
     * Retrieve a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= sprintf("%s %s {\n", $this->getClassName(), $this->getMessageId());
      foreach ($this->header as $name => $attr) {
        $s.= sprintf("  [%-26s] %s\n", $name, $attr);
      }
      $s.= sprintf("\n  %s\n", str_replace("\n", "\n  ", $this->getBody()));

      return $s."}\n";
    }

  }
?>
