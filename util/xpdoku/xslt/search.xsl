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
    relocate ('/classes/'.$className.'.html');
  }
  
  $keyword= urldecode ($_REQUEST['keyword']);
  $keylower= strtolower ($keyword);
  $keypointless= str_replace ('.', '', strtolower ($keylower));
  $keysound= soundex ($keypointless);
  
  $classHits= array ();
  
  // Cycle through array to find matches
  foreach ($classes as $idx=> $fqClassName) {
    $className= strtolower (substr ($fqClassName, max (0, strrpos ($fqClassName, '.')+1)));
    if ($keylower == $className) {
      // It's a (more or less) direct match
      $classHits[]= $idx;
      
      relocateClass ($fqClassName);
    }
    
    if ($keylower == strtolower ($fqClassName)) {
      // Exact match
      $classHits[]= $idx;
      
      relocateClass ($fqClassName);
    }

    if ($keysound == soundex ($className)) {
      // Soundex match
      $classHits[]= $idx;
    }
    
    if ($keysound == soundex (str_replace ('.', '', ($fqClassName)))) {
      // Soundex match on complete classname
      $classHits[]= $idx;
    }
  }
  
  // One hit => direct jumping
  if (count ($classHits) == 1) {
    relocateClass ($classes[$classHits[0]]);
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
