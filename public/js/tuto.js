<script src="https://raw.github.com/elevateweb/elevatezoom/master/jquery.elevatezoom.js"></script>

    <img style="border:1px solid #e8e8e6;" id="zoom_03" src="http://www.elevateweb.co.uk/wp-content/themes/radial/zoom/images/small/image3.png"
        data-zoom-image="http://www.elevateweb.co.uk/wp-content/themes/radial/zoom/images/large/image3.jpg"
        width="411" />

    <div id="gallery_01">

        <a href="#" class="elevatezoom-gallery active" data-update="" data-image="https://image1.jpg" data-zoom-image="image1.jpg">
            <img src="https://image1.jpg" width="100" />
        </a>

        <a href="#" class="elevatezoom-gallery" data-update="" data-image="https://image2.jpg" data-zoom-image="https://image2.jpg">
            <img src="https://image2.jpg" width="100" />
        </a>

        <a href="#" class="elevatezoom-gallery" data-image="http://image3.jpg" data-zoom-image="http://image3.jpg">
            <img src="http://image3.jpg" width="100" />
        </a>

        <a href="#" class="elevatezoom-gallery" data-update="" data-image="http://image4.jpg" data-zoom-image="http://image4.jpg">
            <img src="http://image4.jpg" width="100" />
        </a>

        <a href="#" class="elevatezoom-gallery" data-update="" data-image="images/large/image5.jpg" data-zoom-image="images/large/image5.jpg">
            <img src="images/large/image5.jpg" width="100" />
        </a>

    </div>

//$("#zoom_03").elevateZoom({gallery:'gallery_01', cursor: 'crosshair', galleryActiveClass: "active", zoomType: "inner" }); 


/*var image = $('#gallery_01 a');
var zoomConfig = {  };
var zoomActive = false;

image.on('click', function(){

        $.removeData(image, 'elevateZoom');//remove zoom instance from image
  
        image.elevateZoom(zoomConfig);//initialise zoom
    
});*/
var zoomConfig = { cursor: 'crosshair', zoomType: "inner" };
var image = $('#gallery_01 a');
var zoomImage = $('img#zoom_03');

zoomImage.elevateZoom(zoomConfig);//initialise zoom

image.on('click', function () {
    // Remove old instance od EZ
    $('.zoomContainer').remove();
    zoomImage.removeData('elevateZoom');
    // Update source for images
    zoomImage.attr('src', $(this).data('image'));
    zoomImage.data('zoom-image', $(this).data('zoom-image'));
    // Reinitialize EZ
    zoomImage.elevateZoom(zoomConfig);
});