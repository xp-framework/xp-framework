<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'peer.Socket',
    'peer.URL',
    'peer.news.NntpReply',
    'peer.news.Newsgroup',
    'peer.news.Article',
    'util.Date'
  );
  
  /**
   * NNTP Connection
   *
   * @see      rfc://977
   * @purpose  Wrap
   */
  class NntpConnection extends Object {
    var
      $url      = NULL,
      $response = array();

    /**
     * Constructor
     *
     * @access  private
     * @param   &peer.URL
     */
    function __construct(&$url) {
      $this->url= &$url;
      $this->_sock= &new Socket(
        $this->url->getHost(),
        !$this->url->getPort() ? 119 : $this->url->getPort()
      );
    }
    
    /**
     * Connect
     *
     * @access  public  
     * @param   float timeout default 2.0
     * @return  bool success
     * @throws  IOException in case there's an error during connecting
     */
    function connect($auth= FALSE) {
      try(); {
        $this->_sock->connect();
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      // Read banner message
      if (!($response= $this->_readResponse())) return FALSE;
      $this->cat && $this->cat->debug('<<<', $this->getResponse());
      
      if ($auth) return $this->authenticate();

      return TRUE;
    }

    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     * @throws  IOException in case there's an error during disconnecting
     */
    function close() {
      $status= $this->_sendcmd('QUIT');
      if (!NntpReply::isPositiveCompletion($status)) {
        return throw(new IOException('Error during disconnect:'.$this->getResponse())); 
      }
      return TRUE;
    }

    /**
     * Wrapper that sends a command to the remote host.
     *
     * @access  protected  
     * @param   string format
     * @param   mixed* args
     * @return  bool success
     * @throws  IOException in case the command is too long
     */
    function _sendcmd() {
      $a= func_get_args();
      $cmd= implode(' ', $a);

      // NNTP/RFC977 only allows command up to 512 (-2) chars.
      if (!strlen($cmd) > 510) {
        return throw(new IOException('Command too long! Max. 510 chars'));
      }
      
      $this->cat && $this->cat->debug('>>>', $cmd);
      if ($this->_sock->write($cmd."\r\n")) return $this->_readResponse();
      
      return FALSE;
    }

    /**
     * Get status response
     *
     * @access  private
     * @return  string status
     */
    function _readResponse() {
      if (!($line= $this->_sock->readLine())) return FALSE;
      $this->cat && $this->cat->debug('<<<', $line);
      
      $this->response= array(
        (int) substr($line, 0, 3),
        (string) rtrim(substr($line, 4))
      );
      return $this->response[0];
    }

    /**
     * Get data
     *
     * @access  private
     * @return  string status
     */
    function _readData() {
      if ($this->_sock->eof()) return FALSE;

      $line= $this->_sock->readLine();
      $this->cat && $this->cat->debug('<<<', $line);

      if ('.' == $line) return FALSE;
      return $line;
    }


    /**
     * Return current response
     *
     * @access  public
     * @return  string response
     */
    function getResponse() {
      return $this->response[1];
    }

    /**
     * Return current statuscode
     *
     * @access  public
     * @return  int statuscode
     */
    function getStatus() {
      return $this->response[0];
    }

    /**
     * Authenticate
     *
     * @access  public
     * @param   string authmode
     * @return  bool success
     */  
    function authenticate() {
      $status= $this->_sendcmd('AUTHINFO user', $this->url->getUser());
      
      // Send password if requested
      if (NNTP_AUTH_NEEDMODE === $status) {
        $status= $this->_sendcmd('AUTHINFO pass', $this->url->getPassword());
      }
      
      switch ($status) {
        case NNTP_AUTH_ACCEPT: {
          return TRUE;
          break;
        }
        case NNTP_AUTH_NEEDMODE: {
          return throw(new IOException('Authentication uncomplete'));
          break;
        }
        case NNTP_AUTH_REJECTED: {
          return throw(new IOException('Authentication rejected'));
          break;
        }
        case NNTP_NOPERM: {
          return throw(new IOException('No permission'));
          break;
        }
        default: {
          return throw(new IOException('Unexpected authentication error'));
        }
      }
    }

    /**
     * Get group names
     *
     * @access public
     * @return  array groups
     */
    function getGroupList() {
      $status= $this->_sendcmd('LIST');
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get list of groups'));

      while ( $line= $this->_readData()) $groups[]= current(explode(' ', $line));

      return $groups;
    }
    
    /**
     * Select a group
     *
     * @access  public
     * @param   string groupname
     * @return  &peer.news.Newsgroup
     */
    function &setGroup($group) {
      $status= $this->_sendcmd('GROUP', $group);
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not select group'));

      $buf= explode(' ', $this->getResponse());
      return new Newsgroup($buf[3], $buf[2], $buf[1]);
    }
    
    /**
     * Get groups
     *
     * @access  public
     * @return  array &peer.news.Newsgroup
     */
    function &getGroups() {
      $status= $this->_sendcmd('LIST');
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get groups'));

      while ($line= $this->_readData()) {
        $buf= explode(' ', $line);
        $groups[]= &new Newsgroup($buf[0], $buf[1], $buf[2]);
      }

      return $groups;
    }

    /**
     * Get Article
     *
     * @access  public
     * @param   mixed Id eighter a messageId or an articleId
     * @return  &peer.news.Article
     * @throws  IOException in case article could not be retrieved
     */
    function getArticle($id) {
      $status= $this->_sendcmd('ARTICLE', $id);
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article'));

      $article= &new Article(current(explode(' ', $this->getResponse())));
      
      // retrieve headers
      while ($line= $this->_readData()) {
        $header= explode(': ', $line, 2);
        $article->setHeader($header[0], $header[1]);
      }
      
      // retrieve body
      while (FALSE !== ($line= $this->_readData())) $body.= $line."\n";
      $article->setBody($body);
      
      return $article;
    }

    /**
     * Get a list of all articles in a newsgroup
     *
     * @access  public
     * @return  array messageId
     * @throws  IOException in case article list could not be retrieved
     */
    function getArticleList() {
      $status= $this->_sendcmd('LISTGROUP');
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article list'));
      
      while ($line= $this->_readData()) $articles[]= $line;
      
      return $articles;
    }
    
    /**
     * Retrieve body of an article
     *
     * @access  public  
     * @param   &peer.news.Article
     * @return  &peer.news.Article
     * @throws  IOException in case body could not be retrieved
     */
    function &getBody(&$article) {
      $status= $this->_sendcmd('BODY', $article->getMessageId());
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article body'));

      // retrieve body
      while (FALSE !== ($line= $this->_readData())) $body.= $line."\n";
      $article->setBody($body);

      return $article;
    }

    /**
     * Retrieve header of an article
     *
     * @access  public  
     * @param   &peer.news.Article
     * @return  &peer.news.Article
     * @throws  IOException in case headers could not be retrieved
     */
    function &getHeader(&$article) {
      $status= $this->_sendcmd('HEAD', $article->getMessageId());
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article headers'));

      // retrieve headers
      while ($line= $this->_readData()) {
        $header= explode(': ', $line, 2);
        $article->setHeader($header[0], $header[1]);
      }
      
      return $article;
    }
    
    /**
     * Retrieve   Article
     *
     * @access  public
     * @param   mixed Id eighter a messageId or an articleId default NULL
     * @return  &peer.news.Article
     * @throws  IOException in case article could not be retrieved
     */
    function &stat($id= NULL) {
      $status= $this->_sendcmd('STAT', $id);
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get stat article'));

      return new Article(current(explode(' ', $this->getResponse())));
    }    

    /**
     * Retrieve next article
     *
     * @access  public
     * @return  &peer.news.Article
     * @throws  IOException in case article could not be retrieved
     */
    function &next() {
      $status= $this->_sendcmd('NEXT');
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get next article'));

      return new Article(current(explode(' ', $this->getResponse())));
    }

    /**
     * Retrieve last article
     *
     * @access  public
     * @return  &peer.news.Article
     * @throws  IOException in case article could not be retrieved
     */
    function &last() {
      $status= $this->_sendcmd('LAST');
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get last article'));

      return new Article(current(explode(' ', $this->getResponse())));
    }
    
    /**
     * Get format of xover command
     *
     * @access  public
     * @return  array fields
     * @throws  IOException in case format could not be retrieved
     */    
    function getOverviewFormat() {
      $status= $this->_sendcmd('LIST OVERVIEW.FMT');
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get overview'));
        
      while ($line= $this->_readData()) {
        $fields[]= current(explode(':', $line, 2));

      }
      return $fields;
    }

    /**
     * Get a list of articles in a given range
     *
     * @access  public
     * @param   string range
     * @return  &peer.news.Article
     */
    function &getOverview($range= NULL) {
      try(); {
        $fields= $this->getOverviewFormat();
      } if (catch('IOException', $e)) {
        return throw($e);
      }

      $status= $this->_sendcmd('XOVER', $range);
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get overview'));

      while ($line= $this->_readData()) {
        $args= explode("\t", $line, 9);
        
        $article= &new Article($args[0]);
        foreach ($fields as $key => $value) {
          $article->setHeader($value, $args[++$key]);
        }
        
        $articles[]= &$article;
      }

      return $articles;
    }
    
    /**
     * Get all articles which are newer
     * than the given date
     *
     * @access  public
     * @param   &utilDate
     * @param   string newsgroup
     * @return  array messageId
     */ 
    function newNews(&$date, $newsgroup) {
      $status= $this->_sendcmd(
        'NEWNEWS',
        $newsgroup,
        $date->format('%y%m%d %H%M%S')
      );
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get overview'));
        
      while ($line= $this->_readData()) $articles[]= $line;
      
      return $articles;
    }

    /**
     * Get all groups which are newer
     * than the given date
     *
     * @access  public
     * @param   &util.Date
     * @return  array &peer.news.Newsgroup
     */
    function newGroups(&$date) {
      $status= $this->_sendcmd(
        'NEWGROUPS',
        $date->format('%y%m%d %H%M%S')
      );
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get new groups'));
        
      while ($line= $this->_readData()) {
        $buf= explode(' ', $line);
        $groups[]= &new Newsgroup($buf[0], $buf[1], $buf[2]);
      }

      return $groups;
    }
  }
?>
