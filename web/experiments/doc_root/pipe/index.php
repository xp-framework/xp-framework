<?php
/* This file is part of the XP framework website
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production', 'cgi');
  uses('util.PropertyManager', 'scriptlet.HttpScriptlet', 'io.File', 'util.MimeType');
  
  // {{ class PipeScriptlet
  class PipeScriptlet extends HttpScriptlet {

    protected function _response() {
      return newinstance('scriptlet.HttpScriptletResponse', array(), '{
        protected
          $filename= NULL;
      
        public function setFile(File $file, $mime= NULL) {
          $this->filename= $file->getURI();
          $this->setContentType($mime ? $mime : MimeType::getByFileName($this->filename));
          $this->setHeader("Content-Disposition", "attachment; filename=\"".basename($this->filename)."\"");
          $this->setContentLength($file->size());
        }
                
        public function sendContent() {
          if ($this->filename) {
            readfile($this->filename);
            return;
          }
          parent::sendContent();
        }
      }');
    }
  
    public function doGet($request, $response) {
      sscanf($request->getQueryString(), "%[^:]:%s", $path, $mime);
      $prop= PropertyManager::getInstance()->getProperties('storage');
      $file= new File($prop->readString('storage', 'base'), strtr($path, array(
        ','   => DIRECTORY_SEPARATOR, 
        '..'  => ''
      )));
      $response->setFile($file, $mime);
    }
  }
  // }}} 
  
  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure('../../etc/');
  
  scriptlet::run(new PipeScriptlet());
  // }}}  
?>
