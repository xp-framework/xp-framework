<?php
/* This class is part of the XP framework
 *
 * $Id: BabelFish.class.php 10019 2007-04-17 10:02:08Z kiesel $
 */

  namespace net::xmethods;

  ::uses('webservice.soap.SoapDriver');
  
  // Language defines
  define('BABELFISH_LANG_ENGLISH',    'en');
  define('BABELFISH_LANG_GERMAN',     'de');
  define('BABELFISH_LANG_FRENCH',     'fr');
  define('BABELFISH_LANG_ITALIAN',    'it');
  define('BABELFISH_LANG_PORTUGESE',  'pt');
  define('BABELFISH_LANG_SPANISH',    'es');
  define('BABELFISH_LANG_RUSSIAN',    'ru');
  
  /**
   * Interface for AltaVista's Babelfish service.
   * 
   * Translates text of up to 150 words in length. For more information 
   * about the service, see the babelfish homepage.  
   * 
   * Example:
   * <code>
   *   $bf= new BabelFish();
   *   try {
   *     $translated= $bf->translate(
   *       'Babelfish übersetzt den Text', 
   *       BABELFISH_LANG_GERMAN,
   *       BABELFISH_LANG_ENGLISH
   *     );
   *   } catch(XPException $e) {
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
  class BabelFish extends lang::Object {
    public
      $client = NULL;
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->client= webservice::soap::SoapDriver::getInstance()->forEndpoint(
        'http://services.xmethods.net:80/perl/soaplite.cgi',
        'urn:xmethodsBabelFish'
      );
    }
    
    /**
     * Translates a text
     *
     * @param   string sourcedata The text to be translated.
     * @param   string src_lang Source language
     * @param   string target_lang Target language
     * @return  &string Translated text
     */
    public function translate($sourcedata, $src_lang, $target_lang) {
      $translated= $this->client->invoke(
        'BabelFish',
        new ('translationmode', sprintf('%s_%s', $src_lang, $target_lang)),
        new ('sourcedata', $sourcedata)
      );
      
      return $translated;
    }
  }
?>
