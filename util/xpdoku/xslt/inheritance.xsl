<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  <xsl:param name="mode" select="'inheritance'"/>
  <xsl:include href="xsl-helper.xsl"/>
  
  <xsl:template match="main">
    <xsl:apply-templates/>
  </xsl:template>
  
  <xsl:template match="class">
    <ul>
      <li><a href="/apidoc/classes/{@name}.html"><xsl:value-of select="@name"/></a></li>
      <xsl:apply-templates/>
    </ul>
  </xsl:template>

</xsl:stylesheet>
