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
      
        public function setFile(File $file) {
          $this->filename= $file->getURI();
          $this->setContentType(MimeType::getByFileName($this->filename));
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
      $path= $request->getQueryString();
      $prop= PropertyManager::getInstance()->getProperties('storage');
      $response->setFile(new File($prop->readString('storage', 'base'), strtr($path, array(
        ','   => DIRECTORY_SEPARATOR, 
        '..'  => ''
      ))));
    }
  }
  // }}} 
  
  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure('../../etc/');
  
  scriptlet::run(new PipeScriptlet());
  // }}}  
?>
