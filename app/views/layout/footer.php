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

<!-- Hide/reveal header script -->
<script>
let lastScrollY = window.scrollY;
const header = document.getElementById('mainHeader');
window.addEventListener('scroll', function () {
  if (!header) return;
  if (window.scrollY > lastScrollY && window.scrollY > 80) {
    header.style.transform = 'translateY(-100%)';
    header.style.transition = 'transform 0.3s';
  } else {
    header.style.transform = 'translateY(0)';
    header.style.transition = 'transform 0.3s';
  }
  lastScrollY = window.scrollY;
});
</script>

</body>
</html>
