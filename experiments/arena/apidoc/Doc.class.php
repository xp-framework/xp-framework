<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   *
   * @purpose  Base class
   */
  class Doc extends Object {
    var
      $name         = '',
      $rawComment   = '',
      $detail       = NULL;
    
    /**
     * Returns the non-qualified name of this Doc item
     *
     * @access  public
     * @return  string
     */
    function name() {
      return $this->name;
    }
    
    /**
     * Return the full unprocessed text of the comment.
     *
     * @access  public
     * @return  string
     */
    function getRawCommentText() {
      return $this->rawComment;
    }
    
    /**
     * Helper method which parses the raw doc comment
     *
     * @access  protected
     * @param   int what
     * @return  array
     */
    function parseDetail($what) {
      if (!isset($this->detail)) {
        $stripped= preg_replace('/[\r\n\s\t]+\* ?/', "\n", trim($this->rawComment, "/*\n\r\t "));
        $tagstart= FALSE === ($p= strpos($stripped, "\n@")) ? strlen($stripped)+ 1 : $p;
        
        $this->detail= array();
        $this->detail[DETAIL_COMMENT]= substr($stripped, 0, $tagstart- 1);
      }
      return $this->detail[$what];
    }
    
    /**
     * Return the text of the comment for this doc item.
     *
     * @access  public
     * @return  string
     */
    function commentText() {
      return $this->parseDetail(DETAIL_COMMENT);
    }
  }
?>
