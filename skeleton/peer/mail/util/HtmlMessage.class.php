<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.MethodNotImplementedException',
    'peer.mail.util.FilesystemImageLoader',
    'peer.mail.MimeMessage', 
    'peer.URL'
  );

  /**
   * HTML message (create only)
   *
   * @see      xp://peer.mail.MimeMessage
   * @purpose  Specialized mime message
   */
  class HtmlMessage extends MimeMessage {
    var
      $html         = NULL,
      $text         = NULL;
    
    var
      $_loaders     = array(),
      $_prepared    = FALSE;
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct(-1);
      
      // Construct multipart and set content type accordingly
      with ($multi= &$this->addPart(new MultiPart())); {
        $this->text= &$multi->addPart(new MimePart('This is a HTML message', 'text/plain'));
        $this->text->setDisposition(MIME_DISPOSITION_INLINE);
        $this->html= &$multi->addPart(new MimePart('', 'text/html'));
        $this->html->setDisposition(MIME_DISPOSITION_INLINE);
      }
      $this->setContentType('multipart/related; type="multipart/alternative"');
      
      // Register file:// - loader
      $this->registerLoader('file', new FilesystemImageLoader());
    }
    
    /**
     * Add an image
     *
     * @access  protected
     * @param   string data raw image data
     * @param   string contentType  
     * @return  string content id
     */
    function addImage($data, $contentType) {
      with ($image= &$this->addPart(new MimePart())); {
        $image->setDisposition(MIME_DISPOSITION_INLINE);
        $image->setEncoding(MIME_ENC_BASE64);
        $image->setFilename(NULL);
        $image->setName(NULL);
        $image->setBody(chunk_split(base64_encode($data)), TRUE);
        $image->setContentType($contentType);
        $image->charset= '';
        $image->generateContentId();
      }

      return $image->getContentId();
    }
    
    /**
     * Register an image loader for a specified theme
     *
     * @access  public
     * @param   string scheme
     * @param   &peer.mail.util.ImageLoader loader
     * @return  &peer.mail.util.ImageLoader
     */
    function &registerLoader($scheme, &$loader) {
      $this->_loaders[$scheme]= &$loader;
      return $loader;
    }
    
    /**
     * Loads an image from a given URL
     *
     * @access  protected
     * @param   &peer.URL source
     * @return  string content id
     * @throws  lang.MethodNotImplementedException in case no loader is present
     * @throws  lang.Throwable
     */
    function loadImage(&$source) {
      $scheme= $source->getScheme('file');
      if (!isset($this->_loaders[$scheme])) {
        return throw(new MethodNotImplementedException(
          'Unhandled scheme',
          $scheme
        ));
      }
      
      try(); {
        list($data, $contentType)= $this->_loaders[$scheme]->load($source);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
        
      return $this->addImage($data, $contentType);
    }
    
    /**
     * Prepare HTML part
     *
     * @access  public
     * @throws  lang.Throwable
     */
    function prepare() {
      if ($this->_prepared) return;

      preg_match_all(
        '/<(td|img|input)[^>]+(src|background)=["\']([^"\']+)["\']/i', 
        $this->html->body, 
        $images
      );
      preg_match_all(
        '/(background-image:) *(url)\(([^\)]+)\)/i', 
        $this->html->body, 
        $css
      );
      
      $matches= array_merge($images, $css);
      
      foreach ($matches[3] as $i => $uri) {
        try(); {
          $cid= $this->loadImage(new URL($uri));
        } if (catch('Throwable', $e)) {
          return throw($e);
        }
        
        $this->html->body= str_replace(
          $matches[0][$i],
          str_replace($uri, 'cid:'.$cid, $matches[0][$i]),
          $this->html->body
        );
      }
      $this->_prepared= TRUE;
    }

    /**
     * Return headers as string
     *
     * @access  public
     * @return  string headers
     * @throws  lang.Throwable
     */
    function getHeaderString() {
      $this->prepare();        
      return parent::getHeaderString();
    }
  }
?>
