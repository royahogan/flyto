<?php if (isset($reservaFeedback) && is_string($reservaFeedback) && $reservaFeedback !== ''): ?>
    <p><?= htmlspecialchars($reservaFeedback, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
