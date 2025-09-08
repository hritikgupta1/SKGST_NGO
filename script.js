// for changing color of nav buttons after get clicked
// highlight the current page link
document.querySelectorAll('.nav-link').forEach(link => {
  if (link.href === window.location.href) {
    link.classList.add('active');
  }
});


//for mobile nav script
function openMobileNav() {
  document.getElementById("mobileNav").classList.add("open");
  document.getElementById("navOverlay").classList.add("show");
  document.getElementById("hamburger").style.display = "none";
  document.querySelector(".close-btn").style.display = "block";
}
function closeMobileNav() {
  document.getElementById("mobileNav").classList.remove("open");
  document.getElementById("navOverlay").classList.remove("show");
  document.getElementById("hamburger").style.display = "block";
  document.querySelector(".close-btn").style.display = "none";
}


