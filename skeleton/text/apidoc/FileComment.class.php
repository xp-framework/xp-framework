<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.apidoc.Comment');
  
  /**
   * Class wrapping file comments
   *
   */
  class FileComment extends Comment {
  
    /**
     * Sets CVS version
     *
     * @access  public
     * @param   string ver Version
     * @return  
     */
    function setCVSVersion($ver) {
      $this->cvsver= $ver;
    }

    /**
     * Handles tags
     *
     * @see lang.apidoc.Comment
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
