<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  <xsl:param name="mode" select="'inheritance'"/>
  <xsl:include href="xsl-helper.xsl"/>
  
  <xsl:template match="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <th valign="top" align="left">API Doc: Inheritance tree <a href="/apidoc/"><img src="/image/caret-t.gif" border="0"/></a></th>
	  </tr>
	  <tr bgcolor="#cccccc"><td><img src="/image/spacer.gif" height="1" border="0"/></td></tr>
    </table>
    <br/>

    <xsl:apply-templates/>
  </xsl:template>
  
  <xsl:template match="class">
    <ul>
      <li><a href="/apidoc/classes/{@name}.html"><xsl:value-of select="@name"/></a></li>
      <xsl:apply-templates/>
    </ul>
  </xsl:template>

</xsl:stylesheet>
