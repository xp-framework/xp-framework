<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent an Article
   *
   * @purpose  Base class
   */
  class Article extends Object {
    public
      $articleId        = NULL,
      $messageId        = NULL,
      $header           = array(),
      $body             = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   int articleId
     */
    public function __construct($articleId, $messageId) {
      $this->articleId= $articleId;
      $this->messageId= $messageId;
    }

    /**
     * Set ArticleId
     *
     * @access  public
     * @param   string articleId
     */
    public function setArticleId($articleId) {
      $this->articleId= $articleId;
    }

    /**
     * Get ArticleId
     *
     * @access  public
     * @return  string
     */
    public function getArticleId() {
      return $this->articleId;
    }

    /**
     * Set MessageId
     *
     * @access  public
     * @param   int messageId
     */
    public function setMessageId($messageId) {
      $this->messageId= $messageId;
    }

    /**
     * Get MessageId
     *
     * @access  public
     * @return  int
     */
    public function getMessageId() {
      return $this->messageId;
    }

    /**
     * Set Header
     *
     * @access  public
     * @param   mixed[] header
     */
    public function setHeader($name, $value) {
      $this->header[$name]= $value;
    }

    /**
     * Get Headers
     *
     * @access  public
     * @return  mixed[]
     */
    public function getHeaders() {
      return $this->header;
    }

    /**
     * Get Headers
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    public function getHeader($name) {
      return $this->header[$name];
    }

    /**
     * Set Body
     *
     * @access  public
     * @param   string body
     */
    public function setBody($body) {
      $this->body= $body;
    }

    /**
     * Get Body
     *
     * @access  public
     * @return  string
     */
    public function getBody() {
      return $this->body;
    }
    
    
    /**
     * Retrieve a string representation of this object
     *
     * @access  public
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
