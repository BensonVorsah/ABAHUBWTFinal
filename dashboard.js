document.addEventListener('DOMContentLoaded', function() {
    // Welcome Section Background Slider
    const welcomeBackgroundSlider = {
        images: [],
        dots: [],
        currentIndex: 0,
        sliderInterval: null,

        init: function() {
            this.images = document.querySelectorAll('.welcome-background-slider .background-image');
            this.dots = document.querySelectorAll('.welcome-background-slider .background-dots .dot');

            // Initial setup
            this.showImage(0);

            // Add click event listeners to dots
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.showImage(index));
            });

            // Start automatic sliding
            this.startSlider();
        },

        showImage: function(index) {
            // Reset all images and dots
            this.images.forEach(img => img.classList.remove('active'));
            this.dots.forEach(dot => dot.classList.remove('active'));

            // Show current image and dot
            this.images[index].classList.add('active');
            this.dots[index].classList.add('active');
            this.currentIndex = index;
        },

        startSlider: function() {
            this.sliderInterval = setInterval(() => {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                this.showImage(this.currentIndex);
            }, 5000); // Change image every 5 seconds
        },

        stopSlider: function() {
            clearInterval(this.sliderInterval);
        }
    };

    // Player Stats Leaders Hover Effect
    const statsLeadersSection = {
        table: null,
        profileContainer: null,

        init: function() {
            this.table = document.querySelector('.stats-leaders-table');
            this.profileContainer = document.querySelector('.player-profile-hover');

            if (this.table) {
                this.table.addEventListener('mouseover', this.handleRowHover.bind(this));
                this.table.addEventListener('mouseout', this.clearProfileHover.bind(this));
            }
        },

        handleRowHover: function(event) {
            const row = event.target.closest('tr');
            if (row) {
                // Here you would typically fetch and display player profile
                // For now, we'll just show a placeholder
                this.profileContainer.innerHTML = `
                    <div class="player-hover-details">
                        <h3>Player Profile</h3>
                        <p>PPG: ${row.querySelector('td:nth-child(1)').textContent}</p>
                        <p>APG: ${row.querySelector('td:nth-child(2)').textContent}</p>
                        <p>RPG: ${row.querySelector('td:nth-child(3)').textContent}</p>
                        <p>SPG: ${row.querySelector('td:nth-child(4)').textContent}</p>
                        <p>BPG: ${row.querySelector('td:nth-child(5)').textContent}</p>
                    </div>
                `;
            }
        },

        clearProfileHover: function() {
            this.profileContainer.innerHTML = '';
        }
    };

    // Initialize components
    welcomeBackgroundSlider.init();
    statsLeadersSection.init();

    // Optional: Add hover effects to clickable sections
    const hoverableSections = [
        '#results .results-container',
        '#schedule .schedule-container',
        '#teams .teams-container',
        '#standings .standings-table',
        '#awards .awards-container'
    ];

    hoverableSections.forEach(selector => {
        const section = document.querySelector(selector);
        if (section) {
            section.addEventListener('mouseover', function() {
                this.classList.add('hover-effect');
            });
            section.addEventListener('mouseout', function() {
                this.classList.remove('hover-effect');
            });
        }
    });

    // Media Section Video Hover Effect
    const mediaCards = document.querySelectorAll('#media .media-card');
    mediaCards.forEach(card => {
        card.addEventListener('mouseover', function() {
            this.classList.add('hover-effect');
        });
        card.addEventListener('mouseout', function() {
            this.classList.remove('hover-effect');
        });
    });
});