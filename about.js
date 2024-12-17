document.addEventListener('DOMContentLoaded', () => {
    // Gallery Image Hover Effect
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'scale(1.05)';
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'scale(1)';
        });
    });

    // Smooth Scroll for CTA Buttons
    const ctaButtons = document.querySelectorAll('.cta-buttons a');
    
    ctaButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if (button.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = button.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({ 
                        behavior: 'smooth' 
                    });
                }
            }
        });
    });

    // Animate Content on Scroll
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const revealOnScroll = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Select elements to reveal
    const revealElements = document.querySelectorAll('.about-content, .gallery-section, .cta-section');
    revealElements.forEach(element => {
        element.classList.add('hidden');
        revealOnScroll.observe(element);
    });
});