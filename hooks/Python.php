<?php /**************************************************************
**
** sheaf/hooks/Python.php
** 
**   Hook for rendering simple markdown notation for Python code
**   snippets.
**
*/

/********************************************************************
*/

function sheaf_hook_Python ($s) {
  $s = sheaf::r($s, "\r", "");

  $inp = $s;
  $s = "";
  $flag = array('line' => 0, 'delimited' => 0, 'literal' => 'none', 'escaped' => 0);
  $lineComment = 'false';
  for ($i = 0; $i < strlen($inp); $i++) {
    $c = $inp[$i];

    // Handle single-line newline-terminated comments.
    if ($c === '#' && $flag['line'] == 0 && $flag['delimited'] == 0 && $flag['literal'] == 'none') {
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
      }
    }
    else if ($c == "'") {
      if ($flag['line'] == 0 && $flag['delimited'] == 0 && $flag['literal'] == 'none') {
        $s .= '<span class="literal">';
        $s .= $c;
        $flag['literal'] = "'";
      } else if ($flag['literal'] == "'") {
        $s .= $c;
        $s .= '</span>';
        $flag['literal'] = 'none';
      }
    }
    
    // Defaults.
    else if ($c === " ") {
      $s .= "&nbsp;";
    } else if ($c === "@") {

      // Handle built in commands.
      $handled = false;
      $commands = array(
          'import', 'from', 'pass',
          'class', 'def', 'return', 'lambda',
          'for', 'while', 'break', 'continue',
          'if', 'elif', 'else',
          'or', 'and', 'not',
          'is', 'in'
        );
      $builtins = array(
          'exit',
          'print', 'exec', 'open',
          'len', 'range',
          'max', 'min', 'sum', 'pow',
          'map', 'filter',
          'int', 'float', 'type', 'str', 'list', 'set', 'frozenset', 'dict', 'tuple',
          'None', 'True', 'False'
        );
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

  $s = sheaf::r($s, ">>>", '<span style="color:#ABABAB;">&gt;&gt;&gt;</span>');
  $s = sheaf::r($s, "\n", "<br/>");
  return $s;
}

/*eof*/ ?>