<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
 
  uses(
    'io.Folder',
    'io.File',
    'io.dba.DBAFile',
    'util.MimeType',
    'org.webdav.xml.WebdavPropResponse',
    'org.webdav.xml.WebdavLockRequest',
    'org.webdav.xml.WebdavPropPatchResponse',
    'org.webdav.impl.DavImpl',
    'org.webdav.propertystorage.DBAFilePropertyStorage',
    'lang.ElementNotFoundException'
  );


  /**
   * Base class of DAV implementation
   *
   */ 
  class DavFileImpl extends DavImpl {
    var
      $base=              '',
      $dataDirectory=     '/data/',
      $propStorage=       NULL;
      
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct($base) {
      parent::__construct();

      $this->base= $base.$this->dataDirectory;
      $this->capabilities= (
        WEBDAV_IMPL_PROPFIND | 
        WEBDAV_IMPL_PROPPATCH
      );
      
      $this->propStorage= new DBAFilePropertyStorage($this->base.'../Webdav.props');
      
      $l= &Logger::getInstance();
      $this->c= &$l->getCategory();
    }
    
    /**
     * Private helper function
     *
     * @access  private
     * @param   string path
     * @param   int maxdepth
     * @throws  ElementNotFoundException
     */
    function _recurse(&$request, &$response, $path, $maxdepth) {
      $path= rtrim($path, '/');
      $realpath= $this->base.$path;

      if (!file_exists($realpath)) {
        return throw(new ElementNotFoundException($path.' not found'));
      }
      $rootURL= $request->getRootURL();
      $root= $rootURL->getPath();

      // It's a directory:
      if (is_dir($realpath)) {
        $f= &new Folder($realpath);
        // add object as xml
        try(); {
          $o= &new WebdavObject(
            $root.$path,
            WEBDAV_COLLECTION,
            0,
            NULL,
            new Date(filectime($f->uri)),
            new Date(filemtime($f->uri))
          );

          $eProps= $this->propStorage->getProperties(substr($f->uri, strlen($this->base)));
          foreach ($eProps as $key => $property){
            $o->addProperty($property);
          }
          new WebdavPropResponse(
            $request,
            $response,
            $o
          );
        } if (catch('Exception', $e)) {
          $this->c->debug(get_class($this).'::_recurse','Exeption ', $e->message);
        }
        
        // add entries
        $maxdepth--;
        // Add parentdir if it's a subdir
        if (substr($path,-1) != '/' && !empty($path))
          $path.= '/';
        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          $this->_recurse($request, $response, $path.$entry, $maxdepth);
        }
        $f->close();
        return;
      }
      
      $f= &new File($realpath);
      $o= &new WebdavObject(
        $root.$path,
        NULL,
        filesize($realpath),
        MimeType::getByFilename($realpath, 'text/plain'),
        new Date(filectime($realpath)),
        new Date(filemtime($realpath))
      );
      $eProps= $this->propStorage->getProperties(substr($f->uri, strlen($this->base)));
      
      foreach ($eProps as $key => $property) {
        $o->addProperty($property);
      }

      // lock info:
      $lockinfo= $this->getLockInfo($f->uri);
      if (!empty($lockinfo)){
        $o->addLockInfo(
          $lockinfo['type'],
          $lockinfo['scope'],
          $lockinfo['owner'],
          'Second-'.($lockinfo['timeend']-time()),
          $lockinfo['token'],
          $lockinfo['depth']
          );
      }
      
      //$f->close();

      new WebdavPropResponse(
        $request,
        $response,
        $o
      );
    }
    
    /**
     * Move a file
     *
     * @access  public
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @param   bool docopy
     * @return  bool created
     * @throws  OperationNotAllowedException
     * @throws  OperationFailedException
     */
    function &move($filename, $destination, $overwrite, $docopy= 0) {

      // Securitychecks (../etc/passwd)
      $filename= $this->_normalizePath($this->base.$filename);
      $destination= $this->_normalizePath($this->base.$destination);

      // Copy of folders is not allowed (implemented)
      if ($docopy && is_dir($filename)) {
        return throw(new OperationNotAllowedException($filename.' cannot be copied as it is a directory'));
      }
      
      // Create src and dst objects
      $src= is_dir($filename)    ? new Folder($filename)    : new File($filename);
      $dst= is_dir($destination) ? new Folder($destination) : new File($destination);

      // Is overwriting permitted?
      if ($dst->exists() && !$overwrite) {
        return throw(new OperationNotAllowedException($destination.' may not be overwritten by '.$filename));
      }
      
      // Copy/move the file to destination file/folder
      try(); {
        $docopy ? $src->copy($dst->getURI()) : $src->move($dst->getURI());
      } if (catch('IOException', $e)) {
        return throw(new OperationFailedException($filename.' cannot be copied/moved to '.$destination.' ('.$e->message.')'));
      }
      
      // Move/copy properties aso
      $src= substr($filename, strlen($this->base));
      $dst= substr($destination, strlen($this->base));
      $properties= &$this->propStorage->getProperties($src);
      if (!empty($properties)){
        if (!$docopy) $this->propStorage->setProperties($src, NULL);
        $this->propStorage->setProperties($dst, $properties);
      }
      return !$exists;
    }

    /**
     * Copy a file
     *
     * @access  public
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  OperationNotAllowedException
     * @throws  OperationFailedException
     */
    function &copy($filename, $destination, $overwrite) {
      return $this->move($filename, $destination, $overwrite, 1);
    }

    /**
     * Make a directory ("collection")
     *
     * @access  public
     * @param   string colname
     * @return  bool success
     * @throws  OperationFailedException
     */
    function &mkcol($colname) {
      
      $colname= $this->_normalizePath($this->base.urldecode($colname));
      if (file_exists($colname)) {
        return throw(new OperationFailedException($colname.' already exists'));
      }
          
      try(); {
        $f= &new Folder($colname);
        $f->create(0755);
      } if (catch('IOException', $e)) {
        return throw(new OperationFailedException($colname.' cannot be created ('.$e->message.')'));
      }
      
      return TRUE;
    }

    /**
     * Delete a file
     *
     * @access  public
     * @param   string filename
     * @param   &string data
     * @return  bool success
     * @throws  ElementNotFoundException
     * @throws  OperationFailedException
     * @throws  OperationNotAllowedException
     */
    function &delete($filename) {
      $filename= $this->_normalizePath($this->base.urldecode($filename));

      if (strlen($filename) <= strlen($this->base.'/')) {
        return throw(new OperationNotAllowedException($filename.' root-dir can not be deleted'));
      }
      
      $f= is_dir($filename) ? new Folder($filename): new File($filename);
          
      // If the specified argument doesn't exist, throw an exception
      if (!$f->exists()) {
        return throw(new ElementNotFoundException($filename.' not found'));
      }
      
      try(); {
        $f->unlink();
      } if (catch('IOException', $e)) {
        return throw(new OperationFailedException($filename.' cannot be deleted ('.$e->message.')'));
      }
      // delete properties
      $this->propStorage->setProperties($f->uri, NULL);
      return TRUE;
    }

    /**
     * Put a file
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletRequest request
     * @return  bool new
     * @throws  ElementNotFoundException
     */
    function &put($filename, &$data) {
      $filename= $this->base.$filename;
      if (is_dir($filename)) {
        return throw(new OperationNotAllowedException($filename.' cannot be written (not a file)'));
      }
      
      // Open file and write contents
      $f= &new File($filename);
      try(); {
        $new= !$f->exists();
        $f->open(FILE_MODE_WRITE);
        $f->write($data);
        $f->close();
      } if (catch('IOException', $e)) {
        return throw(new OperationFailedException($filename.' cannot be written to ('.$e->message.')'));
      }

      return $new;
    }
    
    /**
     * Get a file
     *
     * @access  public
     * @param   string filename
     * @return  &org.webdav.WebdavObject
     * @throws  ElementNotFoundException
     * @throws  OperationNotAllowed (locked)
     */
    function &get($filename, $token= NULL) {
    
      $this->c->debug('FILENAME', $filename);
      $this->c->debug('TOKEN', $token);

      $filename= $this->_normalizePath(urldecode($filename));
      
      // check for lock
      $lockinfo= $this->getLockInfo($filename);
      if (
        $lockinfo and 
        $lockinfo['type'] == 'exclusive'  and 
        'opaquelocktoken:'.$lockinfo['token'] != $token
      )
      return throw(new IllegalArgumentException($filename.' is locked exclusive'));
      
      if (is_dir($this->base.$filename)) {
        $this->c->debug(get_class($this),'::GET Dir', $filename);

        $f= &new Folder($this->base.$filename);
        if (!$f->exists()) {
          return throw(new ElementNotFoundException($filename.' not found'));
        }

        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          $isdir= is_dir($this->base.$filename.'/'.$entry);
          $atime= date('H:i:s  d.m.y',fileatime($this->base.$filename.'/'.$entry));
          if ($isdir) {
            $flist[0][$entry].= sprintf('
            <tr>
            <td><a href="%s/">%s</a></td>
            <td>&lt;DIR&gt;</td>
            <td>%s</td>
            <td>&nbsp;&nbsp;</td>
            </tr> 
            ',
            urlencode($entry), $entry, $atime);            
          } else {            
            $flist[1][$entry].= sprintf('
            <tr>
            <td><a href="%s">%s</a></td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>%s</td>
            <td>%s Bytes</td>
            </tr> 
            ',
            urlencode($entry), $entry, $atime,
            filesize($this->base.$filename.'/'.$entry));
          }
        }
        asort($flist[0]);
        $html= '<table cellpadding=3>'.(strlen($filename)>2?'<tr><td><a href="../">..</a></td><td>&lt;DIR&gt;</tr>':''). implode('', $flist[0]);
        asort($flist[1]);
        $flist= $html.implode('', $flist[1]).'</table>'; 

        $o= &new WebdavObject(
          $f->uri,
          NULL,
          strlen($flist),
          'text/html',
          new Date(filectime($f->uri)),
          new Date(filemtime($f->uri))
          );
          
        $o->setData($flist); 
        $f->close();
        return $o;        
      }
     
      $this->c->debug(get_class($this),'::GET filename', $filename);
      $this->c->debug(get_class($this),'::GET base', $this->base);

      // Open file and read contents
      // contentype
      if (!file_exists($this->base.$filename))
        return throw(new ElementNotFoundException($filename.' not found'));


      $f= &new File($this->base.$filename);
      $contentType= '';
      
      $this->c->debug(get_class($this), '::get ', $this->base.filename);
      $eProps= &$this->propStorage->getProperties($f->uri);
      if (!empty($eProps['getcontenttype']))
        $contentType= $eProps['getcontenttype'][0];
      if (empty($contentType))
        $contentType= MimeType::getByFilename($f->uri,'text/plain');

      $o= &new WebdavObject(
        $f->uri,
        NULL,
        $f->size(),
        $contentType,
        new Date($f->createdAt()),
        new Date($f->lastModified())
        );
        
      try(); {
        $f->open(FILE_MODE_READ);
        $o->setData($f->read($f->size()));
        $f->close();
      } if (catch('FileFoundException', $e)) {
        return throw(new ElementNotFoundException($filename.' not found'));
      }
      
      return $o;
    }

    /**
     * Patch properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropPatchRequest request
     * @throws  OperationFailedException
     * @throws  ElementNotFoundException
     */
    function &proppatch(&$request, &$response) {
      if (
        (!is_a($request, 'WebdavPropPatchRequest')) ||
        (!is_a($response, 'WebdavMultistatus'))
      ) {
        return throw(new IllegalArgumentException('Parameters passed of wrong types'));
      }
      
      $realpath= $this->base.$request->getFilename();
      if (!file_exists($realpath)) {
        return throw(new ElementNotFoundException($realpath.' not found'));
      }

      if (is_dir($realpath)) 
        $f= &new Folder($realpath);
      else
        $f= &new File($realpath);
      
      // load additional properties 
      $resp= &new WebDavPropPatchResponse($request, $response, $this);
      $nsmap= array(); //$request->getNamespaces();
      $reqProp= $request->getProperties();

      // Iterate over properties
      foreach ($reqProp as $property) {
        switch ($property->getName()) {
          case 'getetag': // write-proteced
          case 'iscollection':
          case 'isfolder':
          case 'getcontentlength':
            $resp->status_forbidden('executable', $ns);
            break;

          case 'executable':
            $resp->status_forbidden('executable', $ns);
            break;
          default:
            // TOBEDONE is it a stdprop ? then set forbidden
            // else it an extrapop

            $eProps= $this->propStorage->getProperties($request->getPath());
            if ($val['action'] == WEBDAV_PROPPATCH_REMOVE && isset($eProps[$key]))
              unset($eProps[$key]);
            else
              $eProps[$key]= $property;
            try();{
              $this->propStorage->setProperties($request->getPath(), $eProps);
            } if  (catch('Exception', $e)) {
              return throw($e);
            }
            $resp->status_ok($key, $ns);
        }
      }
      
      return $response;
    }
    
    /**
     * do unlocking
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     * @throws  OperationNotAllowedException
     */
    function &unlock(&$request, &$response) {
      
      $path =urldecode($request->uri['path_translated']);
      $locktoken= $request->getHeader('lock-token');
      $uri= $this->base.$path;
      if (!file_exists($uri)) {
        return throw(new ElementNotFoundException($path.' not found'));
      }

      try();{
        $locktoken= $this->unlock($uri, $locktoken);
      } if  (catch('Exception', $e)) {
        return throw($e,get_class($this).'::Unlock'.' no LCK-store '.$e->message);
      }
      if ($locktoken)
        $response->setHeader('Lock-Token', $locktoken);
      return;
    }

    /**
     * do locking
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavLockRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     * @throws  OperationNotAllowedException
     */
    function &lock(&$request, &$response) {
      if (!is_a($request, 'WebdavLockRequest')) {
        return throw(new IllegalArgumentException('Parameters passed of wrong types'));
      }
      // check file
      $path= urldecode($request->getFilename());
      $realpath= $this->base.$path;

      if ( 0 && !file_exists($realpath)) {
        return throw(new ElementNotFoundException($path.' not found'));
      }

      $uri= $realpath;
      $lockinfoRequest= $request->getProperties();
      
      try();{
        $token= $this->lock(
          $uri,
          $lockinfoRequest['owner'],
          $lockinfoRequest['type'],
          $lockinfoRequest['scope'],
          $lockinfoRequest['timeout'],
          $request->ifcondition,
          $lockinfoRequest['depth']
          );

        $lockinfo= &$this->getLockInfo($uri);
      } if (catch('Exception', $e)){
        return throw(new OperationNotAllowedException(' locking not allowed by '.$lockinfo['owner'].' on '.$uri.'('.$uri['filename'].')'));
      }
      
      // response as lockdiscovery;
      $resp=
        '<?xml version="1.0" encoding="utf-8" ?><prop xmlns="DAV:"><lockdiscovery>'. 
        '<activelock><locktype><'.$lockinfo['type'].'/></locktype>'.
        '<lockscope><'.$lockinfo['scope'].'/></lockscope>'.
        '<depth>'.$lockinfo['depth'].'</depth>'.
        '<owner><href>'.$lockinfo['owner'].'</href></owner>'.
        '<timeout>Second-'.($lockinfo['timeend']-time()).'</timeout>'.
        '<locktoken><href>opaquelocktoken:'.$lockinfo['token'].'</href></locktoken>'.
        '</activelock></lockdiscovery></prop>'; 
      $response->setContent($resp);
      $response->setHeader('Content-length', strlen($resp));
      $response->setHeader('Lock-Token', 'opaquelocktoken:'.$lockinfo['token']);  // fuer neon
    }

    /**
     * Find properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest ßrequest
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     */
    function &propfind(&$request, &$response, $useragent= 0) {
      if (
        //(!is_a($request, 'WebdavPropFindRequest')) ||
        (!is_a($response, 'WebdavMultistatus'))
      ) {
        return throw(new IllegalArgumentException('Parameters passed of wrong types'));
      }

      try(); {
        $this->_recurse(
          $request,
          $response,
          $request->getPath(),
          $request->getDepth()
        );
          
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      return $response; 
    }
    
    /**
     * Set active URI
     *
     * @access  public
     * @param   string uri
     * @return  none
     */
    function setUri($uri) {
      $this->uri= $uri;
    }
    
    /**
     * Get active Uri
     *
     * @access  public
     * @param   none
     * @return  string uri
     */
    function getUri() {
      return $this->uri;
    }
    
    /**
     * Retrieve lock information
     *
     * @access  public
     * @param   string uri  The URI
     * @return  array[]
     */
    function &getLockInfo($uri){
      $key= 'LOCK:'.$uri;
      $this->propStorage->open(DBO_READ);
      $lock= $this->propStorage->lookup($key) ? unserialize($this->propStorage->fetch($key)) : NULL;
      $this->propStorage->close();

      if (empty($lock)) return NULL;

      if ($lock['time']+$lock['timeout'] < time()) {  // lock expired
        $this->propStorage->open(DBO_WRITE);
        $this->propStorage->delete($key);
        $this->propStorage->close();
        return NULL;
      }
      return $lock;
    }
    
    /**
     * Set lock for URI
     *
     * @access public
     * @param  string uri     The URI
     * @param  string owner   The owner
     * @param  string type    The type (defaults to "write")
     * @param  string scope   The scope (defaults to "exclusive")
     * @param  int    timeout The timeout for this lock (e.g. time()+3600, defaults to 86400)
     * @param  string token   The 
     * @param  int    depth   The depth (defaults to 0)
     * @return string token
     * @throws OperationNotAllowedException
     */
    function setLockInfo($uri, $owner, $type= 'write', $scope= 'exclusive', $timeout= 86400, $token= NULL, $depth= 0) {
      $key= 'LOCK:'.$uri;
      $lock= $this->getLockInfo($uri);
      $token= preg_replace('/\(|\)|<|>|/|opaquelocktoken:','', $token);
      
      if (
        // There's already a lock
        !empty($lock) &&
        
        // The wrong owner want to set a lock
        !empty($lock['owner']) && ($owner != $lock['owner'])  && 
        
        // The token isn't the same
        !empty($token) && ($token  != $lock['token'])
      ) {
        return throw(new OperationNotAllowedException('Can not lock '.$uri.' for '.$owner));
      }
      
      // Check timeout
      if (substr($timeout,0,7) == 'Second-') $timeout= (int)substr($timeout,7);
      $timeout= $timeout ? (int)$timeout : 86400;

      // Check depth      
      if ($depth != 'infinity') $depth= (int)$depth;
      
      // Check token
      if (empty($token)) $token= md5($owner.$uri);

      // We can't set a lock where owner is empty
      if (empty($lock) && empty($owner)) {
        return throw(new OperationNotAllowedException('Can not set lock with empty owner'));
      }
      
      // Set lock
      $this->propStorage->open(DBO_WRITE);
      $this->propStorage->store($key, serialize(array(
        'owner'   => $owner ? $owner : $lock['owner'],
        'token'   => $token,
        'depth'   => $depth,
        'type'    => $type  !== NULL ? $type  : $lock['type'],
        'scope'   => $scope !== NULL ? $scope : $lock['scope'],
        'timeout' => $timeout,
        'time'    => time()
      )));
      $this->propStorage->close();
      return 'opaquelocktoken:'.$token;
    }
    
    /**
     * Unlock URI
     *
     * @access  public
     * @param   string uri   The URI
     * @return  string token The lock token
     * @throws  OperationNotAllowedException
     */
    function clearLockInfo($uri, $token= NULL) {
      $key= 'LOCK:'.$uri;
      $lock= $this->getLockInfo($uri);
      if (empty($lock)) return $token;
      
      $this->propStorage->open(DBO_WRITE);
      $this->propStorage->delete($key);
      $this->propStorage->close();
      return 'opaquelocktoken:'.$lock['token'];
    }
    
  }
?>
