<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:rss="http://my.netscape.com/rdf/simple/0.9/"
  exclude-result-prefixes="rdf dc rss"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  <xsl:param name="mode" select="'resources'"/>
  <xsl:include href="xsl-helper.xsl"/>
   

  <xsl:template name="navigation">
    
    Resources.
        
  </xsl:template>

  <xsl:template name="listfiles">
    <xsl:param name="filter"/>
    <xsl:processing-instruction name="php"><![CDATA[
      $releases= array();
      $dir= dir(getenv('DOCUMENT_ROOT').'/downloads/');
      while ($e= $dir->read()) {
        if (]]><xsl:value-of select="$filter"/><![CDATA[) continue;
        $releases[]= $e;
      }
      rsort($releases);
      for ($i= 0, $s= min(sizeof($releases), 5); $i < $s; $i++) {
        $md5= (file_exists($dir->path.'/'.$releases[$i].'.md5')
          ? file_get_contents($dir->path.'/'.$releases[$i].'.md5')
          : 'n/a'
        );
        $information= (file_exists($dir->path.'/'.$releases[$i].'.info')
          ? '<ul>'.str_replace("\n* ", "<li>", "\n".file_get_contents($dir->path.'/'.$releases[$i].'.info')).'</ul>'
          : ''
        );
        printf(
          '<li><a href="/downloads/%1$s">%1$s</a> - %2$.2f KB [ MD5: %3$s ]</a><br>%4$s<br></li>',
          $releases[$i],
          filesize($dir->path.'/'.$releases[$i]) / 1024,
          $md5,
          $information
        );
      }
      
      $dir->close();
    ]]></xsl:processing-instruction>
  </xsl:template>

  <xsl:template match="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left">Resources</th>
        <td valign="top" align="right" style="color: #666666">(Ports)</td>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    
    <!-- Introduction -->
    <p style="line-height: 16px">
      <xsl:apply-templates select="introduction"/>
    </p>
    
    <b>Anonymous CVS:</b>
    <pre>
  cvs -d:pserver:anonymous@php3.de:/home/cvs/repositories/xp co .
  (Password is empty)
    </pre>
    
    <b>Current releases:</b>
    <ul>
      <xsl:call-template name="listfiles">
        <xsl:with-param name="filter"><![CDATA[
          'tar.gz' != substr($e, -6) || 'current' == substr($e, 3, 7)
        ]]></xsl:with-param>
      </xsl:call-template>
    </ul>

    <b>Patches:</b>
    <ul>
      <xsl:call-template name="listfiles">
        <xsl:with-param name="filter"><![CDATA[
          'diff' != substr($e, -4)
        ]]></xsl:with-param>
      </xsl:call-template>
    </ul>

  </xsl:template>

  <xsl:template match="introduction//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
</xsl:stylesheet>
