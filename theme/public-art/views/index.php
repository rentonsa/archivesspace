<div class="content">
    <!-- todo replace two with sass or less -->
<div class="home">
    <ul class="cb-slideshow">
        <li><span></span><div><h3></h3></div></li>
        <li><span></span></li>
        <li><span></span></li>
        <li><span></span></li>
    </ul>
    <div class="text-center home-search-buttons col-xs-12">
        <form action = "./search/*:*/" class="home-search col-xs-4" method ="get">
            <input type="hidden" name ="type" value = "images"/>
            <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button><br>
            Search art on campus
        </form>
        <a href="./search/*:*/&type=locations" class="home-search col-xs-4">
            <i class="fa fa-map-marker" aria-hidden="true"></i><br>
            Search by location
        </a>
        <a href="./paolozzi" class="home-search col-xs-4">
            <i class="fa fa-info" aria-hidden="true"></i><br>
            Paolozzi Mosaic Project
        </a>
    </div>

</div>


    <script>
        $(function () {
            $(window).on("scroll", function () {
                if ($(window).scrollTop() > 50) {
                    $(".navbar").addClass("active");
                } else {
                    $(".navbar").removeClass("active");
                }
            });
        });
    </script>

