<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id: static.xsl 7662 2006-08-13 11:47:23Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:template match="/">
    <html>
      <body>
        <xsl:apply-templates select="/formresult/page"/>
        
        Ok, dude.
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
