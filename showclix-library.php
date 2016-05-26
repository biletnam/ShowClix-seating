<?php

/*  SHOWCLIX-LIBRARY.PHP
    2016, Jim McKenna
    This library is used to build and manipulate a JSON object which is used to represent the seats
    available in an auditourium.  For more information, please visit
    https://www.jpmck.com/showclix.php.
*/

//  MAIN FUNCTIONS ---------------------------------------------------------------------------------

/*  BUILD FUNCTION
    Builds and returns a JSON object to represent a seating map.
    $rows: (int) the number of rows
    $cols: (int) the number of columns (a.k.a. seats per row)
    $reserved: (array) a 1D array of strings representing the seats that are already reserved
               (formatted as "RxCy")
*/
function build($rows, $cols, $reserved)
{
  $map_out = array();

  $map_out['rows'] = $rows;
  $map_out['cols'] = $cols;
  $map_out['center'] = (int)($cols / 2);
  $map_out['max_Mhtn_dist'] = $rows + $cols;

  for($i=0; $i<$rows; $i++)
  {
    for($j=0; $j<$cols; $j++)
    {
      // set reserved to TRUE if it's in there, otherwise set reserved to FALSE
      $map_out[$i][$j]['is_reserved'] = in_array('R' . ($i+1) . 'C' . ($j+1), $reserved);

      // set the Manhattan distance
      $map_out[$i][$j]['Mhtn_dist'] = abs($j - $map_out['center']) + $i;
    }
  }

  return json_encode($map_out);
}

/*  RESERVE FUNCTION
    Reserves the best available contiguous block of n seats and returns map with those seats
    reserved
    $map: (array) the data structure from the build function used to represent our seating map
    $n: (int) the number of contiguous seats in a row someone is looking to reserve
*/
function reserve($map, $n)
{
  if ($n > 10 | $n < 1)
  {
    // The max number of tickets someone can request at once is 10, just give back the map
    echo 'Not available';
    return $map;
  }

  else
  {
    $map_in = json_decode($map, TRUE);
    $best_Mhtn_dist = $map_in['max_Mhtn_dist'];
    $reserved = array();
    $found_seating = FALSE;  // flag, set to TRUE if a set of seats is found

    // for all seats in the "auditourium"
    for ($i =0; $i < $map_in['rows']; $i++)
    {
      for ($j =0; $j <= $map_in['cols'] - $n; $j++)
      {
        $made_it = TRUE;  // flag as TRUE, will be set to false if we don't find appropriate seating
        $candidate_reserved = array();

        // if the seat is free
        if (!$map_in[$i][$j]['is_reserved'])
        {
          // set the candidate Manhattan distance
          $candidate_best_Mhtn_dist = $map_in[$i][$j]['Mhtn_dist'];

          // add the seat to the candidate seating array
          $candidate_seat = 'R' . ($i + 1) . 'C' . ($j + 1);
          array_push($candidate_reserved, $candidate_seat);

          // if we're looking for more than one seat...
          if ($n > 1)
          {
            // for the next seat, to the last seat desired
            for($looking_at = $j + 1; $looking_at < $j + $n; $looking_at++)
            {
              // if we can use this seat...
              if (!$map_in[$i][$looking_at]['is_reserved'])
              {
                // push this seat into the array
                $candidate_seat = 'R' . ($i + 1) . 'C' . ($looking_at + 1);
                array_push($candidate_reserved, $candidate_seat);

                // if the Manhattan distance is smaller, update it
                $looking_at_Mhtn_dist = $map_in[$i][$looking_at]['Mhtn_dist'];

                if ($looking_at_Mhtn_dist < $candidate_best_Mhtn_dist)
                {
                  $candidate_best_Mhtn_dist = $looking_at_Mhtn_dist;
                }
              }
              // else if we can't use this seat...
              elseif ($map_in[$i][$looking_at]['is_reserved'])
              {
                // set the flag to false, break to try the next seat over
                $made_it = FALSE;
                break;
              }
            }
          }
        }
        // if current seat is not available
        else if ($map_in[$i][$j]['is_reserved'])
        {
          $made_it = FALSE;
        }

        // if we made it through with an appropriate sized block of seats
        if($made_it)
        {
          if ($candidate_best_Mhtn_dist < $best_Mhtn_dist)
          {
            // we will be able to return a map!
            $found_seating = TRUE;

            // move the candidate reserved array into the reserved array, and
            // clear the candidate arrary for the next run
            $reserved = $candidate_reserved;
            unset($candidate_reserved);

            $best_Mhtn_dist = $candidate_best_Mhtn_dist;
          }
        }
      }
    }

    // if we found seats to reserve...
    if($found_seating)
    {
      // update the seating map
      $map_out = update(json_encode($map_in), $reserved);

      echo $reserved[0];
      if($n >1) echo ' - ' . $reserved[$n-1];

      return json_encode($map_out);
    }
    // else, we didn't find anything
    else
    {
      echo 'Not available';
      return $map;
    }
  }
}

// HELPER FUNCTIONS --------------------------------------------------------------------------------

/*  UPDATE FUNCTION
    Updates the JSON object representing the seating map.
    $map: (array) the data structure from the build function used to represent our seating map
    $reserved: (array) a 1D array of strings representing the seats that are already reserved
               (formatted as "RxCy")*/
function update($map, $reserved)
{
  $map_out = json_decode($map, TRUE);

  // for all of the seats in the seating map...
  for($i = 0; $i < $map_out['rows']; $i++)
  {
    for($j = 0; $j < $map_out['cols']; $j++)
    {
      // set seat to TRUE if reservation is in $reserved, otherwise leave the seats alone
      if(in_array('R' . ($i + 1) . 'C' . ($j + 1), $reserved))
      {
        $map_out[$i][$j]['is_reserved'] = TRUE;
      }
    }
  }

  return $map_out;
}

/*  GET RESERVED FROM MAP FUNCTION
    Returns reserved array as is used by the build and update functions from a map object.
    $map: (array) a JSON object from the build function used to represent our seating map
*/
function getReservedFromMap($map)
{
  $reserved = array();

  $map_in = json_decode($map, TRUE);

  // for all of the seats in the seating map...
  for($i = 0; $i < $map_in['rows']; $i++)
  {
    for($j = 0; $j < $map_in['cols']; $j++)
    {
      // if the seat is reserved...
      if ($map_in[$i][$j]['is_reserved'])
      {
        // ...add its string to the reserved array
        $reserved_string = 'R' . ($i + 1) . 'C' . ($j + 1);
        array_push($reserved, $reserved_string);
      }
    }
  }

  return $reserved;
}

/*  HTML TABLE PRINT FUNCTION
    Prints an HTML table representation of the seats available to reserve.
    $map: (array) the data structure from the build function used to represent our seating map
*/
function htmlTablePrint($map)
{
  $map_in = json_decode($map, TRUE);

  echo '<table>';
  for($i = 0; $i < $map_in['rows']; $i++)
  {
    echo '<tr>';
    for($j = 0; $j < $map_in['cols']; $j++)
    {
      echo '<td style="border: 1px solid black; background-color: ';

      if ($map_in[$i][$j]['is_reserved']) echo 'pink';
      else echo 'lightgreen';

      echo '">R' . ($i + 1) . 'C' . ($j + 1) .'</td>';
      //echo '">' . $map_in[$i][$j]['Mhtn_dist'] .'</td>';
    }
    echo '</tr>';
  }
  echo '</table>';
}

?>
