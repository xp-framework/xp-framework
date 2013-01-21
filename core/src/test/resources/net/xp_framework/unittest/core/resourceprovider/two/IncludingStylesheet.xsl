<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="text" encoding="iso-8859-1"/>

  <xsl:include href="../one/RelativeIncludeTarget.xsl"/>

  <xsl:template match="/">
    Main has been called.
    <xsl:call-template name="call-me-third"/>
  </xsl:template>
</xsl:stylesheet>
