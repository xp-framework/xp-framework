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
    public
      $base = '';
      
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct($base) {
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
    private function _recurse($path, $root, WebdavPropFindResponse $response, $maxdepth) {
      $realpath= $this->base.$path;
      if (!file_exists($realpath)) {
        throw (new ElementNotFoundException($path.' not found'));
      }
      
      if (is_dir($realpath)) {
        $f= new Folder($realpath);
        $response->addEntry(new WebdavObject(
          basename($path),
          $root.$path,
          new Date(filectime($f->uri)),
          new Date(filemtime($f->uri)),
          WEBDAV_COLLECTION
        ));
        $maxdepth--;
        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          self::_recurse($path.$entry, $root, $response, $maxdepth);
        }
        $f->close();
        return;
      }
      
      $response->addEntry(new WebdavObject(
        basename($path),
        $root.$path,
        new Date(filectime($realpath)),
        new Date(filemtime($realpath)),
        NULL,
        filesize($realpath),
        MimeType::getByFilename($path, 'text/plain'),
        is_executable($realpath)
      ));
    }

    /**
     * Move a file
     *
     * @access  public
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  OperationNotAllowedException
     * @throws  OperationFailedException
     */
    public function move($filename, $destination, $overwrite) {
      if (is_dir($this->base.$filename)) {
        $f= new Folder($this->base.$filename);
        $exists= file_exists($this->base.$destination);
      } else {
        $f= new File($this->base.$filename);
        $exists= (file_exists($this->base.$destination) && !is_dir($this->base.$destination));
      }

      // Is overwriting permitted?
      if (!$overwrite && $exists) {
        throw (new OperationNotAllowedException($destination.' may not be overwritten by '.$filename));
      }
      
      try {
        $f->move($this->base.$destination);
      } catch (IOException $e) {
        throw (new OperationFailedException($filename.' cannot be copied to '.$destination.' ('.$e->message.')'));
      }
      
      return !$exists;;
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
    public function copy($filename, $destination, $overwrite) {
      if (is_dir($this->base.$filename)) {
        throw (new OperationNotAllowedException($filename.' cannot be copied as it is a directory'));
      }
      
      // Check if the destination exists
      $exists= (file_exists($this->base.$destination) && !is_dir($this->base.$destination));
      
      // Is overwriting permitted?
      if (!$overwrite && $exists) {
        throw (new OperationNotAllowedException($destination.' may not be overwritten by '.$filename));
      }

      $f= new File($this->base.$filename);
      try {
        $f->copy($this->base.$destination);
      } catch (IOException $e) {
        throw (new OperationFailedException($filename.' cannot be copied to '.$destination.' ('.$e->message.')'));
      }
      
      return !$exists;
    }

    /**
     * Make a directory ("collection")
     *
     * @access  public
     * @param   string colname
     * @return  bool success
     * @throws  OperationFailedException
     * @throws  OperationNotAllowedException
     */
    public function mkcol($colname) {
      $f= new Folder($this->base.$colname);
      if ($f->exists()) {
        throw (new OperationFailedException($colname.' already exists'));
      }
      
      try {
        $f->create(0700);
      } catch (IOException $e) {
        throw (new OperationFailedException($colname.' cannot be created ('.$e->message.')'));
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
    public function delete($filename) {
      if (is_dir($this->base.$filename)) {
        $f= new Folder($this->base.$filename);
      } else {
        $f= new File($this->base.$filename);
      }
      
      // If the specified argument doesn't exist, raise an error
      if (!$f->exists()) {
        throw (new ElementNotFoundException($filename.' not found'));
      }
      
      try {
        $f->unlink();
      } catch (IOException $e) {
        throw (new OperationFailedException($filename.' cannot be deleted ('.$e->message.')'));
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
    public function put($filename, $data) {
      if (is_dir($this->base.$filename)) {
        throw (new OperationNotAllowedException($filename.' cannot be written (not a file)'));
      }
      
      // Open file and write contents
      $f= new File($this->base.$filename);
      try {
        $new= !$f->exists();
        $f->open(FILE_MODE_WRITE);
        $f->write($data);
        $f->close();
      } catch (IOException $e) {
        throw (new OperationFailedException($filename.' cannot be written to ('.$e->message.')'));
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
    public function get($filename) {
      if (is_dir($this->base.$filename)) {
        throw (new OperationNotAllowedException($filename.' cannot be retrieved using GET'));
      }
      
      // Open file and read contents
      $f= new File($this->base.$filename);
      $o= new WebdavObject(
        $f->filename,
        $f->uri,
        new Date($f->createdAt()),
        new Date($f->lastModified()),
        NULL,
        $f->size(),
        MimeType::getByFilename($f->uri, 'text/plain')
      );
      try {
        $f->open(FILE_MODE_READ);
        $o->setData($f->read($f->size()));
        $f->close();
      } catch (FileFoundException $e) {
        throw (new ElementNotFoundException($filename.' not found'));
      }
      
      return $o;
    }

    /**
     * Patch properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropPatcRequest request
     * @throws  OperationFailedException
     * @throws  ElementNotFoundException
     */
    public function proppatch(WebdavPropPatcRequest $request) {
      if (!is_a($request, 'WebdavPropPatchRequest')) {
        throw (new IllegalArgumentException(
          'Parameter request passed of wrong type ('.xp::typeOf($request).')'
        ));
      }
      
      $l= Logger::getInstance();
      $c= $l->getCategory();
      $c->debug('Properties to update for', $request->getFilename(), 'are', $request->getProperties());
      
      $realpath= $this->base.$request->getFilename();
      if (!file_exists($realpath)) {
        throw (new ElementNotFoundException($realpath.' not found'));
      }
      
      // Iterate over properties
      foreach ($request->getProperties() as $key => $val) {
        switch ($key) {
          case 'executable':
            if (FALSE === chmod($realpath, WebdavBool::fromString($val) ? 0700 : 0600)) {
              throw (new OperationFailedException('Cannot change executable flag of '.$realpath));
            }
            break;
            
          default:
            // return throw(new OperationFailedException('Cannot change executable flag of '.$realpath));
        }
      }
      
      return TRUE;
    }
    
    /**
     * Find properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     */
    public function propfind(WebdavPropFindRequest $request, WebdavMultistatus $response) {
      if (
        (!is_a($request, 'WebdavPropFindRequest')) ||
        (!is_a($response, 'WebdavMultistatus'))
      ) {
        throw (new IllegalArgumentException(
          'Parameters passed of wrong types (request: '.xp::typeOf($request).', response: '.xp::typeOf($response).')'
        ));
      }

      $l= Logger::getInstance();
      $c= $l->getCategory();
      $c->debug('Properties requested', $request->getProperties());
 
      try {
        self::_recurse(
          $request->getPath(),   
          $request->getWebroot(), 
          $response, 
          $request->getDepth()
        );
      } catch (XPException $e) {
        throw ($e);
      }
      
      return $response;
    }
  }
?>
