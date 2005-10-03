<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Date functions
 !
 ! $Id: date.inc.xsl,v 1.3 2004/11/23 17:27:12 friebe Exp $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <!--
   ! Function to display a serialized date object (date only)
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="func:date">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="not(exsl:node-set($date)/_utime)">
          <!-- Intentionally empty -->
        </xsl:when>
        <xsl:when test="$__lang = 'en_US'">
          <xsl:value-of select="concat(
            exsl:node-set($date)/year, '-',
            format-number(exsl:node-set($date)/mon, '00'), '-',
            format-number(exsl:node-set($date)/mday, '00')
          )"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="concat(
            format-number(exsl:node-set($date)/mday, '00'), '.',
            format-number(exsl:node-set($date)/mon, '00'), '.',
            exsl:node-set($date)/year
          )"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Function to display a serialized date object (day and month only)
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="func:shortdate">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="not(exsl:node-set($date)/_utime)">
          <!-- Intentionally empty -->
        </xsl:when>
        <xsl:when test="$__lang = 'en_US'">
          <xsl:value-of select="concat(
            format-number(exsl:node-set($date)/mon, '00'), '-',
            format-number(exsl:node-set($date)/mday, '00')
          )"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="concat(
            format-number(exsl:node-set($date)/mday, '00'), '.',
            format-number(exsl:node-set($date)/mon, '00'), '.'
          )"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Function to display a serialized date object (time only)
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="func:time">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="not(exsl:node-set($date)/_utime)">
          <!-- Intentionally empty -->
        </xsl:when>
        <xsl:when test="$__lang = 'en_US'">
          <xsl:choose>
            <xsl:when test="exsl:node-set($date)/hours = 0">
              <xsl:value-of select="concat(
                '12:',
                format-number(exsl:node-set($date)/minutes, '00'),
                ' AM'
              )"/>
            </xsl:when>
            <xsl:when test="exsl:node-set($date)/hours &lt; 13">
              <xsl:value-of select="concat(
                format-number(exsl:node-set($date)/hours, '00'), ':',
                format-number(exsl:node-set($date)/minutes, '00'), 
                ' AM'
              )"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="concat(
                format-number(exsl:node-set($date)/hours - 12, '00'), ':',
                format-number(exsl:node-set($date)/minutes, '00'),
                ' PM'
              )"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="concat(
            format-number(exsl:node-set($date)/hours, '00'), ':',
            format-number(exsl:node-set($date)/minutes, '00')
          )"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Function to display a serialized date object (date AND time)
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="func:datetime">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:if test="exsl:node-set($date)/_utime">
        <xsl:value-of select="concat(func:date($date), ' ', func:time($date))"/>
      </xsl:if>
    </func:result>
  </func:function>
</xsl:stylesheet>
