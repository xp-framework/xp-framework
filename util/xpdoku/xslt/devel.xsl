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
    
    Development.
        
  </xsl:template>

  <xsl:template match="main">
    
    <!-- Introduction -->
    <xsl:for-each select="teaser">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <th valign="top" align="left">Development: <xsl:value-of select="caption"/></th>
          <td valign="top" align="right" style="color: #666666"><xsl:value-of select="extra"/></td>
	    </tr>
	    <tr bgcolor="#cccccc">
          <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
        </td></tr>
      </table>
      <p style="line-height: 16px">
        <xsl:apply-templates select="text"/>
      </p>
      <xsl:for-each select="more">
        <a href="{@href}">
          <xsl:choose>
            <xsl:when test="not(./text())">Read more...</xsl:when>
            <xsl:otherwise><xsl:value-of select="./text()"/></xsl:otherwise>
          </xsl:choose>
        </a>
        <xsl:if test="position() &lt; last()"> | </xsl:if>
      </xsl:for-each>
      <br/><br/><br/>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="text//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
</xsl:stylesheet>
