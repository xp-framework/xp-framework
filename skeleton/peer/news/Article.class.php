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
    var
      $messageId        = 0,
      $articleId        = '',
      $header           = array(),
      $body             = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   int messageId
     * @param   string articleId
     */
    function __construct($messageId, $articleId) {
      $this->messageId= $messageId;
      $this->articleId= $articleId;
    }

    /**
     * Set MessageId
     *
     * @access  public
     * @param   int messageId
     */
    function setMessageId($messageId) {
      $this->messageId= $messageId;
    }

    /**
     * Get MessageId
     *
     * @access  public
     * @return  int
     */
    function getMessageId() {
      return $this->messageId;
    }

    /**
     * Set ArticleId
     *
     * @access  public
     * @param   string articleId
     */
    function setArticleId($articleId) {
      $this->articleId= $articleId;
    }

    /**
     * Get ArticleId
     *
     * @access  public
     * @return  string
     */
    function getArticleId() {
      return $this->articleId;
    }

    /**
     * Set Header
     *
     * @access  public
     * @param   mixed[] header
     */
    function setHeader($name, $value) {
      $this->header[$name]= $value;
    }

    /**
     * Get Headers
     *
     * @access  public
     * @return  mixed[]
     */
    function getHeaders() {
      return $this->header;
    }

    /**
     * Get Headers
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    function getHeader($name) {
      return $this->header[$name];
    }

    /**
     * Set Body
     *
     * @access  public
     * @param   string body
     */
    function setBody($body) {
      $this->body= $body;
    }

    /**
     * Get Body
     *
     * @access  public
     * @return  string
     */
    function getBody() {
      return $this->body;
    }

  }
?>
