<?xml version="1.0" encoding="utf-8"?>
<!-- 
 ! Default page layout
 !
 ! $Id$
 !-->
<xsl:stylesheet 
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:output
   method="html"
   encoding="utf-8"
  />
  <xsl:include href="../master.xsl"/>

  <xsl:template name="page-title">
    <xsl:value-of select="/formresult/config/general/site"/>  
  </xsl:template>

  <xsl:template match="/">
    <html>
      <head>
        <title><xsl:call-template name="page-title"/></title>
        <link rel="stylesheet" href="/style/{/formresult/config/general/style}.css"/>
      </head>
      <body>
        <xsl:call-template name="contents"/>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
