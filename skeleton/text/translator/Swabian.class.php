<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * This class translates any word or sentence
   * from de_DE to de_SW.
   *
   * @see      http://www.schweikhardt.net/schwob
   * @purpose  Schwobify
   */
  class Swabian extends Object {
    /**
     * Translates the given sentence to schwobian.
     *
     * @model   static
     * @access  public
     * @param   string sentence
     * @return  string translation
     */  
    function translate($string) {
      $string= preg_replace ('/\b([Dd])a\b([^ß])/', '$1o$2', $string);
      $string= preg_replace ('/\bdann\b/', 'no', $string);
      $string= preg_replace ('/\bEs\b/', 'S', $string);
      $string= preg_replace ('/\bes\b/', 's', $string);
      $string= preg_replace ('/\beine([sm])\b/', 'oi$1', $string);
      $string= preg_replace ('/\bEine([sm])\b/', 'Oi$1', $string);
      $string= preg_replace ('/\b([DdMmSs])eine?\b/', '$1ei', $string);
      $string= preg_replace ('/\b([DdMmSs])eins\b/', '$1eis', $string);
      $string= preg_replace ('/\b([DdMmSs])einer\b/', '$1einr', $string);
      $string= preg_replace ('/\beine\b/', 'a', $string);
      $string= preg_replace ('/\bEine\b/', 'A', $string);
      $string= preg_replace ('/\beiner\b/', 'oinr', $string);
      $string= preg_replace ('/\bEiner\b/', 'Oinr', $string);
      $string= preg_replace ('/\b([Ee])inen\b/', '$1n', $string);        
      $string= preg_replace ('/\b([Dd])as/', '$1es', $string);           
      $string= preg_replace ('/\b[Ii]ch\b/', 'I', $string);              
      $string= preg_replace ('/\b([Nn])icht\b/', '$1ed', $string);       
      $string= preg_replace ('/\b([Ss])ie\b/', '$1e', $string);          
      $string= preg_replace ('/\bwir\b/', 'mir', $string);
      $string= preg_replace ('/\bWir\b/', 'Mir', $string);
      $string= preg_replace ('/\b(he)?([Rr])unter/', '$2a', $string);
      $string= preg_replace ('/\b([Hh])at\b/', '$1ott', $string);
      $string= preg_replace ('/\b([Hh])aben\b/', '$1enn', $string);
      $string= preg_replace ('/\b([Hh])abe\b/', '$1ann', $string);
      $string= preg_replace ('/\b([Gg])ehen\b/', '$1anga', $string);
      $string= preg_replace ('/\b([Kk])ann\b/', '$1a', $string);
      $string= preg_replace ('/\b([Kk])önnen\b/', '$1enna', $string);
      $string= preg_replace ('/\b([Ww])ollen\b/', '$1ella', $string);
      $string= preg_replace ('/\b([Ss])ollten\b/', '$1oddad', $string);
      $string= preg_replace ('/\b([Ss])ollt?e?\b/', '$1odd', $string);
      $string= preg_replace ('/\bdiese?r?\b/', 'sell', $string);
      $string= preg_replace ('/\bDiese?r?\b/', 'Sell', $string);
      $string= preg_replace ('/\b([Aa])uch\b/', '$1o', $string);        
      $string= preg_replace ('/\b([Nn])och\b/', '$1o', $string);        
      $string= preg_replace ('/\b([Ss])ind\b/', '$1end', $string);      
      $string= preg_replace ('/\b([Ss])chon\b/', '$1cho', $string);     
      $string= preg_replace ('/\b([Mm])an\b/', '$1r', $string);         
      $string= preg_replace ('/\b([Dd])ie\b/', '$1', $string);          
      $string= preg_replace ('/\b([Dd])a?rauf\b/', '$1ruff', $string);  
      $string= preg_replace ('/\bviele?s?\b/', 'en Haufa', $string);
      $string= preg_replace ('/\bViele?s?\b/', 'En Haufa', $string);
      $string= preg_replace ('/\bAuto|Daimler\b/', 'Heilix Blechle', $string);
      $string= preg_replace ('/Marmelade|Konfitüre/', 'Xälz', $string);
      $string= preg_replace ('/\b2\b/', 'zwoi', $string);
      $string= preg_replace ('/\b5\b/', 'fempf', $string);
      $string= preg_replace ('/\b15\b/', 'fuffzehn', $string);
      $string= preg_replace ('/\b50\b/', 'fuffzig', $string);

      // Am Wortanfang und Grossgeschriebenes:
      $string= preg_replace ('/\bAuf/', 'Uff', $string);
      $string= preg_replace ('/\bauf/', 'uff', $string);
      $string= preg_replace ('/\bEin/', 'Oi', $string);
      $string= preg_replace ('/\bein/', 'oi', $string);
      $string= preg_replace ('/\bMal/', 'Mol', $string);
      $string= preg_replace ('/\bUm/', 'Om', $string);
      $string= preg_replace ('/\bunge/', 'og', $string);
      $string= preg_replace ('/\bUnge/', 'Og', $string);
      $string= preg_replace ('/\bunver/', 'ovr', $string);
      $string= preg_replace ('/\bUnver/', 'Ovr', $string);
      $string= preg_replace ('/\bUn/', 'On', $string);
      $string= preg_replace ('/\bun/', 'on', $string);
      $string= preg_replace ('/\bUnd/', 'Ond', $string);
      $string= preg_replace ('/\bin(s?)/', 'en$1', $string);            
      $string= preg_replace ('/\bIn(s?)/', 'En$1', $string);            
      $string= preg_replace ('/\bim/', 'em', $string);
      $string= preg_replace ('/\bIm/', 'Em', $string);
      $string= preg_replace ('/\b([Kk])ein/', '$1oin', $string);
      $string= preg_replace ('/\b([Nn])ein/', '$1oi', $string);
      $string= preg_replace ('/\b([Zz])usa/', '$1a', $string);         

      // Am Wortende:
      $string= preg_replace ('/\Ben\b/', 'a', $string);                
      $string= preg_replace ('/\Bel\b/', 'l', $string);                
      $string= preg_replace ('/([^h])er\b/', '$1r', $string);          
      $string= preg_replace ('/([h])es\b/', '$1s', $string);           
      $string= preg_replace ('/\Bau\b/', 'ao', $string);               
      $string= preg_replace ('/([lt])ein\b/', '$1oi', $string);        

      // Beliebige Position:
      $string= preg_replace ('/([Ff])rag/', '$1rog', $string);
      $string= preg_replace ('/teil/', 'doil', $string);
      $string= preg_replace ('/Teil/', 'Doil', $string);
      $string= preg_replace ('/([Hh])eim/', '$1oim', $string);
      $string= preg_replace ('/steht/', 'stoht', $string);
      $string= preg_replace ('/um/', 'om', $string);
      $string= preg_replace ('/imm/', 'emm', $string);
      $string= preg_replace ('/mal/', 'mol', $string);
      $string= preg_replace ('/zwei/', 'zwoi', $string);
      $string= preg_replace ('/ck/', 'gg', $string);
      $string= preg_replace ('/([Ee])u/', '$1i', $string);
      $string= preg_replace ('/([Vv])er/', '$1r', $string);
      $string= preg_replace ('/([Gg])e([aflmnrs])/', '$1$2', $string); 
      $string= preg_replace ('/([Ss])t/', '$1chd', $string);           
      $string= preg_replace ('/([Ss])p/', '$1chb', $string);           
      $string= preg_replace ('/tio/', 'zio', $string);                 
      $string= preg_replace ('/\?/', ', ha?', $string);
      $string= preg_replace ('/!!/', ', Sagg Zemend!', $string);
      $string= preg_replace ('/!/', ', haidanai!', $string);

      // Spezielles:
      $string= strtr ($string, 'TtPpÖöÜü', 'DdBbEeIi');     
      
      // Was nach tr stehen muss:
      $string= preg_replace ('/ung/', 'ong', $string);
      $string= preg_replace ('/und/', 'ond', $string);
      $string= preg_replace ('/ind/', 'end', $string);
      return $string;
    }
  }
?>
