<?php
/* This class is part of the XP framework
 *
 * $Id: DavFileImpl.class.php 8975 2006-12-27 18:06:40Z friebe $
 */

  namespace org::webdav::impl;

  ::uses(
    'util.Date',
    'io.Folder',
    'io.File',
    'util.MimeType',
    'org.webdav.impl.DavImpl',
    'org.webdav.propertystorage.DBAFilePropertyStorage',
    'org.webdav.version.WebdavFileVersion',
    'org.webdav.version.util.WebdavVersionUtil',
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
    public
      $base=              '',
      $dataDirectory=     '/data/',
      $propStorage=       NULL;
    
    /**
     * Constructor
     *
     * @param   string base
     */
    public function __construct($base) {
      $this->base= $base.$this->dataDirectory;
      $this->capabilities= (
        WEBDAV_IMPL_PROPFIND | 
        WEBDAV_IMPL_PROPPATCH
      );
      
      $this->propStorage= new org::webdav::propertystorage::DBAFilePropertyStorage($this->base.'../Webdav.props');
      
      $l= util::log::Logger::getInstance();
      $this->c= $l->getCategory();
    }
    
    /**
     * Private helper function
     *
     * @param   string path
     * @param   int maxdepth
     * @throws  lang.ElementNotFoundException
     */
    protected function _recurse($request, $response, $path, $maxdepth) {
      $path= rtrim($path, '/');
      $realpath= $this->base.$path;

      if (!file_exists($realpath)) {
        throw(new lang::ElementNotFoundException($path.' not found'));
      }
      $rootURL= $request->getRootURL();
      $root= $rootURL->getPath();

      // It's a directory:
      if (is_dir($realpath)) {
        $f= new io::Folder($realpath);
        try {
          $o= new (
            $root.$path,
            WEBDAV_COLLECTION,
            0,
            NULL,
            new util::Date(filectime($f->uri)),
            new util::Date(filemtime($f->uri))
          );

          $eProps= $this->propStorage->getProperties(substr($f->uri, strlen($this->base)));
          foreach ($eProps as $key => $property) {
            $o->addProperty($property);
          }
          $response->addWebdavObject($o, $request->getProperties());
        } catch (::Exception $e) {
          $this->c->debug(get_class($this).'::_recurse', 'Exeption ', $e->message);
        }
        
        // Read subdirectories
        $maxdepth--;
        if (substr($path, -1) != '/' && !empty($path)) $path.= '/';
        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          $this->_recurse($request, $response, $path.$entry, $maxdepth);
        }
        $f->close();
        return;
      }
      
      // It's a File
      $f= new io::File($realpath);
      $o= new (
        $root.$path,
        NULL,
        filesize($realpath),
        util::MimeType::getByFilename($realpath, 'text/plain'),
        new util::Date(filectime($realpath)),
        new util::Date(filemtime($realpath))
      );

      // Add properties to WebdavObject
      $eProps= $this->propStorage->getProperties($path);
      foreach ($eProps as $key => $property) $o->addProperty($property);
      if (isset($eProps['D:resourcetype'])) $o->setResourceType($eProps['D:resourcetype']->value);

      // lock info:
      $lockinfo= $this->getLockInfo($path);

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
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @param   bool docopy
     * @return  bool created
     * @throws  org.webdav.OperationNotAllowedException
     * @throws  org.webdav.OperationFailedException
     */
    public function move($filename, $destination, $overwrite, $docopy= 0) {

      // Securitychecks (../etc/passwd)
      $uri= $this->_normalizePath($this->base.$filename);
      $destination= $this->_normalizePath($this->base.$destination);

      // Copy of folders is not allowed (implemented)
      if ($docopy && is_dir($uri)) {
        throw(new ($uri.' cannot be copied as it is a directory'));
      }
      
      // Create src and dst objects
      $src= is_dir($uri) ? new io::Folder($uri) : new io::File($uri);
      $dst= is_dir($destination) ? new io::Folder($destination) : new io::File($destination);

      // Is overwriting permitted?
      if ($dst->exists() && !$overwrite) {
        throw(new ($destination.' may not be overwritten by '.$uri));
      }

      // Throw an exception if resource is locked
      if ($this->getLockInfo($filename) !== NULL) {
        throw(new ($src->getURI().' is locked'));
      }
      
      // Copy/move the file to destination file/folder
      try {
        $docopy ? $src->copy($dst->getURI()) : $src->move($dst->getURI());
      } catch (io::IOException $e) {
        throw(new ($filename.' cannot be copied/moved to '.$destination.' ('.$e->message.')'));
      }
      
      $src= substr($uri, strlen($this->base));
      $dst= substr($destination, strlen($this->base));
      
      // Move/copy properties also
      $properties= $this->propStorage->getProperties($src);
      if (!empty($properties)) {
        if (!$docopy) $this->propStorage->setProperties($src, NULL);
        $this->propStorage->setProperties($dst, $properties);
      }
      return !$exists;
    }

    /**
     * Copy a file
     *
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  org.webdav.OperationNotAllowedException
     * @throws  org.webdav.OperationFailedException
     */
    public function copy($filename, $destination, $overwrite) {
      return $this->move($filename, $destination, $overwrite, 1);
    }

    /**
     * Make a directory ("collection")
     *
     * @param   string colname
     * @return  bool success
     * @throws  org.webdav.OperationFailedException
     */
    public function mkcol($col) {

      $colname= $this->_normalizePath($this->base.$col);
      if (file_exists($colname)) {
        throw(new ($colname.' already exists'));
      }
          
      try {
        $f= new io::Folder($colname);
        $f->create(0755);
        
        // Create also backup directory
        $b= new io::Folder($this->_normalizePath($this->base.'../versions/'.$col));
        $b->create(0755);        
      } catch (io::IOException $e) {
        throw(new ($colname.' cannot be created ('.$e->message.')'));
      }
      
      return TRUE;
    }

    /**
     * Delete a file
     *
     * @param   string filename
     * @return  bool success
     * @throws  lang.ElementNotFoundException
     * @throws  org.webdav.OperationFailedException
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function delete($filename) {    
      $uri= $this->_normalizePath($this->base.$filename);

      if (strlen($uri) <= strlen($this->base)) {
        throw(new ($uri.' root-dir can not be deleted'));
      }
      
      $f= is_dir($uri) ? new io::Folder($uri): new io::File($uri);
          
      // If the specified argument doesn't exist, throw an exception
      if (!$f->exists()) {
        throw(new lang::ElementNotFoundException($filename.' not found'));
      }

      // Throw an exception if resource is locked
      if ($this->getLockInfo($filename) !== NULL) {
        throw(new ($filename.' is locked'));
      }

      try {
        $f->unlink();
      } catch (io::IOException $e) {
        throw(new ($filename.' cannot be deleted ('.$e->message.')'));
      }
  
      // Delete backup versions
      if ($this->propStorage->hasProperty($filename, 'D:version')) {
        $prop= $this->propStorage->getProperty($filename, 'D:version');
        $container= $prop->value;
  
        foreach ($container->getVersions() as $v) {
          $this->delete($v->getHref());
        }        
      }
        
      // Delete properties
      $this->propStorage->setProperties($filename, NULL);
      
      return TRUE;
    }

    /**
     * Put a file
     *
     * @param   string filename
     * @param   mixed data
     * @param   string resourcetype, default NULL
     * @return  bool new
     * @throws  org.webdav.OperationNotAllowedException
     * @throws  org.webdav.OperationFailedException
     */
    public function put($filename, $data, $resourcetype= NULL) {
      
      $uri= $this->base.$filename;
      if (is_dir($uri)) {
        throw(new ($uri.' cannot be written (not a file)'));
      }
      
      // Open file and write contents
      $f= new io::File($uri);
      
      // Check if VersionControl is activated
      if (($prop= $this->propStorage->getProperty($filename, 'D:version')) !== NULL) {
        $container= $prop->value;
        
        $newVersion= org::webdav::version::util::WebdavVersionUtil::getNextVersion($container->getLatestVersion(), $f);
        $container->addVersion($newVersion);
        
        // Re-Add modified container to property
        $prop->value= $container;

        // Save property
        $this->propStorage->setProperty($filename, $prop);
       
        // Now, copy the "old" file to versions directory
        $this->backup($filename, '../versions/'.dirname($filename).'/'.$newVersion->getVersionName());
      }
      
      try {
        $new= !$f->exists();
        $f->open(FILE_MODE_WRITE);
        $f->write($data);
        $f->close();
      } catch (io::IOException $e) {
        throw(new ($filename.' cannot be written '.$e->toString()));
      }
      
      // Set the resourcetype on first put
      if (($prop= $this->propStorage->getProperty($filename, 'D:resourcetype')) == NULL) {      
      
        // Set ResourceType
        with ($p= new ('resourcetype', $resourcetype)); {
          $p->setNameSpaceName('DAV:');
          $p->setNameSpacePrefix('D:');          
        }
        
        $this->propStorage->setProperty($filename, $p);  
      }
      return $new;
    }
    
    /**
     * Get a file
     *
     * @param   string filename
     * @return  &org.webdav.WebdavObject
     * @throws  lang.ElementNotFoundException
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function get($filename, $token= NULL) {
    
      $this->c->debug('FILENAME', $filename);
      $this->c->debug('TOKEN', $token);

      $filename= $this->_normalizePath($filename);
      
      // check for lock
      $lockinfo= $this->getLockInfo($filename);
      if (
        $lockinfo and 
        $lockinfo['type'] == 'exclusive'  and 
        'opaquelocktoken:'.$lockinfo['token'] != $token
      )
      throw(new lang::IllegalArgumentException($filename.' is locked exclusive'));
      
      if (is_dir($this->base.$filename)) {
        $this->c->debug(get_class($this), '::GET Dir', $filename);

        $f= new io::Folder($this->base.$filename);
        if (!$f->exists()) {
          throw(new lang::ElementNotFoundException($filename.' not found'));
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
            rawurlencode($entry), $entry, $atime);            
          } else {            
            $flist[1][$entry].= sprintf('
            <tr>
            <td><a href="%s">%s</a></td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>%s</td>
            <td>%s Bytes</td>
            </tr> 
            ',
            rawurlencode($entry), $entry, $atime,
            filesize($this->base.$filename.'/'.$entry));
          }
        }
        asort($flist[0]);
        $html= '<table cellpadding=3>'.(strlen($filename)>2?'<tr><td><a href="../">..</a></td><td>&lt;DIR&gt;</tr>':''). implode('', $flist[0]);
        asort($flist[1]);
        $flist= $html.implode('', $flist[1]).'</table>'; 

        $o= new (
          $f->uri,
          NULL,
          strlen($flist),
          'text/html',
          new util::Date(filectime($f->uri)),
          new util::Date(filemtime($f->uri))
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
        throw(new lang::ElementNotFoundException($filename.' not found'));


      $f= new io::File($this->base.$filename);
      $contentType= '';
      
      $this->c->debug(get_class($this), '::get ', $this->base.filename);
      $eProps= $this->propStorage->getProperties($f->uri);
      if (!empty($eProps['getcontenttype']))
        $contentType= $eProps['getcontenttype'][0];
      if (empty($contentType))
        $contentType= util::MimeType::getByFilename($f->uri, 'text/plain');

      $o= new (
        $f->uri,
        NULL,
        $f->size(),
        $contentType,
        new util::Date($f->createdAt()),
        new util::Date($f->lastModified())
      );
        
      try {
        $f->open(FILE_MODE_READ);
        $o->setData($f->read($f->size()));
        $f->close();
      } catch ( $e) {
        throw(new lang::ElementNotFoundException($filename.' not found'));
      }
      $this->c->debug('OBJ', $o->properties);
      return $o;
    }

    /**
     * Patch properties
     *
     * @param   &org.webdav.xml.WebdavPropPatchRequest request
     * @param   &org.webdav.xml.WebdavPropPatchResponse response
     * @throws  org.webdav.OperationFailedException
     * @throws  lang.ElementNotFoundException
     */
    public function proppatch($request, $response) {
      $realpath= $this->base.$request->getPath();
      if (!file_exists($realpath)) {
        throw(new lang::ElementNotFoundException($realpath.' not found'));
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
              throw(new ($key.' is a standard property'));
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
              try {
                $this->propStorage->setProperties($request->getPath(), $eProps);
              } catch (::Exception $e) {
                throw($e);
              }
          }
        }
      }
    }
    
    /**
     * do unlocking
     *
     * @param   &org.webdav.xml.WebdavScriptletRequest response
     * @param   &org.webdav.xml.WebdavScripltetResponse response
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function unlock($request, $response) {
      $realpath= $this->base.$request->getPath();
      
      if (!file_exists($realpath)) {
        throw(new lang::ElementNotFoundException($realpath.' not found'));
      }
      parent::unlock($request, $response);      
    }
     
    /**
     * do locking
     *
     * @param   &org.webdav.xml.WebdavLockRequest request
     * @param   &org.webdav.xml.WebdavScriptletResponse response
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function lock($request, $response) {
      $realpath= $this->base.$request->getPath();
      
      if (!file_exists($realpath)) {
        $this->put($request->getPath(), $d= NULL, WEBDAV_LOCK_NULL);
      }
      parent::lock($request, $response);
    }

    /**
     * Find properties
     *
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatusResponse response
     */
    public function propfind($request, $response, $useragent= 0) {
      try {
        $this->_recurse(
          $request,
          $response,
          $request->getPath(),
          $request->getDepth()
        );
      } catch (::Exception $e) {
        throw($e);
      }
    }
    
    /**
     * Start Version-Control of file
     *
     * @param   string path
     * @param   &io.File file
     * @throws  lang.ElementNotFoundException 
     */
    public function VersionControl($path, $file) {
      $realpath= $this->base.$path;

      if (!file_exists($realpath)) {
        throw(new lang::ElementNotFoundException($realpath.' not found'));
      }
      
      // Get name of file, without extension
      $fname= basename($realpath, '.'.$file->getExtension());
      
      // Create Version object 
      with ($version= new org::webdav::version::WebdavFileVersion($file->getFilename())); {
        $version->setVersionNumber('1.0');
        $version->setHref('../versions/'.dirname($path).'/'.$fname.'[1.0].'.$file->getExtension());
        $version->setVersionName($fname.'[1.0].'.$file->getExtension());
        $version->setContentLength($file->size());
        $version->setLastModified(util::Date::now());
      }

      parent::VersionControl($path, $version);
    }
    
    /**
     * Report version status
     *
     * @param   &org.webdav.xml.WebdavPropFindRequest
     * @param   &org.webdav.xml.WebdavMultistatusResponse
     * @throws  lang.ElementNotFoundException
     */
    public function report($request, $response) {
      $realpath= $this->base.$request->getPath();

      if (!file_exists($realpath)) {
        throw(new lang::ElementNotFoundException($realpath.' not found'));
      }
      parent::report($request, $response);
    }
    
    /**
     * Move a file
     *
     * @param   string filename
     * @param   string destination
     * @return  bool created
     * @throws  org.webdav.OperationNotAllowedException
     * @throws  org.webdav.OperationFailedException
     */
    public function backup($filename, $destination) {

      // Securitychecks (../etc/passwd)
      $uri= $this->_normalizePath($this->base.$filename);
      $destination= $this->_normalizePath($this->base.$destination);

      // Copy of folders is not allowed (implemented)
      if (is_dir($uri)) {
        throw(new ($uri.' cannot be copied as it is a directory'));
      }
      
      // Create src and dst objects
      $src= is_dir($uri) ? new io::Folder($uri) : new io::File($uri);
      $dst= is_dir($destination) ? new io::Folder($destination) : new io::File($destination);

      // Throw an exception if resource is locked
      if ($this->getLockInfo($filename) !== NULL) {
        throw(new ($src->getURI().' is locked'));
      }
      
      // Copy the file to destination file
      try {
        $src->copy($dst->getURI());
      } catch (io::IOException $e) {
        throw(new ($filename.' cannot be copied/moved to '.$destination.' ('.$e->message.')'));
      }
      
      return !$exists;
    }
  }
?>
