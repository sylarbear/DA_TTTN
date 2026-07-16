<?php
// Shared Course Card — used by Active, Mastered, and Locked sections
// Props: $card['url', 'cefr', 'title', 'desc', 'pct', 'opacity', 'badge', 'footer_left', 'footer_right', 'footer_full', 'locked']

$icon = $card['locked'] ? 'fa-lock' : match($card['cefr'] ?? '') {
    'A1' => 'fa-seedling', 'A2' => 'fa-comments', 'B1' => 'fa-bullseye',
    'B2' => 'fa-chart-line', 'C1' => 'fa-rocket', default => 'fa-book'
};
$grad = match($card['cefr'] ?? '') {
    'A1' => 'from-emerald-500 to-emerald-400', 'A2' => 'from-sky-500 to-sky-400',
    'B1' => 'from-amber-500 to-amber-400', 'B2' => 'from-orange-500 to-orange-400',
    'C1' => 'from-brand-600 to-brand-400', default => 'from-brand-600 to-brand-400'
};
$pct  = $card['pct'] ?? 0;
$href = $card['url'] ?? '#';
$extraClass = $card['opacity'] ?? '';
$title = $card['title'] ?? '';
$desc  = $card['desc'] ?? '';
$badge = $card['badge'] ?? '';
$footerLeft  = $card['footer_left'] ?? '';
$footerRight = $card['footer_right'] ?? '';
$footerFull  = $card['footer_full'] ?? '';
$isLocked    = !empty($card['locked']);
$tag = $isLocked ? 'div' : 'a';
$hrefAttr    = $isLocked ? '' : 'href="' . $href . '"';
$iconClass   = $isLocked ? 'text-2xl' : 'text-4xl';
$iconOpacity = $isLocked ? 'text-white/40' : 'text-white/90';
?>

<<?= $tag ?> <?= $hrefAttr ?> class="group bg-white rounded-xl overflow-hidden border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-200 flex flex-col <?= $extraClass ?>">
    <div class="h-32 bg-gradient-to-br <?= $grad ?> flex items-center justify-center <?= $iconClass ?> <?= $iconOpacity ?> relative">
        <i class="fas <?= $icon ?>"></i>
        <span class="absolute top-3 right-3 bg-black/20 text-white text-xs font-bold px-2.5 py-1 rounded-full"><?= $card['cefr'] ?></span>
        <?= $badge ?>
    </div>
    <div class="p-5 flex flex-col flex-1">
        <h3 class="font-heading font-bold text-slate-800 mb-1 group-hover:text-brand-600 transition-colors"><?= $title ?></h3>
        <p class="text-sm text-slate-400 mb-4 line-clamp-2 flex-1"><?= $desc ?></p>
        <?php if ($pct > 0 && !$isLocked): ?>
        <div class="flex items-center gap-3 mb-4">
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-brand-500 to-brand-400 rounded-full transition-all duration-500" style="width:<?= $pct ?>%"></div>
            </div>
            <span class="text-xs font-bold text-brand-600"><?= $pct ?>%</span>
        </div>
        <?php endif; ?>
        <?php if ($footerFull): ?>
        <div class="pt-3 border-t border-gray-50 mt-3"><?= $footerFull ?></div>
        <?php elseif ($footerLeft || $footerRight): ?>
        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
            <?= $footerLeft ?>
            <?= $footerRight ?>
        </div>
        <?php endif; ?>
    </div>
</<?= $tag ?>>
