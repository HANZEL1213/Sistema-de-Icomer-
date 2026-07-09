
document.addEventListener('DOMContentLoaded', function () {
    const thumbs = Array.from(document.querySelectorAll('.product-thumb'));
    const mainImage = document.getElementById('galleryMainImage');
    const counter = document.getElementById('galleryCounter');
    const prevBtn = document.getElementById('galleryPrev');
    const nextBtn = document.getElementById('galleryNext');
    const modalElement = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    if (!mainImage || thumbs.length === 0) {
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
        return;
    }

    const modalInstance = modalElement ? new bootstrap.Modal(modalElement) : null;

    const images = thumbs.map((thumb, index) => ({
        index,
        url: thumb.dataset.image,
        principal: thumb.dataset.principal === '1',
        thumb
    }));

    let currentIndex = images.findIndex(img => img.thumb.classList.contains('active'));
    if (currentIndex < 0) currentIndex = 0;

    function updateButtons() {
        const multiple = images.length > 1;
        if (prevBtn) prevBtn.disabled = !multiple;
        if (nextBtn) nextBtn.disabled = !multiple;
    }

    function updateCounter() {
        if (counter) {
            counter.textContent = `${currentIndex + 1}/${images.length}`;
        }
    }

    function updateActiveThumb() {
        images.forEach(img => img.thumb.classList.remove('active'));
        images[currentIndex].thumb.classList.add('active');
    }

    function swapMainImage(newUrl) {
        mainImage.classList.add('is-changing');
        setTimeout(() => {
            mainImage.src = newUrl;
            setTimeout(() => {
                mainImage.classList.remove('is-changing');
            }, 60);
        }, 140);
    }

    function goToImage(index) {
        if (index < 0) index = images.length - 1;
        if (index >= images.length) index = 0;
        currentIndex = index;
        swapMainImage(images[currentIndex].url);
        updateActiveThumb();
        updateCounter();
    }

    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', function () {
            goToImage(index);
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            goToImage(currentIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            goToImage(currentIndex + 1);
        });
    }

    mainImage.addEventListener('click', function () {
        if (!modalInstance || !modalImage) return;
        modalImage.src = images[currentIndex].url;
        modalInstance.show();
    });

    document.addEventListener('keydown', function (event) {
        const modalOpen = modalElement && modalElement.classList.contains('show');
        if (modalOpen) return;
        if (event.key === 'ArrowLeft') goToImage(currentIndex - 1);
        if (event.key === 'ArrowRight') goToImage(currentIndex + 1);
        if (event.key === 'Enter' && document.activeElement === mainImage) {
            if (!modalInstance || !modalImage) return;
            modalImage.src = images[currentIndex].url;
            modalInstance.show();
        }
    });

    updateButtons();
    updateCounter();
    updateActiveThumb();
});
