<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.Input');

  /**
   * Represents a stream to be input. Uses the "php://input" stream to
   * read the raw POST data, and represents it as a stream.
   *
   * @see   http://php.net/manual/en/wrappers.php.php
   */
  class StreamingInput extends webservices·rest·srv·Input {

    public function getMediaType() {
      $contentType= $this->request->getHeader('Content-Type');
      return substr($contentType, 0, strcspn($contentType, ';'));
    }

    public function getInputStream() {
      return $this->request->getInputStream();
    }
  }
?>
