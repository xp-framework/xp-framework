<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.apidoc.Comment');

  /**
   * Class wrapping file comments
   *
   * @purpose  Comment
   */
  class FileComment extends Comment {
  
    /**
     * Sets CVS version
     *
     * @access  public
     * @param   string ver Version
     */
    public function setCVSVersion($ver) {
      $this->cvsver= $ver;
    }

    /**
     * Handles tags
     *
     * @access  protected
     * @param   string tag
     * @param   string line
     * @return  &mixed
     * @see     xp://lang.apidoc.Comment
     */
    protected function _handleTag($tag, $line) {
      $descr= parent::_handleTag($tag, $line);
      
      if ('$Id' == substr($line, 0, 3)) {
        self::setCVSVersion($line);
        $descr= NULL;
      }
      
      return $descr;
    }
  }
?>
