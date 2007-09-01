<?php
/* This class is part of the XP framework
 *
 * $Id: Swabian.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::schweikhardt;

  ::uses('net.schweikhardt.Translator');

  /**
   * This class translates any word or sentence from de_DE to de_SW.
   *
   * Example:
   * <code>
   *   uses('net.schweikhardt.Swabian');
   *
   *   $translated= Swabian::translate($text);
   * </code>
   *
   * @see      http://www.schweikhardt.net/schwob
   * @purpose  Schwobify
   */
  class Swabian extends lang::Object implements Translator {

    /**
     * Translates the given sentence to schwobian.
     *
     * @param   string sentence
     * @return  string translation
     */  
    public static function translate($string) {
      static $tr= array(    // Translations array (pattern => replacement)
        '/\b([Dd])a\b([^ß])/'    => '$1o$2',
        '/\bdann\b/'             => 'no',
        '/\bEs\b/'               => 'S',
        '/\bes\b/'               => 's',
        '/\beine([sm])\b/'       => 'oi$1',
        '/\bEine([sm])\b/'       => 'Oi$1',
        '/\b([DdMmSs])eine?\b/'  => '$1ei',
        '/\b([DdMmSs])eins\b/'   => '$1eis',
        '/\b([DdMmSs])einer\b/'  => '$1einr',
        '/\beine\b/'             => 'a',
        '/\bEine\b/'             => 'A',
        '/\beiner\b/'            => 'oinr',
        '/\bEiner\b/'            => 'Oinr',
        '/\b([Ee])inen\b/'       => '$1n',
        '/\b([Dd])as/'           => '$1es',
        '/\b[Ii]ch\b/'           => 'I',
        '/\b([Nn])icht\b/'       => '$1ed',
        '/\b([Ss])ie\b/'         => '$1e',
        '/\bwir\b/'              => 'mir',
        '/\bWir\b/'              => 'Mir',
        '/\b(he)?([Rr])unter/'   => '$2a',
        '/\b([Hh])at\b/'         => '$1ott',
        '/\b([Hh])aben\b/'       => '$1enn',
        '/\b([Hh])abe\b/'        => '$1ann',
        '/\b([Gg])ehen\b/'       => '$1anga',
        '/\b([Kk])ann\b/'        => '$1a',
        '/\b([Kk])önnen\b/'      => '$1enna',
        '/\b([Ww])ollen\b/'      => '$1ella',
        '/\b([Ss])ollten\b/'     => '$1oddad',
        '/\b([Ss])ollt?e?\b/'    => '$1odd',
        '/\bdiese?r?\b/'         => 'sell',
        '/\bDiese?r?\b/'         => 'Sell',
        '/\b([Aa])uch\b/'        => '$1o',
        '/\b([Nn])och\b/'        => '$1o',
        '/\b([Ss])ind\b/'        => '$1end',
        '/\b([Ss])chon\b/'       => '$1cho',
        '/\b([Mm])an\b/'         => '$1r',
        '/\b([Dd])ie\b/'         => '$1',
        '/\b([Dd])a?rauf\b/'     => '$1ruff',
        '/\bviele?s?\b/'         => 'en Haufa',
        '/\bViele?s?\b/'         => 'En Haufa',
        '/\bAuto|Daimler\b/'     => 'Heilix Blechle',
        '/Marmelade|Konfitüre/'  => 'Xälz',
        '/ließ/'                 => 'g\'losse hät',
        '/\b2\b/'                => 'zwoi',
        '/\b5\b/'                => 'fempf',
        '/\b15\b/'               => 'fuffzehn',
        '/\b50\b/'               => 'fuffzig',
        '/\bAuf/'                => 'Uff',
        '/\bauf/'                => 'uff',
        '/\bEin/'                => 'Oi',
        '/\bein/'                => 'oi',
        '/\bMal/'                => 'Mol',
        '/\bUm/'                 => 'Om',
        '/\bunge/'               => 'og',
        '/\bUnge/'               => 'Og',
        '/\bunver/'              => 'ovr',
        '/\bUnver/'              => 'Ovr',
        '/\bUn/'                 => 'On',
        '/\bun/'                 => 'on',
        '/\bUnd/'                => 'Ond',
        '/\bin(s?)/'             => 'en$1',
        '/\bIn(s?)/'             => 'En$1',
        '/\bim/'                 => 'em',
        '/\bIm/'                 => 'Em',
        '/\b([Kk])ein/'          => '$1oin',
        '/\b([Nn])ein/'          => '$1oi',
        '/\b([Zz])usa/'          => '$1a',
        '/\Ben\b/'               => 'a',
        '/\Bel\b/'               => 'l',
        '/([^h])er\b/'           => '$1r',
        '/([h])es\b/'            => '$1s',
        '/\Bau\b/'               => 'ao',
        '/([lt])ein\b/'          => '$1oi',
        '/([Ff])rag/'            => '$1rog',
        '/teil/'                 => 'doil',
        '/Teil/'                 => 'Doil',
        '/([Hh])eim/'            => '$1oim',
        '/steht/'                => 'stoht',
        '/um/'                   => 'om',
        '/imm/'                  => 'emm',
        '/mal/'                  => 'mol',
        '/zwei/'                 => 'zwoi',
        '/ck/'                   => 'gg',
        '/([Ee])u/'              => '$1i',
        '/([Vv])er/'             => '$1r',
        '/([Gg])e([aflmnrs])/'   => '$1$2',
        '/([Ss])t/'              => '$1chd',
        '/([Ss])p/'              => '$1chb',
        '/tio/'                  => 'zio',
        '/\?/'                   => ', ha?',
        '/!!/'                   => ', Sagg Zemend!',
        '/!/'                    => ', haidanai!'
      );
      static $sr= array(    // Special replacements to be executed after the first phase
        'T'                      => 'D',   
        't'                      => 'd',   
        'P'                      => 'B',   
        'p'                      => 'b',   
        'Ö'                      => 'E',   
        'ö'                      => 'e',   
        'Ü'                      => 'I',   
        'ü'                      => 'i',   
        'ung'                    => 'ong',
        'und'                    => 'ond', 
        'ind'                    => 'end'  
      );
      
      return strtr(preg_replace(array_keys($tr), array_values($tr), $string), $sr);
    }
  } 
?>
