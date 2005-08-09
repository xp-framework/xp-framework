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
   * del.icio.us API
   *
   * Example:
   * <code>
   *   require('lang.base.php');
   *   uses('us.icio.del.DeliciousClient');
   *
   *   $d= &new DeliciousClient('username', 'password');
   *   try(); {
   *     $posts= $d->getAllPosts('Flash');
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   var_dump($posts);
   * </code>
   *
   * @purpose Provide an API to del.icio.us
   * @see http://del.icio.us/doc/api
   */
  class DeliciousClient extends Object {
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
     * Do del.icio.us-Request
     *
     * @access  private
     * @param   string url of del.icio.us-request
     * @param   array param of del.icio.us-request
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
     * Returns a list of dates with the number of posts at each date 
     *
     * @access  public
     * @param   string tag filter by this tag
     * @return  string
     */  
    function getDatesByTag($tag) {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/dates?',
        array(
          'tag' => $tag
        )
      );
    }

    /**
     * Returns a list of tags the user has used
     *
     * @access  public
     * @return  string
     */  
    function getTags() {
      return $this->_doRequest(
        'http://del.icio.us/api/tags/get?'
      );
    }
    
    /**
     * Returns a list of posts on a given date, filtered by tag 
     *
     * @access  public
     * @param   string tag filter by this tag
     * @param   &util.Date filter by this date - defaults to the most recent date
     * @return  string 
     */
    function getPostsByTag($tag, $date) {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/get?',
        array(
          'tag' => $tag,
          'dt'  => is_object($date) ? $date->format('%Y-%m-%d') : ''
        )
      );
    }
    
    /**
     * Returns a list of most recent posts
     *
     * @access  public
     * @param   string tag filter by this tag
     * @param   string count - defaults to 15, max is 100
     * @return  string
     */
    function getRecentPosts($tag, $count) {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/recent?',
        array(
          'tag'   => $tag,
          'count' => $count
        )
      );
    }

    /**
     * Returns all posts
     *
     * @access  public
     * @param   string tag filter by this tag
     * @return  string
     */
    function getAllPosts($tag) {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/all?',
        array(
          'tag' => $tag
        )
      );
    }
    
    /**
     * Returns the last update time for the user
     *
     * @access  public
     * @return  string
     */
    function getLastUpdateTime() {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/update?'
      );
    }

    /**
     * Makes a post to delicious
     *
     * @access  public
     * @param   string url url for post
     * @param   string description description for post
     * @param   string extended extended for post
     * @param   string tags space-delimited list of tags
     * @param   &util.Date dt datestamp for post, format "CCYY-MM-DDThh:mm:ssZ"     
     * @return  string
     */
    function addPost($url, $description, $extended, $tags, $dt) {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/add?',
        array(
          'url'         => $url,
          'description' => $description,
          'extended'    => $extended,
          'tags'        => $tags,
          'dt'          => is_object($dt) ? $dt->format('%Y-%m-%dT%H:%i:%sZ') : '',
        )
      );
    }

    /**
     * Deletes a post from delicious
     *
     * @access  public
     * @param   string url url for post
     * @return  string
     */
    function deletePost($url) {
      return $this->_doRequest(
        'http://del.icio.us/api/posts/delete?',
        array(
          'url' => $url
        )
      );
    }

    /**
     * Renames a tag
     *
     * @access  public
     * @param   string old old tag
     * @param   string new new tag
     * @return  string
     */
    function renameTag($old, $new) {
      return $this->_doRequest(
        'http://del.icio.us/api/tags/rename?',
        array(
          'old' => $old,
          'new' => $new
        )
      );
    }

    /**
     * Retrieves all bundles
     *
     * @access  public
     * @return  string
     */
    function getAllBundles() {
      return $this->_doRequest(
        'http://del.icio.us/api/tags/bundles/all?'
      );
    }

    /**
     * Assigns a set of tags to a single bundle, wipes away previous settings for bundle
     *
     * @access  public
     * @param   string bundle bundle name
     * @param   string tags space-separated list of tags
     * @return  string
     */
    function setBundle($bundle, $tags) {
      return $this->_doRequest(
        'http://del.icio.us/api/tags/bundles/set?',
        array(
          'bundle' => $bundle,
          'tags'   => $tags
        )
      );
    }

    /**
     * Deletes a bundle
     *
     * @access  public
     * @param   string bundle bundle name
     * @return  string
     */
    function deleteBundle($bundle) {
      return $this->_doRequest(
        'http://del.icio.us/api/tags/bundles/delete?',
        array(
          'bundle' => $bundle
        )
      );
    }    
  }
?>
