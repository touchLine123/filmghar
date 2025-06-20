///lazy loading image script
function lazyLoading() {
  let images = document.querySelectorAll(".loading-img");
  function preloadImage(image) {
    let src = image.getAttribute("data-src");
    image.setAttribute("src", src);
    image = $(image);
    image.siblings(".thumb_overlay").fadeTo(2500, 0);
  }
  let imageOptions = {
    threshold: 1,
  };
  const imageObserver = new IntersectionObserver((entries, imageObserver) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // setTimeout(() => {
        preloadImage(entry.target);
        // overlay.style.display = 'none';
        // }, 2000);
        imageObserver.unobserve(entry.target);
      }
    });
  }, imageOptions);
  images.forEach((image) => {
    imageObserver.observe(image);
  }, imageOptions);
}

lazyLoading();
