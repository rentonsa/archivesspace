/**
 * Created by Kristiyan Tsvetanov on 23/03/2017.
 * Contact me: kristiyan.c@gmail.com
 */
(function($){
    var showNavigator = screen.width>768;
    var seadragon = OpenSeadragon({
        id: "openseadragon",
        prefixUrl: "https://test.collections.ed.ac.uk/assets/openseadragon/images/",
        toolbar: "toolbarDiv",
        zoomInButton: "zoom-in",
        zoomOutButton: "zoom-out",
        homeButton: "home",
        fullPageButton: "full-page",
        nextButton: "next",
        previousButton: "previous",
        showNavigator: showNavigator,
        mouseNavEnabled: false,
        tileSources: [imageSource],
        sequenceMode: true,
        nextButton: 'next-pic',
        previousButton: 'previous-pic'
    });

    $("#openseadragon").on('click', function () {
        seadragon.setMouseNavEnabled(true);
    });
    $("#openseadragon").on('mouseleave', function () {
        seadragon.setMouseNavEnabled(false);
    });

})(jQuery);
