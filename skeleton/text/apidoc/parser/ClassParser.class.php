<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.apidoc.parser.GenericParser',
    'lang.apidoc.CommentFactory'
  );
  
  class ClassParser extends GenericParser {
    var $config= 'class';
    
    var 
      $comments = array(),
      $defines  = array();
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct($filename= NULL) {
      parent::__construct($filename);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function parse() {
      $this->comments= array(
        APIDOC_COMMENT_FILE     => array(),
        APIDOC_COMMENT_CLASS    => array(),
        APIDOC_COMMENT_FUNCTION => array()
      );
      $this->defines= array();
      
      if (FALSE === parent::parse()) return FALSE;
      return array(
        'comments' => $this->comments,
        'defines'  => $this->defines
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setDefine($const, $val) {
      $this->defines[substr($const, 1, -1)]= $val;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setFileComment($str) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_FILE);
      $comment->fromString($str);
      $this->comments[APIDOC_COMMENT_FILE]= &$comment;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setClassComment($class, $extends, $str) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_CLASS);
      $comment->fromString($str);
      $comment->setExtends($extends);
      $this->comments[APIDOC_COMMENT_CLASS]= &$comment;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setFunctionComment($function, $str, $returnsReference= FALSE) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_FUNCTION);
      $comment->fromString($str);
      $comment->return->reference= $returnsReference;
      $this->comments[APIDOC_COMMENT_FUNCTION][$function]= &$comment;
    }
  }
?>
