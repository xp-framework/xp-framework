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
   * Usage [retrieve newsgroup listing]:
   * <code>
   *   $c= &new NntpConnection('nntp://news.xp-framework.net');
   *   try(); {
   *     $c->connect();
   *     $groups= &$c->getGroups();
   *     $c->close();
   *   } if (catch('IOException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   foreach ($groups as $group) {
   *     var_dump($group->getName());
   *   }
   * </code>

   * @see      rfc://977
   * @purpose  News protocol implementation
   */
  class NntpConnection extends Object {
    var
      $url      = NULL,
      $cat      = NULL,
      $response = array();

    /**
     * Constructor
     *
     * @access  private
     * @param   &peer.URL url
     */
    function __construct(&$url) {
      $this->url= &$url;
      $this->_sock= &new Socket(
        $this->url->getHost(),
        $this->url->getPort(119)
      );
    }

    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }
    
    /**
     * Wrapper that sends a command to the remote host.
     *
     * @access  protected  
     * @param   string format
     * @param   mixed* args
     * @return  bool success
     * @throws  ProtocolException in case the command is too long
     */
    function _sendcmd() {
      if (!$this->_sock->isConnected()) return FALSE;

      $a= func_get_args();
      $cmd= implode(' ', $a);

      // NNTP/RFC977 only allows command up to 512 (-2) chars.
      if (strlen($cmd) > 510) {
        return throw(new ProtocolException('Command too long! Max. 510 chars'));
      }
      
      $this->cat && $this->cat->debug('>>>', $cmd);
      try(); {
        $this->_sock->write($cmd."\r\n");
      } if (catch('SocketException', $e)) {
        return FALSE;
      }
      
      // read first line and return
      // nntp statuscode
      return $this->_readResponse();
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
     * Connect
     *
     * @access  public  
     * @param   float timeout default 2.0
     * @return  bool success
     * @throws  ConnectException in case there's an error during connecting
     */
    function connect($auth= FALSE) {
      try(); {
        $this->_sock->connect();
      } if (catch('ConnectException', $e)) {
        return throw($e);
      }
      
      // Read banner message
      if (!($response= $this->_readResponse()))
        return throw(new ConnectException('No valid response from server'));
        
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
      if (!$this->_sock->isConnected()) return TRUE;

      $status= $this->_sendcmd('QUIT');
      if (!NntpReply::isPositiveCompletion($status)) {
        return throw(new IOException('Error during disconnect'));
      }
      $this->_sock->close();
      return TRUE;
    }

    /**
     * Authenticate
     *
     * @access  public
     * @param   string authmode
     * @return  bool success
     * @throws  AuthenticationException in case authentication failed
     */  
    function authenticate() {
      try(); {
        $status= $this->_sendcmd('AUTHINFO user', $this->url->getUser());

        // Send password if requested
        if (NNTP_AUTH_NEEDMODE === $status) {
          $status= $this->_sendcmd('AUTHINFO pass', $this->url->getPassword());
        }
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      switch ($status) {
        case NNTP_AUTH_ACCEPT: {
          return TRUE;
          break;
        }
        case NNTP_AUTH_NEEDMODE: {
          return throw(new AuthenticatorException('Authentication uncomplete'));
          break;
        }
        case NNTP_AUTH_REJECTED: {
          return throw(new AuthenticatorException('Authentication rejected'));
          break;
        }
        case NNTP_NOPERM: {
          return throw(new AuthenticatorException('No permission'));
          break;
        }
        default: {
          return throw(new AuthenticatorException('Unexpected authentication error'));
        }
      }
    }

    /**
     * Select a group
     *
     * @access  public
     * @param   string groupname
     * @return  success
     */
    function setGroup($group) {
      $status= $this->_sendcmd('GROUP', $group);
      if (!NntpReply::isPositiveCompletion($status))
        return throw (new IOException('Could not set group'));

      return TRUE;
    }
    
    /**
     * Get groups
     *
     * @access  public
     * @return  &peer.news.Newsgroup[]
     */
    function &getGroups() {
      $status= $this->_sendcmd('LIST');
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get groups'));

      while ($line= $this->_readData()) {
        $buf= explode(' ', $line);
        $groups[]= &new Newsgroup($buf[0], (int)$buf[1], (int)$buf[2], $buf[3]);
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
    function &getArticle($id= NULL) {
      $status= $this->_sendcmd('ARTICLE', $id);
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article'));
        
      with($args= explode(' ', $this->getResponse())); {
        $article= &new Article($args[0], $args[1]);
      }
      
      // retrieve headers
      while ($line= $this->_readData()) {
        if ("\t" == $line{0} || ' ' == $line{0}) {
          $article->setHeader(
            $header[0], 
            $article->getHeader($header[0])."\n".$line
          );
          continue;
        }
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
     * @return  array articleId
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
     * @param   mixed Id eighter a messageId or an articleId default NULL 
     * @return  string body
     * @throws  IOException in case body could not be retrieved
     */
    function getBody($id= NULL) {
      $status= $this->_sendcmd('BODY', $id);
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article body'));

      // retrieve body
      while (FALSE !== ($line= $this->_readData())) $body.= $line."\n";
      return $body;
    }

    /**
     * Retrieve header of an article
     *
     * @access  public  
     * @param   mixed Id eighter a messageId or an articleId default NULL
     * @return  array headers
     * @throws  IOException in case headers could not be retrieved
     */
    function getHeaders($id= NULL) {
      $status= $this->_sendcmd('HEAD', $id);
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get article headers'));

      // retrieve headers
      while ($line= $this->_readData()) {
        $header= explode(': ', $line, 2);
        $headers[$header[0]]= $header[1];
      }
      
      return $headers;
    }
    
    /**
     * Retrieve next article
     *
     * @access  public
     * @return  &peer.news.Article
     * @throws  IOException in case article could not be retrieved
     */
    function &getNextArticle() {
      $status= $this->_sendcmd('NEXT');
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get next article'));

      return $this->getArticle(current(explode(' ', $this->getResponse())));
    }

    /**
     * Retrieve last article
     *
     * @access  public
     * @return  &peer.news.Article
     * @throws  IOException in case article could not be retrieved
     */
    function &getLastArticle() {
      $status= $this->_sendcmd('LAST');
      if (!NntpReply::isPositiveCompletion($status)) 
        return throw(new IOException('Could not get last article'));

      return $this->getArticle(current(explode(' ', $this->getResponse())));
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
     * @param   string range default NULL
     * @return  &int[] articleId
     */
    function &getOverview($range= NULL) {
      $status= $this->_sendcmd('XOVER', $range);
      if (!NntpReply::isPositiveCompletion($status))
        return throw(new IOException('Could not get overview'));

      while ($line= $this->_readData()) {
        $articles[]= current(explode("\t", $line, 9));
      }
      return $articles;
    }
    
    /**
     * Get all articles which are newer
     * than the given date
     *
     * @access  public
     * @param   &util.Date date
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
        return throw(new IOException('Could not get new articles'));
        
      while ($line= $this->_readData()) $articles[]= $line;
      
      return $articles;
    }

    /**
     * Get all groups which are newer
     * than the given date
     *
     * @access  public
     * @param   &util.Date date
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
        $groups[]= &new Newsgroup($buf[0], (int)$buf[1], (int)$buf[2], $buf[3]);
      }

      return $groups;
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

  } implements(__FILE__, 'util.log.Traceable');
?>
