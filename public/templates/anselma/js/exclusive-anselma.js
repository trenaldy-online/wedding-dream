// Gallery Single Slider
window.GALLERY_SINGLE_SLIDER = true;


// Resize Photo Nav
var resize_photo_nav = function() {
    var $nav = $('.photo-nav');

    // width
    var width = $nav.width() || 1;

    // decrease size to smaller size to parent width
    width = Math.floor(width - (38.4 / 100) * width);    

    // set maximal height
    var height = width + (width / 3);
    
    // each height
    $nav.find('.preview-wrap').each((i, o) => {
        $(o).css({
            'width': `${width}px`,
            'height': `${height}px`
        });
    });
}


// toggle show wedding gift bank
var toggleGift = function () {
    $(".gift-form-sender-wrapper").slideToggle();
};
var toggleKado = function () {
    $(".hadiah-content").slideToggle();
};

$(".slider-bank-wrap").on('click', () => {
    $('.slider-bank-wrap').toggleClass("active");
    toggleGift()
});

let checkInterval = setInterval(() => {
  if ($('.wk-wrap').length > 0) {
    clearInterval(checkInterval);

    // pasang event disini
    $(".wk-wrap").on('click', function () {
      $(this).toggleClass("active");
      toggleKado();
    });
  }
}, 300);


setTimeout(() => {
  clearInterval(checkInterval);
}, 5000);



$(".wedding-gift__next").on('click', () => {
    $('.wedding-gift-details').css({
        opacity: 0,
    });
    $('.wedding-gift-picture').css({
        opacity: 1,
    });
});

$(".wedding-gift__prev").on('click', () => {
    $('.wedding-gift-details').css({
        opacity: 1,
    });
    $('.wedding-gift-picture').css({
        opacity: 0,
    });
});

$(document).ready(function() {
    const video = $('#tcVideo');
    const source = $('#tcVideoSource');
    
    function setVideoSource() {
        const width = window.innerWidth;
        let videoSrc;
        
        if (width <= 560) {
            videoSrc = 'https://katsudoto.id/media/template/exclusive/anselma/original/vid-mb.mp4';
        } else {
            videoSrc = 'https://katsudoto.id/media/template/exclusive/anselma/original/vid-tab.mp4';
        }
        
        const currentSrc = source.attr('src');
        if (currentSrc !== videoSrc) {
            source.attr('src', videoSrc);
            
            video[0].load();
            
            video[0].play().catch(function(error) {
                console.log('Video autoplay prevented:', error);
            });
        }
        
    }
    
    setVideoSource();
    
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(setVideoSource, 250);
    });
    
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible' && source.attr('src')) {
            video[0].play().catch(function(error) {
                console.log('Video play prevented:', error);
            });
        }
    });
});

// On Ready
$(document).ready(function () {
    resize_photo_nav();

    startSliderSyncing();

    $(".photo-arrow.next").on('click', function () {
        $(".slider-syncing__preview").slick('slickNext');
    });
    $(".photo-arrow.prev").on('click', function () {
        $(".slider-syncing__preview").slick('slickPrev');
    });
})