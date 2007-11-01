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
    <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'attendeventhandler']/@id}"/>
    <input type="hidden" name="event_id" value="{/formresult/formvalues/param[@name= 'event_id']}"/>
    
    <xsl:variable name="status" select="/formresult/handlers/handler[@name= 'attendeventhandler']/@status"/>

    <xsl:copy-of select="func:display_wizard_error('attendeventhandler')"/>
    <xsl:copy-of select="func:display_wizard_success('attendeventhandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('attendeventhandler')"/>
    
    <xsl:if test="$status = 'initialized' or $status = 'setup' or $status = 'errors'">
    <div id="form">
      <fieldset>
        <legend>Informationen zur Teilnahme</legend>
        
        <table>

          <!-- Name of guest, if guestmode -->
          <xsl:if test="/formresult/handlers/handler[@name= 'attendeventhandler']/values/mode = 'addguest'">
            <xsl:copy-of select="func:wizard_row_input('attendeventhandler', 'firstname')"/>
            <xsl:copy-of select="func:wizard_row_input('attendeventhandler', 'lastname')"/>
          </xsl:if>

          <!-- Attend selection -->
          <xsl:variable name="attend">
            <option id="1">yes</option>
            <option id="0">no</option>
            <option id="2">unknown</option>
          </xsl:variable>
          <xsl:copy-of select="func:wizard_row_select('attendeventhandler', 'attend', $attend)"/>

          <!-- Driving information -->
          <xsl:copy-of select="func:wizard_row_checkbox('attendeventhandler', 'needs_seat')"/>
          <xsl:copy-of select="func:wizard_row_input('attendeventhandler', 'offers_seats', 10)"/>
        </table>
      </fieldset>
       
      <table>
        <tr>
          <td colspan="3" align="right">
            <input type="submit" name="submit" value="Abschicken!"/>
          </td>
        </tr>
      </table>
    </div>
    </xsl:if>
    </form>
  </xsl:template>
</xsl:stylesheet>
