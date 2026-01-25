var themeUri = sparksPostCard.themeUri;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.slider').forEach(function(slider) {
        var slides = slider.querySelectorAll('.slide');
        var currentSlide = 0;

        // Display the first slide if it exists
        if (slides.length > 0) {
            slides[currentSlide].style.display = 'block';
        }

        // Function to navigate slides (keyboard and click)
        function navigateSlide(isNext) {
            // Pause any playing videos in the current slide
            var currentVideos = slides[currentSlide].querySelectorAll('video');
            currentVideos.forEach(function(video) {
                video.pause();
            });

            // Hide current slide
            slides[currentSlide].style.display = 'none';
            slides[currentSlide].setAttribute('aria-hidden', 'true');

            // Calculate next slide
            if (isNext) {
                currentSlide = (currentSlide < slides.length - 1) ? currentSlide + 1 : 0;
            } else {
                currentSlide = (currentSlide > 0) ? currentSlide - 1 : slides.length - 1;
            }

            // Show new slide
            slides[currentSlide].style.display = 'block';
            slides[currentSlide].setAttribute('aria-hidden', 'false');

            // Announce to screen readers
            announceSlideChange();
        }

        // Function to create an arrow button with accessibility
        function createArrow(className, svgName, isNext) {
            var arrow = document.createElement('button');
            arrow.className = 'slider-arrow ' + className;
            arrow.type = 'button';

            // Add ARIA labels
            var label = isNext ? 'Next slide' : 'Previous slide';
            arrow.setAttribute('aria-label', label);
            arrow.setAttribute('title', label);

            // Create an img element for the SVG
            var img = document.createElement('img');
            img.src = themeUri + '/elements/' + svgName;
            img.alt = ''; // Decorative, screen reader uses button aria-label
            img.setAttribute('aria-hidden', 'true'); // Hide from screen readers

            arrow.appendChild(img);
            arrow.onclick = function() {
                navigateSlide(isNext);
            };

            return arrow;
        }

        // Add arrows only if there are multiple slides
        if (slides.length > 1) {
            var prevArrow = createArrow('prev-arrow', 'chevron-left-solid.svg', false);
            var nextArrow = createArrow('next-arrow', 'chevron-right-solid.svg', true);

            slider.appendChild(prevArrow);
            slider.appendChild(nextArrow);

            // Set up ARIA attributes for carousel
            slider.setAttribute('role', 'region');
            slider.setAttribute('aria-roledescription', 'carousel');
            slider.setAttribute('aria-label', 'Post media gallery');
            slider.setAttribute('tabindex', '0');

            // Set initial ARIA states for slides
            slides.forEach(function(slide, index) {
                slide.setAttribute('role', 'group');
                slide.setAttribute('aria-roledescription', 'slide');
                slide.setAttribute('aria-label', 'Slide ' + (index + 1) + ' of ' + slides.length);
                slide.setAttribute('aria-hidden', index !== 0 ? 'true' : 'false');
            });

            // Keyboard event listener for accessibility
            slider.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    navigateSlide(false);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    navigateSlide(true);
                }
            });
        }

        // Add live region for screen reader announcements
        function announceSlideChange() {
            var liveRegion = slider.querySelector('.slider-live-region');
            if (!liveRegion) {
                liveRegion = document.createElement('div');
                liveRegion.className = 'slider-live-region';
                liveRegion.setAttribute('aria-live', 'polite');
                liveRegion.setAttribute('aria-atomic', 'true');
                liveRegion.style.position = 'absolute';
                liveRegion.style.left = '-10000px';
                liveRegion.style.width = '1px';
                liveRegion.style.height = '1px';
                liveRegion.style.overflow = 'hidden';
                slider.appendChild(liveRegion);
            }

            liveRegion.textContent = 'Slide ' + (currentSlide + 1) + ' of ' + slides.length;
        }
    });
});

// Pause videos when they leave viewport
document.addEventListener('DOMContentLoaded', function() {
    const videos = document.querySelectorAll('.slider video');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                entry.target.pause();
            }
        });
    }, {
        threshold: 0.25 // Adjust this value based on when you want to pause the video
    });

    videos.forEach(video => {
        observer.observe(video);
    });
});
