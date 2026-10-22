<?php

use NurseryNest\Core\Session;

foreach (Session::takeFlashes() as $flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endforeach; ?>
<?php if (!empty($error)): ?>
    <div class="flash flash-error" role="alert"><?= e($error) ?></div>
<?php endif; ?>
