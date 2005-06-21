<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.BasicAuthorization',
    'peer.http.HttpUtil'
  );

  /**
   * Simpy is a social bookmark manager
   *
   * Example:
   * <code>
   *   $s= &new SimpyClient('username', 'password');
   *   try(); {
   *     $links= &$s->getLinks();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   var_dump($links);
   * </code>
   *
   * @purpose Provide an API to Simpy
   * @see http://www.simpy.com/simpy/service/api/rest/
   */
  class SimpyClient extends Object {
    var
      $username= '',
      $password= '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string username from simpy
     * @param   string password from simpy
     */
    function __construct(
      $username,
      $password
    ) {
      $this->username= $username;
      $this->password= $password;
    }

    /**
     * Do Simpy-Request
     *
     * @access  private
     * @param   string url of simpy-request
     * @param   array param of simply-request
     * @return  string
     */
    function _doRequest($url, $param= array()) {
      try(); {
         $buf= HttpUtil::get(
           new HttpConnection($url),
           $param,
           array(new BasicAuthorization($this->username, $this->password))
         );
       } if (catch('UnexpectedResponseException', $e)) {
         return throw($e);
         exit(-1);
       }
       return $buf;    
    }

    /**
     * Get list of users most popular tags and counts
     *
     * @access  public
     * @return  string
     */  
    function getTags() {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetTags.do'
      );
    }

    /**
     * Get list of users links and counts
     *
     * @access  public  
     * @param   string q query-string for notes
     * @return  string
     */  
    function getLinks($q= '') {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetLinks.do', 
        array('q' => $q)
      );
    }

    /**
     * Get list of users topics, meta-data, number of new links since last login
     *
     * @access  public
     * @return  string
     */  
    function getTopics() {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetTopics.do'
      );
    }

    /**
     * Get list of users notes in reverse chronological order by add date or by rank
     *
     * @access  public
     * @param   string q query-string for notes
     * @return  string
     */  
    function getNotes($q= '') {
      return $this->_doRequest(
        'http://www.simpy.com/simpy/api/rest/GetNotes.do', 
        array('q' => $q)
      );
    }
  
  }
?>
