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
  <xsl:include href="../news.inc.xsl"/>
  <xsl:include href="../wizard.inc.xsl"/>
  <xsl:include href="calendar.inc.xsl"/>
  
  <xsl:template name="context">
    <xsl:call-template name="default_subnavigation">
      <xsl:with-param name="items">
        <xsl:if test="'' != func:hasPermission('create_event')">
          <item href="{func:link('event/edit')}">Neuer Termin</item>
        </xsl:if>
        <item href="{func:link('events?training,0,2')}">Trainings Mädels</item>
        <item href="{func:link('events?training,0,1')}">Trainings Jungs</item>
        <item href="{func:link('events?tournament')}">Turniere</item>
        <item href="{func:link('events?misc')}">Sonstiges</item>
      </xsl:with-param>
    </xsl:call-template>

    <xsl:call-template name="calendar">
      <xsl:with-param name="month" select="/formresult/month"/>
      <xsl:with-param name="prefix" select="func:link(concat('events?', /formresult/events/@team, ',1,1,'))"/>
    </xsl:call-template>
  </xsl:template>
  
  <xsl:template name="content">
    <xsl:variable name="events" select="/formresult/events"/>
    
    <!-- Display message when no events exist -->
    <xsl:if test="count($events/event) = 0">
      <xsl:copy-of select="func:box('hint', func:get_text('events#no-events'))"/>
    </xsl:if>
    
    <xsl:for-each select="$events/event">
      <xsl:variable name="pos" select="position()"/>
      
      <xsl:if test="$pos = 1 or (func:date($events/event[$pos - 1]/target_date) != func:date(target_date))">
        <h3><xsl:value-of select="func:date(target_date)"/></h3>
      </xsl:if>
      
      <div class="eventbox eventbox{event_type_id}">
        <h3>
          <a href="{func:link(concat('event/view?', event_id))}"><xsl:value-of select="name"/> (<xsl:value-of select="func:time(target_date)"/>)</a>
          <xsl:if test="'' != func:hasPermission('create_event')">
            - <a href="{func:link(concat('event/edit?event_id=', event_id))}">(editieren)</a>
          </xsl:if>
        </h3>
        <xsl:if test="description != ''">
          <xsl:apply-templates select="description"/><br/>
        </xsl:if>
        <xsl:value-of select="func:get_text('event#max')"/>: <b><xsl:value-of select="max_attendees"/></b> / 
        <xsl:value-of select="func:get_text('event#req')"/>: <b><xsl:value-of select="req_attendees"/></b> /
        <xsl:value-of select="func:get_text(concat('event#guests_allowed-', allow_guests))"/>
        <br/><br/>
        
        <small><xsl:value-of select="func:get_text('event#createdby')"/>&#160;<xsl:value-of select="changedby"/> am <xsl:value-of select="func:datetime(lastchange)"/></small>
      </div>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
