// Mobile Navigation Toggle
const hamburger = document.getElementById("hamburger");
const navMenu = document.querySelector(".nav-menu");
const header = document.querySelector(".header");

if (hamburger) {
  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    navMenu.classList.toggle("active");
  });
}

// Close mobile menu when clicking on a link
document.querySelectorAll(".nav-menu a").forEach((link) => {
  link.addEventListener("click", () => {
    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
  });
});

// Header scroll effect
window.addEventListener("scroll", () => {
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();

    const targetId = this.getAttribute("href");
    if (targetId === "#") return;

    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      window.scrollTo({
        top: targetElement.offsetTop - 80,
        behavior: "smooth",
      });
    }
  });
});

// Animation on scroll
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("animate");
    }
  });
}, observerOptions);

// Observe elements for animation
document
  .querySelectorAll(".service-card, .department-card, .about-content")
  .forEach((el) => {
    observer.observe(el);
  });

// Form validation for contact page
if (document.getElementById("contactForm")) {
  const contactForm = document.getElementById("contactForm");

  contactForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Basic validation
    let isValid = true;
    const requiredFields = contactForm.querySelectorAll("[required]");

    requiredFields.forEach((field) => {
      if (!field.value.trim()) {
        isValid = false;
        field.style.borderColor = "red";
      } else {
        field.style.borderColor = "";
      }
    });

    // Email validation
    const emailField = contactForm.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailField.value)) {
        isValid = false;
        emailField.style.borderColor = "red";
        alert("Please enter a valid email address");
      }
    }

    if (isValid) {
      // Show success message
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;

      submitBtn.textContent = "Sending...";
      submitBtn.disabled = true;

      // Simulate form submission
      setTimeout(() => {
        alert("Thank you for your message! We will contact you soon.");
        contactForm.reset();
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }, 1500);
    } else {
      alert("Please fill in all required fields correctly.");
    }
  });
}

// Department filter functionality
if (document.getElementById("departmentFilter")) {
  const departmentFilter = document.getElementById("departmentFilter");
  const departmentCards = document.querySelectorAll(".department-card");

  departmentFilter.addEventListener("change", function () {
    const selectedValue = this.value;

    departmentCards.forEach((card) => {
      if (
        selectedValue === "all" ||
        card.dataset.department === selectedValue
      ) {
        card.style.display = "block";
        setTimeout(() => {
          card.style.opacity = "1";
          card.style.transform = "translateY(0)";
        }, 10);
      } else {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        setTimeout(() => {
          card.style.display = "none";
        }, 300);
      }
    });
  });
}
