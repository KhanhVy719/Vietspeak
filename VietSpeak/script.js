document.addEventListener('DOMContentLoaded', function () {
    // Smooth scroll chỉ trong trang
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var href = anchor.getAttribute('href');
            if (href.length > 1) {
                e.preventDefault();
                var target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Hiệu ứng slideshow
    const slides = document.querySelectorAll('.slideshow img');
    let currentIndex = 0;
    if (slides.length > 0) {
        slides[currentIndex].classList.add('active');
        setInterval(() => {
            slides[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % slides.length;
            slides[currentIndex].classList.add('active');
        }, 4000);
    }

    // Xử lý gửi form liên hệ (demo)
    window.sendMessage = function (e) {
        e.preventDefault();
        const thankyou = document.querySelector('.thankyou');
        if (thankyou) thankyou.style.display = 'block';
        e.target.reset();
        return false;
    };

});
document.getElementById("open-hidden-sidebar").onclick = function () {
    document.getElementById("hidden-sidebar").classList.add("show");
};

document.getElementById("close-hidden-sidebar").onclick = function () {
    document.getElementById("hidden-sidebar").classList.remove("show");
};