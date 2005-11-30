<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id: view.xsl 6109 2005-11-12 12:41:58Z kiesel $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:include href="../../news.inc.xsl"/>
  <xsl:include href="../layout.xsl"/>

  <xsl:template name="context">
    <xsl:call-template name="default_subnavigation">
      <xsl:with-param name="items">
        <xsl:if test="/formresult/event">
          <item href="{func:link(concat('event/viewpoints?team_id=', /formresult/event/team_id))}">Gesamtpunkte</item>
          <item href="{func:link(concat('event/account?event_id=', /formresult/event/event_id))}">Punkte ändern</item>
        </xsl:if>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>

  <xsl:template name="content">
    <xsl:if test="/formresult/event">
      <h3>
        Punkte für <xsl:value-of select="/formresult/event/name"/>
      </h3>
    </xsl:if>
    
    <xsl:if test="not(/formresult/event)">
      <h3>Gesamtpunktliste</h3>
    </xsl:if>
    
    <xsl:apply-templates select="/formresult/attendeeinfo"/>
  </xsl:template>
  
  <xsl:template match="attendeeinfo">
    <table>
      <tr>
        <th width="200">Spieler</th>
        <th>Gesamtpunkte</th>
        <th>Spiele</th>
        <th>Durchschnitt</th>
      </tr>
      <xsl:for-each select="player">
        <xsl:sort select="@points div @attendcount" order="descending"/>
        <tr class="list_{position() mod 2}">
          <td>
            <xsl:value-of select="concat(@firstname, ' ', @lastname)"/>
          </td>
          <td><xsl:value-of select="@points"/></td>
          <td><xsl:value-of select="@attendcount"/></td>
          <td><xsl:value-of select="format-number(@points div @attendcount, '#.##')"/></td>
        </tr>
      </xsl:for-each>
      <tr class="list_compute">
        <td>Gesamt: <xsl:value-of select="count(player)"/> Spieler</td>
        <td></td>
        <td><xsl:value-of select="format-number(sum(player/@attendcount) div count(player), '#.##')"/></td>
        <td><xsl:value-of select="format-number(sum(player/@points) div sum(player/@attendcount), '#.##')"/></td> 
      </tr>
    </table>
  </xsl:template>
</xsl:stylesheet>
