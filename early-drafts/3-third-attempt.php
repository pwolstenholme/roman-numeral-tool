<?php
// Third version (no regex so more performant, but at the cost of readability?)

function decode($romanString) {
	// Check input is not empty
	if ( $romanString === "" ) {
		throw new InvalidArgumentException('Please provide a string');
	}

	$romanString = strtoupper($romanString);
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
		// echo "<p>Looking for $roman in $romanString</p>";
		// We need to run the below for as many times as it takes until there is no match
		// The substr code slices $romanString left to right by the length of the $roman.
		// The sliced value is compared to $roman. If they match then we know the string starts with the representation, without the cost of regex
		while ( substr( $romanString, 0, strlen($roman) ) === $roman  ) {
			// Assign the right value
			$convertedValue = $convertedValue + $arabic;
			// Show whether or not we got a hit
			// echo "<p>Hit for $roman ($arabic) - total value is now $convertedValue</p>";
			// Remove the hit so the loop can continue to do its thing
			$romanString = substr($romanString, strlen($roman));
		}
	}
	return $convertedValue;
}


echo "<p style=\"font-weight:bold; color:red;\">Converted figure is ".decode('MCMLXXXIX')."</p>";
// Now to find a way to test it. Let's get a set of known valid comparisons then we can see if my function comes to the same conclusions
?>