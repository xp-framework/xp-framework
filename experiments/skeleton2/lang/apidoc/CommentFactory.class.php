<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.apidoc.Comment',
    'lang.apidoc.FileComment',
    'lang.apidoc.ClassComment',
    'lang.apidoc.FunctionComment'
  );

  // Types of comments this class can factor
  define('APIDOC_COMMENT_FILE',        'file');
  define('APIDOC_COMMENT_FUNCTION',    'function');
  define('APIDOC_COMMENT_CLASS',       'class');
  
    
  /**
   * Comment factory
   *
   * Usage example:
   * <code>
   *   $comment= CommentFactory::factory(APIDOC_COMMENT_FUNCTION):
   * </code>
   *
   * @model static
   */
  class CommentFactory extends Object {
  
    /**
     * Creates
     *
     * @access  public
     * @param   const type one of APIDOC_COMMENT_*
     * @return  lang.apidoc.Comment object
     */
    public function factory($type) {
      switch ($type) {
        case APIDOC_COMMENT_FILE:
          $obj= new FileComment();
          break;
          
        case APIDOC_COMMENT_FUNCTION:
          $obj= new FunctionComment();
          break;
          
        case APIDOC_COMMENT_CLASS:
          $obj= new ClassComment();
          break;
          
        default:
          $obj= new Comment();
          break;
      }
      
      return $obj;
    }
  }
?>
