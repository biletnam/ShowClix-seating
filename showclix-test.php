<?php

/*  SHOWCLIX-TEST.PHP
    2016, Jim McKenna
    This file is used as a test driver for the showclix-library.  For more information, please visit
    https://www.jpmck.com/showclix.php.
*/

include('showclix-library.php');

// VARIABLES (either get from HTML GET or set to defaults)
if (isset($_GET['r']) && $_GET['r'] != NULL) $rows = $_GET['r'];
else $rows = 3;

if (isset($_GET['c'])  && $_GET['c'] != NULL) $cols = $_GET['c'];
else $cols = 11;

if (isset($_GET['n']) && $_GET['n'] != NULL) $n = $_GET['n'];
else $n = 3;

if (isset($_GET['res_string']) && $_GET['res_string'] != NULL)
{
  $res_string = $_GET['res_string'];
  $reserved = explode(" ", $res_string);
}
else
{
  if (isset($_GET['res'])) $reserved = $_GET['res'];
  else
  {
    $res_string = 'R1C4 R1C6 R2C3 R2C7 R3C9 R3C10';
    $reserved = ["R1C4","R1C6","R2C3","R2C7","R3C9","R3C10"];
  }
}

// HTML HEADER
echo '<html>';
echo '  <head>';
echo '    <title>ShowClix Coding Interview</title>';
echo '  </head>';
echo '  <body>';
echo '    <h1>ShowClix Interview Puzzle</h1>';

// OUTPUT RESERVED TABLE IN HTML TABLE
echo '    <h2>Initial Seating Chart</h2>';
echo '    <p><pre>build(' . $rows . ', ' . $cols . ', ["' . implode("\", \"", $reserved) . '"]);</pre></p>';

// shows that build works
$map = build($rows, $cols, $reserved);
htmlTablePrint($map);

// RESERVE SOME SEATS
echo '    <p>Tried the following:';
echo '    <p><pre>reserve($map, ' . $n . ');</pre></p>';
echo '    <p>The <kbd>reserve</kbd> function echoed back:<br><kbd>';

// shows that reserve works without using build first
$map = reserve($map, $n);

echo '    </kbd></p>';

echo '    <h2>Updated Seating Chart</h2>';
// PRINT IT!
htmlTablePrint($map);

// FOOTER

$reserve = getReservedFromMap($map);
$res_string = implode(" ", $reserve);
?>

    <form action="showclix-test.php" method="get">
      <input type="hidden" name="r" value="<?php echo $rows;?>"></input>
      <input type="hidden" name="c" value="<?php echo $cols;?>"></input>
      <p>
        <table>
          <tr>
            <td>Reserved:</td><td><input type="text" name="res_string" value="<?php echo $res_string;?>"></input></td>
          </tr>
          <tr>
            <td>Seats:</td><td><input type="text" name="n" value="<?php echo $n;?>"></input></td>
          </tr>
          <tr>
            <td></td><td><input type="submit" value="Again!"></input></td>
          </tr>
        </table>
      </p>
    </form>


<?php

echo '    <hr><p>Try with defaults: <a href="https://www.jpmck.com/showclix-test.php">Reset</a> | ';
echo 'Back to intro: <a href="https://www.jpmck.com/showclix.php">Intro</a></p>';
echo '  </body>' . PHP_EOL . '</html>';

?>
