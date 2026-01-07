document.addEventListener('DOMContentLoaded', function() {
  
  window.addEventListener('load', () => {
    setTimeout(() => {
      document.querySelector('.loader').classList.add('hidden');
    }, 1500);
  });