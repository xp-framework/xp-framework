<?php
/* This class is part of the XP framework
 *
 * $Id: ErrorDocument.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace scriptlet;
  uses('io.File');
  
  /**
   * Base class representing an error document
   *
   * Example:
   * <code>
   *   uses('foo.bar.MyScriptlet');
   *
   *   $s= &new MyScriptlet();
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *     $response= &$e->getResponse(); 
   *
   *     // Make a nicer errordocument and hide the real message
   *     $d= &new ErrorDocument(
   *       $response->statusCode, 
   *       'en_US'
   *     );
   *     $response->setContent($d->getContent());
   *   }
   * 
   *   $response->sendHeaders();
   *   $response->sendContent();
   * 
   *   $s->finalize();
   * </code> 
   *
   * Files used with this class may contain the string
   * <pre><xp:value-of select="reason"/></pre> where the error
   * message is filled in. You will find a couple of predefined
   * error documents in the <pre>static/</pre> subdirectory relative
   * to the location of this file.
   *
   * Be aware that on production servers it might *not* be a good
   * idea to display the full contents of errormessages as they might
   * contain details not intended for the outside world.
   *
   * @see scriptlet.HttpScriptlet
   */
  class ErrorDocument extends lang::Object {
    public 
      $statusCode,
      $language,
      $message,
      $filename;
    
    /**
     * Constructor
     *
     * @param   int statusCode
     * @param   string language
     * @param   string message default ''
     * @param   string filename default ''
     */
    public function __construct($statusCode, $language, $message= '', $filename= '') {
      $this->statusCode= $statusCode;
      $this->language= $language;
      $this->message= $message;
      $this->filename= (empty($filename) 
        ? dirname(__FILE__).'/static/'.$this->language.'/error'.$this->statusCode.'.html'
        : $filename
      );
      
    }
    
    /**
     * Retrieve contents of errordocument
     *
     * @return  string content
     */
    public function getContent() {
      $f= new io::File($this->filename);
      try {
        $f->open(FILE_MODE_READ);
        $contents= $f->read($f->size());
        $f->close();
      } catch (::Exception $e) {
        $this->message.= $e->toString();
        $contents= '<xp:value-of select="reason"/>';
      }

      return str_replace(
        '<xp:value-of select="reason"/>', 
        $this->message, 
        $contents
      );
    }
  }
?>
