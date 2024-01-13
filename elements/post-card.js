var myThemeUri = myScriptParams.myThemeUri;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.slider').forEach(function(slider) {
        var slides = slider.querySelectorAll('.slide');
        var currentSlide = 0;

        // Display the first slide if it exists
        if (slides.length > 0) {
            slides[currentSlide].style.display = 'block';
        }

        // Function to create an arrow button
        function createArrow(className, svgName, isNext) {
			var arrow = document.createElement('button');
			arrow.className = 'slider-arrow ' + className;

			// Create an img element for the SVG
			var img = document.createElement('img');
			img.src = myThemeUri + '/elements/' + svgName; // Use a variable for the theme URI
			img.alt = isNext ? 'Next' : 'Previous';

			arrow.appendChild(img);
			
            arrow.onclick = function() {
                
				// Pause any playing videos in the current slide
                var currentVideos = slides[currentSlide].querySelectorAll('video');
                currentVideos.forEach(video => video.pause());

                // Slide change logic
                slides[currentSlide].style.display = 'none';
                if (isNext) {
                    currentSlide = (currentSlide < slides.length - 1) ? currentSlide + 1 : 0;
                } else {
                    currentSlide = (currentSlide > 0) ? currentSlide - 1 : slides.length - 1;
                }
                slides[currentSlide].style.display = 'block';
            };
            return arrow;
        }

        // Add arrows only if there are multiple slides
		if (slides.length > 1) {
			var prevArrow = createArrow('prev-arrow', 'chevron-left-solid.svg', false);
			var nextArrow = createArrow('next-arrow', 'chevron-right-solid.svg', true);

			slider.appendChild(prevArrow);
			slider.appendChild(nextArrow);
		}

    });
});



//pause videos when the leave viewport
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
