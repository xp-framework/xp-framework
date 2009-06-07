<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * GNU Gettext
   *
   * <code>
   *   $g= GetText::bind('greetings', './locale/');
   *   $g->setLanguage('de_DE');
   *   var_dump($g->get('str_hello'));
   *
   *   // Override default language
   *   var_dump($g->get('str_hello', 'fr_FR'));
   * </code>
   *
   * The files for this example reside in:
   * <pre>
   * ./locale/de_DE/LC_MESSAGES/greetings.mo
   * ./locale/de_DE/LC_MESSAGES/greetings.po
   * ./locale/fr_FR/LC_MESSAGES/greetings.mo
   * ./locale/fr_FR/LC_MESSAGES/greetings.po
   * </pre>
   *
   * The .po-files are the "sourcecode", the .mo-files are generated
   * by calling the command line tool "msgfmt" and specifying the .po-
   * file as parameter.
   * 
   * Example (./locale/de_DE/LC_MESSAGES/greetings.po):
   * <pre>
   * # German po for test
   * # Copyright (C) 2002
   * # Timm Friebe <thekid@thekid.de>
   * #
   * msgid ""
   * msgstr ""
   * "Project-Id-Version: test 1.0\n"
   * "POT-Creation-Date: 2002-05-06 14:32-0400\n"
   * "PO-Revision-Date: 2001-12-04 20:48+0100\n"
   * "Last-Translator:  Timm Friebe <thekid@thekid.de>\n"
   * "Language-Team: German <test-de@thekid.de>\n"
   * "MIME-Version: 1.0\n"
   * "Content-Type: text/plain; charset=iso-8859-1\n"
   * "Content-Transfer-Encoding: 8bit\n"
   * 
   * #: test:1
   * msgid "str_hello"
   * msgstr "Hallo"
   * </pre>
   *
   * @purpose  Provide an API to GNU gettext
   * @ext      gettext
   * @see      http://www.gnu.org/software/gettext/
   * @see      http://www.gnu.org/manual/gettext/index.html
   */
  class GetText extends Object {
    public
      $domain    = '',
      $directory = '',
      $lang      = '';
      
    /**
     * Sets path for text domain
     *
     * @param   string domain
     * @param   string directory
     * @return  org.gnu.GetText
     */
    public static function bind($domain, $directory) {
      static $g= array();
      
      $idx= $domain.'@'.$directory;
      if (!isset($g[$idx])) {
        $g[$idx]= new GetText();
        $g[$idx]->domain= $domain;
        $g[$idx]->directory= $directory;
        bindtextdomain($domain, $directory);
      }
      return $g[$idx];
    }
    
    /**
     * Set language
     *
     * @param   string lang
     */
    public function setLanguage($lang) {
      textdomain($this->domain);
      $this->lang= $lang;
      putenv('LANG='.$lang);
    }
    
    /**
     * Get text. The id is returned in case there is no string matching
     * the given id
     *
     * @param   string id
     * @param   string lang default NULL
     * @return  string
     */
    public function get($id, $lang= NULL) {
      static $l= NULL;
      
      if (NULL == $lang) $lang= $this->lang;
      if ($l != $lang) {
        $l= $lang;
        $this->setLanguage($lang);
      }
      return _($id);
    }
  }
?>
