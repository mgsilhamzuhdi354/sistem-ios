// Main JavaScript for PT Indo OceanCrew Services Website
// Enhanced with Premium Animations

class WebsiteApp {
  constructor() {
    this.currentHeroSlide = 0;
    this.heroSlides = document.querySelectorAll(".hero-slide");
    this.heroIndicators = document.querySelectorAll(".indicator");
    this.heroInterval = null;
    this.scrollProgress = 0;
    this.init();
  }

  init() {
    // Initialize all components
    this.initNavigation();
    this.initHeroSlider();
    this.initSmoothScroll();
    this.initAnimations();
    this.initStatistics();
    this.initPageLoader();
    this.initCrewingPage();
    this.setCurrentYear();

    // Premium Animation Features
    this.initScrollProgress();
    this.initBackToTop();
    this.initScrollAnimations();
    this.initParallaxEffects();
    this.initStaggeredReveals();
  }


  // Crewing Page Functions
  initCrewingPage() {
    // Crewing navigation
    const crewingNavItems = document.querySelectorAll(".crewing-nav-item");
    if (crewingNavItems.length === 0) return;

    const departmentSections = document.querySelectorAll(
      ".department-section, .recruitment-section"
    );

    // Smooth scroll to department sections
    crewingNavItems.forEach((item) => {
      item.addEventListener("click", function (e) {
        e.preventDefault();

        // Remove active class from all items
        crewingNavItems.forEach((navItem) => {
          navItem.classList.remove("active");
        });

        // Add active class to clicked item
        this.classList.add("active");

        // Get target section
        const targetId = this.getAttribute("data-department");
        const targetSection = document.getElementById(targetId);

        if (targetSection) {
          // Calculate scroll position
          const headerHeight = document.querySelector(".header").offsetHeight;
          const navHeight = document.querySelector(".crewing-nav-section").offsetHeight;
          const offset = headerHeight + navHeight + 20;
          const targetPosition = targetSection.offsetTop - offset;

          // Smooth scroll to section
          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          });
        }
      });
    });

    // Update active nav item on scroll
    window.addEventListener("scroll", () => {
      let currentSection = "";

      departmentSections.forEach((section) => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        const scrollPosition = window.scrollY + window.innerHeight / 3;

        if (
          scrollPosition >= sectionTop &&
          scrollPosition < sectionTop + sectionHeight
        ) {
          currentSection = section.id;
        }
      });

      // Update active nav item
      crewingNavItems.forEach((item) => {
        item.classList.remove("active");
        if (item.getAttribute("data-department") === currentSection) {
          item.classList.add("active");
        }
      });
    });

    // Position card interactions
    const positionCards = document.querySelectorAll(".position-card");
    positionCards.forEach((card) => {
      card.addEventListener("mouseenter", function () {
        this.style.transform = "translateY(-5px)";
        this.style.boxShadow = "var(--shadow-lg)";
      });

      card.addEventListener("mouseleave", function () {
        this.style.transform = "translateY(0)";
        this.style.boxShadow = "var(--shadow-md)";
      });
    });

    // Animate elements on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -100px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in");
        }
      });
    }, observerOptions);

    // Observe department sections
    departmentSections.forEach((section) => observer.observe(section));
  }

  // Navigation Functions
  initNavigation() {
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.querySelector(".nav-menu");
    const header = document.querySelector(".header");

    // Mobile menu toggle
    if (hamburger) {
      const langSwitcher = document.querySelector(".language-switcher");

      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
        // Toggle language switcher on mobile
        if (langSwitcher) {
          langSwitcher.classList.toggle("mobile-visible");
        }
        document.body.style.overflow = navMenu.classList.contains("active")
          ? "hidden"
          : "";
      });
    }

    // Close mobile menu on link click
    const langSwitcher = document.querySelector(".language-switcher");
    document.querySelectorAll(".nav-menu a").forEach((link) => {
      link.addEventListener("click", () => {
        hamburger.classList.remove("active");
        navMenu.classList.remove("active");
        if (langSwitcher) langSwitcher.classList.remove("mobile-visible");
        document.body.style.overflow = "";
      });
    });

    // Header scroll effect
    window.addEventListener("scroll", () => {
      if (window.scrollY > 100) {
        header.classList.add("scrolled");
      } else {
        header.classList.remove("scrolled");
      }
    });

    // Close mobile menu on escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && navMenu.classList.contains("active")) {
        hamburger.classList.remove("active");
        navMenu.classList.remove("active");
        const ls = document.querySelector(".language-switcher");
        if (ls) ls.classList.remove("mobile-visible");
        document.body.style.overflow = "";
      }
    });
  }

  // Hero Slider Functions
  initHeroSlider() {
    if (this.heroSlides.length === 0) return;

    const prevBtn = document.querySelector(".hero-nav-btn.prev");
    const nextBtn = document.querySelector(".hero-nav-btn.next");

    // Set initial active slide
    this.showSlide(this.currentHeroSlide);

    // Auto slide
    this.startAutoSlide();

    // Previous button
    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        this.prevSlide();
        this.resetAutoSlide();
      });
    }

    // Next button
    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        this.nextSlide();
        this.resetAutoSlide();
      });
    }

    // Indicators
    this.heroIndicators.forEach((indicator, index) => {
      indicator.addEventListener("click", () => {
        this.showSlide(index);
        this.resetAutoSlide();
      });
    });

    // Pause auto slide on hover
    const hero = document.querySelector(".hero");
    if (hero) {
      hero.addEventListener("mouseenter", () => {
        this.stopAutoSlide();
      });

      hero.addEventListener("mouseleave", () => {
        this.startAutoSlide();
      });
    }
  }

  showSlide(index) {
    // Hide all slides
    this.heroSlides.forEach((slide) => {
      slide.classList.remove("active");
    });

    // Remove active from all indicators
    this.heroIndicators.forEach((indicator) => {
      indicator.classList.remove("active");
    });

    // Show current slide
    this.heroSlides[index].classList.add("active");
    this.heroIndicators[index].classList.add("active");
    this.currentHeroSlide = index;
  }

  nextSlide() {
    let nextIndex = this.currentHeroSlide + 1;
    if (nextIndex >= this.heroSlides.length) {
      nextIndex = 0;
    }
    this.showSlide(nextIndex);
  }

  prevSlide() {
    let prevIndex = this.currentHeroSlide - 1;
    if (prevIndex < 0) {
      prevIndex = this.heroSlides.length - 1;
    }
    this.showSlide(prevIndex);
  }

  startAutoSlide() {
    this.stopAutoSlide();
    this.heroInterval = setInterval(() => {
      this.nextSlide();
    }, 5000);
  }

  stopAutoSlide() {
    if (this.heroInterval) {
      clearInterval(this.heroInterval);
      this.heroInterval = null;
    }
  }

  resetAutoSlide() {
    this.stopAutoSlide();
    this.startAutoSlide();
  }

  // Smooth Scroll
  initSmoothScroll() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault();

        const targetId = this.getAttribute("href");
        if (targetId === "#") return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          const headerHeight = document.querySelector(".header").offsetHeight;
          const targetPosition =
            targetElement.getBoundingClientRect().top +
            window.pageYOffset -
            headerHeight;

          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          });
        }
      });
    });
  }

  // Animations
  initAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -100px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate__animated");

          // Add different animations based on element class
          if (entry.target.classList.contains("about-preview-image")) {
            entry.target.classList.add("animate__fadeInLeft");
          } else if (entry.target.classList.contains("about-preview-text")) {
            entry.target.classList.add("animate__fadeInRight");
          } else if (entry.target.classList.contains("service-card")) {
            entry.target.classList.add("animate__fadeInUp");
          } else if (entry.target.classList.contains("stat-card")) {
            entry.target.classList.add("animate__fadeIn");
          }
        }
      });
    }, observerOptions);

    // Observe elements
    const animatedElements = document.querySelectorAll(
      ".service-card, .about-preview-image, .about-preview-text, .stat-card, .service-card-large"
    );

    animatedElements.forEach((el) => observer.observe(el));
  }

  // Animated Statistics Counter
  initStatistics() {
    // Only select stat-numbers that have data-count attribute for animation
    const statNumbers = document.querySelectorAll(".stat-number[data-count]");
    if (statNumbers.length === 0) return;

    const observerOptions = {
      threshold: 0.5,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const statNumber = entry.target;
          const dataCount = statNumber.getAttribute("data-count");

          // Skip if no data-count attribute or not a valid number
          if (!dataCount || isNaN(parseInt(dataCount))) {
            observer.unobserve(statNumber);
            return;
          }

          const target = parseInt(dataCount);
          const duration = 2000; // 2 seconds
          const increment = target / (duration / 16); // 60fps
          let current = 0;

          const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
              current = target;
              clearInterval(timer);
            }
            statNumber.textContent = Math.floor(current);
          }, 16);

          observer.unobserve(statNumber);
        }
      });
    }, observerOptions);

    statNumbers.forEach((stat) => observer.observe(stat));
  }

  // Page Loader and Transition
  initPageLoader() {
    // Create loader if it doesn't exist
    if (!document.querySelector(".page-loader")) {
      const loader = document.createElement("div");
      loader.className = "page-loader";
      loader.innerHTML = `
        <div class="loader-content">
          <div class="loader-spinner"></div>
          <div class="loader-text">LOADING...</div>
        </div>
      `;
      document.body.appendChild(loader);
    }

    const loader = document.querySelector(".page-loader");

    // Hide loader function
    const hideLoader = () => {
      // Prevent multiple calls
      if (loader.classList.contains("hidden")) return;

      setTimeout(() => {
        loader.classList.add("hidden");
      }, 600);
    };

    // Check if page is already loaded
    if (document.readyState === "complete" || document.readyState === "interactive") {
      hideLoader();
    } else {
      window.addEventListener("load", hideLoader);
      window.addEventListener("DOMContentLoaded", hideLoader); // Faster fallback
    }

    // Safety timeout: Force hide after 5 seconds max (prevents infinite loading)
    setTimeout(hideLoader, 5000);

    // Handle link clicks for transition
    document.addEventListener("click", (e) => {
      const link = e.target.closest("a");

      // Check if it's a valid internal link
      if (
        link &&
        link.href &&
        link.origin === window.location.origin && // Internal link
        !link.getAttribute("href").startsWith("#") && // Not anchor
        link.getAttribute("target") !== "_blank" && // Not new tab
        !link.href.includes("javascript:") // Not JS
      ) {
        e.preventDefault();
        loader.classList.remove("hidden");

        // Navigate after delay
        setTimeout(() => {
          window.location.href = link.href;
        }, 800);
      }
    });
  }

  // Set current year in footer
  setCurrentYear() {
    const yearElement = document.getElementById("currentYear");
    if (yearElement) {
      yearElement.textContent = new Date().getFullYear();
    }
  }

  // ===== PREMIUM ANIMATION FEATURES =====

  // Scroll Progress Indicator
  initScrollProgress() {
    // Create scroll progress element
    if (!document.querySelector('.scroll-progress')) {
      const progressBar = document.createElement('div');
      progressBar.className = 'scroll-progress';
      document.body.appendChild(progressBar);
    }

    const progressBar = document.querySelector('.scroll-progress');

    window.addEventListener('scroll', () => {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const scrollPercent = (scrollTop / docHeight) * 100;
      progressBar.style.width = scrollPercent + '%';
    });
  }

  // Back to Top Button
  initBackToTop() {
    // Create back to top button
    if (!document.querySelector('.back-to-top')) {
      const backToTop = document.createElement('button');
      backToTop.className = 'back-to-top';
      backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
      backToTop.setAttribute('aria-label', 'Back to top');
      document.body.appendChild(backToTop);
    }

    const backToTop = document.querySelector('.back-to-top');

    // Show/hide on scroll
    window.addEventListener('scroll', () => {
      if (window.scrollY > 500) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    });

    // Scroll to top on click
    backToTop.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // Enhanced Scroll Animations with Intersection Observer
  initScrollAnimations() {
    const animatedElements = document.querySelectorAll(
      '.service-highlight-card, .service-card-large, .value-card, .cert-card, ' +
      '.team-card, .about-preview-image, .about-preview-text, .mission, .vision, ' +
      '.section-header, .about-features, .experience-badge, .stat-card'
    );

    // Add animation classes
    animatedElements.forEach((el, index) => {
      el.classList.add('animate-on-scroll', 'fade-up');
      el.style.transitionDelay = (index % 4) * 0.1 + 's';
    });

    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -80px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, observerOptions);

    animatedElements.forEach((el) => observer.observe(el));
  }

  // Parallax Effects
  initParallaxEffects() {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    window.addEventListener('scroll', () => {
      const scrolled = window.scrollY;
      const heroHeight = hero.offsetHeight;

      if (scrolled < heroHeight) {
        // Parallax for hero content
        const heroContent = hero.querySelector('.hero-content');
        if (heroContent) {
          heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
          heroContent.style.opacity = 1 - (scrolled / heroHeight) * 0.5;
        }
      }
    });
  }

  // Staggered Reveal for Grid Items
  initStaggeredReveals() {
    const grids = document.querySelectorAll(
      '.services-grid, .services-grid-large, .values-grid, .certifications, ' +
      '.team-grid, .stats-grid, .footer-links'
    );

    grids.forEach((grid) => {
      const children = grid.children;
      Array.from(children).forEach((child, index) => {
        child.classList.add('animate-on-scroll', 'fade-up');
        child.style.transitionDelay = index * 0.15 + 's';
      });
    });

    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');

          // Trigger children animation
          const children = entry.target.querySelectorAll('.animate-on-scroll');
          children.forEach((child) => {
            child.classList.add('visible');
          });
        }
      });
    }, observerOptions);

    grids.forEach((grid) => observer.observe(grid));
  }
}

// Initialize website when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  const websiteApp = new WebsiteApp();
});

// Form handling for contact page
if (document.getElementById("contactForm")) {
  const contactForm = document.getElementById("contactForm");

  contactForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    // Get form data
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
      // Simulate API call (replace with actual API endpoint)
      await new Promise((resolve) => setTimeout(resolve, 1500));

      // Show success message
      const successMessage =
        document.documentElement.lang === "id"
          ? "Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan menghubungi Anda segera."
          : "Thank you! Your message has been sent successfully. We will contact you shortly.";

      alert(successMessage);
      contactForm.reset();
    } catch (error) {
      // Show error message
      const errorMessage =
        document.documentElement.lang === "id"
          ? "Maaf, terjadi kesalahan. Silakan coba lagi nanti."
          : "Sorry, an error occurred. Please try again later.";

      alert(errorMessage);
    } finally {
      // Re-enable submit button
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });
}
