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
  <xsl:include href="../../wizard.inc.xsl"/>
  
  <xsl:template name="context">
  </xsl:template>
  
  <xsl:template name="content">
    <form method="post" action="{func:link($__state)}">
    <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'editeventhandler']/@id}"/>
    <xsl:variable name="status" select="/formresult/handlers/handler[@name= 'editeventhandler']/@status"/>

    <xsl:copy-of select="func:display_wizard_error('editeventhandler')"/>
    <xsl:copy-of select="func:display_wizard_success('editeventhandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('editeventhandler')"/>
    
    <xsl:if test="$status = 'initialized' or $status = 'setup' or $status = 'errors'">
    <div id="form">
      <fieldset>
        <legend>Termin-Daten</legend>
        <table width="600">
          <!-- Event name -->
          <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'name', 40)"/>

          <!-- Team selection -->
          <xsl:variable name="teams">
            <xsl:for-each select="/formresult/handlers/handler[@name= 'editeventhandler']/values/teams/team">
              <option id="{team_id}"><xsl:value-of select="name"/></option>
            </xsl:for-each>
          </xsl:variable>
          <xsl:copy-of select="func:wizard_row_select('editeventhandler', 'team', $teams, 0)"/>

          <!-- Event type -->
          <xsl:variable name="eventtypes">
            <xsl:for-each select="/formresult/eventtypes/type">
              <option id="{@id}"><xsl:value-of select="."/></option>
            </xsl:for-each>
          </xsl:variable>
          <xsl:copy-of select="func:wizard_row_select('editeventhandler', 'event_type', $eventtypes, 0)"/>

          <!-- Description -->
          <xsl:copy-of select="func:wizard_row_textarea('editeventhandler', 'description', 40, 4)"/>
        </table>
        </fieldset>
        
        <fieldset>
          <legend>Uhrzeit und Datum</legend>
          <table width="600">

            <!-- Target date and deadline -->
            <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'target_date', 12)"/>
            <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'target_time', 12)"/>
            <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'deadline_date', 12)"/>
            <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'deadline_time', 12)"/>
          </table>
        </fieldset>
        
        <fieldset>
          <legend>Teilnehmer</legend>
          <table width="600">

            <!-- Max, req and guests -->
            <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'max', 4)"/>
            <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'req', 4)"/>
            <xsl:copy-of select="func:wizard_row_checkbox('editeventhandler', 'guests')"/>
          </table>
        </fieldset>
        
        <table width="600">
        <tr>
          <td colspan="3" align="right">
            <input type="submit" name="submit" value="Termin eintragen"/>
          </td>
        </tr>
      </table>
    </div>
    </xsl:if>
    </form>
  </xsl:template>
</xsl:stylesheet>
