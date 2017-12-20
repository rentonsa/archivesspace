<?php
// Set up some variables to easily refer to particular fields you've configured
$id_field = $this->skylight_utilities->getField('ID');
$title_field = $this->skylight_utilities->getField('Title');
$image_uri = $this->skylight_utilities->getField("Image URI");
$location = $this->skylight_utilities->getField("Spatial Coverage");
$type = $_GET['type'];
echo 'TYPE:'.$type;

if ($type == 'images')
{
?>

<div class="row">
    <div class="col-sm-9 col-xs-12">
        <div class="gallery-container">
            <?php
            $n = 0;
            foreach ($docs as $doc) {
//               Setting up variables if they exist

                $image_name = isset($doc[$image_uri][0]) ? $doc[$image_uri][0] : 'missing.jpg';
                $title = isset($doc[$title_field][0]) ? $doc[$title_field][0] : "Untitled";


                // $coverImageJSON = "http://127.0.0.1:8182/iiif/2/" . $image_name;

                $thumbnailLink = '<a  class= "record-link" href="./record/' . $doc['id'] . '" title = "' . $title . '"> ';
                $thumbnailLink .= '<img class="img-responsive" src ="' . $image_name . '" title="' . $title . '" /></a>';
                ?>

                <!--                Displaying-->
                <div class="row record invisible <?php echo $doc[$id_field] ?>">
                    <?php echo $thumbnailLink; ?>

                    <div class="col-sm-9 hidden-xs result-info">
                        <h4 class="record-title">
                            <a href="./record/<?php echo $doc['id'] ?>"><?php echo $title; ?></a>
                        </h4>
                    </div>
                </div>
                <hr class="visible-xs">
                <?php
//                End of for each
                $n++;
            } ?>
        </div>
        <!--        Pagination  -->
        <div class="row">
            <div class="centered text-center">
                <nav>
                    <ul class="pagination">
                        <?php
                        /*
                        foreach ($paginationlinks as $pagelink) {
                            echo $pagelink;
                        }*/
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <?php
    }

else
    {
        ?>
       <div class="row">
    <?php foreach ($locations as $index => $location) {
        if($index%2==0){?>
        <section class="full-height-section">
            <h3 class="col-xs-12 col-md-8 text-center"><?php echo $location; ?></h3>
            <div class="gallery-container col-xs-12 col-md-8">
                <?php
                foreach (array_slice($docs, 0, rand(2, 7), true) as $doc) {
                    $image_name = isset($doc[$coverImageName][0]) ? $doc[$coverImageName][0] : 'missing.jpg';
                    $title = isset($doc[$title_field][0]) ? $doc[$title_field][0] : "Untitled";

                    $coverImageJSON = "http://127.0.0.1:8182/iiif/2/" . $image_name;
                    $coverImageURL = $coverImageJSON . '/full/,400/0/default.jpg';
                    $thumbnailLink = '<a  class= "record-link" href="./record/' . $doc['id'] . '" title = "' . $title . '"> ';
                    $thumbnailLink .= '<img class="img-responsive" src ="' . $coverImageURL . '" title="' . $title . '" /></a>';
                    ?>
                    <div class="row record invisible <?php echo $doc[$id_field] ?>">
                        <?php echo $thumbnailLink; ?>

                        <div class="col-sm-9 hidden-xs result-info">
                            <h4 class="record-title">
                                <a href="./record/<?php echo $doc['id'] ?>"><?php echo $title; ?></a>
                            </h4>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="col-xs-12 col-md-4 map">
<!--                Add the id of the record in the id of the map, then change the initMap to accept
                    an argument which is the if of the map so that we do not have multiple id's-->

<!--                Add the location of each item in the quotes-->

                <div id="map">
                    <script>
                        $(window).bind("load", function() {
                            initMap(); addLocation("");
                        });
                    </script>
                </div>
            </div>
        </section>
    <?php }
    else{
    ?>
    <section class="full-height-section bg-darker">
        <div class="col-xs-12 col-md-4 map">
            <!--                Add the id of the record in the id of the map, then change the initMap to accept
                                an argument which is the if of the map so that we do not have multiple id's-->

            <!--                Add the location of each item in the quotes-->

            <div id="map">
                <script>
                    $(window).bind("load", function() {
                        initMap(); addLocation("");
                    });
                </script>
            </div>
        </div>
        <h3 class="col-xs-12 col-md-8 text-center"><?php echo $location; ?></h3>
        <div class="gallery-container col-xs-12 col-md-8">
            <?php
            foreach (array_slice($docs, 0, rand(2, 7), true) as $doc) {
                $image_name = isset($doc[$coverImageName][0]) ? $doc[$coverImageName][0] : 'missing.jpg';
                $title = isset($doc[$title_field][0]) ? $doc[$title_field][0] : "Untitled";

                $coverImageJSON = "http://127.0.0.1:8182/iiif/2/" . $image_name;
                $coverImageURL = $coverImageJSON . '/full/,400/0/default.jpg';
                $thumbnailLink = '<a  class= "record-link" href="./record/' . $doc['id'] . '" title = "' . $title . '"> ';
                $thumbnailLink .= '<img class="img-responsive" src ="' . $coverImageURL . '" title="' . $title . '" /></a>';
                ?>
                <div class="row record invisible <?php echo $doc[$id_field] ?>">
                    <?php echo $thumbnailLink; ?>

                    <div class="col-sm-9 hidden-xs result-info">
                        <h4 class="record-title">
                            <a href="./record/<?php echo $doc['id'] ?>"><?php echo $title; ?></a>
                        </h4>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </section>
    <?php }} ?>
</div>


    <?php
    }

?>