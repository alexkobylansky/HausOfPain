let jQuery = require('../../assets/js/jquery-3.1.1.min')
let $ = jQuery

// Главное меню сворачивается в тонкую полоску при прокручивании страницы вниз
window.addEventListener('scroll', function () {
  if (document.defaultView.pageYOffset >= 80) {
    document.querySelector('.navbar').classList.add('navbar-fixed-top');
  } else {
    document.querySelector('.navbar').classList.remove('navbar-fixed-top');
  }
});

// Загрузка Видимых Картинок
function isVisible(elem) {

  let coords = elem.getBoundingClientRect();

  let windowHeight = document.documentElement.clientHeight;

  // видны верхний ИЛИ нижний край элемента
  let topVisible = coords.top > 0 && coords.top < windowHeight;
  let bottomVisible = coords.bottom < windowHeight && coords.bottom > 0;

  return topVisible || bottomVisible;
}

/*
 Вариант проверки, считающий элемент видимым,
 если он не более чем -1 страница назад или +1 страница вперед.

 function isVisible(elem) {

      let coords = elem.getBoundingClientRect();

      let windowHeight = document.documentElement.clientHeight;

      let extendedTop = -windowHeight;
      let extendedBottom = 2 * windowHeight;

      // top visible || bottom visible
      let topVisible = coords.top > extendedTop && coords.top < extendedBottom;
      let bottomVisible = coords.bottom < extendedBottom && coords.bottom > extendedTop;

      return topVisible || bottomVisible;
    }
 */

function showVisible() {
  for (let img of document.querySelectorAll('img')) {
    let realSrc = img.dataset.src;
    if (!realSrc) continue;

    if (isVisible(img)) {

      img.src = realSrc;

      img.dataset.src = '';
    }
  }
}

window.addEventListener('scroll', showVisible);
showVisible();

// Переключение мобильное меню
document.addEventListener('DOMContentLoaded', function () {
  let nav = document.querySelector('.navbar');

  if (document.documentElement.clientWidth < 992) {
    nav.classList.add('navbar-mobile');
  }

  window.addEventListener('resize', function () {

    if (document.documentElement.clientWidth < 992) {
      nav.classList.add('navbar-mobile');
      nav.classList.remove('navbar-fixed-top');
    } else if (document.documentElement.clientWidth > 991) {
      nav.classList.remove('navbar-mobile');
      nav.classList.add('navbar-fixed-top');
    }
  })
});

// Отозбарить или скрыть главное мобильное меню
document.querySelector('.navbar-toggler').addEventListener('click', () => {

  document.querySelector('.navbar-toggler').classList.toggle('close');
  document.querySelector('.navbar-collapse').classList.toggle('collapse');
  document.querySelector('.navbar-mobile').classList.toggle('black');
  document.querySelector('nav');
});
// Работа с выпадающим списком главного меню

$(document).ready(function(){
  //the trigger on hover when cursor directed to this class
  $(".main-menu li").hover(
    function(){
      //i used the parent ul to show submenu
      $(this).children('ul').slideDown('fast');
    },
    //when the cursor away
    function () {
      $('ul', this).slideUp('fast');
    });
  console.log($('ul', this))
});


$(document).ready(function(){
  let Swiper = require('../../assets/js/swiper-bundle')

  var swiperH = new Swiper('.swiper-container-h', {
    spaceBetween: 0,
    initialSlide: 1,
    pagination: {
      el: '.swiper-pagination-h',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev'
    },
    keyboard: {
      enabled: true,
    },
  });
  var swiperV = new Swiper('.swiper-container-v', {
    direction: 'vertical',
    spaceBetween: 0,
    mousewheel: true,
    pagination: {
      el: '.swiper-pagination-v',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-button-down',
      prevEl: '.swiper-button-up'
    },
    keyboard: {
      enabled: true,
    },
  });

})//-- Initialize Swiper --

let width = window.innerWidth;
let height = window.innerHeight;

function changeSwiperSlide(){
  let swiperV = document.querySelectorAll('.swiper-container-v')
  for (let i = 0; i < swiperV.length; i++){
    let swiperSlide = swiperV[i].querySelectorAll('.swiper-slide');
    swiperSlide[i].style.height = height;
  }

}

function changeIMG() {
    for (elId of ["hs1", "hs2", "hs3", "fx1", "fx2", "fx3", "cali1", "cali2", "cali3"]) {
      let el = document.getElementById(elId)
      if (el) {
        if (window.innerHeight > window.innerWidth) {
          el.src = el.dataset.mobile;
        } else {
          el.src = el.dataset.desktop;
        }
      }
    }

    let mainSlide = document.querySelector('.mainAboutSascha');
    if (window.innerHeight > window.innerWidth) {
      mainSlide.innerHTML = '<img id="mainFoto" src="assets/img/slider/main.mobile.jpg" alt="main photo">'
    } else {
      mainSlide.innerHTML =
        '<div class="video"><video id="mainVideo" src="assets/img/slider/hausofpainintro.mp4" type="video/mp4" height="100%" width="100%" autoplay loop muted></video></div>'
    }
}

window.addEventListener('DOMContentLoaded', function(){
    changeSwiperSlide();
    changeIMG();
})

window.addEventListener('resize', function () {
    changeIMG();
})
