<?php
/* This class is part of the XP framework
 *
 * $Id: DeliciousClient.class.php 8975 2006-12-27 18:06:40Z friebe $ 
 */

  namespace us::icio::del;
 
  define('API_URL',     'https://api.del.icio.us');
  define('API_VERSION', 'v1');

  ::uses(
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
  class DeliciousClient extends lang::Object {
    public
      $username= '',
      $password= '';

    /**
     * Constructor
     *
     * @param   string username from del.icio.us
     * @param   string password from del.icio.us
     */
    public function __construct(
      $username,
      $password
    ) {
      $this->username= $username;
      $this->password= $password;
    }

    /**
     * Do del.icio.us-Request
     *
     * @param   string url of del.icio.us-request
     * @param   array param of del.icio.us-request
     * @return  string
     */
    protected function _doRequest($url, $param= array()) {
      try {
         $buf= peer::http::HttpUtil::get(
           new peer::http::HttpConnection($url),
           $param,
           array(new peer::http::BasicAuthorization($this->username, $this->password))
         );
       } catch ( $e) {
         throw($e);
         exit(-1);
       }
       return $buf;    
    }

    /**
     * Returns a list of dates with the number of posts at each date 
     *
     * @param   string tag filter by this tag
     * @return  string
     */  
    public function getDatesByTag($tag) {
      return $this->_doRequest(
        sprintf('%s/%s/posts/dates?', API_URL, API_VERSION),
        array(
          'tag' => $tag
        )
      );
    }

    /**
     * Returns a list of tags the user has used
     *
     * @return  string
     */  
    public function getTags() {
      return $this->_doRequest(
        sprintf('%s/%s/tags/get?', API_URL, API_VERSION)
      );
    }
    
    /**
     * Returns a list of posts on a given date, filtered by tag 
     *
     * @param   string tag filter by this tag
     * @param   &util.Date filter by this date - defaults to the most recent date
     * @return  string 
     */
    public function getPostsByTag($tag, $date) {
      return $this->_doRequest(
        sprintf('%s/%s/posts/get?', API_URL, API_VERSION),
        array(
          'tag' => $tag,
          'dt'  => is_object($date) ? $date->format('%Y-%m-%d') : ''
        )
      );
    }
    
    /**
     * Returns a list of most recent posts
     *
     * @param   string tag filter by this tag
     * @param   string count - defaults to 15, max is 100
     * @return  string
     */
    public function getRecentPosts($tag, $count) {
      return $this->_doRequest(
        sprintf('%s/%s/posts/recent?', API_URL, API_VERSION),
        array(
          'tag'   => $tag,
          'count' => $count
        )
      );
    }

    /**
     * Returns all posts
     *
     * @param   string tag filter by this tag
     * @return  string
     */
    public function getAllPosts($tag) {
      return $this->_doRequest(
        sprintf('%s/%s/posts/all?', API_URL, API_VERSION),
        array(
          'tag' => $tag
        )
      );
    }
    
    /**
     * Returns the last update time for the user
     *
     * @return  string
     */
    public function getLastUpdateTime() {
      return $this->_doRequest(
        sprintf('%s/%s/posts/update?', API_URL, API_VERSION)
      );
    }

    /**
     * Makes a post to delicious
     *
     * @param   string url url for post
     * @param   string description description for post
     * @param   string extended extended for post
     * @param   string tags space-delimited list of tags
     * @param   &util.Date dt datestamp for post, format "CCYY-MM-DDThh:mm:ssZ"     
     * @return  string
     */
    public function addPost($url, $description, $extended, $tags, $dt) {
      return $this->_doRequest(
        sprintf('%s/%s/posts/add?', API_URL, API_VERSION),
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
     * @param   string url url for post
     * @return  string
     */
    public function deletePost($url) {
      return $this->_doRequest(
        sprintf('%s/%s/posts/delete?', API_URL, API_VERSION),
        array(
          'url' => $url
        )
      );
    }

    /**
     * Renames a tag
     *
     * @param   string old old tag
     * @param   string new new tag
     * @return  string
     */
    public function renameTag($old, $new) {
      return $this->_doRequest(
        sprintf('%s/%s/tags/rename?', API_URL, API_VERSION),
        array(
          'old' => $old,
          'new' => $new
        )
      );
    }

    /**
     * Retrieves all bundles
     *
     * @return  string
     */
    public function getAllBundles() {
      return $this->_doRequest(
        sprintf('%s/%s/tags/bundles/all?', API_URL, API_VERSION)
      );
    }

    /**
     * Assigns a set of tags to a single bundle, wipes away previous settings for bundle
     *
     * @param   string bundle bundle name
     * @param   string tags space-separated list of tags
     * @return  string
     */
    public function setBundle($bundle, $tags) {
      return $this->_doRequest(
        sprintf('%s/%s/tags/bundles/set?', API_URL, API_VERSION),
        array(
          'bundle' => $bundle,
          'tags'   => $tags
        )
      );
    }

    /**
     * Deletes a bundle
     *
     * @param   string bundle bundle name
     * @return  string
     */
    public function deleteBundle($bundle) {
      return $this->_doRequest(
        sprintf('%s/%s/tags/bundles/delete?', API_URL, API_VERSION),
        array(
          'bundle' => $bundle
        )
      );
    }    
  }
?>
