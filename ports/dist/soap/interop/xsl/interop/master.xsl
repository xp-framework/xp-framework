<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
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
  <xsl:output method="html" encoding="iso-8859-1" indent="no"/>
  <xsl:param name="__page"/>
  <xsl:param name="__frame"/>
  <xsl:param name="__state"/>
  <xsl:param name="__lang"/>
  <xsl:param name="__product"/>
  <xsl:param name="__sess"/>
  <xsl:param name="__query"/>

  <!--
   ! Function to display a serialized date object
   !
   ! @param  node-set date
   !-->
  <func:function name="func:datetime">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:value-of select="concat(
        exsl:node-set($date)/year, '-',
        format-number(exsl:node-set($date)/mon, '00'), '-',
        format-number(exsl:node-set($date)/mday, '00'), ' ',
        format-number(exsl:node-set($date)/hours, '00'), ':',
        format-number(exsl:node-set($date)/minutes, '00')
      )"/>
    </func:result>
  </func:function>

  <!--
   ! Function that trims characters off the beginning of a string
   !
   ! @param  string text
   ! @param  string chars
   !-->  
  <func:function name="func:ltrim">
    <xsl:param name="text"/>
    <xsl:param name="chars"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="contains(substring($text, 1, 1), $chars)">
          <xsl:value-of select="func:ltrim(substring($text, 2, string-length($text)), $chars)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$text"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Function that concatenates a text conditionally
   !
   ! @param  bool condition
   ! @param  string text
   !-->  
  <func:function name="func:concatif">
    <xsl:param name="condition"/>
    <xsl:param name="text"/>
    
    <func:result>
      <xsl:if test="$condition">
        <xsl:value-of select="$text"/>
      </xsl:if>
    </func:result>
  </func:function>
  
  <!--
   ! Template that matches on everything and copies it through
   ! one to one.
   !
   !-->
  <xsl:template match="*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy> 
  </xsl:template>
  
</xsl:stylesheet>
