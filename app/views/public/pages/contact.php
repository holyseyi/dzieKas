<?php /** @var string $csrf_token */ ?>
<div class="container page">
    <h1 class="page__title">Contact Us</h1>
    <p class="page__lead">Have a question, suggestion, or a broken link to report? Send us a message.</p>
    <div class="page__split">
        <form action="/contact" method="post" class="form panel">
            <input type="hidden" name="_csrf_token" value="<?= e($csrf_token ?? '') ?>">
            <label class="field"><span>Your Name</span><input type="text" name="name" required></label>
            <label class="field"><span>Email</span><input type="email" name="email" required></label>
            <label class="field"><span>Subject</span><input type="text" name="subject"></label>
            <label class="field"><span>Message</span><textarea name="message" rows="6" required></textarea></label>
            <button type="submit" class="btn btn--primary">Send Message</button>
        </form>
        <div class="panel">
            <h3 class="panel__title">Get in touch</h3>
            <p>Email: <a href="mailto:<?= e($config['name']) ?>">contact@dziekas.com</a></p>
            <p class="muted">We usually respond within 48 hours.</p>
        </div>
    </div>
</div>
