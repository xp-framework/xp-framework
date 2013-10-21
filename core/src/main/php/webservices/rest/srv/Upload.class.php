<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.FileInputStream');

  /**
   * Represents a single file upload
   */
  class Upload extends Object {
    protected $param;

    /**
     * Creates a new Upload instance
     *
     * @param  [:var] param
     */
    public function __construct(array $param) {
      $this->param= $param;
    }

    public function getName() {
      return $this->param['name'];
    }

    public function getMediaType() {
      return $this->param['type'];
    }

    public function getSize() {
      return $this->param['size'];
    }

    public function getInputStream() {
      return new FileInputStream($this->param['tmp_name']);
    }
  }
?>
