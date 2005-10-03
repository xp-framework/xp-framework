<?xml version="1.0" encoding="utf-8"?>
<!-- 
 ! View pictures
 !
 ! $Id: view.xsl 5883 2005-10-03 10:40:22Z kiesel $
 !-->
<xsl:stylesheet 
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
 exclude-result-prefixes="exsl"
>
  <xsl:output
   method="xml"
   encoding="iso-8859-1"
   indent="yes"
  />
  <xsl:include href="../date.inc.xsl"/>

  <xsl:template match="/">
    <rss version="2.0">
      <xsl:call-template name="channel"/>
    </rss>
  </xsl:template>

  <xsl:template name="channel">
    <channel>
      <title><xsl:value-of select="/formresult/config/general/site"/></title>
      <link><xsl:value-of select="concat(/formresult/uri/scheme, '://', /formresult/uri/host)"/></link>
      <description>Monolog photoblog site powered by the XP framework</description>
      <generator>XP framework / [Mono]logue - $Id$</generator>
      <language><xsl:value-of select="$__lang"/></language>
      <pubDate><xsl:value-of select="func:isodate(/formresult/pictures/picture[1]/pubDate)"/></pubDate>
      <xsl:apply-templates select="/formresult/pictures/picture"/>
    </channel>
  </xsl:template>
  
  <xsl:template match="picture">
    <item>
      <title><xsl:value-of select="picture/title"/></title>
      <link><xsl:value-of select="concat(
        /formresult/uri/scheme, '://',
        /formresult/uri/host, '/',
        date)"/>
      </link>
      <pubDate><xsl:value-of select="func:isodate(pubDate)"/></pubDate>
      <author><xsl:value-of select="/formresult/config/general/author"/></author>
      <guid><xsl:value-of select="concat(
        /formresult/uri/scheme, '://',
        /formresult/uri/host, '/',
        date)"/>
      </guid>
      <description><xsl:copy-of select="picture/description"/></description>
      <category>general</category>
      
    </item>
  </xsl:template>
</xsl:stylesheet>
