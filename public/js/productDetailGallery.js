document.addEventListener('DOMContentLoaded', function () {
    let currentGalleryIndex = 0;
    const galleryImages = window.galleryImages || [];
    const mainImg = document.getElementById('mainProductImage');
    const thumbs = document.querySelectorAll('.thumbnail');
    const prevBtn = document.getElementById('galleryPrevBtn');
    const nextBtn = document.getElementById('galleryNextBtn');

    function setMainImage(idx) {
        if(idx === currentGalleryIndex) return;
        mainImg.classList.add('fade-out');
        setTimeout(() => {
            mainImg.src = galleryImages[idx];
            mainImg.classList.remove('fade-out');
        }, 300);
        thumbs.forEach((t,i)=>t.classList.toggle('active',i===idx));
        currentGalleryIndex = idx;
        updateNavBtn();
    }
    window.switchProductImage = setMainImage;
    window.switchThumb = function(dir) {
        let next = currentGalleryIndex + dir;
        if(next < 0) next = galleryImages.length-1;
        if(next >= galleryImages.length) next = 0;
        setMainImage(next);
    }
    function updateNavBtn() {
        if (galleryImages.length <= 1) {
            prevBtn.setAttribute('disabled', 'disabled');
            nextBtn.setAttribute('disabled', 'disabled');
        } else {
            prevBtn.removeAttribute('disabled');
            nextBtn.removeAttribute('disabled');
        }
    }
    updateNavBtn();
});

document.addEventListener("DOMContentLoaded", function () {
    const desc = document.getElementById("descriptionText");
    const toggle = document.getElementById("toggleText");

    const lineHeight = parseFloat(getComputedStyle(desc).lineHeight) || 18;
    const maxHeight = lineHeight * 18;

    if (desc.scrollHeight > maxHeight) {
        desc.classList.add("collapsed");
        toggle.style.display = "inline";
    }

    toggle.addEventListener("click", function () {
        desc.classList.toggle("collapsed");
        toggle.textContent = desc.classList.contains("collapsed")
            ? "Baca selengkapnya"
            : "Tampilkan lebih sedikit";
    });
}); 