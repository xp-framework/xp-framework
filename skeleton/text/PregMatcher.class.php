<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
   
  uses(
    'util.text.SyntaxCheck'
  );
  
  /**
   * Regular expression matcher base class, will need to be extended, e.g.:
   * <pre>
   *   class EMailChecker extends PregMatcher {
   *     var $_expressions= array(
   *       'localpart' => '#^[^@]$#',
   *       'domain'    => '#^[a-z.]+\.[a-z]{2,8}$#'
   *     );
   *
   *     var $localpart, $domain;
   *   }
   * </pre>
   * 
   * @see  php://preg
   */
  class PregMatcher extends SyntaxCheck {
    var 
      $_expressions= array(),
      $_matches= array();
    
    /**
     * Validation
     *
     * @access  public
     * @return  bool True when all of the values listed as arraykeys in _expressions match
     */
    function validate() {
      $result= TRUE;
      foreach ($this->_expressions as $key=> $re) {
        $this->$key= trim(chop($this->$key)); 
        $match= preg_match($re, $this->$key);
        $this->_matches[$key]= $match;
        if (!$match) {
          $this->addError($key, 'match');
          $result= FALSE;
        }
      }
      return $result;
    }

  }
?>
