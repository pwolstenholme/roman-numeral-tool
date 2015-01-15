<?php
///////////////////////////////////////////////////////////////////
// Testing against known conversions verified using Wolfram Alpha
///////////////////////////////////////////////////////////////////

$knownValidConversions = [
	"1" => "I",
	"2" => "II",
	"3" => "III",
	"4" => "IV",
	"5" => "V",
	"6" => "VI",
	"7" => "VII",
	"8" => "VIII",
	"9" => "IX",
	"10" => "X",
	"11" => "XI",
	"20" => "XX",
	"30" => "XXX",
	"40" => "XL",
	"50" => "L",
	"60" => "LX",
	"100" => "C",
	"111" => "CXI",
	"150" => "CL",
	"200" => "CC",
	"500" => "D",
	"1000" => "M",
	"1989" => "MCMLXXXIX",
	"2000" => "MM",
	"3999" => "MMMCMXCIX"
];

function testDecode($knownValidConversions) {
	$failCount = 0;
	foreach ( $knownValidConversions as $validOutput => $validInput ) {
		$myOutput = decode($validInput);
		if ( $myOutput !== $validOutput ) { 
			$failCount = $failCount + 1;
			echo "<p style=\"color: red;\">Testing against $validInput, expecting $validOutput, got $myOutput</p>";
		}
	}
	
	if ( $failCount > 0 ) {
		$color = 'red';
		$prefix = 'Fail: ';
	} else {
		$color = 'green';
		$prefix = 'Success: ';
	}
	
	echo "<p style=\"color: $color\">$prefix $failCount tests failed</p>";
}

testDecode($knownValidConversions);

// This worked but I worried that the examples I picked weren't 'tricky' enough ones. Had I missed an edge case? I had some time left to work on the exercise so started to think about how I could get a random sample of Roman numerals to test against.
?>

