<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
 
  enum Language {
    German("de_DE"), 
    BritishEnglish("en_UK"), 
    AmericanEnglish("en_US"),
    French("fr_FR")
  }

  for ($i= 0; $i < Language::size(); $i++) {
    var_dump(Language::valueAt($i));
  }
