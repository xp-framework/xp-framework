<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.webdav.impl.DavImpl',
    'io.Folder',
    'io.File',
    'lang.ElementNotFoundException',
    'util.MimeType'
  );
  
  /**
   * Base class of DAV implementation
   *
   */ 
  class DavFileImpl extends DavImpl {
    var
      $base = '';
      
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct($base) {
      $this->base= $base;
      $this->capabilities= (
        WEBDAV_IMPL_PROPFIND | 
        WEBDAV_IMPL_PROPPATCH
      );
      parent::__construct();
    }
    
    /**
     * Private helper function
     *
     * @access  private
     * @param   string path
     * @param   string base
     * @param   &org.webdav.xml.WebdavPropFindResponse response
     * @param   int maxdepth
     * @throws  ElementNotFoundException
     */
    function _recurse($path, $root, &$response, $maxdepth) {
      // DEBUG $l= &Logger::getInstance();
      // DEBUG $c= &$l->getCategory();
      
      $realpath= $this->base.$path;
      if (!file_exists($realpath)) {
        return throw(new ElementNotFoundException($path.' not found'));
      }
      
      if (is_dir($realpath)) {
        // DEBUG $c->debug('DIRECTORY', $path);
        $f= &new Folder($realpath);
        $response->addEntry(new WebdavObject(
          basename($path),
          $root.$path,
          new Date(filectime($f->uri)),
          new Date(filemtime($f->uri)),
          WEBDAV_COLLECTION
        ));
        $maxdepth--;
        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          // DEBUG $c->debug('RECURSE', $path.$entry);
          $this->_recurse($path.$entry, $root, $response, $maxdepth);
        }
        $f->close();
        return;
      }
      
      // DEBUG $c->debug('FILE', $realpath);
      $response->addEntry(new WebdavObject(
        basename($path),
        $root.$path,
        new Date(filectime($realpath)),
        new Date(filemtime($realpath)),
        NULL,
        filesize($realpath),
        MimeType::getByFilename($path, 'text/plain')
      ));
    }

    /**
     * Make a directory ("collection")
     *
     * @access  public
     * @param   string colname
     * @return  bool success
     * @throws  MethodNotImplementedException
     */
    function &mkcol($colname) {
      $f= &new Folder($this->base.$colname);
      try(); {
        $f->create(0700);
      } if (catch('IOException', $e)) {
        return throw(new Exception($filename.' cannot be created ('.$e->message.')'));
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
     */
    function &delete($filename) {
      if (is_dir($this->base.$filename)) {
        $f= &new Folder($this->base.$filename);
      } else {
        $f= &new File($this->base.$filename);
      }
      
      // If the specified argument doesn't exist, raise an error
      if (!$f->exists()) {
        return throw(new ElementNotFoundException($filename.' not found'));
      }
      
      try(); {
        $f->unlink();
      } if (catch('IOException', $e)) {
        return throw(new Exception($filename.' cannot be deleted ('.$e->message.')'));
      }
      
      return TRUE;
    }

    /**
     * Put a file
     *
     * @access  public
     * @param   string filename
     * @param   &string data
     * @return  bool new
     * @throws  ElementNotFoundException
     */
    function &put($filename, &$data) {
      if (is_dir($this->base.$filename)) {
        return throw(new Exception($filename.' cannot be written (not a file)'));
      }
      
      // Open file and write contents
      $f= &new File($this->base.$filename);
      try(); {
        $new= !$f->exists();
        $f->open(FILE_MODE_WRITE);
        $f->write($data);
        $f->close();
      } if (catch('IOException', $e)) {
        return throw(new Exception($filename.' cannot be written to ('.$e->message.')'));
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
     */
    function &get($filename) {
      if (is_dir($this->base.$filename)) {
        return throw(new Exception($filename.' cannot be retreived using GET'));
      }
      
      // Open file and read contents
      $f= &new File($this->base.$filename);
      $o= &new WebdavObject(
        $f->filename,
        $f->uri,
        new Date($f->createdAt()),
        new Date($f->lastModified()),
        NULL,
        $f->size(),
        MimeType::getByFilename($f->uri, 'text/plain')
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
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavPropFindResponse response
     * @return  &org.webdav.xml.WebdavPropFindResponse response
     */
    function &proppatch(&$request, &$response) {
      
    }
    
    /**
     * Find properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavPropFindResponse response
     * @return  &org.webdav.xml.WebdavPropFindResponse response
     */
    function &propfind(&$request, &$response) {
      if (
        (!is_a($request, 'WebdavPropFindRequest')) ||
        (!is_a($response, 'WebdavPropFindResponse'))
      ) {
        trigger_error('[request.type ] '.get_class($request), E_USER_NOTICE);
        trigger_error('[response.type] '.get_class($response), E_USER_NOTICE);
        return throw(new IllegalArgumentException('Parameters passed of wrong types'));
      }

      $l= &Logger::getInstance();
      $c= &$l->getCategory();
      $c->debug('Properties requested', $request->getProperties());
 
      try(); {
        $this->_recurse(
          $request->getPath(),   
          $request->getWebroot(), 
          $response, 
          $request->getDepth()
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return $response;
    }
  }
?>
