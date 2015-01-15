<?php

interface RomanNumeralGenerator {
  public function generate($integer); // convert from int -> Roman
  public function parse($string); // Convert from Roman -> int
}

class RomanNumeralTool implements RomanNumeralGenerator {
  
  function generate($arabicInt) {
    // Check input is valid (within 1-3999 and a whole number)
    if ( $arabicInt > 3999 || $arabicInt <= 0 || !is_int($arabicInt)) {
        error_log("Please provide a number between 1 and 3999", 0);
        return false;
    }

    /*
    We need maps of all possible values at each of the four 'columns' of the integer, i.e. 
    the units, tens, hundreds, and thousands. (u)nit, (d)eci, (c)enti, (m)illi abbreviations refer 
    to the columns. The array index will represent the integer within the column, and the value 
    will hold the Roman equivalent of the Arabic integer.
    */
    
    $u = array('','I','II','III','IV','V','VI','VII','VIII','IX'); // Originally these had unnecessarily defined keys, let PHP do it automatically instead
    $d = array('','X','XX','XXX','XL','L','LX','LXX','LXXX','XC');
    $c = array('','C','CC','CCC','CD','D','DC','DCC','DCCC','CM');
    $m = array('','M','MM','MMM');  // This one is shortened as we only support up to 3999

    // Now we know what to look for we need to break apart $arabicInt into the units, 10s, 100s, 1000s
    // This will convert our integer into a string
    $arabicIntSplit = str_split($arabicInt);
    $arabicIntLength = strlen($arabicInt); // Could have checked length of $arabicIntSplit array too

    // Only slightly different code for each length of $arabicInt. Could this be a bit more DRY?
    if ($arabicIntLength == 1) {
        return $u[$arabicIntSplit[0]];
    }
    if ($arabicIntLength == 2) {
        return $d[$arabicIntSplit[0]].
               $u[$arabicIntSplit[1]];
    }

    if ($arabicIntLength == 3) {
        return $c[$arabicIntSplit[0]].
               $d[$arabicIntSplit[1]].
               $u[$arabicIntSplit[2]];
    }

    if ($arabicIntLength == 4) {
        return $m[$arabicIntSplit[0]].
               $c[$arabicIntSplit[1]].
               $d[$arabicIntSplit[2]].
               $u[$arabicIntSplit[3]];
    }

  }

  function parse($romanString) {
    // Check input is not empty and it doesn't have non Roman characters within it
    if ( $romanString == "" || !preg_match('/^[I V X L C D M]*$/', $romanString))  {
        error_log("Please provide a correct Roman numeral (e.g. I,II,V,X)", 0);
        return false;
    }
    
    // Roman numerals should be uppercase
    $romanString = strtoupper($romanString);
    
    // Build map of Roman representations against corresponding Arabic numbers
    $romanToArabicTable = array(
       "M" => "1000", "CM" => "900",
       "D" => "500", "CD" => "400",
       "C" => "100", "XC" => "90",
       "L" => "50", "XL" => "40",
       "X" => "10", "IX" => "9",
       "V" => "5", "IV" => "4",
       "I" => "1"
    );
            
    // Loop through all the possible representations and see if they exist at the start of the string
    $convertedValue = 0;
    foreach ( $romanToArabicTable as $roman => $arabic ) {
      // echo "<p>Looking for $roman in $romanString</p>";
      /*
      We need to run the below for as many times as it takes until there is no match
      The substr code slices $romanString left to right by the length of $roman.
      The sliced value is compared to $roman. If they match then we know the string
      starts with the representation, without the cost of using ^ in regex.
      */
      while ( substr( $romanString, 0, strlen($roman) ) === $roman  ) {
        // Add the value of the Arabic number to the cumulative value so far
        $convertedValue += $arabic;
        // Show whether or not we got a hit
        // echo "<p>Hit for $roman ($arabic) - total value is now $convertedValue</p>";
        // Remove the hit from the Roman string so the while loop can do its thing
        $romanString = substr($romanString, strlen($roman));
      }
    }

    return $convertedValue;
    }

}

?>