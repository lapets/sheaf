<?php /**************************************************************
**
** sheaf/hooks/SQL.php
** 
**   Hook for rendering simple markdown notation for SQL-like
**   code snippets.
**
*/

/********************************************************************
*/

function sheaf_hook_SQL ($s) {
  $s = sheaf::r($s, "\r", "");

  $inp = $s;
  $s = "";
  $flag = array('line' => 0, 'delimited' => 0, 'literal' => 'none', 'escaped' => 0);
  $lineComment = 'false';
  for ($i = 0; $i < strlen($inp); $i++) {
    $c = $inp[$i];

    // Handle single-line newline-terminated comments.
    if (($c === "-" && $i < strlen($inp)-1 && $inp[$i+1] == "-") && $flag['line'] == 0 && $flag['delimited'] == 0 && $flag['literal'] == 'none') {
      $s .= '<span class="comment">';
      $s .= $c;
      $flag['line'] = 1;
    } else if ($c == "\n" && $flag['line'] == 1) {
      $s .= '</span>';
      $s .= $c;
      $flag['line'] = 0;
    }

    // Handle string literals.
    else if ($c == '"') {
      if ($flag['line'] == 0 && $flag['delimited'] == 0 && $flag['literal'] == 'none') {
        $s .= '<span class="literal">';
        $s .= $c;
        $flag['literal'] = '"';
      } else if ($flag['literal'] == '"') {
        $s .= $c;
        $s .= '</span>';
        $flag['literal'] = 'none';
      } else {
        $s .= $c;
      }
    }
    else if ($c == "'" && strlen($inp) > $i+2 && $inp[$i+2] == "'") {
      if ($flag['line'] == 0 && $flag['delimited'] == 0 && $flag['literal'] == 'none') {
        $s .= '<span class="literal">'.$inp[$i+1].'</span>';
        $i += 2;
      }
    }
    
    // Defaults.
    else if ($c === " ") {
      $s .= "&nbsp;";
    } else if ($c === "@") {

      // Handle built in commands.
      $handled = false;
      $commands = array('SELECT','FROM', 'WHERE');
      $builtins = array('SUM');
      foreach ($builtins as $prefix) {
        if (sheaf::startsWith(substr($inp, $i), '@'.$prefix)) {
          $s .= '<span class="builtin">' . $prefix . '</span>';
          $i += strlen('@'.$prefix)-1;
          $handled = true;
          break;
        }
      }
      foreach ($commands as $prefix) {
        if (sheaf::startsWith(substr($inp, $i), '@'.$prefix)) {
          $s .= '<span class="keyword">' . $prefix . '</span>';
          $i += strlen('@'.$prefix)-1;
          $handled = true;
          break;
        }
      }
      if (!$handled)
        $s .= $c;
    } else {
      $s .= $c;
    }
  }

  $s = sheaf::r($s, "*>", '<span style="color:#ABABAB;">*&gt;</span>');
  $s = sheaf::r($s, "\n", "<br/>");
  return $s;
}

/*eof*/ ?>