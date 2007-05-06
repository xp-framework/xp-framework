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
  <xsl:output method="xml" encoding="utf-8"/>
  <xsl:include href="../master.xsl"/>
  
  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:value-of select="/formresult/config/title"/>
          
          <xsl:if test="/formresult/page/@title != ''">
           - <xsl:value-of select="/formresult/page/@title"/>
          </xsl:if>
        </title>
        <link rel="stylesheet" href="/styles/default.css"/>
        <link rel="alternate" type="application/rss+xml" href="/feeds/rss/"/>
      </head>
      <body>
        <div id="header-background">&#160;</div>
        <center>
        <div id="page">
          <div id="content">
            <xsl:call-template name="page-body"/>
          </div>
        </div>
        </center>
        <div id="footer">
          <xsl:value-of select="concat(/formresult/config/title, ' -  ', /formresult/config/copyright)"/>
          /
          <a href="{func:link('admin/listpage')}">admin</a>
          /
          <b>not</b> listed on photoblog.org
          /
          <a href="http://xp-framework.net/"><img src="/image/powered_by_xp.png" valign="bottom" border="0" title="Powered by XP framework"/></a>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
