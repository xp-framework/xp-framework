<?xml version="1.0" encoding="iso-8859-1"?>

<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

<xsl:output
  method="text"
  encoding="iso-8859-1"
  indent="no"
/>

  <xsl:include href="texts.xsl"/>

  <xsl:template match="apply-node">
    Result: <xsl:value-of select="exsl:node-set($text)/text[@for= 'emaillist#rewriteconfig']"/>
  </xsl:template>

  <xsl:template match="/">
    <xsl:for-each select="document/apply-node">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
