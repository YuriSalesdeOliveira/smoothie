(function(){let slides = document.querySelectorAll('.slide');

slides.forEach((slide) => {
    slide.addEventListener('click', () => {
        slide.classList.add('slide__azure');
    });
});
})();(function(){
    console.log('sim')
})();