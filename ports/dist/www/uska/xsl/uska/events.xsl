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

  <xsl:include href="layout.xsl"/>
  
  <xsl:template name="context">
    <xsl:call-template name="calendar">
      <xsl:with-param name="month" select="/formresult/month"/>
    </xsl:call-template>
    <br/>
    
    <table class="sidebar" cellpadding="0" cellspacing="0" width="170">
      <tr><td class="sidebar_head">Aktionen</td></tr>
      <tr><td><a href="{func:link('event/edit')}">Neuen Termin eintragen</a></td></tr>
    </table>
  </xsl:template>
  
  <xsl:template name="content">
    <b>Die nächsten Events:</b>
    
    <xsl:variable name="events" select="/formresult/events"/>
    
    <xsl:for-each select="$events/event">
      <xsl:variable name="pos" select="position()"/>
      
      <xsl:if test="$pos = 1 or ($events/event[$pos - 1]/target_date/year != target_date/year or $events/event[$pos - 1]/target_date/yday != target_date/yday)">
        <h3><xsl:value-of select="func:date(target_date)"/></h3>
      </xsl:if>
      
      <div class="eventbox eventbox{event_type_id}">
        <h3><a href="{func:link(concat('event/view?', event_id))}"><xsl:value-of select="name"/> (<xsl:value-of select="func:time(target_date)"/>)</a></h3>
        <xsl:if test="description != ''"><xsl:value-of select="description"/><br/></xsl:if>
        <xsl:value-of select="func:get_text('event#max')"/>: <b><xsl:value-of select="max_attendees"/></b> / 
        <xsl:value-of select="func:get_text('event#req')"/>: <b><xsl:value-of select="req_attendees"/></b> /
        <xsl:value-of select="func:get_text(concat('event#guests_allowed-', allow_guests))"/>
        <br/><br/>
        
        <small><xsl:value-of select="func:get_text('event#createdby')"/>&#160;<xsl:value-of select="changedby"/> at <xsl:value-of select="func:datetime(lastchange)"/></small>
      </div>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
