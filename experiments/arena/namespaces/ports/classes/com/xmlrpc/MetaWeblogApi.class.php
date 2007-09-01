<?php
/* This class is part of the XP framework
 *
 * $Id: MetaWeblogApi.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::xmlrpc;

  ::uses(
    'webservices.xmlrpc.XmlRpcClient',
    'webservices.xmlrpc.transport.XmlRpcHttpTransport',
    'io.File',
    'io.FileUtil',
    'util.MimeType',
    'text.encode.Base64'
  );

  /**
   * MetaWeblog API
   *
   * Example:
   * <code>
   *   $m= &new MetaWeblogApi('url', 'username', 'password');
   *   try(); {
   *     $links= &$m->getRecentPosts(5); // Get the 5 Recent posts
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   var_dump($links);
   * </code>
   *
   * @purpose Provide an API to Blogs with the MetaWeblog API
   * @see http://www.xmlrpc.com/metaWeblogApi
   */
  class MetaWeblogApi extends webservices::xmlrpc::XmlRpcClient {
    public
      $url      = '',
      $username = '',
      $password = '',
      $blogid   = '';

    /**
     * Constructor.
     *
     * Note: blogid is only needed if the blog is hosted by blogger.com, 
     * in which case it is the unique blog id
     *
     * @param   string url for the weblog you want to access
     * @param   string username for the weblog
     * @param   string password for the weblog
     * @param   int blogid of the weblog
     */
    public function __construct($url, $username, $password, $blogid= '0') {
      parent::__construct(new ($url));
      $this->username= $username;
      $this->password= $password;
      $this->blogid= $blogid;
    }

    /**
     * Add a new post to the Blog
     *
     * @param   string title of the post
     * @param   string link
     * @param   string description it is the body of the post
     * @param   bool publish if set false, post will be saved as draft, true is publish it
     * @return  string the postid of the new post
     */  
    public function newPost($title= '', $link= '', $description= '', $publish= FALSE) {
      return $this->invoke(
        'metaWeblog.newPost',
        $this->blogid,
        $this->username,
        $this->password,
        array(
          'title'       => $title,
          'link'        => $link,
          'description' => $description
        ),
        $publish == TRUE ? 1 : 0
      );    
    }

    /**
     * Edit a post
     *
     * @param   int postid the id of the post you want to edit
     * @param   string title of the post
     * @param   string link
     * @param   string description it is the body of the post
     * @param   bool publish if set false, post will be saved as draft, true is publish it
     * @return  bool true when post is successfull
     */  
    public function editPost($postid, $title= '', $link= '', $description= '', $publish= FALSE) {
      $response= $this->invoke(
        'metaWeblog.editPost',
        $postid,
        $this->username,
        $this->password,
        array(
          'title'       => $title,
          'link'        => $link,
          'description' => $description
        ),
        (int)$publish
      );
      return $response[0];
    }

    /**
     * Get a post
     *
     * @param   int postid
     * @return  array if posting
     */  
    public function getPost($postid) {
      return $this->invoke(
        'metaWeblog.getPost',
        $postid,
        $this->username,
        $this->password
      );
    }

    /**
     * Add a file to the Blog
     *
     * @param   &io.File file
     * @return  array url of the file
     */  
    public function newMediaObject($file) {
      return $this->invoke(
        'metaWeblog.newMediaObject',
        $this->blogid,
        $this->username,
        $this->password,
        array(
          'name' => $file->getFileName(),
          'type' => util::MimeType::getByFilename($file->getFileName()),
          'bits' => text::encode::Base64::encode(io::FileUtil::getContents($file))
        )
      );
    }

    /**
     * Get all categories of the blog
     *
     * @return  array categories
     */  
    public function getCategories() {
      return $this->invoke(
        'metaWeblog.getCategories',
        $this->blogid,
        $this->username,
        $this->password
      );
    }

    /**
     * Get recent post of the blog
     *
     * @param   int number of posts to get
     * @return  array categories
     */  
    public function getRecentPosts($numberofposts) {
      return $this->invoke(
        'metaWeblog.getRecentPosts',
        $this->blogid,
        $this->username,
        $this->password,
        $numberofposts
      );
    }
  }
?>
