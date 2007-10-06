<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id: master.xsl 4410 2004-12-18 18:19:28Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>

  <!--
   ! Function to display a serialized date object (date only)
   !
   ! @param  node-set date
   !-->
  <func:function name="xp:date">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="not($date/value)">
          <!-- Intentionally empty -->
        </xsl:when>
        <xsl:when test="$__lang = 'de_DE' or $__lang = 'en_UK'">
          <xsl:value-of select="xp:dateformat($date, 'd.m.Y')"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="xp:dateformat($date, 'Y-m-d')"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="xp:dateformat">
    <xsl:param name="date"/>
    <xsl:param name="format"/>
    
    <func:result select="php:function('XSLCallback::invoke', 'xp.date', 'format', string($date/value), $format)"/> 
  </func:function>

  <!--
   ! Function to display a human readable date.
   !
   ! Dates within the last two days will be written as "Today",
   ! "Yesterday, <time>" or "The day before yesterday".
   !
   ! Dates not within that range but in the last week will be written
   ! using the day-name and time.
   !
   ! All other days are written with date and time.
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="xp:smartdate">
    <xsl:param name="date"/>
    
    <xsl:variable name="diff" select="php:function('XSLCallback::invoke', 'xp.date', 'diff', 'day', /formresult/@serial, $date)"/>
    
    <func:result>
      <!-- DEBUG (<xsl:value-of select="$diff"/>) -->
      <xsl:choose>
        <xsl:when test="$diff = 0">Today</xsl:when>
        <xsl:when test="$diff = 1">Yesterday</xsl:when>
        <xsl:when test="$diff &lt;= 7"><xsl:value-of select="xp:dateformat($date, 'l')"/></xsl:when>
        <xsl:when test="$diff &lt;= 14">Last <xsl:value-of select="xp:dateformat($date, 'l')"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="xp:date($date)"/></xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Function to display a serialized date object (time only)
   !
   ! @param  node-set date
   !-->
  <func:function name="xp:time">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="not($date/value)">
          <!-- Intentionally empty -->
        </xsl:when>
        <xsl:when test="$__lang = 'de_DE' or $__lang = 'en_UK'">
          <xsl:value-of select="xp:dateformat($date, 'H:i')"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="xp:dateformat($date, 'h:i A')"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Function to display a serialized date object (date AND time)
   !
   ! @param  node-set date
   !-->
  <func:function name="xp:datetime">
    <xsl:param name="date"/>
    
    <func:result>
      <xsl:if test="$date/value">
        <xsl:value-of select="concat(xp:date($date), ' ', xp:time($date))"/>
      </xsl:if>
    </func:result>
  </func:function>

  <!--
   ! Function to display a human readable date.
   !
   ! Dates within the last two days will be written as "Today, <time>",
   ! "Yesterday, <time>" or "The day before yesterday, <time>".
   !
   ! Dates not within that range but in the last week will be written
   ! using the day-name and time.
   !
   ! All other days are written with date and time.
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="xp:smartdatetime">
    <xsl:param name="date"/>
    
    <func:result><xsl:value-of select="concat(xp:smartdate($date), ', ', xp:time($date))"/></func:result>
  </func:function>
</xsl:stylesheet>