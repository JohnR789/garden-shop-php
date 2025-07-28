</div> <!-- .container -->

<footer class="footer bg-success text-white py-3 mt-auto shadow-sm" style="width:100%; z-index:1030;">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <span>&copy; <?= date('Y') ?> Garden Tools Shop</span>
        <nav>
            <a href="/" class="text-white text-decoration-underline me-3">Home</a>
            <a href="/cart" class="text-white text-decoration-underline me-3">Cart</a>
            <a href="/about" class="text-white text-decoration-underline me-3">About</a>
            <a href="/contact" class="text-white text-decoration-underline">Contact</a>
        </nav>
    </div>
</footer>

<!-- Toast notification (hidden by default) -->
<div id="toast-notification" class="toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-4"
     role="alert" aria-live="assertive" aria-atomic="true" style="min-width:220px; z-index:20000; display:none;">
  <div class="d-flex">
    <div class="toast-body" id="toast-message"></div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close" onclick="hideToast()"></button>
  </div>
</div>
<script>
function showToast(message, colorClass = "bg-success") {
  const toast = document.getElementById("toast-notification");
  const msg = document.getElementById("toast-message");
  if (!toast || !msg) return;
  toast.className = "toast align-items-center text-white border-0 position-fixed bottom-0 end-0 m-4 " + colorClass;
  msg.textContent = message;
  toast.style.display = "block";
  setTimeout(hideToast, 2300);
}
function hideToast() {
  const toast = document.getElementById("toast-notification");
  if (toast) toast.style.display = "none";
}
</script>
<?php if (!empty($_SESSION['toast'])):
  $toast = $_SESSION['toast'];
  $msg = is_array($toast) ? $toast['message'] : $toast;
  $color = (is_array($toast) && isset($toast['class'])) ? $toast['class'] : 'bg-success';
?>
<script>
  showToast("<?= htmlspecialchars($msg) ?>", "<?= htmlspecialchars($color) ?>");
</script>
<?php unset($_SESSION['toast']); endif; ?>

<!-- Header hide/reveal on scroll (desktop only) -->
<script>
let lastScrollY = window.scrollY;
const header = document.getElementById('mainHeader');
window.addEventListener('scroll', function () {
  if (!header) return;
  // Only hide on desktop
  if (window.innerWidth >= 768) {
    if (window.scrollY > lastScrollY && window.scrollY > 80) {
      header.style.transform = 'translateY(-100%)';
      header.style.transition = 'transform 0.3s';
    } else {
      header.style.transform = 'translateY(0)';
      header.style.transition = 'transform 0.3s';
    }
  } else {
    // Always visible on mobile
    header.style.transform = 'translateY(0)';
    header.style.transition = 'transform 0.3s';
  }
  lastScrollY = window.scrollY;
});
</script>
</body>
</html>


