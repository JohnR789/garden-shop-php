<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <h1 class="mb-4">Contact Us</h1>
      <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <form method="post" action="/contact" class="bg-white p-4 rounded shadow-sm border" autocomplete="off">
          <div class="mb-3">
              <label for="name" class="form-label">Your Name</label>
              <input type="text" class="form-control" name="name" id="name" required autocomplete="name"
                     value="<?= htmlspecialchars($form['name'] ?? '') ?>">
          </div>
          <div class="mb-3">
              <label for="email" class="form-label">Your Email</label>
              <input type="email" class="form-control" name="email" id="email" required autocomplete="email"
                     value="<?= htmlspecialchars($form['email'] ?? '') ?>">
          </div>
          <div class="mb-3">
              <label for="message" class="form-label">Message</label>
              <textarea class="form-control" name="message" id="message" rows="5" required><?= htmlspecialchars($form['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-success px-4">Send Message</button>
      </form>
      <div class="mt-4">
          <strong>Email:</strong> <a href="mailto:info@gardentoolsshop.com" class="text-success">info@gardentoolsshop.com</a>
          <br>
          <strong>Phone:</strong> <a href="tel:+18005551234" class="text-success">1-800-555-1234</a>
          <br>
          <strong>Location:</strong> 123 Garden Path, Yourtown, USA
      </div>
    </div>
  </div>
</div>


