<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * define join select
   *
   */
  class Fetchmode extends Object {

    private
      $path= '',
      $mode= '';

    /**
     * Constructor
     *
     * @param   string
     * @param   string
     */
    public function __construct($path, $mode) {
      $this->mode= $mode;
      $this->path= $path;
    }

    /**
     * Get path
     *
     * @return  string
     */
    public function getPath() {
      return $this->path;
    }

    /**
     * Get mode
     *
     * @return  string
     */
    public function getMode() {
      return $this->mode;
    }

    /**
     * make join for path
     *
     * @param   string
     */
    public static function select($path) {
      return new self($path, 'select');
    }
    
    /**
     * make select for path
     *
     * @param   string
     */
    public static function join($path) {
      return new self($path, 'join');
    }
    
  }
?>
