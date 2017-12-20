/* to deal with fixed nav bars in st cecilia's */
(function(document, history, location) {
    var HISTORY_SUPPORT = !!(history && history.pushState);

    var anchorScrolls = {
        ANCHOR_REGEX: /^#[^ ]+$/,
        OFFSET_HEIGHT_PX: 100,

        /**
         * Establish events, and fix initial scroll position if a hash is provided.
         */
        init: function() {
            this.scrollToCurrent();
            $(window).on('hashchange', $.proxy(this, 'scrollToCurrent'));
            $('body').on('click', 'a', $.proxy(this, 'delegateAnchors'));
        },

        /**
         * Return the offset amount to deduct from the normal scroll position.
         * Modify as appropriate to allow for dynamic calculations
         */
        getFixedOffset: function() {
            return this.OFFSET_HEIGHT_PX;
        },

        /**
         * If the provided href is an anchor which resolves to an element on the
         * page, scroll to it.
         * @param  {String} href
         * @return {Boolean} - Was the href an anchor.
         */
        scrollIfAnchor: function(href, pushToHistory) {
            var match, anchorOffset;

            if(!this.ANCHOR_REGEX.test(href)) {
                return false;
            }

            match = document.getElementById(href.slice(1));

            if(match) {
                anchorOffset = $(match).offset().top - this.getFixedOffset();
                $('html, body').animate({ scrollTop: anchorOffset});

                // Add the state to history as-per normal anchor links
                if(HISTORY_SUPPORT && pushToHistory) {
                    history.pushState({}, document.title, location.pathname + href);
                }
            }

            return !!match;
        },

        /**
         * Attempt to scroll to the current location's hash.
         */
        scrollToCurrent: function(e) {
            if(this.scrollIfAnchor(window.location.hash) && e) {
                e.preventDefault();
            }
        },

        /**
         * If the click event's target was an anchor, fix the scroll position.
         */
        delegateAnchors: function(e) {
            var elem = e.target;

            if(this.scrollIfAnchor(elem.getAttribute('href'), true)) {
                e.preventDefault();
            }
        }
    };

    $(document).ready($.proxy(anchorScrolls, 'init'));
})(window.document, window.history, window.location);



var currentDiv = "#openseadragon0";
var currentDivNum = 0;

var previousNum = 0;
var nextNum = 0;

$('.image-toggler').click(function(){
    $('div.image-toggle').hide();
    $($(this).attr('data-image-id')).show();
    currentDiv = $(this).attr('data-image-id');
    currentDivNum = parseInt(currentDiv.replace("#openseadragon", ""));

    previousNum = (currentDivNum - 1);
    nextNum = (currentDivNum + 1);

    var imageCounter = parseInt($("#imageCounter").text());
    if(previousNum == -1) { previousNum = (imageCounter - 1);}
    if(nextNum > (imageCounter - 1)) { nextNum = 0;}


    $("#prev").attr("data-image-id", "#openseadragon" + previousNum);
    $("#next").attr("data-image-id", "#openseadragon" + nextNum);
});

$("div[id^='navigator-'].navigator").css("background", "transparent none repeat scroll 0% 0%");
$("div[id^='navigator-'].navigator").css("border", "medium none");

$(".displayregion").css("border", "2px solid #630d0d");

$('.accordion-toggle').click(function(){

// push the footer to the bottom for piccolo
    var docHeight = $(window).height();
    var footerHeight = $('.footer-piccolo').height();
    var footerTop = $('.footer-piccolo').position().top + footerHeight;

    var footerMargin = docHeight - footerTop;

    if (footerTop < (docHeight)) {
        $('.footer-margin').css('height', footerMargin + 'px');
    }

});

// if it's chrome, get rid of flex
if(window.chrome) {
    $(".box").css("display", "inline-block");
}

//front page hover text
var e = document.getElementById(name^='gallery');
e.onmouseover = function() {
    document.getElementById('gallery-title').style.display = 'block';
}
e.onmouseout = function() {
    document.getElementById('gallery-title').style.display = 'none';
}
