<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text" encoding="iso-8859-1"/>

  <xsl:include href="res://net/xp_framework/unittest/core/resourceprovider/two/ModuleTwo.xsl"/>
  <xsl:include href="simpleinclude.xsl"/>

  <xsl:template match="/">
    <xsl:call-template name="call-me"/>
    <xsl:call-template name="call-me-too"/>
  </xsl:template>
</xsl:stylesheet>
