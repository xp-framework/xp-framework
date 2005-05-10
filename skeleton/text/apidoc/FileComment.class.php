<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.apidoc.Comment');
  
  /**
   * Class wrapping file comments
   *
   * @deprecated
   * @purpose  Comment
   */
  class FileComment extends Comment {
  
    /**
     * Sets CVS version
     *
     * @access  public
     * @param   string ver Version
     */
    function setCVSVersion($ver) {
      $this->cvsver= $ver;
    }

    /**
     * Handles tags
     *
     * @access  protected
     * @param   string tag
     * @param   string line
     * @return  &mixed
     * @see     xp://text.apidoc.Comment
     */
    function _handleTag($tag, $line) {
      $descr= parent::_handleTag($tag, $line);
      
      if ('$Id' == substr($line, 0, 3)) {
        $this->setCVSVersion($line);
        $descr= NULL;
      }
      
      return $descr;
    }
  }
?>
