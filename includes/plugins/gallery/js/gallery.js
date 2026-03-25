window.addEventListener('DOMContentLoaded', () => {
    const items = Array.from(document.querySelectorAll('[data-gallery-item]'));
    items.forEach((item, index) => {
        window.setTimeout(() => {
            item.classList.add('show');
        }, index * 60);
    });

    const modalElement = document.getElementById('lightboxModal');
    if (!modalElement || typeof bootstrap === 'undefined') {
        return;
    }

    const triggers = Array.from(document.querySelectorAll('.lightbox-trigger'));
    if (!triggers.length) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxTitle = document.getElementById('lightboxTitle');
    const lightboxMeta = document.getElementById('lightboxMeta');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const lightboxCounter = document.getElementById('lightboxCounter');
    const lightboxDownload = document.getElementById('lightboxDownload');
    const lightboxTags = document.getElementById('lightboxTags');
    const lightboxMetaRow = document.getElementById('lightboxMetaRow');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    const slides = triggers.map((trigger) => ({
        src: trigger.dataset.src || trigger.getAttribute('href') || '',
        title: trigger.dataset.title || '',
        caption: trigger.dataset.caption || '',
        category: trigger.dataset.category || '',
        photographer: trigger.dataset.photographer || '',
        tags: trigger.dataset.tags || '',
        alt: trigger.dataset.alt || '',
        download: trigger.dataset.download || trigger.getAttribute('href') || '',
    }));

    let currentIndex = 0;
    let touchStartX = 0;

    const renderSlide = (index) => {
        const slide = slides[index];
        if (!slide) {
            return;
        }

        currentIndex = index;
        lightboxImage.src = slide.src;
        lightboxImage.alt = slide.alt || slide.title || '';
        lightboxTitle.textContent = slide.title;

        const metaParts = [slide.category, slide.photographer].filter(Boolean);
        lightboxMeta.textContent = metaParts.join(' - ');
        lightboxMeta.hidden = metaParts.length === 0;

        lightboxCaption.textContent = slide.caption;
        lightboxCaption.hidden = slide.caption === '';

        lightboxCounter.textContent = `${index + 1} / ${slides.length}`;
        lightboxDownload.href = slide.download;

        if (lightboxTags) {
            const tagList = slide.tags
                .split(',')
                .map((tag) => tag.trim())
                .filter(Boolean);

            lightboxTags.textContent = tagList.join(' - ');
            lightboxTags.hidden = tagList.length === 0;
        }

        if (lightboxMetaRow) {
            lightboxMetaRow.hidden = !slide.title && metaParts.length === 0 && (!lightboxTags || lightboxTags.hidden);
        }
    };

    const showPrev = () => {
        renderSlide((currentIndex - 1 + slides.length) % slides.length);
    };

    const showNext = () => {
        renderSlide((currentIndex + 1) % slides.length);
    };

    triggers.forEach((trigger, index) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            renderSlide(index);
            modal.show();
        });
    });

    prevBtn?.addEventListener('click', showPrev);
    nextBtn?.addEventListener('click', showNext);

    document.addEventListener('keydown', (event) => {
        if (!modalElement.classList.contains('show')) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            showPrev();
        } else if (event.key === 'ArrowRight') {
            showNext();
        }
    });

    modalElement.addEventListener('touchstart', (event) => {
        if (!event.changedTouches[0]) {
            return;
        }
        touchStartX = event.changedTouches[0].screenX;
    }, { passive: true });

    modalElement.addEventListener('touchend', (event) => {
        if (!event.changedTouches[0]) {
            return;
        }

        const delta = event.changedTouches[0].screenX - touchStartX;
        if (Math.abs(delta) < 40) {
            return;
        }

        if (delta > 0) {
            showPrev();
        } else {
            showNext();
        }
    }, { passive: true });

    modalElement.addEventListener('hidden.bs.modal', () => {
        lightboxImage.removeAttribute('src');
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('overflow');
        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
    });
});
