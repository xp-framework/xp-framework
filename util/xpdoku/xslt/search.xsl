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
  
  function relocateClass ($className) {
    relocate ('http://'.getenv('HTTP_HOST').'/apidoc/classes/'.$className.'.html');
  }

  function found (&$hits, $idx) {
    $hits[$idx]= isset($hits[$idx]) ? $hits[$idx]++ : 1;
  }
    
  $keyword= urldecode ($_REQUEST['keyword']);
  
  $classHits= array ();
  
  if (!empty($keyword)) {
  
    $keylower= strtolower ($keyword);
    $keypointless= str_replace ('.', '', strtolower ($keylower));
    $keysound= soundex ($keypointless);

    

    // Cycle through array to find matches
    foreach ($classes as $idx=> $fqClassName) {
      $className= strtolower (substr ($fqClassName, max (0, strrpos ($fqClassName, '.')+1)));
      
      // It's a (more or less) direct match
      if ($keylower == $className) {
        found($classHits, $idx);
        break;
      }

      // Exact match
      if ($keylower == strtolower ($fqClassName)) {
        found($classHits, $idx);
        break;
      }

      // Substring match
      if (strstr(strtolower ($fqClassName), $keylower)) {
        found($classHits, $idx);
      }

      // Soundex match
      if ($keysound == soundex ($className)) {
        found($classHits, $idx);
      }

      // Soundex match on complete classname
      if ($keysound == soundex (str_replace ('.', '', ($fqClassName)))) {
        found($classHits, $idx);
      }
    }
  }
    
  // One hit => direct jumping
  if (sizeof ($classHits) == 1) {
    relocateClass ($classes[key($classHits)]);
    exit;
  }
  
  $r= 'An error has occured.';

  if (sizeof ($classHits) == 0) {
    $r= '<b>Your search for "'.htmlspecialchars ($keyword).'" did not match any classes.</b>';
  }
  
  if (sizeof ($classHits) > 1) {
    $r= '<b>Your search for "'.htmlspecialchars ($keyword).'" returned multiple results:</b><br/>';
    $r.= '<ul>';
    
    asort($classHits);
    foreach ($classHits as $idx=> $hits) {
      $r.= sprintf(
        '<li><a href="/apidoc/classes/%1$s.html">%1$s</a></li>',
        $classes[$idx]
      );
    }
    
    $r.= '</ul>';
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
    <br/>
    <br/>
    <xsl:call-template name="divider"/>
    <br/>
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="content">
        To improve your search results, here you are a few tips:<br/>
        <ul>
          <li>Search is case insensitive.</li>
          <li>You can specify the class-name as you call the class in your
            scripts.
          </li>
          <li>You can specify the complete class-path and class-name (this is
            known as the fully qualified classname (fqcn)).
          </li>
          <li>The search takes advantage of the <tt>soundex</tt>-functions in
            PHP, so you can do prefix-searching (as the function only looks at
            the first few letters of your input) and even make a mistake
            entering your search.<br/>
            E.g. search will find <tt>rdbms.sybase.SPSybase</tt> if you type in
            <tt>spsübase</tt>.
          </li>
        </ul>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>
</xsl:stylesheet>
