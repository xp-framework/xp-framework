<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:output method="xhtml" encoding="iso-8859-1"/>
  <xsl:param name="mode" select="'index'"/>
  <xsl:param name="package" select="''"/>
  <xsl:param name="collection" select="''"/>

  
  <xsl:include href="xsl-helper.xsl"/>
  
  <xsl:template name="main">
    Welcome
  </xsl:template>

</xsl:stylesheet>
