/* ==========================================================================
   CLETUS PORTFOLIO CORE JAVASCRIPT
   Interactivity, Typing Animation, 3D Card Tilt, & Full-Website Grid Engine
   ========================================================================== */

// --------------------------------------------------------------------------
// 0. INSTANT INTERNAL NAVIGATION ENGINE (CLICK -> IMMEDIATE NAVIGATION)
// --------------------------------------------------------------------------
(function () {
    function handleImmediateNavigation(e) {
        var link = e.target.closest('a');
        if (!link) return;

        var href = link.getAttribute('href');
        if (!href) return;

        if (link.target === '_blank' ||
            link.hasAttribute('download') ||
            e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ||
            href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:')) {
            return;
        }

        var targetUrl;
        try {
            targetUrl = new URL(href, window.location.origin).href;
        } catch (err) {
            return;
        }

        if (!targetUrl.startsWith(window.location.origin)) {
            return;
        }

        var currentUrlNoHash = window.location.href.split('#')[0];
        var targetUrlNoHash = targetUrl.split('#')[0];

        if (currentUrlNoHash === targetUrlNoHash && targetUrl.includes('#')) {
            return;
        }

        window.location.href = targetUrl;
    }

    document.addEventListener('click', handleImmediateNavigation, true);
})();

document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------------------------------------
    // 0.5 AUTOMATIC ACTIVE-PAGE NAVIGATION HIGHLIGHTING ENGINE
    // ----------------------------------------------------------------------
    const activeNavLinks = document.querySelectorAll('.cletus-nav-list .cletus-nav-link');
    var rawPath = window.location.pathname.replace(/\/$/, "");
    var cleanPath = rawPath.replace(/^\/cletusportfolio/i, "").replace(/\/$/, "");

    if (activeNavLinks.length > 0) {
        activeNavLinks.forEach(link => {
            if (link.classList.contains('cletus-header-whatsapp')) return;
            link.classList.remove('cletus-nav-active');

            var linkHref = link.getAttribute('href') || '';
            var linkPath = '';
            try {
                linkPath = new URL(linkHref, window.location.origin).pathname.replace(/\/$/, "").replace(/^\/cletusportfolio/i, "");
            } catch (e) { }
            linkPath = linkPath.replace(/\/$/, "");

            var isMatched = false;
            if (cleanPath === '' || cleanPath === '/' || cleanPath === '/home') {
                if (linkPath === '' || linkPath === '/' || linkPath === '/home') isMatched = true;
            } else if (cleanPath.startsWith('/portfolio-projects') || cleanPath === '/projects') {
                if (linkPath.startsWith('/portfolio-projects') || linkPath === '/projects') isMatched = true;
            } else if (cleanPath.startsWith('/about')) {
                if (linkPath.startsWith('/about')) isMatched = true;
            } else if (cleanPath.startsWith('/services')) {
                if (linkPath.startsWith('/services')) isMatched = true;
            } else if (cleanPath.startsWith('/blog') || document.body.classList.contains('single-post')) {
                if (linkPath.startsWith('/blog')) isMatched = true;
            } else if (cleanPath.startsWith('/contact')) {
                if (linkPath.startsWith('/contact')) isMatched = true;
            }

            if (isMatched) {
                link.classList.add('cletus-nav-active');
            }
        });
    }

    // ----------------------------------------------------------------------
    // 1. CONTINUOUS TYPING / DELETING ANIMATION
    // ----------------------------------------------------------------------
    const roles = ["Creative Designer", "Web Developer"];
    let roleIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const typingSpeed = 90;
    const deletingSpeed = 45;
    const pauseDelay = 2200;

    const typingElement = document.getElementById('cletus-typing-text');

    function typeRole() {
        if (!typingElement) return;

        const currentRole = roles[roleIndex];

        if (isDeleting) {
            typingElement.textContent = currentRole.substring(0, charIndex - 1);
            charIndex--;
        } else {
            typingElement.textContent = currentRole.substring(0, charIndex + 1);
            charIndex++;
        }

        let nextSpeed = isDeleting ? deletingSpeed : typingSpeed;

        if (!isDeleting && charIndex === currentRole.length) {
            nextSpeed = pauseDelay;
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            roleIndex = (roleIndex + 1) % roles.length;
            nextSpeed = 400;
        }

        setTimeout(typeRole, nextSpeed);
    }

    if (typingElement) {
        setTimeout(typeRole, 400);
    }

    // ----------------------------------------------------------------------
    // 2. AC IDENTITY CARD 3D MOUSE & TOUCH FOLLOW TILT EFFECT
    // ----------------------------------------------------------------------
    const acCardWrapper = document.getElementById('cletus-ac-card');

    if (acCardWrapper) {
        const heroCardArea = acCardWrapper.parentElement || acCardWrapper;

        function applyCardTilt(clientX, clientY) {
            const rect = acCardWrapper.getBoundingClientRect();
            if (!rect.width || !rect.height) return;

            const x = clientX - rect.left - rect.width / 2;
            const y = clientY - rect.top - rect.height / 2;

            const rotateX = (-y / (rect.height / 2)) * 14;
            const rotateY = (x / (rect.width / 2)) * 14;

            acCardWrapper.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.02, 1.02, 1.02)`;
        }

        function resetCardTilt() {
            acCardWrapper.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        }

        function handlePointer(e) {
            var clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            if (clientX !== undefined && clientY !== undefined) {
                applyCardTilt(clientX, clientY);
            }
        }

        // Desktop Mouse Events
        heroCardArea.addEventListener('mousemove', handlePointer, { passive: true });
        heroCardArea.addEventListener('mouseleave', resetCardTilt, { passive: true });

        // Touch & Pointer Events for Tablets & Mobile Phones (mousemove, pointermove, touchmove, touchstart, touchend, touchcancel, pointerleave)
        [heroCardArea, acCardWrapper].forEach(function (targetEl) {
            targetEl.addEventListener('pointermove', handlePointer, { passive: true });
            targetEl.addEventListener('touchmove', handlePointer, { passive: true });
            targetEl.addEventListener('touchstart', handlePointer, { passive: true });
            targetEl.addEventListener('touchend', resetCardTilt, { passive: true });
            targetEl.addEventListener('touchcancel', resetCardTilt, { passive: true });
            targetEl.addEventListener('pointerleave', resetCardTilt, { passive: true });
        });
    }

    // ----------------------------------------------------------------------
    // 3. MOBILE HAMBURGER NAVIGATION TOGGLE
    // ----------------------------------------------------------------------
    const hamburgerBtn = document.getElementById('cletus-hamburger');
    const navMenu = document.getElementById('cletus-nav-menu')?.querySelector('.cletus-nav-list');

    if (hamburgerBtn && navMenu) {
        hamburgerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            navMenu.classList.toggle('cletus-nav-active');
            hamburgerBtn.classList.toggle('is-active');
        });

        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('cletus-nav-active');
                hamburgerBtn.classList.remove('is-active');
            });
        });

        document.addEventListener('touchstart', function (e) {
            if (!navMenu.contains(e.target) && !hamburgerBtn.contains(e.target)) {
                navMenu.classList.remove('cletus-nav-active');
                hamburgerBtn.classList.remove('is-active');
            }
        }, { passive: true });
    }

    // ----------------------------------------------------------------------
    // 4. SCROLL REVEAL MICRO-ANIMATIONS
    // ----------------------------------------------------------------------
    const animateElements = document.querySelectorAll('.cletus-showcase-row, .cletus-service-row, .cletus-editorial-col, .cletus-blog-row, .cletus-profile-frame-square');

    if ('IntersectionObserver' in window && animateElements.length > 0) {
        const observerOptions = {
            threshold: 0.05,
            rootMargin: '0px 0px -20px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        animateElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(24px)';
            el.style.transition = 'opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
            observer.observe(el);
        });
    } else {
        animateElements.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }

    // ----------------------------------------------------------------------
    // 5. FULL-WEBSITE GEOMETRIC SQUARE GRID WITH RADIAL MOUSE & TOUCH ILLUMINATION
    // ----------------------------------------------------------------------
    class CletusGridBG {
        constructor() {
            this.canvas = document.getElementById('cletus-grid-canvas');
            if (!this.canvas) return;
            this.ctx = this.canvas.getContext('2d');
            if (!this.ctx) return;

            this.mouse = { x: -1000, y: -1000, targetX: -1000, targetY: -1000 };
            this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            this.gridSize = 60;
            this.glowRadius = 220;

            this.init();
            this.bindEvents();
            this.animate();
        }

        init() {
            this.resize();
        }

        resize() {
            this.width = this.canvas.width = document.documentElement.clientWidth || window.innerWidth;
            this.height = this.canvas.height = window.innerHeight;

            if (this.width < 768) {
                this.gridSize = 48;
                this.glowRadius = 150;
            } else {
                this.gridSize = 60;
                this.glowRadius = 220;
            }
        }

        bindEvents() {
            window.addEventListener('resize', () => this.resize(), { passive: true });

            const handlePointerMove = (e) => {
                var clientX = e.clientX;
                var clientY = e.clientY;
                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                }
                if (clientX !== undefined && clientY !== undefined) {
                    this.mouse.targetX = clientX;
                    this.mouse.targetY = clientY;
                }
            };

            const handlePointerLeave = () => {
                this.mouse.targetX = -1000;
                this.mouse.targetY = -1000;
            };

            // Mouse Events
            window.addEventListener('mousemove', handlePointerMove, { passive: true });
            window.addEventListener('mouseleave', handlePointerLeave, { passive: true });

            // Tablet & Mobile Touch Events
            window.addEventListener('pointermove', handlePointerMove, { passive: true });
            window.addEventListener('touchmove', handlePointerMove, { passive: true });
            window.addEventListener('touchstart', handlePointerMove, { passive: true });
            window.addEventListener('touchend', handlePointerLeave, { passive: true });
            window.addEventListener('touchcancel', handlePointerLeave, { passive: true });
            window.addEventListener('pointerleave', handlePointerLeave, { passive: true });
        }

        animate() {
            this.mouse.x += (this.mouse.targetX - this.mouse.x) * 0.15;
            this.mouse.y += (this.mouse.targetY - this.mouse.y) * 0.15;

            this.ctx.clearRect(0, 0, this.width, this.height);

            const step = this.gridSize;
            const mx = this.mouse.x;
            const my = this.mouse.y;
            const radius = this.glowRadius;

            if (mx > 0 && my > 0 && !this.reducedMotion) {
                const radial = this.ctx.createRadialGradient(mx, my, 0, mx, my, radius);
                radial.addColorStop(0, 'rgba(242, 201, 76, 0.14)');
                radial.addColorStop(0.35, 'rgba(212, 160, 23, 0.07)');
                radial.addColorStop(1, 'rgba(212, 160, 23, 0)');
                this.ctx.fillStyle = radial;
                this.ctx.fillRect(0, 0, this.width, this.height);
            }

            for (let x = 0; x <= this.width; x += step) {
                const dx = Math.abs(x - mx);
                let opacity = 0.04;
                let strokeColor = '#D4A017';

                if (dx < radius && mx > 0 && !this.reducedMotion) {
                    const factor = 1 - dx / radius;
                    opacity = 0.04 + factor * 0.42;
                    if (factor > 0.45) strokeColor = '#F2C94C';
                }

                this.ctx.beginPath();
                this.ctx.moveTo(x, 0);
                this.ctx.lineTo(x, this.height);
                this.ctx.strokeStyle = strokeColor;
                this.ctx.globalAlpha = opacity;
                this.ctx.lineWidth = opacity > 0.18 ? 1.2 : 0.8;
                this.ctx.stroke();
            }

            for (let y = 0; y <= this.height; y += step) {
                const dy = Math.abs(y - my);
                let opacity = 0.04;
                let strokeColor = '#D4A017';

                if (dy < radius && my > 0 && !this.reducedMotion) {
                    const factor = 1 - dy / radius;
                    opacity = 0.04 + factor * 0.42;
                    if (factor > 0.45) strokeColor = '#F2C94C';
                }

                this.ctx.beginPath();
                this.ctx.moveTo(0, y);
                this.ctx.lineTo(this.width, y);
                this.ctx.strokeStyle = strokeColor;
                this.ctx.globalAlpha = opacity;
                this.ctx.lineWidth = opacity > 0.18 ? 1.2 : 0.8;
                this.ctx.stroke();
            }

            this.ctx.globalAlpha = 1;

            requestAnimationFrame(() => this.animate());
        }
    }

    new CletusGridBG();
});
