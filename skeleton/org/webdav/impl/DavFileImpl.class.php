<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.Folder',
    'io.File',
    'util.MimeType',
    'org.webdav.impl.DavImpl',
    'org.webdav.propertystorage.DBAFilePropertyStorage',
    'lang.ElementNotFoundException',
    'util.log.Logger'
  );

  /**
   * Base class of DAV implementation
   *
   * @see      xp://org.webdav.impl.DavImpl
   * @purpose  Dav Implementation
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
     * @param   string base
     */
    function __construct($base) {
      $this->base= $base.$this->dataDirectory;
      $this->capabilities= (
        WEBDAV_IMPL_PROPFIND | 
        WEBDAV_IMPL_PROPPATCH
      );
      
      $this->propStorage= &new DBAFilePropertyStorage($this->base.'../Webdav.props');
      
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
          foreach ($eProps as $key => $property) {
            $o->addProperty($property);
          }
          $response->addWebdavObject($o, $request->getProperties());
        } if (catch('Exception', $e)) {
          $this->c->debug(get_class($this).'::_recurse', 'Exeption ', $e->message);
        }
        
        // add entries
        $maxdepth--;
        // Add parentdir if it's a subdir
        if (substr($path, -1) != '/' && !empty($path)) $path.= '/';
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
      $lockinfo= $this->getLockInfo(substr($f->uri, strlen($this->base)));

      if (!empty($lockinfo)) {
        $o->addLockInfo(
          $lockinfo->getLockType(),
          $lockinfo->getLockScope(),
          $lockinfo->getOwner(),
          'Second-'.($lockinfo->getTimeout()),
          $lockinfo->getLockToken(),
          $lockinfo->getDepth()
        );
      }
      $response->addWebdavObject($o, $request->getProperties());
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
      $uri= $this->_normalizePath($this->base.$filename);
      $destination= $this->_normalizePath($this->base.$destination);

      // Copy of folders is not allowed (implemented)
      if ($docopy && is_dir($uri)) {
        return throw(new OperationNotAllowedException($uri.' cannot be copied as it is a directory'));
      }
      
      // Create src and dst objects
      $src= is_dir($uri) ? new Folder($uri) : new File($uri);
      $dst= is_dir($destination) ? new Folder($destination) : new File($destination);

      // Is overwriting permitted?
      if ($dst->exists() && !$overwrite) {
        return throw(new OperationNotAllowedException($destination.' may not be overwritten by '.$uri));
      }

      // Throw an exception if resource is locked
      if ($this->getLockInfo($filename) !== NULL) {
        return throw(new OperationNotAllowedException($src->getURI().' is locked'));
      }
      
      // Copy/move the file to destination file/folder
      try(); {
        $docopy ? $src->copy($dst->getURI()) : $src->move($dst->getURI());
      } if (catch('IOException', $e)) {
        return throw(new OperationFailedException($filename.' cannot be copied/moved to '.$destination.' ('.$e->message.')'));
      }
      
      // Move/copy properties also
      $src= substr($uri, strlen($this->base));
      $dst= substr($destination, strlen($this->base));
      $properties= &$this->propStorage->getProperties($src);
      if (!empty($properties)) {
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
      $uri= $this->_normalizePath($this->base.$filename);

      if (strlen($uri) <= strlen($this->base.'/')) {
        return throw(new OperationNotAllowedException($uri.' root-dir can not be deleted'));
      }
      
      $f= is_dir($uri) ? new Folder($uri): new File($uri);
          
      // If the specified argument doesn't exist, throw an exception
      if (!$f->exists()) {
        return throw(new ElementNotFoundException($filename.' not found'));
      }

      // Throw an exception if resource is locked
      if ($this->getLockInfo($filename) !== NULL) {
        return throw(new OperationNotAllowedException($filename.' is locked'));
      }
      
      try(); {
        $f->unlink();
      } if (catch('IOException', $e)) {
        return throw(new OperationFailedException($filename.' cannot be deleted ('.$e->message.')'));
      }
      // delete properties
      $this->propStorage->setProperties($filename, NULL);
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
        $this->c->debug(get_class($this), '::GET Dir', $filename);

        $f= &new Folder($this->base.$filename);
        if (!$f->exists()) {
          return throw(new ElementNotFoundException($filename.' not found'));
        }

        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          $isdir= is_dir($this->base.$filename.'/'.$entry);
          $atime= date('H:i:s  d.m.y', fileatime($this->base.$filename.'/'.$entry));
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
     
      $this->c->debug(get_class($this), '::GET filename', $filename);
      $this->c->debug(get_class($this), '::GET base', $this->base);

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
        $contentType= MimeType::getByFilename($f->uri, 'text/plain');

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
     * @param   &org.webdav.xml.WebdavPropPatchRequest  request
     * @param   &org.webdav.xml.WebdavPropPatchResponse response
     * @throws  OperationFailedException
     * @throws  ElementNotFoundException
     */
    function &proppatch(&$request, &$response) {
      $realpath= $this->base.$request->getPath();
      if (!file_exists($realpath)) {
        return throw(new ElementNotFoundException($realpath.' not found'));
      }

      foreach (array(FALSE, TRUE) as $remove) {
        $reqProp= $request->getProperties($remove);

        // Iterate over properties
        foreach ($reqProp as $property) {
          switch ($key= $property->getName()) {
            case 'getetag': // write-proteced
            case 'iscollection':
            case 'isfolder':
            case 'getcontentlength':
            case 'executable':
              return throw(new OperationNotAllowedException($key.' is a standard property'));
              break;

            default:
              $eProps= $this->propStorage->getProperties($request->getPath());
              if ($remove && isset($eProps[$key])) {
                $response->addProperty($eProps[$key]);
                unset($eProps[$key]);
              } else {
                $response->addProperty($property);
                $eProps[$key]= $property;
              }
              try();{
                $this->propStorage->setProperties($request->getPath(), $eProps);
              } if  (catch('Exception', $e)) {
                return throw($e);
              }
          }
        }
      }
    }
    
    /**
     * do unlocking
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavScriptletRequest  response
     * @param   &org.webdav.xml.WebdavScripltetResponse response
     * @throws  OperationNotAllowedException
     */
    function &unlock(&$request, &$response) {
      $realpath= $this->base.$request->getPath();
      
      if (!file_exists($realpath)) {
        return throw(new ElementNotFoundException($realpath.' not found'));
      }
      parent::unlock($request, $response);      
    }
     
    /**
     * do locking
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavLockRequest       request
     * @param   &org.webdav.xml.WebdavScriptletResponse response
     * @throws  OperationNotAllowedException
     */
    function &lock(&$request, &$response) {
      $realpath= $this->base.$request->getPath();
      
      if (!file_exists($realpath)) {
        return throw(new ElementNotFoundException($realpath.' not found'));
      }
      parent::lock($request, $response);
    }

    /**
     * Find properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest     request
     * @param   &org.webdav.xml.WebdavMultistatusResponse response
     */
    function &propfind(&$request, &$response, $useragent= 0) {
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
    }
  }
?>
