<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.webdav.impl.DavImpl',
    'io.Folder',
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
     */
    function _recurse($path, $root, &$response, $maxdepth) {
      $l= &Logger::getInstance();
      $c= &$l->getCategory();
      
      $realpath= $this->base.$path;

      if (is_dir($realpath)) {
        $c->debug('DIRECTORY', $path);
        
        $f= &new Folder($realpath);
        $response->addEntry(
          basename($path),
          $root.$path,
          new Date(filectime($f->uri)),
          new Date(filemtime($f->uri)),
          WEBDAV_COLLECTION
        );
        $maxdepth--;
        while ($maxdepth >= 0 && $entry= $f->getEntry()) {
          $c->debug('RECURSE', $path.$entry);
          
          $this->_recurse($path.$entry, $root, $response, $maxdepth);
        }
        $f->close();
        return;
      }
      
      $c->debug('FILE', $realpath);
      $response->addEntry(
        basename($path),
        $root.$path,
        new Date(filectime($realpath)),
        new Date(filemtime($realpath)),
        NULL,
        filesize($realpath),
        MimeType::getByFilename($path)
      );
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
