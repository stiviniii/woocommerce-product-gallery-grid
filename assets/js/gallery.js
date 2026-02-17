/**
 * WooCommerce Product Gallery Grid - Frontend Logic
 */

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('wpgg-gallery-container');
    if (!container) return;

    const breakpoint = parseInt(wpggData.breakpoint) || 1024;
    const sliderElement = document.getElementById('wpgg-slider');
    let splideInstance = null;

    // 1. SPLIDE (MOBILE SLIDER)
    const mediaQuery = window.matchMedia(`(max-width: ${breakpoint - 1}px)`);

    function toggleSlider(e) {
        const matches = e.matches !== undefined ? e.matches : e.target.matches;
        if (matches) {
            // Mobile: Initialize Splide
            if (!splideInstance && typeof Splide !== 'undefined') {
                splideInstance = new Splide('#wpgg-slider', {
                    type: 'slide',
                    perPage: 1,
                    pagination: true,
                    arrows: false,
                    gap: 0,
                    autoHeight: true,
                    drag: true,
                }).mount();
            }
        } else {
            // Desktop: Destroy Splide
            if (splideInstance) {
                splideInstance.destroy();
                splideInstance = null;
            }
        }
    }

    // Older Safari/Browsers support addListener, newer support addEventListener
    if (mediaQuery.addEventListener) {
        mediaQuery.addEventListener('change', toggleSlider);
    } else if (mediaQuery.addListener) {
        mediaQuery.addListener(toggleSlider);
    }

    toggleSlider(mediaQuery);

    // 2. PHOTOSWIPE (MODAL)

    // For UMD builds, the classes are attached to window
    // We use long names or check versions to avoid conflicts with PhotoSwipe 4
    const PS_Lightbox = window.PhotoSwipeLightbox;
    // PS5 Core usually has 'on' or 'addFilter' on its prototype, PS4 does not.
    let PS_Core = window.PhotoSwipe;

    // Check if we have the correct version (v5)
    if (PS_Core && PS_Core.default) {
        PS_Core = PS_Core.default;
    }

    const isPS5 = PS_Core && PS_Core.prototype && (PS_Core.prototype.on || PS_Core.prototype.addFilter);

    if (PS_Lightbox && isPS5) {

        const lightbox = new PS_Lightbox({
            gallery: '#wpgg-gallery-container',
            children: 'a.gallery-item',
            // pswpModule must return the core class. 
            // Returning as a function ensures it's compatible with PS's loader
            pswpModule: () => PS_Core,
            showHideAnimationType: 'fade',
            // Add padding to prevent images from touching edges
            padding: { top: 60, bottom: 60, left: 20, right: 20 },
            // Explicitly enable these to ensure they aren't disabled by defaults
            zoom: true,
            arrowPrev: true,
            arrowNext: true,
            close: true,
        });

        // Add counter
        lightbox.on('uiRegister', function () {
            // 1. Custom Counter
            lightbox.pswp.ui.registerElement({
                name: 'custom-counter',
                order: 7,
                className: 'pswp__custom-counter',
                onInit: (el, pswp) => {
                    pswp.on('change', () => {
                        el.innerText = (pswp.currIndex + 1) + ' ' + wpggData.i18n.counter + ' ' + pswp.getNumItems();
                    });
                }
            });

            // 2. Fullscreen Toggle
            lightbox.pswp.ui.registerElement({
                name: 'fullscreen',
                ariaLabel: 'Toggle fullscreen',
                order: 9,
                isButton: true,
                html: '<svg aria-hidden="true" class="pswp__icn" viewBox="0 0 32 32" width="32" height="32"><path d="M8 8v6.4h2.133V10.133H14.4V8H8zm6.4 16h-4.267v-4.267H8V24h6.4v-2.133zM24 8h-6.4v2.133H21.867V14.4H24V8zm-2.133 16V19.867H18.4V24H24v-6.4h-2.133V24z"/></svg>',
                onClick: (event, el, pswp) => {
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        pswp.element.requestFullscreen();
                    }
                }
            });
        });

        lightbox.init();

        // Overlay Button Click (Using Delegation)
        container.addEventListener('click', function (e) {
            const showAllBtn = e.target.closest('.wpgg-show-all');
            if (showAllBtn) {
                e.preventDefault();
                e.stopPropagation();

                // Find the index of the container
                const parent = showAllBtn.closest('.gallery-item');
                const index = parseInt(parent.getAttribute('data-index')) || 0;

                lightbox.loadAndOpen(index);
            }
        });
    }

    // 3. VARIATION SUPPORT
    // Since WC core uses jQuery for events, we listen for the change in variation_id
    // and try to find the variation data if possible, or we use the delegated event.
    // However, to keep it "No jQuery", we can observe the variation_id input.
    const variationIdInput = document.querySelector('input.variation_id');
    if (variationIdInput) {
        let lastVariationId = variationIdInput.value;

        // MutationObserver to watch for value changes on the hidden input
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName === "value") {
                    const newId = mutation.target.value;
                    if (newId && newId !== lastVariationId) {
                        handleVariationChange(newId);
                        lastVariationId = newId;
                    }
                }
            });
        });

        observer.observe(variationIdInput, { attributes: true });
    }

    function handleVariationChange(variationId) {
        const form = document.querySelector('.variations_form');
        if (!form) return;

        let variationData = form.getAttribute('data-product_variations');
        if (!variationData) return;

        try {
            variationData = JSON.parse(variationData);
            const variation = variationData.find(v => v.variation_id == variationId);
            if (variation && variation.image) {
                updateMainImage(variation.image);
            }
        } catch (e) {
            // Silently fail in production
        }
    }

    function updateMainImage(imageData) {
        const mainItem = container.querySelector('.grid-item--main');
        if (!mainItem) return;

        const img = mainItem.querySelector('img');
        if (img && imageData.src) {
            img.src = imageData.src;
            if (imageData.srcset) img.srcset = imageData.srcset;
            if (imageData.alt) img.alt = imageData.alt;
        }

        // Update PhotoSwipe data attributes and href
        const fullUrl = imageData.full_src || imageData.src;
        mainItem.href = fullUrl;
        mainItem.setAttribute('data-pswp-width', imageData.src_w || 1200);
        mainItem.setAttribute('data-pswp-height', imageData.src_h || 1200);

        // If splide is active, reset to first slide to show variation
        if (splideInstance) {
            splideInstance.go(0);
        }
    }
});
