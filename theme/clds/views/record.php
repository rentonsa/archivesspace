<?php

$subject_field = $this->skylight_utilities->getField("Subject");
$type_field = $this->skylight_utilities->getField("Type");
$bitstream_field = $this->skylight_utilities->getField("Bitstream");
$thumbnail_field = $this->skylight_utilities->getField("Thumbnail");


$type = 'Unknown';

if(isset($solr[$type_field])) {
    $type = "media-" . strtolower(str_replace(' ','-',$solr[$type_field][0]));
}


?>


<h1 class="itemtitle"><?php echo $record_title ?></h1>
<div class="tags">
    <?php

    if (isset($solr[$subject_field])) {
        foreach($solr[$subject_field] as $subject) {

            $orig_filter = urlencode($subject);

            $lower_orig_filter = strtolower($subject);
            $lower_orig_filter = urlencode($lower_orig_filter);

            echo '<a class="subject" href="./search/*:*/Subject:%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$subject.'</a>';

        }
    }

    ?>
</div>

<div class="content">

    <?php
    $abstract_field = $this->skylight_utilities->getField("Abstract");
    if(isset($solr[$abstract_field])) {
        ?> <h3>Abstract</h3> <?php
        foreach($solr[$abstract_field] as $abstract) {
            echo '<p>'.$abstract.'</p>';
        }
    }
    ?>

    <table>
        <tbody>
        <?php foreach($recorddisplay as $key) {

            $element = $this->skylight_utilities->getField($key);
            if(isset($solr[$element])) {
                echo '<tr><th>'.$key.'</th><td>';
                foreach($solr[$element] as $index => $metadatavalue) {
                    echo $metadatavalue;
                    if($index < sizeof($solr[$element]) - 1) {
                        echo '; ';
                    }
                }
                echo '</td></tr>';
            }

        } ?>
        </tbody>
    </table>

    <?php if(isset($solr[$bitstream_field]) && $link_bitstream) {
    ?><div class="record_bitstreams"><?php


        $numThumbnails = 0;
        $mainImage = false;
        $videoFile = false;
        $audioFile = false;
        $audioLink = "";
        $videoLink = "";
        foreach($solr[$bitstream_field] as $bitstream) {

            $b_segments = explode("##", $bitstream);
            $b_filename = $b_segments[1];
            $b_handle = $b_segments[3];
            $b_seq = $b_segments[4];
            $b_handle_id = preg_replace('/^.*\//', '',$b_handle);
            $b_uri = './record/'.$b_handle_id.'/'.$b_seq.'/'.$b_filename;

            if (strpos($b_uri, ".jpg") > 0)
            {
                if (!$mainImage) {

                    $bitstreamLink = '<div class="main-image">';

                    $bitstreamLink .= '<a title = "' . $record_title . '" class="fancybox" rel="group" href=' . $b_uri . '> ';
                    $bitstreamLink .= '<img class="record-main-image" src = "'. $b_uri .'">';
                    $bitstreamLink .= '</a>';

                    /* $bitstreamLink .= '<p><span class="bitstream_size">';
                    $bitstreamLink .= getBitstreamSize($bitstream);
                    $bitstreamLink .= '</span></p>'; */
                    $bitstreamLink .= '</div>';

                    $mainImage = true;

                }
                else {

                    if(isset($doc[$thumbnail_field])) {
                        $thumbnail = $doc[$thumbnail_field][0];

                        $t_segments = explode("##", $thumbnail);
                        $t_filename = $t_segments[1];
                        $t_handle = $t_segments[3];
                        $t_seq = $t_segments[4];
                        $t_handle_id = preg_replace('/^.*\//', '',$t_handle);
                        $t_uri = './record/'.$t_handle_id.'/'.$t_seq.'/'.$t_filename;

                        $thumbnailLink[$numThumbnails] = '<div class="thumbnail-tile">';
                        $thumbnailLink[$numThumbnails] .= '<a title = "' . $record_title . '" class="fancybox" rel="group" href=' . $t_uri . '> ';
                        $thumbnailLink[$numThumbnails] .= '<img src = "'.$t_uri.'" class="record-thumbnail" title="'. $record_title .'" /></a></div>';

                    }
                    else {

                        $thumbnailLink[$numThumbnails] = '<div class="thumbnail-tile">';
                        $thumbnailLink[$numThumbnails] .= '<a title = "' . $record_title . '" class="fancybox" rel="group" href=' . $b_uri . '> ';
                        $thumbnailLink[$numThumbnails] .= '<img src = "'.$b_uri.'" class="record-thumbnail" title="'. $record_title .'" /></a></div>';
                    }

                    $numThumbnails++;

                }

            }
            else if (strpos($b_uri, ".mp3") > 0) {

                $audioLink .= '<script src="http://api.html5media.info/1.1.6/html5media.min.js"></script>';
                $audioLink .= '<audio src="'.$b_uri.'" controls preload></audio>';

                $audioFile = true;
            }


            else if (strpos($b_uri, ".mp4") > 0)
            {
                $videoLink .= '<script src="http://api.html5media.info/1.1.6/html5media.min.js"></script>';
                $videoLink .= '<video width="320" height="200" controls> <source src="'.$b_uri.'" type="video/mp4">Sorry, it does not work</video>';

                $videoFile = true;
            }

            ?>
        <?php
        }

        if($mainImage) {

            echo $bitstreamLink;
            echo '<div class="clearfix"></div>';
        }

        if($numThumbnails > 0) {

            echo '<div class="thumbnail-strip">';

            foreach($thumbnailLink as $thumb) {
                echo $thumb;
            }
            echo '</div><div class="clearfix"></div>';
        }

        if($audioFile) {

            echo $audioLink;
        }

        if($videoFile) {

            echo $videoLink;
        }

        echo '</div><div class="clearfix"></div>';

        }

        echo '</div>';
        ?>


