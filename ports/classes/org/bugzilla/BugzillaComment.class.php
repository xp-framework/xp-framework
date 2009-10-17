<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'org.bugzilla.db.BugzillaLongDescs',
    'util.Date'
  );

  /**
   * Bugzilla comment accessor class
   *
   * @purpose  Simplify handling with bugzilla comments
   */
  class BugzillaComment extends Object {
    public
      $bug_id=      NULL,
      $user_id=     NULL,
      $comment=     NULL;
   

    /**
     * Constructor
     *
     * @param   int bug_id,     The ID of the bug related to this comment
     * @param   int user_id,    The bugzilla user id
     * @param   string comment, The comment
     */
    public function __construct($bug_id= NULL, $user_id= NULL, $comment= NULL) {
      $this->bug_id= $bug_id;
      $this->user_id= $user_id;
      $this->comment= $comment;
    }    
    
    /**
     * Set Bug_id
     *
     * @param   int bug_id
     */
    public function setBug_id($bug_id) {
      $this->bug_id= $bug_id;
    }

    /**
     * Get Bug_id
     *
     * @return  int bug_id
     */
    public function getBug_id() {
      return $this->bug_id;
    }

    /**
     * Set User_id
     *
     * @param   int user_id
     */
    public function setUser_id($user_id) {
      $this->user_id= $user_id;
    }

    /**
     * Get User_id
     *
     * @return  int user_id
     */
    public function getUser_id() {
      return $this->user_id;
    }

    /**
     * Set Comment
     *
     * @param   string comment
     */
    public function setComment($comment) {
      $this->comment= $comment;
    }

    /**
     * Get Comment
     *
     * @return  string
     */
    public function getComment() {
      return $this->comment;
    }
    
    /**
     * Add a comment
     *
     * @throws  lang.IllegalArgumentException
     * @throws  rdbms.SQLException
     * @return  bool
     */
    public function add() {

      // Check if all needed params are given
      if (
        empty($this->bug_id) or
        empty($this->user_id) or
        empty($this->comment)
      ) throw(new IllegalArgumentException('Too few arguments given'));

      with ($desc= new BugzillaLongDescs()); {
        $desc->setBug_id($this->bug_id);
        $desc->setBug_when(Date::now());
        $desc->setThetext($this->comment);
        $desc->setWho($this->user_id);
      }
      $desc->insert();
        
      return TRUE;
    }
  }
?>
