<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('TagletManager');

  /**
   *
   * @see      http://java.sun.com/j2se/1.5.0/docs/guide/javadoc/
   * @purpose  Base class
   */
  class Doc extends Object {
    var
      $name         = '',
      $rawComment   = '',
      $detail       = NULL,
      $root         = NULL;

    /**
     * Set rootdoc
     *
     * @access  public
     * @param   &RootDoc root
     */
    function setRoot(&$root) {
      $this->root= &$root;
      $this->interfaces->root= &$root;
      $this->usedClasses->root= &$root;    
    }
    
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
        $tm= &TagletManager::getInstance();

        $stripped= preg_replace('/[\r\n\s\t]+\* ?/', "\n", trim($this->rawComment, "/*\n\r\t "));
        $tagstart= FALSE === ($p= strpos($stripped, "\n@")) ? strlen($stripped)+ 1 : $p;
        
        $this->detail= array(
          'text' => substr($stripped, 0, $tagstart- 1),
          'tags' => array()
        );

        if ($t= strtok(trim(substr($stripped, $tagstart)), '@')) do {
          list($kind, $rest)= explode(' ', $t, 2);
          
          if ($tag= &$tm->make($this, $kind, trim($rest))) {
            $this->detail['tags'][$kind][]= &$tag;
          }
        } while ($t= strtok('@'));
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
      return $this->parseDetail('text');
    }
    
    /**
     * Return tags. If the parameter "kind" is non-null, will return
     * tags only of the specified kind, otherwise all.
     *
     * @access  public
     * @param   string kind default NULL kind of tags, e.g. "param"
     * @return  Tag[]
     */
    function tags($kind= NULL) {
      $tags= $this->parseDetail('tags');
      if ($kind) {
        return isset($tags[$kind]) ? $tags[$kind] : array();
      }
      
      // List all tags
      $return= array();
      foreach (array_keys($tags) as $kind) {
        $return= array_merge($return, $tags[$kind]);
      }  
      return $return;
    }
  }
?>
