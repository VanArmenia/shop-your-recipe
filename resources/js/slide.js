import Splide from '@splidejs/splide';

document.addEventListener('DOMContentLoaded', function () {
  const splide = document.getElementsByClassName('splide');
  if(splide.length){
    new Splide('.splide', {
      type: 'loop',
      perPage: 5,
      breakpoints: {
        640: {
          perPage: 2,
        },
        940: {
          perPage: 3,
        },
      },
      perMove: 1,
      autoplay: true,
    }).mount();
  }
});
