<?php
/*
1. We know that Roman numbers are represented using the below, and are constructed left to right

Representations:

 M = 1000
CM = 900
 D = 500
CD = 400
 C = 100
XC = 90
 L = 50
XL = 40
 X = 10
IX = 9
 V = 5
IV = 4
 I = 1

The plan:

* Take our string. Loop through the representations above and check whether they are found at the start of the string using a ^ in a regex.
* If they are found then add the corresponding int to the sum and remove the matching representations from the start of the string.
* When loop has completed we should have the result
*/

function decode($romanString) {
	// Build array of Roman representations against corresponding Arabic numbers
	$romanToArabicTable = [
		 "M" => "1000",
		"CM" => "900",
		 "D" => "500",
		"CD" => "400",
		 "C" => "100",
		"XC" => "90",
		 "L" => "50",
		"XL" => "40",
		 "X" => "10",
		"IX" => "9",
		 "V" => "5",
		"IV" => "4",
		 "I" => "1"
	];
	// Loop through all the possible representations and see if they exist at the start of the string
	$convertedValue = 0;
	foreach ( $romanToArabicTable as $roman => $arabic ) {
		if (preg_match("/^".$roman."/", $romanString)) {
			// Show whether or not we got a hit (the maths can come later)
			echo "Hit for $roman ($arabic) </br>";
			// Assign the right value
			$convertedValue = $convertedValue + $arabic;
			// Remove the hit so the loop can continue to do its thing
			$romanString = str_replace($roman, "", $romanString);
		}
	}
	return $convertedValue;
}

echo decode("MMMCMXCIX");
// First attempt gave hits for MCXI or 1111 - It needs a while loop for the regex and also to use preg_replace not str_replace

?>