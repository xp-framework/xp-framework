<?php
/* This file is part of Planet XP
 *
 * $Id$ 
 */

  $target= urldecode(getenv('QUERY_STRING'));
  header('Refresh: 0;url='.$target);

  // Browsers that do not understand this header will just have to 
  // eat the following:)
?>
<html>
  <head>
    <title>Dereferer</title>
    <meta http-equiv="refresh" content="0;url=<?php echo $target; ?>">
  </head>
  <body bgcolor="#ffffff" color="#ffffff" alink="#ffffff" vlink="#ffffff" link="#ffffff">
    <table width="100%" height="100%" border="0">
      <tr>
        <td valign="middle" align="center">
          <a href="<?php echo $target; ?>">
            <?php echo $target; ?> 
          </a>
        </td>
      </tr>
    </table>
  </body>
</html>
