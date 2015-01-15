$(function() {
    
    var inputFields = $(".js-arabicInput, .js-romanInput");

    // Clear other field when one is clicked into
    $(inputFields).on("focus keyup", function() {
        $(inputFields).not(this).val('');
    });

    // Error handling:
    function triggerError(errorText, problemElement) {
        // Set text
        if (errorText) {
            $('.js-error-text').html(errorText);
        }
        // Clear conversion if it's errorneous
        //$('input').val('');
        console.log(problemElement);

        // Add class to .form-group parent
        $(problemElement).parent().addClass('has-error');
        // Show error region
        $('.js-error-row').show();
    }

    function clearError(problemElement) {
        // Hide error region
        $('.js-error-row').hide();
        // Remove class
        $('input').parent().removeClass('has-error');
        // Set generic error message just incase error is called without errorText
        $('.js-error-text').html('An error has occurred');
    }

    // Check Arabic field is numeric
    $(".js-arabicInput").on("change keyup focus blur", function() {
        var value = $(this).val();
        // originally if ( !$.isNumeric( value ) && value.length > 0 ) {
        // Only allow numbers that are between 1 and 3999
        if ( ( isNaN(value) && value.length > 0 ) || parseInt(value) <= 0 || parseInt(value) > 3999 ) {
            triggerError(
                'This field is only meant for Arabic numbers like 1,2,5,10. The maximum value is 3999 and the minimum is 1.', this);
        } else {
            clearError(this);
        }
    });

    // Check Roman field is only alpha
    $(".js-romanInput").on("change keyup focus blur", function() {
        var value = $(this).val();
        var matches = value.match(/^[I V X L C D M]*$/i); // Returns null if the string is not empty, but doesn't contain a character that could make up a roman numeral (case insensitive)
        if (matches == null) {
            triggerError(
                'This field is only meant for Roman numerals like <span class="roman-type">I,II,V,X</span>', this);
        } else {
            clearError(this);
        }
    });

    function postAndReceiveConversions(action, input, dataTarget) {
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            data: {'action': action, 'input': input },
            success: function(data, status) {
                // Try and make the updating of the fields smoother by fading (from the CSS transition property) the text to white while it is being updated
                $(dataTarget).addClass('text-light');
                $(dataTarget).val(data);
                $(dataTarget).removeClass('text-light');
            },
            error: function(xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }

    // AJAX calls on user input
    $(".js-arabicInput").on("keyup", function() {
        postAndReceiveConversions('generate', $(".js-arabicInput").val(), '.js-romanInput');
    } );

    $(".js-romanInput").on("keyup", function() {
        postAndReceiveConversions('parse', $(".js-romanInput").val(), '.js-arabicInput');
    } );

}); 