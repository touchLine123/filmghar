"use strict";

// menu options custom affix
var fixed_top = $(".header");
$(window).on("scroll", function () {
  if ($(window).scrollTop() > 50) {
    fixed_top.addClass("animated fadeInDown menu-fixed");
  } else {
    fixed_top.removeClass("animated fadeInDown menu-fixed");
  }
});

$(".navbar-toggler").on("click", function () {
  $(".header").toggleClass("active");
});

$(".nav-right__search-btn").on("click", function () {
  $(".header-search-area").addClass("active");
});
//close when click off of container
$(document).on("click touchstart", function (e) {
  if (
    !$(e.target).is(
      ".nav-right__search-btn, .nav-right__search-btn *, .header-search-form, .header-search-form *"
    )
  ) {
    $(".header-search-area").removeClass("active");
  }
});

// mobile menu js
$(".navbar-collapse>ul>li>span, .navbar-collapse ul.sub-menu>li>span").on(
  "click",
  function () {
    const element = $(this).parent("li");
    if (element.hasClass("open")) {
      element.removeClass("open");
      element.find("li").removeClass("open");
    } else {
      element.addClass("open");
      element.siblings("li").removeClass("open");
      element.siblings("li").find("li").removeClass("open");
    }
  }
);

let img = $(".bg_img");
img.css("background-image", function () {
  let bg = "url(" + $(this).data("background") + ")";
  return bg;
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

$("#langSel").niceSelect();

new WOW().init();

// lightcase plugin init
$("a[data-rel^=lightcase]").lightcase();

// mainSlider
function mainSlider() {
  var BasicSlider = $(".hero__slider");
  BasicSlider.on("init", function (e, slick) {
    var $firstAnimatingElements = $(".movie-slide:first-child").find(
      "[data-animation]"
    );
    doAnimations($firstAnimatingElements);
  });
  BasicSlider.on("beforeChange", function (e, slick, currentSlide, nextSlide) {
    var $animatingElements = $(
      '.movie-slide[data-slick-index="' + nextSlide + '"]'
    ).find("[data-animation]");
    doAnimations($animatingElements);
  });
  BasicSlider.slick({
    autoplay: true,
    autoplaySpeed: 3000,
    dots: true,
    fade: false,
    arrows: false,
    // nextArrow: '<div class="next"><i class="las la-long-arrow-alt-right"></i></div>',
    // prevArrow: '<div class="prev"><i class="las la-long-arrow-alt-left"></i></div>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
        },
      },
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        },
      },
    ],
  });

  function doAnimations(elements) {
    var animationEndEvents =
      "webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend";
    elements.each(function () {
      var $this = $(this);
      var $animationDelay = $this.data("delay");
      var $animationType = "animated " + $this.data("animation");
      $this.css({
        "animation-delay": $animationDelay,
        "-webkit-animation-delay": $animationDelay,
      });
      $this.addClass($animationType).one(animationEndEvents, function () {
        $this.removeClass($animationType);
      });
    });
  }
}
mainSlider();

$(".button").click(function () {
  var buttonId = $(this).attr("id");
  $("#modal-container").removeAttr("class").addClass(buttonId);
  $("body").addClass("modal-active");
});

$(".modal-close").click(function () {
  $("#modal-container").addClass("out");
  $("body").removeClass("modal-active");
});

$(function () {
  $(".movie-list-scroll").slimScroll({
    height: "420px",
  });
});

$(".movie-slider-one").slick({
  autoplay: true,
  slidesToShow: 7,
  slidesToScroll: 1,
  infinite: true,
  speed: 700,
  dots: false,
  arrows: true,
  nextArrow:
    '<div class="next"><i class="las la-long-arrow-alt-right"></i></div>',
  prevArrow:
    '<div class="prev"><i class="las la-long-arrow-alt-left"></i></div>',
  responsive: [
    {
      breakpoint: 1650,
      settings: {
        slidesToShow: 5,
      },
    },
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 4,
      },
    },
    {
      breakpoint: 992,
      settings: {
        slidesToShow: 3,
      },
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 2,
      },
    },
    {
      breakpoint: 512,
      settings: {
        slidesToShow: 2,
      },
    },
  ],
});

$(".trailer-slider").slick({
  autoPlay: true,
  slidesToShow: 6,
  slidesToScroll: 1,
  infinite: false,
  speed: 700,
  dots: false,
  arrows: true,
  nextArrow:
    '<div class="next"><i class="las la-long-arrow-alt-right"></i></div>',
  prevArrow:
    '<div class="prev"><i class="las la-long-arrow-alt-left"></i></div>',
  responsive: [
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 5,
      },
    },
    {
      breakpoint: 992,
      settings: {
        slidesToShow: 4,
      },
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 3,
      },
    },
    {
      breakpoint: 576,
      settings: {
        slidesToShow: 2,
      },
    },
  ],
});

// Animate the scroll to top
$(".scroll-to-top").on("click", function (event) {
  event.preventDefault();
  $("html, body").animate({ scrollTop: 0 }, 300);
});

//preloader js code
$("#preloader")
  .delay(300)
  .animate(
    {
      opacity: "0",
    },
    300,
    function () {
      $("#preloader").css("display", "none");
    }
  );

setInterval(function () {
  $("#first-popup").addClass("active");
}, 3000);

$(".first-popup-close").on("click", function () {
  $("#first-popup").addClass("close");
});

var $offerCountdown5 = $(".draw-countdown");
if ($offerCountdown5.length) {
  $offerCountdown5.each(function () {
    var jc_year = parseInt($(this).attr("data-year"));
    if (!jc_year) jc_year = 1;
    var jc_month = parseInt($(this).attr("data-month"));
    if (!jc_month) jc_month = 1;
    var jc_day = parseInt($(this).attr("data-day"));
    if (!jc_day) jc_day = 1;
    var jc_hour = parseInt($(this).attr("data-hour"));
    if (!jc_hour) jc_hour = 1;

    $.syotimerLang.neng = {
      second: ["sec", "sec"],
      minute: ["min", "min"],
      hour: ["hrs", "hrs"],
      day: ["days", "days"],
    };

    $offerCountdown5.syotimer({
      lang: "neng",
      year: jc_year,
      month: jc_month,
      day: jc_day,
      hour: jc_hour,
      minute: 59,
    });
  });
}

let inputField = $("#update-photo");
let uploadImg = $("#upload-img");

inputField.on("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function () {
      const result = reader.result;
      uploadImg.attr("src", result);
    };
    reader.readAsDataURL(file);
  }
});

$(".copy-code").on("click", function () {
  var copyText = $(".party-code");
  copyText = copyText[0];
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  document.execCommand("copy");
  copyText.blur();
});

$(document).ready(function () {
  const $mainlangList = $(".langList");
  const $langBtn = $(".language-content");
  const $langListItem = $mainlangList.children();

  $langListItem.each(function () {
    const $innerItem = $(this);
    const $languageText = $innerItem.find(".language_text");
    const $languageFlag = $innerItem.find(".language_flag");

    $innerItem.on("click", function (e) {
      $langBtn.find(".language_text_select").text($languageText.text());
      $langBtn.find(".language_flag").html($languageFlag.html());
    });
  });
});
