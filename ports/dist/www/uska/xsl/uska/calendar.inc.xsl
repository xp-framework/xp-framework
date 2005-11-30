<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet include for entries
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

  <xsl:variable name="months">
    <month id="1">Jan</month>
    <month id="2">Feb</month>
    <month id="3">Mar</month>
    <month id="4">Apr</month>
    <month id="5">Mai</month>
    <month id="6">Jun</month>
    <month id="7">Jul</month>
    <month id="8">Aug</month>
    <month id="9">Sep</month>
    <month id="10">Okt</month>
    <month id="11">Nov</month>
    <month id="12">Dez</month>
  </xsl:variable>
  
  <func:function name="func:days">
    <xsl:param name="month"/>
    <xsl:param name="prefix"/>
    <xsl:param name="number" select="1"/>
    <xsl:param name="i" select="1"/>
    <xsl:variable name="day" select="($number - 1) * 7 + $i - exsl:node-set($month)/@start"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="($day &lt; 1) or ($day &gt; exsl:node-set($month)/@days and $i &lt;= 7)">
          <td>
            <xsl:if test="$i &gt; 5">
              <xsl:attribute name="class">weekend</xsl:attribute>
            </xsl:if>
            &#160;
          </td>
          <xsl:copy-of select="func:days($month, $prefix, $number, $i + 1)"/>
        </xsl:when>
        <xsl:when test="$i &lt;= 7">
          <td>
            <xsl:choose>
              <xsl:when test="exsl:node-set($month)/entries[@day = $day]">
                <xsl:attribute name="class">entries</xsl:attribute>
                <xsl:attribute name="title"><xsl:value-of select="exsl:node-set($month)/entries[@day = $day]"/> Einträge</xsl:attribute>
              </xsl:when>
              <xsl:when test="$i &gt; 5">
                <xsl:attribute name="class">weekend</xsl:attribute>
              </xsl:when>
            </xsl:choose>
            
            <a>
              <xsl:if test="exsl:node-set($month)/entries[@day = $day]">
                <xsl:attribute name="href"><xsl:value-of select="concat(
                  $prefix,
                  exsl:node-set($month)/@year, ',',
                  exsl:node-set($month)/@num, ',',
                  $day
                )"/></xsl:attribute>
              </xsl:if>
              <xsl:value-of select="$day"/>
            </a>
          </td>
          <xsl:copy-of select="func:days($month, $prefix, $number, $i + 1)"/>
        </xsl:when>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:weeks">
    <xsl:param name="month"/>
    <xsl:param name="prefix"/>
    <xsl:param name="i" select="1"/>
    
    <func:result>
      <tr><xsl:copy-of select="func:days($month, $prefix, $i)"/></tr>
      <xsl:if test="$i &lt; (exsl:node-set($month)/@days + exsl:node-set($month)/@start - 1) div 7">
        <xsl:copy-of select="func:weeks($month, $prefix, $i + 1)"/>
      </xsl:if>
    </func:result>
  </func:function>

  <func:function name="func:previous_month">
    <xsl:param name="month"/>

    <func:result>
      <xsl:choose>
        <xsl:when test="exsl:node-set($month)/@num = 1">
          <month num="12" year="{exsl:node-set($month)/@year - 1}"/>
        </xsl:when>
        <xsl:otherwise>
          <month num="{exsl:node-set($month)/@num - 1}" year="{exsl:node-set($month)/@year}"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <func:function name="func:next_month">
    <xsl:param name="month"/>

    <func:result>
      <xsl:choose>
        <xsl:when test="exsl:node-set($month)/@num = 12">
          <month num="1" year="{exsl:node-set($month)/@year + 1}"/>
        </xsl:when>
        <xsl:otherwise>
          <month num="{exsl:node-set($month)/@num + 1}" year="{exsl:node-set($month)/@year}"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Template which displays a calendar
   !
   ! Example of XML necessary:
   !
   ! <month num="4" start="4" days="28" year="2004">
   !   <entries day="2">32</entries>
   !   <entries day="12">1</entries>
   !   <entries day="29">5</entries>
   ! </month>
   !
   ! @param    node-set month
   ! @purpose  Context widget
   !-->
  <xsl:template name="calendar">
    <xsl:param name="month"/>
    <xsl:param name="prefix" select="'bydate?'"/>
    <xsl:variable name="current" select="exsl:node-set($month)/@num"/>
    
    <!-- Calendar -->
    <div id="sub_container1">
      <div id="calendar_container">
        <div id="calendar_container_headline">
          <h1>
          <xsl:variable name="previous_month" select="func:previous_month(exsl:node-set($month))"/>
          <a href="{$prefix}{exsl:node-set($previous_month)/month/@year},{exsl:node-set($previous_month)/month/@num}">&#xab;</a>
          &#160;
          <xsl:value-of select="concat(
            exsl:node-set($months)/month[@id = $current], ' ',
            exsl:node-set($month)/@year
          )"/>
          &#160;
          <xsl:variable name="next_month" select="func:next_month(exsl:node-set($month))"/>
          <a href="{$prefix}{exsl:node-set($next_month)/month/@year},{exsl:node-set($next_month)/month/@num}">&#xbb;</a>
          </h1>
        </div>
      <table>
        <tr>
          <th class="header">Mo</th>
          <th class="header">Di</th>
          <th class="header">Mi</th>
          <th class="header">Do</th>
          <th class="header">Fr</th>
          <th class="header">Sa</th>
          <th class="header">So</th>
        </tr>
        <xsl:copy-of select="func:weeks($month, $prefix)"/>
      </table>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>
