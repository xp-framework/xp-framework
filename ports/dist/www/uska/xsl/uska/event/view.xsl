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

  <xsl:include href="../layout.xsl"/>

  <xsl:template name="context">
    <xsl:call-template name="default_subnavigation">
      <xsl:with-param name="items">
        <item href="{func:link(concat('event/attend?event_id=', /formresult/event/event_id))}">Anmelden</item>
        
        <xsl:if test="/formresult/event/allow_guests = 1">
          <item href="{func:link(concat('event/attend?guest=add&amp;event_id=', /formresult/event/event_id))}">Gast anmelden</item>
        </xsl:if>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>

  <xsl:template name="content">
    <xsl:apply-templates select="/formresult/event"/>
  </xsl:template>
  
  <xsl:template match="event">
    <h3>
      <xsl:value-of select="/formresult/event/name"/> - 
      <xsl:value-of select="func:datetime(/formresult/event/target_date)"/>
    </h3>
    
    <p>
      <xsl:if test="description != ''"><xsl:value-of select="description"/><br/></xsl:if>
      <xsl:value-of select="func:get_text('event#max')"/>: <b><xsl:value-of select="max_attendees"/></b> / 
      <xsl:value-of select="func:get_text('event#req')"/>: <b><xsl:value-of select="req_attendees"/></b> /
      <xsl:value-of select="func:get_text(concat('event#guests_allowed-', allow_guests))"/>
      <br/><br/>
        
      <small><xsl:value-of select="func:get_text('event#createdby')"/>&#160;<xsl:value-of select="changedby"/> at <xsl:value-of select="func:datetime(lastchange)"/></small>
    </p>
    
    <table>
      <tr>
        <th width="300">Spieler</th>
        <th>Teilnahme</th>
        <th>Fahrerinfo</th>
      </tr>
      <xsl:for-each select="attendeeinfo/player">
        <tr class="list_{position() mod 2}">
          <td>
            <xsl:value-of select="concat(@firstname, ' ', @lastname)"/>
            <xsl:if test="@player_type_id = 2">
              <br/>
              (<xsl:value-of select="concat(func:get_text('attendee#guestof'), ' ', creator/firstname, ' ', creator/lastname)"/>)
            </xsl:if>
          </td>
          <td>
            <xsl:value-of select="func:get_text(concat('attendee#status-', @attend))"/>
            <xsl:if test="/formresult/user and ((@player_type_id = 1 and @player_id = /formresult/user/player_id) or (@player_type_id= 2 and @created_by = /formresult/user/player_id))">
              &#160;(<a href="{func:link(concat('event/attend?event_id=', /formresult/event/event_id, '&amp;player_id=', @player_id))}">Ändern</a>)
            </xsl:if>
          </td>
          <td>
            <xsl:if test="@attend = 1">
              <xsl:choose>
                <xsl:when test="@offers_seats &gt; 0"><xsl:value-of select="concat(@offers_seats, ' ', func:get_text('attendee#offers_seats'))"/></xsl:when>
                <xsl:when test="@needs_driver = 1"><xsl:value-of select="func:get_text('attendee#needs_driver')"/></xsl:when>
                <xsl:otherwise>&#160;</xsl:otherwise>
              </xsl:choose>
            </xsl:if>
          </td>
        </tr>
      </xsl:for-each>
      <tr class="list_compute">
        <td>Gesamt</td>
        <td>
          <xsl:value-of select="count(attendeeinfo/player[@attend = 1])"/>&#160;<xsl:value-of select="func:get_text('attendee#attendees')"/>
        </td>
        <td>
          <xsl:value-of select="sum(attendeeinfo/player[@offers_seats != '' and @attend = 1]/@offers_seats)"/>&#160;<xsl:value-of select="func:get_text('attendee#availableseats')"/> /
          <xsl:value-of select="sum(attendeeinfo/player[@needs_driver != '' and @attend = 1]/@needs_driver)"/>&#160;<xsl:value-of select="func:get_text('attendee#neededseats')"/>
        </td>
      </tr>
    </table>
  </xsl:template>
</xsl:stylesheet>
