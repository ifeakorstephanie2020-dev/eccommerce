// script.js - Retro Washed Animations

document.addEventListener('DOMContentLoaded', function() {

    // 1. Fade in elements on scroll
    const fadeElements = document.querySelectorAll('.scroll-fade');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    fadeElements.forEach(el => observer.observe(el));

    // 2. Add scroll-fade class to articles if not already present
    document.querySelectorAll('article').forEach((article, index) => {
        if (!article.classList.contains('scroll-fade')) {
            article.classList.add('scroll-fade');
            article.style.transitionDelay = (index * 0.1) + 's';
        }
    });

    // 3. Button hover sound effect (retro click)
    const buttons = document.querySelectorAll('button, [role="button"]');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });

        btn.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // 4. Retro typewriter effect for hero headings
    const heroHeadings = document.querySelectorAll('.hero h1');
    heroHeadings.forEach(heading => {
        const text = heading.textContent;
        heading.textContent = '';
        heading.classList.add('glitch-text');

        let i = 0;
        const typeInterval = setInterval(() => {
            if (i < text.length) {
                heading.textContent += text.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
            }
        }, 50);
    });

    // 5. Image hover effect - retro scanline
    document.querySelectorAll('article img').forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // 6. Random retro color flash on nav links
    const navLinks = document.querySelectorAll('nav ul li a');
    const retroColors = ['#d4a5a5', '#c4b5a0', '#e8c9b0', '#b88383', '#e8c9c9'];

    navLinks.forEach((link, index) => {
        link.addEventListener('mouseenter', function() {
            const randomColor = retroColors[Math.floor(Math.random() * retroColors.length)];
            this.style.color = randomColor;
        });

        link.addEventListener('mouseleave', function() {
            this.style.color = '';
        });
    });

    // 7. Animated background pattern (subtle)
    const body = document.body;
    let bgPos = 0;

    setInterval(() => {
        bgPos += 0.5;
        if (bgPos > 100) bgPos = 0;
        body.style.backgroundPosition = bgPos + 'px ' + bgPos + 'px';
    }, 100);

    // 8. Cart counter animation (if exists)
    const cartLinks = document.querySelectorAll('a[href*="products"]');
    cartLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Just a visual feedback
            this.style.transition = 'all 0.2s';
            this.style.transform = 'scale(0.9)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });

    console.log('✨ STEPHIE\'S STORE - Retro Washed Edition ✨');
});

// 9. Window load animation
window.addEventListener('load', function() {
    document.body.style.opacity = '1';
    document.body.style.transition = 'opacity 0.5s ease';
});

// 10. Prevent flash on page load
document.body.style.opacity = '0';
document.addEventListener('DOMContentLoaded', function() {
    document.body.style.opacity = '1';
});
