<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text"/>

  <xsl:template match="/">
    <xsl:text>#</xsl:text>
    <xsl:value-of select="count(/document/nodes/node)"/>
    <xsl:text>#</xsl:text>
  </xsl:template>

</xsl:stylesheet>
