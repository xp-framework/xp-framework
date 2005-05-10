<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'text.apidoc.parser.GenericParser',
    'text.apidoc.CommentFactory'
  );
  
  /**
   * Implementation of GenericParser for classes from within
   * the XP framework
   *
   * @deprecated
   * @see      xp://text.apidoc.parser.GenericParser
   * @purpose  Parses classes
   */
  class ClassParser extends GenericParser {
    var 
      $config   = 'class',
      $comments = array(),
      $defines  = array();
    
    /**
     * Parse
     *
     * @access  public
     * @param   util.log.LogCategory CAT default NULL a log category to print debug to
     * @return  array an associative array containing comments and defines
     */
    function parse($cat= NULL) {
      $this->comments= array(
        APIDOC_COMMENT_FILE     => array(),
        APIDOC_COMMENT_CLASS    => array(),
        APIDOC_COMMENT_FUNCTION => array()
      );
      $this->defines= array();
      
      if (FALSE === parent::parse($cat)) return FALSE;
      return array(
        'comments' => $this->comments,
        'defines'  => $this->defines
      );
    }
    
    /**
     * Callback function for defines
     *
     * @access  protected
     * @param   string const
     * @param   string val
     */
    function setDefine($const, $val) {
      $this->defines[substr($const, 1, -1)]= $val;
    }

    /**
     * Callback function for the "file comment" (this is the comment at
     * the top of the file)
     *
     * @access  protected
     * @param   string str the comment's content
     */
    function setFileComment($str) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_FILE);
      $comment->fromString($str);
      $this->comments[APIDOC_COMMENT_FILE]= &$comment;
    }
    
    /**
     * Callback function for the "class comment" (this is the comment
     * right above the class declaration)
     *
     * @access  protected
     * @param   string class the class' name
     * @param   string extends what this class extends
     * @param   string str the comment's content
     */
    function setClassComment($class, $extends, $str) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_CLASS);
      $comment->fromString($str);
      $comment->setClassName($class);
      $comment->setExtends($extends);
      $this->comments[APIDOC_COMMENT_CLASS]= &$comment;
    }
    
    /**
     * Callback function for "function comments" (these are the comments
     * above a function declaration)
     *
     * @access  protected
     * @param   string function the function's name
     * @param   string str the comment's content
     * @param   bool returnsReference default FALSE TRUE when this function returns its value by reference
     */
    function setFunctionComment($function, $str, $returnsReference= FALSE) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_FUNCTION);
      $comment->fromString($str);
      $comment->return->reference= $returnsReference;
      $this->comments[APIDOC_COMMENT_FUNCTION][$function]= &$comment;
    }
  }
?>
