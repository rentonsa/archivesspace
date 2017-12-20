/**
 * Created by Kristiyan on 23/05/2017.
 */

var map_mode = false;

$(".map-view").click(function(){
    //Toggle map mode
    map_mode = !map_mode;

    map_mode ? mapMode() : galleryMode();

    centerMap();

});

function galleryMode(){
    $('.map-view').text('Map view');
    $('.gallery').fadeIn(1000).addClass('col-md-7');
    $('.sidebar').removeClass('col-md-12').addClass('col-md-5');
    google.maps.event.trigger(map, "resize");
}

function mapMode(){
    $('.map-view').text('Gallery view');
    $('.gallery').fadeOut(1000).removeClass('col-md-7');
    $('.sidebar').removeClass('col-md-5').addClass('col-md-12');
    google.maps.event.trigger(map, "resize");
    enableScrollingWithMouseWheel();

    for (var key in markers) {
        markers[key].setOpacity(1);
    }
}