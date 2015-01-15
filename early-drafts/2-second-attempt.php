<?php
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
		// echo "<p>Looking for $roman in $romanString</p>";
		$pattern = "/^".$roman."/";
		// We need to run the below for as many times as it takes until there is no match
		while (preg_match($pattern, $romanString)) {
			// Assign the right value
			$convertedValue = $convertedValue + $arabic;
			// Show whether or not we got a hit
			// echo "<p>Hit for $roman ($arabic) - total value is now $convertedValue</p>";
			// Remove the hit so the loop can continue to do its thing
			$romanString = preg_replace($pattern, "", $romanString);
		}
	}
	return $convertedValue;
}

echo "<p style=\"font-weight:bold; color:red;\">Converted figure is ".decode('MMMCMXCIX')." (MMMCMXCIX)</p>";
// Can we get away from using regex for performance gains?
?>