<?php if (!$data) return; ?>

<div id="fsb-bar" class="fsb-bar-wrapper">
  <?php if ($data['remaining'] > 0): ?>
    <div class="fsb-bar fsb-warning">
      FALTAM <strong><?php echo wc_price($data['remaining']); ?></strong> PARA PORTES GRÁTIS!
    </div>
  <?php else: ?>
    <div class="fsb-bar fsb-success">
      🎉 VOCÊ GANHOU FRETE GRÁTIS!
    </div>
  <?php endif; ?>
</div>