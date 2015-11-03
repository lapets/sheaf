<?php /*********************************************************
**
** Lecture and assignment materials example.
** 
** example.php
**   Invokes the sheaf instance for a course.
*/

////////////////////////////////////////////////////////////////
// Import the library and hooks.

// Load the library and rendering hooks.
include("sheaf/sheaf.php");
include("sheaf/hooks/math.php");
include("sheaf/hooks/Python.php");
include("sheaf/hooks/SQL.php");
include("sheaf/hooks/Haskell.php");
include("sheaf/hooks/JavaScript.php");
include("sheaf/hooks/machine.php");

// Build the course material data structure instance by setting
// the configuration parameters for the sheaf invocation.
$s = new Sheaf(
       array(
           'file' => 'example.xml',
           'path' => 'sheaf/',
           'message' => '<b>NOTE:</b> This page contains all the examples presented during the lectures, as well as all the homework assignments.',
           'toc' => 'true'
         )
      );

// Render the course materials in the specified XML file.
$s->html();

/*eof*/?>