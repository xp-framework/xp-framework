<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.TagletManager');

  /**
   * Base class for all other Doc classes
   *
   * @see      xp://text.doclet.ClassDoc
   * @see      xp://text.doclet.FieldDoc
   * @see      xp://text.doclet.PackageDoc
   * @see      xp://text.doclet.MethodDoc
   * @see      http://java.sun.com/j2se/1.5.0/docs/guide/javadoc/
   * @test     xp://net.xp_framework.unittest.doclet.CommentParserTest
   * @purpose  Base class
   */
  class Doc extends Object {
    public
      $name         = '',
      $rawComment   = '',
      $detail       = NULL,
      $root         = NULL;

    /**
     * Set rootdoc
     *
     * @param   text.doclet.RootDoc root
     */
    public function setRoot($root) {
      $this->root= $root;
    }
    
    /**
     * Returns the non-qualified name of this Doc item
     *
     * @return  string
     */
    public function name() {
      return $this->name;
    }
    
    /**
     * Return the full unprocessed text of the comment.
     *
     * @return  string
     */
    public function getRawCommentText() {
      return $this->rawComment;
    }
    
    /**
     * Helper method which parses the raw doc comment
     *
     * @param   int what
     * @return  array
     */
    protected function parseDetail($what) {
      if (!isset($this->detail)) {
        $tm= TagletManager::getInstance();

        $stripped= preg_replace('/[\r\n][\s\t]+\* ?/', "\n", trim($this->rawComment, "/*\n\r\t "));
        $tagstart= FALSE === ($p= strpos($stripped, "\n@")) ? strlen($stripped)+ 1 : $p;
        
        $this->detail= array(
          'text' => substr($stripped, 0, $tagstart- 1),
          'tags' => array()
        );

        if ($t= strtok(trim(substr($stripped, $tagstart)), '@')) do {
          list($kind, $rest)= explode(' ', trim($t), 2);
          
          if ($tag= $tm->make($this, $kind, trim($rest))) {
            $this->detail['tags'][$kind][]= $tag;
          }
        } while ($t= strtok('@'));
      }
      return $this->detail[$what];
    }
    
    /**
     * Return the text of the comment for this doc item.
     *
     * @return  string
     */
    public function commentText() {
      return $this->parseDetail('text');
    }
    
    /**
     * Return tags. If the parameter "kind" is non-null, will return
     * tags only of the specified kind, otherwise all.
     *
     * @param   string kind default NULL kind of tags, e.g. "param"
     * @return  text.doclet.Tag[]
     */
    public function tags($kind= NULL) {
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
    
    /**
     * Returns whether another object is equal to this object
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->name === $cmp->name;
    }
  }
?>
