<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:rss="http://my.netscape.com/rdf/simple/0.9/"
  exclude-result-prefixes="rdf dc rss"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  <xsl:param name="mode" select="'index'"/>
  <xsl:include href="xsl-helper.xsl"/>

  <xsl:template name="navigation">
    <xsl:for-each select="/main/references/ref">
      <a href="/content/{@link}.html"><xsl:value-of select="."/></a>
    </xsl:for-each>
  </xsl:template>
  
  <xsl:template match="main/content">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <th valign="top" align="left"><xsl:value-of select="title"/></th>
        <td valign="top" align="right">(<xsl:value-of select="editor"/>)</td>
	  </tr>
	  <tr bgcolor="#cccccc">
        <td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/>
      </td></tr>
    </table>
    <br/>
    
    <xsl:apply-templates select="para"/>
    
  </xsl:template>
  
  <xsl:template match="main/content/para">
    <b><xsl:value-of select="caption"/></b><br/>
    <xsl:call-template name="divider"/>
    <br/>
    
    <xsl:apply-templates select="text"/>
    
    <br/><br/>
  </xsl:template>

  <xsl:template match="code">
    <br/><br/>
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="content">
        <code><pre style="display: block"><xsl:apply-templates/></pre></code>
      </xsl:with-param>
    </xsl:call-template>
    <br/>
  </xsl:template>

</xsl:stylesheet>
