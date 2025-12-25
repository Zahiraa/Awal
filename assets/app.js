import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

// Function to animate counter
function animateCounter(counter) {
  const target = parseInt(counter.getAttribute("data-target"));
  const suffix = counter.getAttribute("data-suffix") || "";
  const duration = 2000; // 2 seconds
  const increment = target / (duration / 16);
  let current = 0;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      current = target;
      clearInterval(timer);
    }

    // Format the number
    let displayValue;
    if (suffix === "k" && current >= 1000) {
      displayValue =
        (current / 1000).toFixed(current % 1000 === 0 ? 0 : 1) + "k";
    } else {
      displayValue = Math.floor(current).toLocaleString();
    }

    counter.textContent = displayValue;
  }, 16);
}

// Function to initialize counter animations
function initializeCounterAnimations() {
  const counters = document.querySelectorAll(".counter");
  const statsSection = document.getElementById("stats-section");

  if (!statsSection || counters.length === 0) {
    return; // No stats section or counters found, exit early
  }

  // Check if animations have already been initialized for this section
  if (statsSection.hasAttribute("data-animations-initialized")) {
    return;
  }

  // Mark this section as initialized
  statsSection.setAttribute("data-animations-initialized", "true");

  // Intersection Observer to trigger animation when section is visible
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Start animations for all counters
          counters.forEach((counter) => {
            // Only animate if not already animated
            if (!counter.hasAttribute("data-animated")) {
              counter.setAttribute("data-animated", "true");
              animateCounter(counter);
            }
          });

          // Stop observing after animation starts
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.5,
    }
  );

  observer.observe(statsSection);
}

function copyLink() {
  const link = window.location.href;
  const copyButton = document.getElementById("copy-link");
  const originalContent = copyButton.innerHTML;

  // Get translations from data attributes (from Symfony translations)
  const successMessage =
    copyButton.getAttribute("data-success-message") || "Lien copié !";
  const errorMessage =
    copyButton.getAttribute("data-error-message") || "Erreur";
  navigator.clipboard
    .writeText(link)
    .then(() => {
      // Change button content to show success
      copyButton.innerHTML = `
      <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
      ${successMessage}
    `;

      // Add success styling
      copyButton.classList.remove(
        "bg-white",
        "border-light-gray-100",
        "text-gray-700",
        "hover:bg-gray-100"
      );
      copyButton.classList.add(
        "bg-green-50",
        "border-green-200",
        "text-green-700"
      );

      // Add a subtle animation
      copyButton.style.transform = "scale(0.95)";
      setTimeout(() => {
        copyButton.style.transform = "scale(1)";
      }, 150);

      // Revert back to original state after 2 seconds
      setTimeout(() => {
        copyButton.innerHTML = originalContent;
        copyButton.classList.remove(
          "bg-green-50",
          "border-green-200",
          "text-green-700"
        );
        copyButton.classList.add(
          "bg-white",
          "border-light-gray-100",
          "text-gray-700",
          "hover:bg-gray-100"
        );
      }, 2000);
    })
    .catch((err) => {
      console.error("Failed to copy link: ", err);

      // Show error state
      copyButton.innerHTML = `
      <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
      ${errorMessage}
    `;

      copyButton.classList.remove(
        "bg-white",
        "border-light-gray-100",
        "text-gray-700",
        "hover:bg-gray-100"
      );
      copyButton.classList.add("bg-red-50", "border-red-200", "text-red-700");

      // Revert back after 2 seconds
      setTimeout(() => {
        copyButton.innerHTML = originalContent;
        copyButton.classList.remove(
          "bg-red-50",
          "border-red-200",
          "text-red-700"
        );
        copyButton.classList.add(
          "bg-white",
          "border-light-gray-100",
          "text-gray-700",
          "hover:bg-gray-100"
        );
      }, 2000);
    });
}

// Main initialization function
function initializeApp() {
  // Initialize counter animations
  initializeCounterAnimations();

  // Use event delegation for mobile menu button (works for dynamically added elements)
  document.addEventListener("click", function (event) {
    const mobileMenuButton = document.getElementById("mobile-menu-button");
    const mobileMenu = document.getElementById("mobile-menu");

    if (event.target.closest("#mobile-menu-button")) {
      if (mobileMenu) {
        mobileMenu.classList.toggle("hidden");
      }
    }

    // Close mobile menu when clicking on a link inside it
    if (
      mobileMenu &&
      !mobileMenu.classList.contains("hidden") &&
      event.target.closest("#mobile-menu a")
    ) {
      mobileMenu.classList.add("hidden");
    }

    // Close mobile menu when clicking outside
    if (
      mobileMenuButton &&
      mobileMenu &&
      !mobileMenu.classList.contains("hidden") &&
      !mobileMenuButton.contains(event.target) &&
      !mobileMenu.contains(event.target)
    ) {
      mobileMenu.classList.add("hidden");
    }
  });

  // Use event delegation for user dropdown menu (works for dynamically added elements)
  document.addEventListener("click", function (event) {
    const userMenuButton = document.getElementById("user-menu-button");
    const userDropdown = document.getElementById("user-dropdown");

    if (event.target.closest("#user-menu-button")) {
      if (userDropdown) {
        userDropdown.classList.contains("hidden")
          ? userDropdown.classList.remove("hidden")
          : userDropdown.classList.add("hidden");
      }
    }

    // Close user dropdown when clicking outside
    if (
      userMenuButton &&
      userDropdown &&
      !userMenuButton.contains(event.target) &&
      !userDropdown.contains(event.target)
    ) {
      userDropdown.classList.add("hidden");
    }
  });

  // Use event delegation for copy link
  document.addEventListener("click", function (event) {
    if (event.target.closest("#copy-link")) {
      copyLink();
    }
  });
}

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", function () {
  initializeApp();
});

// Reinitialize animations when content might have changed (e.g., after redirects)
document.addEventListener("turbo:load", function () {
  initializeCounterAnimations();
});

// Also reinitialize after a short delay to catch any dynamic content loading
window.addEventListener("load", function () {
  setTimeout(() => {
    initializeCounterAnimations();
  }, 100);
});
