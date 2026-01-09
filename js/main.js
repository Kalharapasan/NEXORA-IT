/* ==========================================
   NEXORA - ENHANCED JAVASCRIPT
   All Interactive Features
   ========================================== */

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
  
  // ============================================
  // LOADER
  // ============================================
  window.addEventListener('load', () => {
    setTimeout(() => {
      document.querySelector('.loader').classList.add('hidden');
    }, 1500);
  });

  // ============================================
  // PARTICLES ANIMATION
  // ============================================
  const particlesContainer = document.getElementById('particles');
  if (particlesContainer) {
    for (let i = 0; i < 60; i++) {
      const particle = document.createElement('div');
      particle.className = 'particle';
      particle.style.left = Math.random() * 100 + '%';
      particle.style.animationDelay = Math.random() * 20 + 's';
      particle.style.animationDuration = (15 + Math.random() * 10) + 's';
      particlesContainer.appendChild(particle);
    }
  }

  // ============================================
  // NAVBAR SCROLL EFFECT
  // ============================================
  const navbar = document.querySelector('.navbar');
  let lastScroll = 0;

  window.addEventListener('scroll', throttle(() => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
    
    lastScroll = currentScroll;
  }, 100));

  // ============================================
  // MOBILE MENU TOGGLE
  // ============================================
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      mobileMenuBtn.classList.toggle('active');
      mobileMenu.classList.toggle('active');
      document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
    });

    // Close mobile menu when clicking on a link
    const mobileLinks = mobileMenu.querySelectorAll('a');
    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        mobileMenuBtn.classList.remove('active');
        mobileMenu.classList.remove('active');
        document.body.style.overflow = '';
      });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
      if (mobileMenu.classList.contains('active') && 
          !mobileMenu.contains(e.target) && 
          !mobileMenuBtn.contains(e.target)) {
        mobileMenuBtn.classList.remove('active');
        mobileMenu.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }

  // ============================================
  // SMOOTH SCROLL FOR ANCHOR LINKS
  // ============================================
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      
      // Skip if it's just '#'
      if (href === '#') return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      
      if (target) {
        const offsetTop = target.offsetTop - 80;
        window.scrollTo({
          top: offsetTop,
          behavior: 'smooth'
        });
      }
    });
  });

  // ============================================
  // FADE IN ON SCROLL ANIMATION
  // ============================================
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, observerOptions);

  document.querySelectorAll('.fade-in').forEach(el => {
    observer.observe(el);
  });

  // ============================================
  // COUNTER ANIMATION FOR STATS
  // ============================================
  const animateCounter = (element) => {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;

    const updateCounter = () => {
      current += increment;
      if (current < target) {
        element.textContent = Math.ceil(current);
        requestAnimationFrame(updateCounter);
      } else {
        element.textContent = target;
        // Add '+' after the number for certain stats
        const label = element.nextElementSibling;
        if (label && (label.textContent.includes('Happy') || label.textContent.includes('Projects') || label.textContent.includes('Team'))) {
          element.textContent = target + '+';
        } else if (label && label.textContent.includes('Rate')) {
          element.textContent = target + '%';
        }
      }
    };

    updateCounter();
  };

  const statsSection = document.querySelector('.stats-section');
  if (statsSection) {
    const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const statNumbers = entry.target.querySelectorAll('.stat-number');
          statNumbers.forEach(num => {
            if (num.textContent === '0' || !num.textContent.includes('+')) {
              animateCounter(num);
            }
          });
          statsObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    statsObserver.observe(statsSection);
  }

  // ============================================
  // SCROLL INDICATOR
  // ============================================
  const scrollIndicator = document.querySelector('.scroll-indicator');
  if (scrollIndicator) {
    scrollIndicator.addEventListener('click', () => {
      const aboutSection = document.querySelector('#about');
      if (aboutSection) {
        aboutSection.scrollIntoView({ behavior: 'smooth' });
      }
    });

    // Hide scroll indicator when scrolling down
    window.addEventListener('scroll', () => {
      if (window.pageYOffset > 200) {
        scrollIndicator.style.opacity = '0';
        scrollIndicator.style.visibility = 'hidden';
      } else {
        scrollIndicator.style.opacity = '1';
        scrollIndicator.style.visibility = 'visible';
      }
    });
  }

  // ============================================
  // BACK TO TOP BUTTON
  // ============================================
  const backToTop = document.getElementById('backToTop');
  
  if (backToTop) {
    window.addEventListener('scroll', throttle(() => {
      if (window.pageYOffset > 300) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    }, 100));

    backToTop.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // ============================================
  // CONTACT FORM HANDLING
  // ============================================
  const contactForm = document.getElementById('contactForm');
  const formStatus = document.getElementById('formStatus');

  if (contactForm) {
    // Form validation
    const validateEmail = (email) => {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    };

    const validatePhone = (phone) => {
      return /^[\d\s\+\-\(\)]{10,}$/.test(phone);
    };

    // Input validation on blur
    const inputs = contactForm.querySelectorAll('input, textarea');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value) {
          this.style.borderColor = 'var(--error)';
        } else {
          this.style.borderColor = '#e0e0e0';
        }
      });

      input.addEventListener('focus', function() {
        this.style.borderColor = 'var(--accent-bright)';
      });
    });

    contactForm.addEventListener('submit', async function(e) {
      e.preventDefault();

      // Get form data
      const formData = new FormData(contactForm);
      const name = formData.get('name');
      const email = formData.get('email');
      const phone = formData.get('phone');
      const subject = formData.get('subject');
      const message = formData.get('message');

      // Validation
      if (!name || !email || !subject || !message) {
        showFormStatus('Please fill in all required fields', 'error');
        return;
      }

      if (!validateEmail(email)) {
        showFormStatus('Please enter a valid email address', 'error');
        return;
      }

      if (phone && !validatePhone(phone)) {
        showFormStatus('Please enter a valid phone number', 'error');
        return;
      }
      
      // Show loading state
      const submitButton = contactForm.querySelector('button[type="submit"]');
      const originalButtonHTML = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      submitButton.disabled = true;

      try {
        // Send form data to PHP backend
        const response = await fetch('php/contact.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          showFormStatus('Thank you! Your message has been sent successfully. We\'ll get back to you soon.', 'success');
          contactForm.reset();
          
          // Send confirmation email or notification
          console.log('Form submitted successfully:', result);
        } else {
          showFormStatus(result.message || 'Something went wrong. Please try again.', 'error');
        }
      } catch (error) {
        console.error('Form submission error:', error);
        showFormStatus('Unable to send message. Please try again or contact us directly.', 'error');
      } finally {
        submitButton.innerHTML = originalButtonHTML;
        submitButton.disabled = false;
      }
    });
  }

  function showFormStatus(message, type) {
    formStatus.textContent = message;
    formStatus.className = `form-status ${type}`;
    formStatus.style.display = 'block';

    // Auto-hide after 5 seconds
    setTimeout(() => {
      formStatus.style.display = 'none';
    }, 5000);
  }

  // ============================================
  // NEWSLETTER FORM HANDLING
  // ============================================
  const newsletterForm = document.getElementById('newsletterForm');
  
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const emailInput = this.querySelector('input[type="email"]');
      const submitButton = this.querySelector('button');
      const originalButtonText = submitButton.innerHTML;
      
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
      submitButton.disabled = true;
      
      try {
        const formData = new FormData();
        formData.append('email', emailInput.value);
        
        const response = await fetch('php/newsletter.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          alert('Thank you for subscribing to our newsletter!');
          emailInput.value = '';
        } else {
          alert('Subscription failed. Please try again.');
        }
      } catch (error) {
        console.error('Newsletter subscription error:', error);
        alert('Unable to subscribe. Please try again later.');
      } finally {
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
      }
    });
  }

  // ============================================
  // TESTIMONIAL SLIDER
  // ============================================
  const testimonials = [
    {
      content: "Nexora transformed our business operations completely. The automation tools saved us countless hours, and the POS system is incredibly intuitive. Our revenue has increased by 40% since implementation.",
      author: "Sarah Mitchell",
      role: "CEO, Retail Solutions Inc."
    },
    {
      content: "The team at Nexora delivered exactly what we needed. Their POS system is fast, reliable, and the customer support is outstanding. Highly recommended!",
      author: "John Anderson",
      role: "Owner, Tech Store"
    },
    {
      content: "Working with Nexora has been a game-changer for our business. The cloud solutions they provided have given us the flexibility we needed to grow rapidly.",
      author: "Emily Chen",
      role: "Director, E-commerce Co."
    },
    {
      content: "Professional, efficient, and innovative. Nexora exceeded our expectations in every way. Their custom development solution solved our unique challenges perfectly.",
      author: "Michael Roberts",
      role: "CTO, Manufacturing Ltd."
    }
  ];

  let currentTestimonial = 0;
  const testimonialContent = document.querySelector('.testimonial-content');
  const testimonialAuthor = document.querySelector('.testimonial-author');
  const testimonialRole = document.querySelector('.testimonial-role');
  const prevButton = document.getElementById('prevTestimonial');
  const nextButton = document.getElementById('nextTestimonial');
  const dotsContainer = document.getElementById('testimonialDots');

  function createDots() {
    if (dotsContainer) {
      testimonials.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = 'testimonial-dot';
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => showTestimonial(index));
        dotsContainer.appendChild(dot);
      });
    }
  }

  function updateDots() {
    if (dotsContainer) {
      const dots = dotsContainer.querySelectorAll('.testimonial-dot');
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentTestimonial);
      });
    }
  }

  function showTestimonial(index) {
    if (testimonialContent && testimonialAuthor && testimonialRole) {
      // Fade out
      testimonialContent.style.opacity = '0';
      testimonialAuthor.style.opacity = '0';
      testimonialRole.style.opacity = '0';
      
      setTimeout(() => {
        currentTestimonial = index;
        const testimonial = testimonials[currentTestimonial];
        
        testimonialContent.textContent = `"${testimonial.content}"`;
        testimonialAuthor.textContent = testimonial.author;
        testimonialRole.textContent = testimonial.role;
        
        // Fade in
        testimonialContent.style.opacity = '1';
        testimonialAuthor.style.opacity = '1';
        testimonialRole.style.opacity = '1';
        
        updateDots();
      }, 300);
    }
  }

  function nextTestimonial() {
    const next = (currentTestimonial + 1) % testimonials.length;
    showTestimonial(next);
  }

  function prevTestimonialFunc() {
    const prev = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
    showTestimonial(prev);
  }

  // Initialize testimonials
  if (testimonialContent) {
    createDots();
    
    if (prevButton) {
      prevButton.addEventListener('click', prevTestimonialFunc);
    }
    
    if (nextButton) {
      nextButton.addEventListener('click', nextTestimonial);
    }
    
    // Auto-rotate testimonials every 7 seconds
    let testimonialInterval = setInterval(nextTestimonial, 7000);
    
    // Pause auto-rotate on hover
    const testimonialSection = document.querySelector('.testimonials');
    if (testimonialSection) {
      testimonialSection.addEventListener('mouseenter', () => {
        clearInterval(testimonialInterval);
      });
      
      testimonialSection.addEventListener('mouseleave', () => {
        testimonialInterval = setInterval(nextTestimonial, 7000);
      });
    }
  }

  // ============================================
  // LAZY LOADING FOR IMAGES
  // ============================================
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          const src = img.getAttribute('data-src');
          if (src) {
            img.src = src;
            img.removeAttribute('data-src');
            observer.unobserve(img);
          }
        }
      });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }

  // ============================================
  // PREVENT FORM RESUBMISSION ON REFRESH
  // ============================================
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

  // ============================================
  // ACCESSIBILITY: KEYBOARD NAVIGATION
  // ============================================
  document.addEventListener('keydown', (e) => {
    // ESC key closes mobile menu
    if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('active')) {
      mobileMenuBtn.classList.remove('active');
      mobileMenu.classList.remove('active');
      document.body.style.overflow = '';
    }
    
    // Arrow keys for testimonial navigation
    if (e.key === 'ArrowLeft') {
      prevTestimonialFunc();
    } else if (e.key === 'ArrowRight') {
      nextTestimonial();
    }
  });

  // ============================================
  // SCROLL PROGRESS INDICATOR
  // ============================================
  const scrollProgress = document.createElement('div');
  scrollProgress.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, #3d6cb9, #5a8fd9);
    z-index: 9999;
    transition: width 0.1s ease;
  `;
  document.body.appendChild(scrollProgress);

  window.addEventListener('scroll', throttle(() => {
    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrolled = (window.pageYOffset / scrollHeight) * 100;
    scrollProgress.style.width = scrolled + '%';
  }, 50));

  // ============================================
  // PERFORMANCE MONITORING
  // ============================================
  if ('PerformanceObserver' in window) {
    const perfObserver = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        // Log performance metrics for debugging
        console.log('Performance:', entry.name, entry.duration);
      }
    });

    try {
      perfObserver.observe({ entryTypes: ['measure', 'navigation'] });
    } catch (e) {
      // Browser doesn't support this type
      console.log('Performance observer not supported');
    }
  }

  // ============================================
  // CURSOR EFFECTS (Optional - for modern browsers)
  // ============================================
  if (window.innerWidth > 1024) {
    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.style.cssText = `
      width: 20px;
      height: 20px;
      border: 2px solid #3d6cb9;
      border-radius: 50%;
      position: fixed;
      pointer-events: none;
      z-index: 9999;
      transition: transform 0.15s ease;
      display: none;
    `;
    document.body.appendChild(cursor);

    document.addEventListener('mousemove', (e) => {
      cursor.style.display = 'block';
      cursor.style.left = e.clientX - 10 + 'px';
      cursor.style.top = e.clientY - 10 + 'px';
    });

    document.querySelectorAll('a, button, .service-card, .portfolio-item').forEach(el => {
      el.addEventListener('mouseenter', () => {
        cursor.style.transform = 'scale(2)';
        cursor.style.borderColor = '#5a8fd9';
      });
      
      el.addEventListener('mouseleave', () => {
        cursor.style.transform = 'scale(1)';
        cursor.style.borderColor = '#3d6cb9';
      });
    });
  }

  // ============================================
  // CONSOLE LOG - INITIALIZATION COMPLETE
  // ============================================
  console.log('%c🚀 Nexora Website Initialized Successfully!', 'color: #3d6cb9; font-size: 16px; font-weight: bold;');
  console.log('%cDeveloped with ❤️ by Nexora Team', 'color: #666; font-size: 12px;');
  
});

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Debounce function for performance optimization
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Throttle function for scroll events
function throttle(func, limit) {
  let inThrottle;
  return function() {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

// Check if element is in viewport
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

// Smooth scroll to element
function scrollToElement(element, offset = 80) {
  if (element) {
    const elementPosition = element.offsetTop - offset;
    window.scrollTo({
      top: elementPosition,
      behavior: 'smooth'
    });
  }
}

// Get cookie value
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

// Set cookie
function setCookie(name, value, days) {
  const date = new Date();
  date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
  const expires = `expires=${date.toUTCString()}`;
  document.cookie = `${name}=${value};${expires};path=/`;
}

// ============================================
// EXPORT FUNCTIONS FOR EXTERNAL USE
// ============================================
window.NexoraUtils = {
  debounce,
  throttle,
  isInViewport,
  scrollToElement,
  getCookie,
  setCookie
};

// ============================================
// LOAD TEAM MEMBERS FROM DATABASE
// ============================================
async function loadTeamMembers() {
  const teamGrid = document.getElementById('teamGrid');
  
  if (!teamGrid) return;
  
  try {
    const response = await fetch('php/get_team.php');
    const data = await response.json();
    
    if (data.success && data.members && data.members.length > 0) {
      teamGrid.innerHTML = '';
      
      data.members.forEach(member => {
        const teamMemberDiv = document.createElement('div');
        teamMemberDiv.className = 'team-member';
        
        // Create social links HTML
        let socialLinks = '<div class="team-social">';
        if (member.linkedin_url && member.linkedin_url !== '#') {
          socialLinks += `<a href="${member.linkedin_url}" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin"></i></a>`;
        }
        if (member.twitter_url && member.twitter_url !== '#') {
          socialLinks += `<a href="${member.twitter_url}" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>`;
        }
        if (member.github_url && member.github_url !== '#') {
          socialLinks += `<a href="${member.github_url}" target="_blank" rel="noopener noreferrer"><i class="fab fa-github"></i></a>`;
        }
        socialLinks += '</div>';
        
        teamMemberDiv.innerHTML = `
          <div class="team-image">
            <img src="${member.image_url || 'https://via.placeholder.com/400x400?text=No+Image'}" 
                 alt="${member.name}"
                 onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'">
            ${socialLinks}
          </div>
          <h4>${member.name}</h4>
          <p>${member.position}</p>
        `;
        
        teamGrid.appendChild(teamMemberDiv);
      });
    } else {
      teamGrid.innerHTML = '<p style="text-align: center; grid-column: 1/-1; color: #666;">No team members available at the moment.</p>';
    }
  } catch (error) {
    console.error('Error loading team members:', error);
    teamGrid.innerHTML = '<p style="text-align: center; grid-column: 1/-1; color: #666;">Unable to load team members. Please try again later.</p>';
  }
}

// Load team members when page loads
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadTeamMembers);
} else {
  loadTeamMembers();
}
