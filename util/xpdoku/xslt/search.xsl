<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  
  <xsl:param name="collection"/>
  <xsl:param name="package"/>
  <xsl:param name="mode" select="'search'"/>

  <!-- Include main window part -->
  <xsl:include href="xsl-helper.xsl"/>

  <xsl:template name="search">
    <xsl:processing-instruction name="php">
    
      <!-- Supply a list of all known classes to the script
           classnames must be in array $classes -->

      $classes= array ();     
      <xsl:for-each select="//class">
        $classes[]= '<xsl:value-of select="./@className"/>';
      </xsl:for-each>
      
      <!-- Now the rest of the script -->
      <![CDATA[
      
  function relocate ($url) {
    if (!headers_sent()) {
      header ('Location: '.$url);
    } else {
      echo '<a href="'.$url.'">Relocate</a>';
    }
    exit;
  }
  
  $keyword= urldecode ($_REQUEST['keyword']);
  $keylower= strtolower ($keyword);
  $keypointless= str_replace ('.', '', strtolower ($keypointless));
  $keysound= soundex ($keypointless);
  
  $classHits= array ();
  
  // Cycle through array to find matches
  foreach ($classes as $idx=> $fqClassName) {
    $className= substr ($fqClassName, max (0, strrpos ($fqClassName, '.')+1));
    if ($keylower == strtolower (basename ($className))) {
      // It's a match
      $classHits[]= $idx;
    }
    
    if ($keylower == strtolower ($fqClassName)) {
      // Exact match
      $classHits[]= $idx;
      
      // Direct relocate?
    }

    if ($keysound == soundex (str_replace ('.', '', strtolower ($fqClassName)))) {
      // Soundex match
      $classHits[]= $idx;
    }
  }
  
  // One hit => direct jumping
  if (count ($classHits) == 1) {
    relocate ('/classes/'.$classes[$classHits[0]].'.html');
    exit;
  }
  
  $r= 'An error has occured.';

  if (count ($classHits) == 0) {
    $r= '<b>Your search for '.htmlspecialchars ($keyword).' did not match any classes.</b>';
  }
  
  if (count ($classHits) > 1) {
    $r= '<b>Your search for '.htmlspecialchars ($keyword).' did match multiple classes:</b><br/>';
    $r.= '<ul>';
    
    foreach ($classHits as $idx) {
      $r.= '<li><a href="/classes/'.$classes[$idx].'.html">'.$classes[$idx].'</a></li>';
    }
  }
  
  // Result gets displayed below...
      ]]>
    </xsl:processing-instruction>
  </xsl:template>
  
  <xsl:template name="searchresults">
    <xsl:processing-instruction name="php">
      <![CDATA[
        echo $r;
      ]]>
    </xsl:processing-instruction>
  </xsl:template>
</xsl:stylesheet>
