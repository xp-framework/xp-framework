<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text" encoding="iso-8859-1"/>

  <xsl:include href="res://two/ModuleTwo.xsl"/>
  <xsl:include href="simpleinclude.xsl"/>

  <xsl:template match="/">
    <xsl:call-template name="call-me"/>
    <xsl:call-template name="call-me-too"/>
    <xsl:call-template name="call-me-third"/>
  </xsl:template>
</xsl:stylesheet>
