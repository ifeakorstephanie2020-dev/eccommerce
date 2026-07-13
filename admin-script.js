// admin-script.js - Retro Washed Admin Animations

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

    // 3. Button click animation
    const buttons = document.querySelectorAll('button, input[type="submit"], [role="button"]');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // 4. Table row hover effect (already in CSS, but add extra flair)
    const tableRows = document.querySelectorAll('table tr');
    tableRows.forEach((row, index) => {
        if (index > 0) { // Skip header
            row.addEventListener('mouseenter', function() {
                this.style.transition = 'background 0.2s ease';
            });
        }
    });

    // 5. Form input animation
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transition = 'all 0.3s ease';
        });
    });

    // 6. Delete confirmation enhancement
    const deleteLinks = document.querySelectorAll('.btn-delete, a[href*="delete_id"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const productName = this.closest('tr')?.querySelector('td')?.textContent || 'this item';
            if (!confirm(`⚠️ Are you sure you want to delete ${productName}?`)) {
                e.preventDefault();
            }
        });
    });

    // 7. Success message auto-dismiss
    const messages = document.querySelectorAll('.message');
    messages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => {
                msg.style.display = 'none';
            }, 500);
        }, 3000);
    });

    // 8. Navigation link retro color flash
    const navLinks = document.querySelectorAll('nav ul li a');
    const retroColors = ['#d4a5a5', '#c4b5a0', '#e8c9b0', '#b88383', '#e8c9c9'];

    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const randomColor = retroColors[Math.floor(Math.random() * retroColors.length)];
            this.style.color = randomColor;
        });

        link.addEventListener('mouseleave', function() {
            this.style.color = '';
        });
    });

    // 9. Admin badge pulse
    const badges = document.querySelectorAll('.admin-badge');
    badges.forEach(badge => {
        setInterval(() => {
            badge.style.transition = 'transform 0.3s ease';
            badge.style.transform = 'scale(1.05)';
            setTimeout(() => {
                badge.style.transform = 'scale(1)';
            }, 300);
        }, 3000);
    });

    console.log('⚡ STEPHIE\'S ADMIN - Retro Washed Edition ⚡');
});

// 10. Window load animation
window.addEventListener('load', function() {
    document.body.style.opacity = '1';
    document.body.style.transition = 'opacity 0.5s ease';
});

// 11. Prevent flash on page load
document.body.style.opacity = '0';
document.addEventListener('DOMContentLoaded', function() {
    document.body.style.opacity = '1';
});
