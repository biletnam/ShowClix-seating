<html>
  <head>
    <title>ShowClix Coding Interview</title>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-67234868-1', 'auto');
      ga('send', 'pageview');
    </script>
  </head>
  <body>
    <h1>ShowClix Interview Puzzle</h1>
    <h2>Test Driving ("Does This Thing Work?")</h2>

    <p>The form below allows you to test my solution to the ShowClix Interview Puzzle.</p>

    <p>Below you can set the number of rows, columns and reserved seats. These first three fields will be used for the <kbd>build(rows, cols, reserved)</kbd> function.  The seats field allows you to select the number of seats you would like to reserve and represents <kbd>n</kbd> in my <kbd>reserve(map, n)</kbd> function (the <kbd>map</kbd> is supplied by the initial build call). <em>Don't worry - if you don't enter anything, it'll run with the default variables from your test page!</em></p>

    <p>Once you submit your supplied inputs, I'll show you what your seating chart looks like using just <kbd>build()</kbd> function, and then we'll try to get the number of seats you'd like to reserve. If there are seats available, we'll reserve them with <kbd>reserve(map, n)</kbd> or we'll echo back <kbd>Not available</kbd>. Either way, I'll show you an updated seating chart afterwards.</p>

    <form action="showclix-test.php" method="get">
      <p>
        <table>
          <tr>
            <td>Rows:</td><td><input type="text" name="r" placeholder="3"></input></td>
          </tr>
          <tr>
            <td>Columns:</td><td> <input type="text" name="c" placeholder="11"></input></td>
          </tr>
          <tr>
            <td>Reserved:</td><td><input type="text" name="res_string" placeholder="R1C4 R1C6 R2C3 R2C7 R3C9 R3C10"></input></td>
          </tr>
          <tr>
            <td>Seats:</td><td><input type="text" name="n" placeholder="3"></input></td>
          </tr>
          <tr>
            <td></td><td><input type="submit" value="Let's Go!"></input></td>
          </tr>
        </table>
      </p>
    </form>

    <hr>

    <h2>The Nitty Gritty ("How Does This Thing Work?")</h2>

    <h3>Notes</h3>

    <p>The source code for everything here is available on my Bitbucket: <a href="https://bitbucket.org/snippets/jpmck/zrEzB" target="_blank">ShowClix Interview Puzzle Snippet</a></p>
    <p>All functions that return a <kbd>$map</kbd> object always return the map as a JSON encoded object. All functions that have a <kbd>$map</kbd> object as a parameter always expect it to be a JSON encoded object and always decode the object as an associative array.</p>

    <h3>Main Classes</h3>
    <h4><pre>build($rows, $cols, $reserved)</pre></h4>

    <p><kbd>build</kbd> is used to set up the JSON object and returns it to the user.</p>
    <p>We start out by creating an associate array <kbd>$map_out</kbd>, then adding <kbd>$rows</kbd> as <kbd>$map_out['rows']</kbd>, <kbd>$columns</kbd> as <kbd>$map_out['cols']</kbd>, the center seat location as <kbd>$map_out['center']</kbd>, and maximum possible Manhattan distance as <kbd>$map_out['max_Mhtn_dist']</kbd> to it.</p>

    <p>We then loop through and create a 2D array for each possible seating location, and if it matches a string in <kbd>$reserved</kbd> set it as reserved (<kbd>$map_out[x][y]['is_reserved']</kbd>).  We also set the Manhattan distance (<kbd>$map_out[x][y]['Mhtn_dist']</kbd>) while looping through every seat.</p>
    <p>When we're done, we encode our array as a JSON object and return it to the user.</p>

    <h4><pre>reserve($map, $n)</pre></h4>

    <p><kbd>reserve</kbd> is used to check if there is a block of <em>n</em> contiguous seats available in our seating chart, and if there are, update the seating chart. This is the most complex function that we have to deal with...</p>

    <ul>
      <li>If <kbd>$n</kbd> is greater than 10, or if it is less than 1</li>
      <ul>
        <li>We don't do anything.</li>
        <li>Echo "Not available."</li>
        <li>Return the <kbd>$map</kbd> as is.</li>
      </ul>
      <li>Else we can keep going...</li>

      <ul>
        <li>We set <kbd>$best_Mhtn_dist</kbd> to <kbd>$map_in['max_Mhtn_dist']</kbd>, which will be updated each time we find a good set of seats.</li>
        <li>We create a <kbd>$reserved</kbd> array, which will be updated each time we find a good set of seats.</li>
        <li>We set the <kbd>$found_seating</kbd> flag to FALSE, this will be set to true if we find a good set of seats.</li>
        <li>We start looping through each seat in the array...</li>

        <ul>
          <li>We set the <kbd>$made_it</kbd> flag to TRUE, which will be set to FALSE if we don't find a good seat in the set we're looking at.</li>
          <li>We create a <kbd>$candidate_reserved</kbd> array, which will be updated each time we find a good seat.</li>
          <li>We look at the current seat, and..</li>
            <ul>
              <li>We can use it...</li>

              <ul>
                <li>We create <kbd>$candidate_best_Mhtn_dist</kbd>, and update it with <kbd>$map_in[$i][$j]['Mhtn_dist']</kbd></li>
                <li>Push the seat to the <kbd>$candidate_reserved</kbd> array.</li>
                <li>Loop through the next <em>n - 1</em> seats...</li>

                <ul>
                  <li>If we can use the seat...</li>

                  <ul>
                    <li>Push the seat to the <kbd>$candidate_reserved</kbd> array.</li>
                    <li>If the <kbd>$map_in[$i][$j]['Mhtn_dist']</kbd> is less than the <kbd>$candidate_best_Mhtn_dist</kbd>, replace it.</li>
                  </ul>
                  <li>If we can't use the seat...</li>
                  <ul>
                    <li>Set <kbd>$made_it</kbd> to FALSE.</li>
                    <li>Break out so we can try again with the next seat.</li>
                  </ul>
                </ul>
              </ul>

              <li>We can't use it...</li>

              <ul>
                <li>Set <kbd>$made_it</kbd> to FALSE.</li>
              </ul>

              <li>If we found a good set of seats (<kbd>$made_it</kbd> is TRUE)...</li>
              <ul>
                <li>If the <kbd>$candidate_best_Mhtn_dist</kbd> is less than <kbd>$best_Mhtn_dist</kbd>...</li>
                <ul>
                  <li>Set <kbd>$found_seating</kbd> to TRUE.</li>
                  <li>Move the <kbd>$candidate_reserved</kbd> array into the <kbd>$reserved</kbd> array.</li>
                  <li>Clear the <kbd>$candidate_reserved</kbd> arrary for the next run</li>
                  <li>Update <kbd>$best_Mhtn_dist</kbd> to <kbd>$candidate_best_Mhtn_dist</kbd>.</li>
                </ul>
              </ul>
            </ul>

          <li>Otherwise, we go to the next seat in the array</li>
        </ul>
        <li>If we found seating a good set of seats (<kbd>$found_seating</kbd> is TRUE)</li>
        <ul>
          <li>Update the map with <kbd>update($map)</kbd></li>
          <li>Echo the seats reserved.</li>
          <li>Return the updated <kbd>$map</kbd>.</li>
        </ul>

        <li>Else we didn't..</li>
        <ul>
          <li>We don't do anything.</li>
          <li>Echo "Not available."</li>
          <li>Return the <kbd>$map</kbd> as is.</li>
        </ul>
      </ul>
    </ul>

    <h3>Helper Classes</h3>

    <p><em>These functions are all relatively simple and make things easier for everyone.</em></p>

    <h4><pre>update($map, $reserved)</pre></h4>

    <p><kbd>update</kbd> is used to update the map of seats with additional reserved seats. We loop through each seat in the array, and when we find a seat that matches up with a string in <kbd>$reserved</kbd> we set <kbd>['is_reserved']</kbd> to TRUE. Once we're through, we return the updated map.</p>

    <h4><pre>getReservedFromMap($map)</pre></h4>

    <p><kbd>getReservedFromMap</kbd> is used to get a <kbd>$reserved</kbd> array when we already have a map. We start out with an empty <kbd>$reserved</kbd> array. We then loop through each seat in the array, and when we find a seat that is reserved (<kbd>['is_reserved']</kbd> is TRUE), we push an "RxCy" string to the <kbd>$reserved</kbd> array. Once we're through, we return the <kbd>$reserved</kbd> array.</p>

    <h4><pre>htmlTablePrint($map)</pre></h4>

    <p><kbd>htmlTablePrint</kbd> is used to print an HTML table that represents the seating chart. We then loop through each seat in the array.  When we find a seat that is reserved (<kbd>['is_reserved']</kbd> is TRUE), we set the appropriate CSS value to color for the table cell to be red, otherwise we set the cell as green.  Nothing is returned.</p>

  </body>
</html>
