# ShowClix Interview Puzzle #

## The Nitty Gritty ("How Does This Thing Work?") ##

### Notes ###
A functioning version of this solution is available at my [website](https://www.jpmck.com/showclix.php).

All functions that return a $map object always return the map as a JSON encoded object. All functions that have a $map object as a parameter always expect it to be a JSON encoded object and always decode the object as an associative array.

### Main Classes ###
#### build($rows, $cols, $reserved)####

`build` is used to set up the JSON object and returns it to the user.

We start out by creating an associate array `$map_out`, then adding `$rows` as `$map_out['rows']`, `$columns` as `$map_out['cols']`, the center seat location as `$map_out['center']`, and maximum possible Manhattan distance as `$map_out['max_Mhtn_dist']` to it.

We then loop through and create a 2D array for each possible seating location, and if it matches a string in `$reserved` set it as reserved (`$map_out[x][y]['is_reserved']`). We also set the Manhattan distance (`$map_out[x][y]['Mhtn_dist']`) while looping through every seat.

When we're done, we encode our array as a JSON object and return it to the user.

#### reserve($map, $n) ####

`reserve` is used to check if there is a block of n contiguous seats available in our seating chart, and if there are, update the seating chart. This is the most complex function that we have to deal with...

* If `$n` is greater than 10, or if it is less than 1
    * We don't do anything.
    * Echo "Not available."
    * Return the `$map` as is.
* Else we can keep going...
    * We set `$best_Mhtn_dist` to `$map_in['max_Mhtn_dist']`, which will be updated each time we find a good set of seats.
    * We create a `$reserved array`, which will be updated each time we find a good set of seats.
    * We set the `$found_seating` flag to FALSE, this will be set to true if we find a good set of seats.
    * We start looping through each seat in the array...
        * We set the `$made_it` flag to TRUE, which will be set to FALSE if we don't find a good seat in the set we're looking at.
        * We create a `$candidate_reserved array`, which will be updated each time we find a good seat.
        * We look at the current seat, and..
            * We can use it...
                * We create `$candidate_best_Mhtn_dist`, and update it with `$map_in[$i][$j]['Mhtn_dist']`
                * Push the seat to the `$candidate_reserved` array.
                * Loop through the next _n - 1_ seats...
                    * If we can use the seat...
                        * Push the seat to the `$candidate_reserved` array.
                        * If the `$map_in[$i][$j]['Mhtn_dist']` is less than the `$candidate_best_Mhtn_dist`, replace it.
                    * If we can't use the seat...
                        * Set `$made_it` to FALSE.
                        * Break out so we can try again with the next seat.
            * We can't use it...
                * Set `$made_it` to FALSE.
            * If we found a good set of seats (`$made_it` is TRUE)...
                * If the `$candidate_best_Mhtn_dist` is less than `$best_Mhtn_dist`...
                    * Set `$found_seating` to TRUE.
                    * Move the `$candidate_reserved` array into the `$reserved` array.
                    * Clear the `$candidate_reserved` array for the next run
                    * Update `$best_Mhtn_dist` to `$candidate_best_Mhtn_dist`.
        * Otherwise, we go to the next seat in the array
    * If we found seating a good set of seats (`$found_seating` is TRUE)
        * Update the map with `update($map)`
        * Echo the seats reserved.
        * Return the updated $map.
    * Else we didn't..
        * We don't do anything.
        * Echo "Not available."
        * Return the $map as is.

### Helper Classes ###

These functions are all relatively simple and make things easier for everyone.

#### update($map, $reserved) ####

`update` is used to update the map of seats with additional reserved seats. We loop through each seat in the array, and when we find a seat that matches up with a string in `$reserved` we set `['is_reserved']` to TRUE. Once we're through, we return the updated map.

#### getReservedFromMap($map) ####

`getReservedFromMap` is used to get a $reserved array when we already have a map. We start out with an empty `$reserved` array. We then loop through each seat in the array, and when we find a seat that is reserved (`['is_reserved']` is TRUE), we push an "RxCy" string to the `$reserved` array. Once we're through, we return the `$reserved` array.

#### htmlTablePrint($map) ####

`htmlTablePrint` is used to print an HTML table that represents the seating chart. We then loop through each seat in the array. When we find a seat that is reserved (`['is_reserved']` is TRUE), we set the appropriate CSS value to color for the table cell to be red, otherwise we set the cell as green. Nothing is returned.