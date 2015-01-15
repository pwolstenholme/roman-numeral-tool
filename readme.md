# Coding Kata: Roman Numerals

A coding exercise to develop a class to convert Arabic (1,2,3) to Roman (I,II,II) numbers, including tests and a web interface.

[![Screenshot of the web front end](http://roman.wolstenhol.me/screenshots/Close%20up.png)](http://roman.wolstenhol.me/web/)

You can run this submission on your machine or see the interface at [http://roman.wolstenhol.me](http://roman.wolstenhol.me/web/).

## Structure
````

├── README.md
├── early-drafts
│   ├── 1-first-attempt.php
│   ├── 2-second-attempt.php
│   ├── 3-third-attempt.php
│   ├── 4-first-test.php
│   └── 5-first-using-interface.php
├── roman-numeral-tool.php
├── screenshots
│   ├── Arabic to Roman.png
│   ├── Close up.png
│   ├── Error checking 1.png
│   ├── Error checking 2.png
│   ├── Mobile.png
│   └── Roman to Arabic.png
├── tests
│   ├── all-tests.php
│   ├── config.example.php
│   ├── config.php
│   └── valid-comparisons.json
└── web
    ├── ajax.php
    ├── css
    │   └── custom.css
    ├── gruntfile.js
    ├── img
    │   └── bg.png
    ├── index.html
    ├── js
    │   ├── main.js
    │   └── main.min.js
    ├── package.json
    └── sass
        └── custom.scss

````
Files and directories included in my submission:

* `README.md` - this file
* `roman-numeral-tool.php` - this is the bulk of the back end of my solution. It implements the given interface.

### Early drafts

* The `early-drafts` directory contains the first few iterations of my Roman -> Arabic solution. It shows my first successful solution (`2-second-attempt.php`), the first time I packaged my solution up in an OOP manner (`5-first-using-interface.php`) and my first approach to testing (`4-first-test.php`).

### Tests

* `tests` > `config.example.php` - this config file stores a Wolfram Alpha API key. This is only required if `valid-comparisons.json` needs to be rebuilt. Rename this file to `config.php` before use.
* `tests` > `all-tests.php` - run this in the CLI or browser to test my parsing and generating functions.
* `tests` > `valid-comparisons.json` - this is a JSON file of ~150 pairs of valid Roman to Arabic conversions, retrieved from the Wolfram Alpha API. This is used as a point of truth to test my functions against.

### Web

* This is the web front end of my application. The unminified CSS and JS can be seen in the `sass` and `js` directories. The minification and Sass compilation was performed automatically using Grunt. The Grunt packages used can be seen in `gruntfile.js`. jQuery's AJAX function is used to POST data to `ajax.php` which returns the converted (generated/parsed) value. jQuery is then used to insert the converted value into the DOM of the page.

### Screenshots

* Some screenshots are supplied to give an impression of the web interface.
	* [Arabic to Roman](http://roman.wolstenhol.me/screenshots/Arabic%20to%20Roman.png)
	* [Roman to Arabic](http://roman.wolstenhol.me/screenshots/Roman%20to%20Arabic.png)
	* [Viewed on a Nexus 5 Android device](http://roman.wolstenhol.me/screenshots/Mobile.png)
	* Error checking: [1](http://roman.wolstenhol.me/screenshots/Error%20checking%201.png) and [2](http://roman.wolstenhol.me/screenshots/Error%20checking%202.png)

## My approach

* I was initially  a little fazed by the combinations of rules involved in generating and parsing Roman numerals. This feeling passed when I wrote some Roman numerals down on a sheet of paper and started thinking about how I would decode them in my head.
	* I realised that all I needed to worry about was working from left to right and looking for any one of 13 combinations. A tally of the combinations could be kept then finally all their values added together to give the final sum.
* Each of these combinations had a set value (e.g. M = 100, CM = 900). If I looked for these in descending order of value and stopped at the first combination that I found, then that combination could be recorded and removed from the string, and the next one looked for. When the right hand end of the string had been reached, all the combinations that had been removed could have their valued added together to give the parsed value of the Roman numeral.
* I initially searched for the combinations using a regular expression (using ^ to find matches at the start of the string), but I wanted to avoid relying on regular expressions as I thought there would be a less costly way of doing it.
	* My solution was to use `substr()` and `strlen()`. We know how many characters our combinations are (e.g. CM is 2 characters), so if I chopped into the left hand side of the string by that many number of characters I would get a shortened string which I could compare to my combination. If they matched then we had a match without using any regex!
* Once I had got running and felt comfortable with my parsing proof of concept code, I built it into a class and functions to implement the `RomanNumeralGenerator` interface.
* I then started to take a look at generating Roman numerals. I realised the key here was that the resulting numeral would be split into one to four columns - the units, tens, hundreds and thousands. Each column could only be 1-9, and we knew that each Arabic value had a corresponding Roman equivalent. I built arrays to hold the mapping of the integer value against the Roman value. 
	* To generate the Numeral I split the integer figure up into an array of its columns (units, tens, hundreds, thousands).
	* I looked at the length of the integer value, and depending on it's length (e.g. 1 2, 3, or 4) I built the Roman numeral by taking the column's integer and comparing it against my arrays of conversions. I've included an example below:

	````php
	$u = array();   
    $u[0] = '';
    $u[1] = 'I'; // 1
	 // ...
    $u[8] = 'VIII';
    $u[9] = 'IX';

    $d = array();   
    $d[0] = ''; 
	 // ...
    $d[9] = 'XC';

    $c = array();   
    $c[0] = '';
	 // ...
    $c[9] = 'CM';

    // This one is shortened as we only support up to 3999
    $m = array();   
    $m[0] = '';
	 // ...
    $m[3] = 'MMM';

    $arabicIntSplit = str_split($arabicInt);
    $arabicIntLength = strlen($arabicInt);

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
	````
	I was happy with this solution although I have a feeling I could have done the code above in a more efficient and a bit more 'DRY' manner. There seems to be a lot of repetition of those `$arabicIntSplit` values!

### Testing

* My first test was quite rudimentary. I created a set of Roman to Arabic conversions that were either obviously correct or I had checked against the Wolfram Alpha website (depending on the conversion's complexity - V compared to MCMLXXXIX for example). I looped through the array of correct conversions and compared my function's results to the results I had entered by hand.
* This worked but I worried that the examples I picked weren't 'tricky' enough ones. Had I missed an edge case? I had some time left to work on the exercise so started to think about how I could get a random sample of Roman numerals to test against.
* I decided to collect a random sample using the Wolfram Alpha API. It's a trustworthy source for all things mathematical and it's also got an easy to use API. I could collect a set of valid conversions myself like I did earlier, but this was more automated and covered a wider and more random spread of Roman <-> Arabic conversions. I wrote some code to query Wolfram Alpha's API, download a corpus of comparisons once, then save it to disk to use in future tests. This might be going a little bit overboard and might not be the approach I'd take if time was more limited, but I always enjoy working with new APIs and it was reassuring to test my class against almost 150 randomly generated conversions.

### Front-end choices

* I am a primarily front-end developer so this is the part of the challenge that came most naturally to me.
* The front end is fully responsive, thanks to the Bootstrap framework. Loading a whole framework incurs a penalty in terms of download performance, but in this case I am mitigating that slightly by only downloading the Bootstrap CSS - none of their JavaScript is required.
* Although the PHP backend validates the input, there is client side validation. The user can only enter A-Z/a-z characters in the Roman numerals field, and the value in the Arabic field can't be less than zero or over 3999.
* The main body font is Lato, which is the closest free web font available to Gill Sans MT, a [font used in the BBC logo's typeface](http://idsgn.org/posts/know-your-type-gill-sans/). The colour scheme is based on the iPlayer design language, with the distinctive magenta being used for the link colour and `input` focus effect.
* Roman numerals are displayed in a separate font (Marcellus) which suits Roman numerals better. The pairing of fonts also serves to emphasise the differences between the two number systems.
* The front end is designed to be as simple as possible to use. There is no form to submit as the user's conversion is carried out as they type. The form field they need to enter their Roman numeral into is even focussed for them on load to save them clicking into it. Analytics could be used to detect whether the application was used to convert Arabic numbers into Roman numerals, and if this was the case then the Arabic `input` could be automatically focussed instead.
* I used Grunt to minify both my CSS and JS to reduce their file size. Compass was used to get access to some mixins to vendor prefix some CSS3 properties like `box-shadow` and `transform`. The Bootstrap CSS and the jQuery JS are loaded from a CDN which can also bring performance gains. My front end scores well in [Google's PageSpeed insights](https://developers.google.com/speed/pagespeed/insights/?url=http%3A%2F%2Froman.wolstenhol.me%2Fweb%2F&tab=desktop) for both mobile experience and desktop speed.

## Assumptions and caveats
* In Roman numerals a vinculum symbol (I̅) can be used above a numeral to signify that it represents 1000x it's normal value. For our range of 0-3999 we could encounter I̅, I̅I̅, or I̅I̅I̅. This is not supported by my submission.
* My error handling/reporting approach could be built upon. Currently if the `parse` or `generate` functions need to thrown an error then it is logged to the PHP error log and then the function returns false. I was originally throwing exceptions but I didn't then find an approach to catching them. I also toyed with the idea of calling `die('error explanation')` so the user would see an error explanation. None of my approaches so far have been optimal, so this is something I would get a lot of satisfaction from improving in a future version of this application.
* There isn't strict checking of the validity of Roman numerals that are entered. At one point, entering XYLOPHONE would return a result of 10... I got around this by including a regular expression that checks for characters other than those that you would expect to see in a Roman numeral. This means XYLOPHONE won't return a result any more, but `IIIIIIIIIIIIIIIIIIIIIIIII` still returns 25 - the rule of no character repeating itself no more than three times wasn't enforced. Although, thinking about it now it could have been with another regular expression.
* I could have improved my testing by altering the JSON file of known valid comparisons to include a mix of uppercase and lowercase Roman numerals.
* I wanted to be able to prevent users entering 0 in the Arabic field while still allowing the field to be empty without triggering a warning. I spent quite a while wondering why I was getting no results from checking the value of my `input` to see if it was less than or equal to zero. I had a facepalm moment when I realised that (of course) `$(".js-arabic-Input")` was returning the value of zero as "0" (a string), rather than an integer that I could do less than/equal to comparisons on. This was quickly fixed with `parseInt(value) <= 0`.
* The front end uses a `translateY` CSS3 `transform` property to vertically center the content - this isn't available in IE8 or below. This would be relatively easy to find a workaround, but I didn't have time.
* The Bootstrap CSS and the jQuery JS are loaded from a CDN. This has performance benefits, but it means I am vulnerable if the CDN provider suffers a DDOS attack (services like Cloudflare have been targeted previously) or if they have a technical hitch or require downtime for maintenance. In a production level piece of work I would include a local fallback if the remote files failed to load.
* I haven't annotated my code with DocBlocks as I didn't have my environment set up to produce these automatically.
