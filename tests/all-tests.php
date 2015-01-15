<?php
require 'config.php';
require dirname(dirname(__FILE__)).'/roman-numeral-tool.php';

/*
To test we need a set of valid conversions. I'm going to collect a random sample using 
the Wolfram Alpha API. It's a trustworthy source for all things mathematical and it's also got
an easy to use API. I could collect a set of valid conversions myself, but this is more
automated and will cover a wider and more random spread of Roman <-> Arabic conversions. We'll
download the corpus of comparisons once then save it to disk. This might be going a little
bit overboard and might not be the approach I'd take if time was more limited, but I always
enjoy working with new APIs.
*/

class RomanNumeralToolTest {

    function __construct($init) {
        $this->wolframAlphaAPIKey = $init;
    }

    private function generateRandomArabicNumbers() {
        // Generate set of random values to convert, returns array
        $maxValues = 1; // Only generate up to this number of values
        $randomValues = array();
        
        for ($n=1; $n<=$maxValues; $n++) {
            $randomValues[] = rand(1,3999); // Only generate numbers that match the caveats of the challenge
        }
        
        return $randomValues;
    }
    
    private function getValidComparisonData() {
        // If we already have a set of valid comparison data then use it to avoid making more HTTP requests
        $validResultsJSON = 'valid-comparisons.json';
        if (file_exists($validResultsJSON)) {
            $json = json_decode(file_get_contents($validResultsJSON), true);
            return $json;
        } 

        // If we don't have an API key for Wolfram Alpha then go no further
        if (empty($this->wolframAlphaAPIKey)) {
            throw new Exception('Please supply a Wolfra Alpha API key');
        }
        
        // Otherwise generate set of random values to convert
        $queryValues = $this->generateRandomArabicNumbers();
        
        foreach ($queryValues as $arabicValue) {
            // Make call to Wolfram to get the value's Roman equivalent
            $url = "http://api.wolframalpha.com/v2/query?appid=".$this->wolframAlphaAPIKey."&input=".$arabicValue."%20in%20Roman%20numerals&format=plaintext";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'RomanNumeralTool');
            $wolframData = curl_exec($ch);

            if(curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            } else {
                $xml = simplexml_load_string($wolframData);
            }

            curl_close($ch);
            
            // Check our XML object exists because sometimes the HTTP request to Wolfram fails and we don't have an object to work with
            if (isset($xml->pod[1]->subpod[0]->plaintext)) {
                $romanValue = (string)$xml->pod[1]->subpod[0]->plaintext;

                // Now we need to store the two forms of value together if we have a decent result
                // Sometimes Wolfram returned an empty Roman numeral value. Originally had error 
                // checking for this in testParse() and testGenerate() but it makes more sense to
                // fix the source of the error here

                if($romanValue !== "") {
                    $validComparisonResults[] = $romanValue;
                    $validComparisonValues[] = $arabicValue;
                }
            }

            
        }
        
        if(isset($validComparisonResults) && isset($validComparisonValues)) {
            $validResults = array_combine($validComparisonValues, $validComparisonResults);
            // Write set of results to a JSON file to use later
            $json = json_encode($validResults);
            $file = fopen($validResultsJSON, 'w');
            fwrite($file, $json);
            fclose($file);

            return $validResults;

        } else {
            throw new Exception('No data could be downloaded from Wolfram Alpha to test against.');
        }

    }
    
    private function testResultsAreSame($myOutput, $validOutput, $validInput) {
        // Testing my value of $myOutput ($validInput parsed/generated) against the expected value of $validOutput
        if ( $myOutput === $validOutput ) { 
            // Success, a match!
            return true;
        } else {
            // Provide debugging information. As we're not using a test framework (no 3rd party code) I'm printing feedback to screen
            echo "Testing against $validInput, expecting $validOutput, got $myOutput";
            return false;
        }
    }
    
    private function testResultsDisplay($function, $failCount) {
        if ( $failCount > 0 ) {
            $prefix = 'Fail: ';
        } else {
            $prefix = 'Success! ';
        }
        
        return "Testing $function(): " . PHP_EOL . "$prefix $failCount tests failed. " . PHP_EOL . PHP_EOL;
    
    }

    
    public function testParse() {

        $failCount = 0;
        $validComparisons = $this->getValidComparisonData();

        foreach ( $validComparisons as $validArabic => $validRoman ) {
            
            $validInput = $validRoman; // This is what we are decoding
            $validOutput = $validArabic; // This is what expect to see returned
            
            $RomanNumeralTool = new RomanNumeralTool();
            $myOutput = $RomanNumeralTool->parse($validInput); // This is what my tool is returning
            
            if(!$this->testResultsAreSame($myOutput,$validOutput, $validInput))  {
                $failCount = $failCount + 1;
            }
            
        }
        
        echo $this->testResultsDisplay(__FUNCTION__, $failCount);
        
    }
    
    public function testGenerate() {
        $failCount = 0;
        $validComparisons = $this->getValidComparisonData();        
        
        foreach ( $validComparisons as $validArabic => $validRoman ) {
            
            $validInput = $validArabic; // This is what we are decoding
            $validOutput = (string)$validRoman; // This is what expect to see returned
            
            $RomanNumeralTool = new RomanNumeralTool();
            $myOutput = $RomanNumeralTool->generate($validInput); // This is what my tool is returning
            
            if(!$this->testResultsAreSame($myOutput,$validOutput, $validInput)) {
                $failCount = $failCount + 1;
            }
            
        }
        
        echo $this->testResultsDisplay(__FUNCTION__, $failCount);
    }
}

// Run the tests:
$RomanNumeralToolTest = new RomanNumeralToolTest($wolframAlphaAPIKey);
$RomanNumeralToolTest->testParse();
$RomanNumeralToolTest->testGenerate();
?>