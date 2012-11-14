<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.Header'
  );

  /**
   * Represents a Content MD5 header
   * The value of the header will be determined, at the last possible moment
   * Therefore, instead of the value to be send, the content itself is required as parameter
   * Upon this content the md5 is later generated.
   *
   * @purpose
   */
  class AbstractContentHeader extends Header {

    protected
      $content= NULL;

    /**
     * Will set a content for this header
     *
     * @param string content
     */
    public function setContent($content) {
      $this->content= &$content;
    }

    /**
     * Set content fluent interface
     *
     * @param string content
     */
    public function withContent($content) {
      $this->setContent($content);
      return $this;
    }

    /**
     * Will return the set content
     *
     * @return string
     */
    protected function &getContent() {
      return $this->content;
    }

    /**
     * Will check if a content was set
     */
    protected function hasContent() {
      return (NULL !== $this->content);
    }

    /**
     * Will check a header value and generate the the output determined by the given format
     *
     * @param string  format
     * @param [:var]  value should not be an object
     * @return string
     */
    protected function getPart($format, $value= NULL) {
      $part= '';
      if(!empty($value)) {
        $part= sprintf($format, $value);
      }
      return $part;
    }

    /**
     * By default mark content headers as unique
     *
     * @return bool TRUE
     */
    public function isUnique() {
      return TRUE;
    }
  }
?>
