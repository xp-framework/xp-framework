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
    <h3><xsl:value-of select="func:get_text('editeventhandler#newevent')"/></h3>
    
    <form method="post" action="{func:link($__state)}">
    <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'editeventhandler']/@id}"/>
    <xsl:variable name="status" select="/formresult/handlers/handler[@name= 'editeventhandler']/@status"/>

    <xsl:copy-of select="func:display_wizard_error('editeventhandler')"/>
    <xsl:copy-of select="func:display_wizard_success('editeventhandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('editeventhandler')"/>
    
    <xsl:if test="$status = 'initialized' or $status = 'setup' or $status = 'errors'">
      <table class="form" width="600" border="0">

        <!-- Event name -->
        <xsl:copy-of select="func:wizard_row_input('name', 40)"/>
        
        <!-- Team selection -->
        <xsl:variable name="teams">
          <xsl:for-each select="/formresult/handlers/handler[@name= 'editeventhandler']/values/teams/team">
            <option id="{team_id}"><xsl:value-of select="name"/></option>
          </xsl:for-each>
        </xsl:variable>
        <xsl:copy-of select="func:wizard_row_select('team', $teams, 0)"/>

        <!-- Event type -->
        <xsl:variable name="eventtypes">
          <option id="1">training</option>
          <option id="2">tournament</option>
          <option id="3">misc</option>
        </xsl:variable>
        <xsl:copy-of select="func:wizard_row_select('event_type', $eventtypes)"/>

        <!-- Description -->
        <xsl:copy-of select="func:wizard_row_textarea('description', 72, 4)"/>

        <!-- Target date and deadline -->
        <xsl:copy-of select="func:wizard_separator('dates')"/>
        <xsl:copy-of select="func:wizard_row_input('target_date', 12)"/>
        <xsl:copy-of select="func:wizard_row_input('deadline', 12)"/>

        <!-- Max, req and guests -->
        <xsl:copy-of select="func:wizard_separator('attendees')"/>
        <xsl:copy-of select="func:wizard_row_input('max', 4)"/>
        <xsl:copy-of select="func:wizard_row_input('req', 4)"/>
        <xsl:copy-of select="func:wizard_row_checkbox('guests')"/>

        <tr>
          <td colspan="3" align="right">
            <input type="submit" name="submit" value="Termin eintragen"/>
          </td>
        </tr>
      </table>
    </xsl:if>
    </form>
  </xsl:template>
</xsl:stylesheet>
