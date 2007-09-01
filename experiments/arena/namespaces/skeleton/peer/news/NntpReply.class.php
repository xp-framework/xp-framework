<?php
/* This class is part of the XP framework
 *
 * $Id: NntpReply.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::news;

  define('NNTP_HELP',                 100);
  define('NNTP_DEBUG',                199);
  define('NNTP_OK',                   200);
  define('NNTP_OK_NOPOSTING',         201);
  define('NNTP_CLOSED',               205);
  define('NNTP_GROUP_SELECTED',       211);
  define('NNTP_GROUP_FOLLOWS',        215);
  define('NNTP_ARTICLE_FOLLOWS',      220);
  define('NNTP_HEAD_FOLLOWS',         221);
  define('NNTP_BODY_FOLLOWS',         222);
  define('NNTP_ARTICLE_RETRIEVED',    223);
  define('NNTP_ARTICLE_LIST',         230);
  define('NNTP_GROUP_LIST',           231);
  define('NNTP_ARTICLE_TRANSFERRED',  235);
  define('NNTP_ARTICLE_POSTOK',       240);
  define('NNTP_AUTH_ACCEPT',          281);
  define('NNTP_ARTICLE_IHAVE',        335);
  define('NNTP_ARTICLE_POST',         340);
  define('NNTP_AUTH_NEEDMORE',        381);
  define('NNTP_NOGROUP',              411);
  define('NNTP_NOARTICLE',            420);
  define('NNTP_NOARTICLE_NEXT',       421);
  define('NNTP_NOARTICLE_LAST',       422);
  define('NNTP_NOARTICLE_NUMBER',     423);
  define('NNTP_ARTICLE_NOTFOUND',     430);
  define('NNTP_IHAVE_NACK',           435);
  define('NTTP_TRANSFER_FAILED',      436);
  define('NNTP_ARTICLE_REJECTED',     437);
  define('NNTP_POST_NACK',            440);
  define('NNTP_POST_FAILED',          441);
  define('NNTP_AUTH_REQUIRED',        480);
  define('NNTP_AUTH_REJECTED',        482);
  define('NNTP_COMMAND_UNKNOWN',      500);
  define('NNTP_SYNTAX_ERROR',         501);
  define('NNTP_PERMISSION_DENIED',    502);
  define('NNTP_PROGRAM_FAULT',        503);


  /**
   * Stores NNTP constants
   *
   * @see      rfc://977
   * @purpose  Base class
   */
  class NntpReply extends lang::Object {
  
    /**
     * Check if a status code is informational.
     * All codes beginning with 1 are 
     * informational responses
     *
     * @param   int statuscode
     * @return  bool
     */
    public function isInformational($status) {
      return (bool) (1 == substr($status,0,1));
    }
  
    /**
     * Check if a status code is a positive 
     * completion.All codes beginning with 2 are 
     * positive completion responses
     *
     * @param   int statuscode
     * @return  bool
     */
    public function isPositiveCompletion($status) {
      return (bool) (2 == substr($status,0,1));
    }

    /**
     * Check if a status code is a positive 
     * intermediate.All codes beginning with 3 are 
     * positive intermediate responses
     *
     * @param   int statuscode
     * @return  bool
     */
    public function isPositiveIntermediate($status) {
      return (bool) (3 == substr($status,0,1));
    }

    /**
     * Check if a status code is a negative 
     * transient.All codes beginning with 4 are 
     * negative transient responses
     *
     * @param   int statuscode
     * @return  bool
     */
    public function isNegativeTransient($status) {
      return (bool) (4 == substr($status,0,1));
    }

    /**
     * Check if a status code is a negative 
     * permanent.All codes beginning with 5 are 
     * negative permanent responses
     *
     * @param   int statuscode
     * @return  bool
     */
    public function isNegativePermanent($status) {
      return (bool) (5 == substr($status,0,1));
    }
  }
?>
