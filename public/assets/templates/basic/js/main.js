(function ($) {
  "user strict";

  // preloader
  $(".preloader")
    .delay(800)
    .animate(
      {
        opacity: "0",
      },
      800,
      function () {
        $(".preloader").css("display", "none");
      }
    );

  // wow
  if ($(".wow").length) {
    var wow = new WOW({
      boxClass: "wow",
      // animated element css class (default is wow)
      animateClass: "animated",
      // animation css class (default is animated)
      offset: 0,
      // distance to the element when triggering the animation (default is 0)
      mobile: false,
      // trigger animations on mobile devices (default is true)
      live: true, // act on asynchronously loaded content (default is true)
    });
    wow.init();
  }

  //Create Background Image
  (function background() {
    let img = $(".bg_img");
    img.css("background-image", function () {
      var bg = "url(" + $(this).data("background") + ")";
      return bg;
    });
  })();

  // header-fixed
  var fixed_top = $(".header-section");
  $(window).on("scroll", function () {
    if ($(window).scrollTop() > 500) {
      fixed_top.addClass("animated fadeInDown header-fixed");
    } else {
      fixed_top.removeClass("animated fadeInDown header-fixed");
    }
  });

  // navbar-click
  $(".navbar li span").on("click", function () {
    var element = $(this).parent("li");
    if (element.hasClass("show")) {
      element.removeClass("show");
      element.find("li").removeClass("show");
    } else {
      element.addClass("show");
      element.siblings("li").removeClass("show");
      element.siblings("li").find("li").removeClass("show");
    }
  });

  // scroll-to-top
  var ScrollTop = $(".scrollToTop");
  $(window).on("scroll", function () {
    if ($(this).scrollTop() < 500) {
      ScrollTop.removeClass("active");
    } else {
      ScrollTop.addClass("active");
    }
  });

  /* ---------------------------------------------
  ## Draw Count Down
--------------------------------------------- */
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

  // slider
  var swiper = new Swiper(".banner-slider", {
    slidesPerView: 3,
    spaceBetween: 20,
    loop: true,
    effect: "coverflow",
    coverflowEffect: {
      rotate: 0,
      stretch: 80,
      depth: 200,
      modifier: 1,
      slideShadows: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    autoplay: {
      speed: 1000,
      delay: 3000,
    },
    speed: 1000,
    breakpoints: {
      1479: {
        slidesPerView: 3,
      },
      1199: {
        slidesPerView: 2,
      },
      991: {
        slidesPerView: 3,
      },
      767: {
        slidesPerView: 2,
      },
      575: {
        slidesPerView: 2,
      },
      420: {
        slidesPerView: 2,
      },
    },
  });

  // slider
  var swiper = new Swiper(".movie-slider", {
    slidesPerView: 5,
    spaceBetween: 30,
    loop: true,
    navigation: {
      nextEl: ".slider-next",
      prevEl: ".slider-prev",
    },
    autoplay: {
      speed: 1000,
      delay: 5000,
    },
    speed: 1000,
    breakpoints: {
      1479: {
        slidesPerView: 4,
      },
      1199: {
        slidesPerView: 3,
      },
      991: {
        slidesPerView: 3,
      },
      767: {
        slidesPerView: 2,
      },
      575: {
        slidesPerView: 2,
      },
    },
  });

  // Search options
  $(".search-bar > a").on("click", function () {
    $(".header-top-search-area").slideToggle();
  });

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

  $.each($(".select2"), function () {
    $(this)
      .wrap(`<div class="position-relative"></div>`)
      .select2({
        dropdownParent: $(this).parent(),
      });
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
})(jQuery);
