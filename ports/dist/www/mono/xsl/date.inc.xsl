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
  
  <!--
   ! Function to display a isodate
   !
   ! @param   node-set date
   ! @return  string
   !-->
  <func:function name="func:isodate">
    <xsl:param name="date"/>
    <xsl:variable name="days">
      <name nr="0">Sun</name>
      <name nr="1">Mon</name>
      <name nr="2">Tue</name>
      <name nr="3">Wed</name>
      <name nr="4">Thu</name>
      <name nr="5">Fri</name>
      <name nr="6">Sat</name>
      <name nr="7">Sun</name>
    </xsl:variable>
    <xsl:variable name="months">
      <name nr="1">Jan</name>
      <name nr="2">Feb</name>
      <name nr="3">Mar</name>
      <name nr="4">Apr</name>
      <name nr="5">May</name>
      <name nr="6">Jun</name>
      <name nr="7">Jul</name>
      <name nr="8">Aug</name>
      <name nr="9">Sep</name>
      <name nr="10">Oct</name>
      <name nr="11">Nov</name>
      <name nr="12">Dec</name>
    </xsl:variable>
    <xsl:variable name="tz">
      <xsl:if test="/formresult/@tz &gt;= 0">+</xsl:if>
      <xsl:value-of select="format-number(/formresult/@tz div 36, '0000')"/>
    </xsl:variable>
    
    <func:result>
      <xsl:value-of select="concat(
        exsl:node-set($days)/name[@nr= $date/wday], ', ',
        format-number($date/mday, '00'), ' ',
        exsl:node-set($months)/name[@nr= $date/mon], ' ',
        $date/year, ' ',
        format-number($date/hours, '00'), ':',
        format-number($date/minutes, '00'), ':',
        format-number($date/seconds, '00'), ' ',
        $tz
      )"/>
    </func:result>
  </func:function>
</xsl:stylesheet>
