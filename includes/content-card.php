<?php
/**
 * Movify - İçerik Kartı Bileşeni (Partial)
 * Bu dosya content-slider içinde include edilir.
 * $item değişkeninin tanımlı olduğunu varsayar.
 */
?>
<div class="content-card" onclick="window.location.href='<?= BASE_URL ?>/content-detail.php?id=<?= $item['id'] ?>'">
    <div class="content-card__image">
        <?php if (!empty($item['cover_image'])): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= e($item['cover_image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
        <?php else: ?>
            <div class="content-card__placeholder"><?= e(mb_substr($item['title'], 0, 1)) ?></div>
        <?php endif; ?>
        <div class="content-card__overlay">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
        </div>
        <?php if (!empty($item['age_rating'])): ?>
            <span class="content-card__age"><?= e($item['age_rating']) ?></span>
        <?php endif; ?>
    </div>
    <div class="content-card__info">
        <span class="content-card__title"><?= e($item['title']) ?></span>
        <div class="content-card__meta">
            <?php if (!empty($item['release_year'])): ?>
                <span><?= e($item['release_year']) ?></span>
            <?php endif; ?>
            <?php if (!empty($item['imdb_rating'])): ?>
                <span class="content-card__rating">⭐ <?= e($item['imdb_rating']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
