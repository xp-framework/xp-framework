<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.soap.SOAPClient',
    'xml.soap.transport.SOAPHTTPTransport'
  );

  /**
   * Interface for AltaVista's Babelfish service.
   * 
   * Translates text of up to 150 words in length. For more information 
   * about the service, see the babelfish homepage.  
   * 
   * Example:
   * <code>
   *   $bf= new BabelFish();
   *   try(); {
   *     $translated= $bf->translate(
   *       'Babelfish übersetzt den Text', 
   *       BABELFISH_LANG_GERMAN,
   *       BABELFISH_LANG_ENGLISH
   *     );
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   * 
   *   var_dump($translated);
   * </code>
   * 
   * @see      http://xmethods.net/ve2/ViewListing.po?serviceid=14
   * @purpose  SOAP-Proxy
   */
  class BabelFish extends SOAPClient {
    const
      LANG_ENGLISH = 'en',
      LANG_GERMAN = 'de',
      LANG_FRENCH = 'fr',
      LANG_ITALIAN = 'it',
      LANG_PORTUGESE = 'pt',
      LANG_SPANISH = 'es',
      LANG_RUSSIAN = 'ru';


    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      parent::__construct(
        new SOAPHTTPTransport('http://services.xmethods.net:80/perl/soaplite.cgi'),
        'urn:xmethodsBabelFish'
      );
    }
    
    /**
     * Translates a text
     *
     * @access  public
     * @param   string sourcedata The text to be translated.
     * @param   string src_lang Source language
     * @param   string target_lang Target language
     * @return  string Translated text
     */
    public function translate($sourcedata, $src_lang, $target_lang) {
      $translated= self::invoke(
        'BabelFish',
        new SOAPNamedItem('translationmode', sprintf('%s_%s', $src_lang, $target_lang)),
        new SOAPNamedItem('sourcedata', $sourcedata)
      );
      
      return $translated;
    }
  }
?>
