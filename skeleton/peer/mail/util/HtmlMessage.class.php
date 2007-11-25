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
    public
      $html         = NULL,
      $text         = NULL;
    
    public
      $_loaders     = array(),
      $_prepared    = FALSE;
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      parent::__construct(-1);
      
      // Construct multipart and set content type accordingly
      with ($multi= $this->addPart(new MultiPart())); {
        $this->text= $multi->addPart(new MimePart('', 'text/plain'));
        $this->text->setDisposition(MIME_DISPOSITION_INLINE);
        $this->html= $multi->addPart(new MimePart('', 'text/html'));
        $this->html->setDisposition(MIME_DISPOSITION_INLINE);
      }
      $this->setContentType('multipart/related; type="multipart/alternative"');
      
      // Register file:// - loader
      $this->registerLoader('file', new FilesystemImageLoader());
    }
    
    /**
     * Add an image
     *
     * @param   string data raw image data
     * @param   string contentType  
     * @return  string content id
     */
    public function addImage($data, $contentType) {
      with ($image= $this->addPart(new MimePart())); {
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
     * Register an image loader for a specified scheme
     *
     * @param   string scheme
     * @param   peer.mail.util.ImageLoader loader
     * @return  peer.mail.util.ImageLoader
     */
    public function registerLoader($scheme, $loader) {
      $this->_loaders[$scheme]= $loader;
      return $loader;
    }
    
    /**
     * Loads an image from a given URL
     *
     * @param   peer.URL source
     * @return  string content id
     * @throws  lang.MethodNotImplementedException in case no loader is present
     * @throws  lang.Throwable
     */
    public function loadImage($source) {
      $scheme= $source->getScheme('file');
      if (!isset($this->_loaders[$scheme])) {
        throw(new MethodNotImplementedException(
          'Unhandled scheme',
          $scheme
        ));
      }
      
      list($data, $contentType)= $this->_loaders[$scheme]->load($source);
      return $this->addImage($data, $contentType);
    }
    
    /**
     * Prepare HTML part. This goes through all the images, loading them
     * as necessary and rewriting the image tags contained within the
     * HTML sourcecode to reference the MIME parts created.
     *
     * @throws  lang.Throwable
     */
    public function prepare() {
      if ($this->_prepared) return;

      // Find images references
      preg_match_all(
        '/<(table|tr|td|img|input)[^>]+(src|background)=["\']([^"\']+)["\']/i', 
        $this->html->body, 
        $images
      );
      preg_match_all(
        '/(background-image:) *(url)\(([^\)]+)\)/i', 
        $this->html->body, 
        $css
      );

      $matches= array();      
      foreach (array($images, $css) as $data) {
        foreach ($data as $key => $values) {
          foreach ($values as $value) {
            $matches[$key][]= $value;
          }
        }
      }

      $images= array();
      foreach ($matches[3] as $i => $uri) {
        if (isset($images[$uri])) {
          $cid= $images[$uri];
        } else {
          $cid= $this->loadImage(new URL($uri));
        }
        
        $this->html->body= str_replace(
          $matches[0][$i],
          str_replace($uri, 'cid:'.$cid, $matches[0][$i]),
          $this->html->body
        );
        $images[$uri]= $cid;
      }
      $this->_prepared= TRUE;
    }

    /**
     * Return headers as string
     *
     * @return  string headers
     * @throws  lang.Throwable if prepare() fails
     */
    public function getHeaderString() {
      $this->prepare();        
      return parent::getHeaderString();
    }
  }
?>
