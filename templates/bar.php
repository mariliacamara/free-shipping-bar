<?php if (!$data) return; ?>

<div id="fsb-bar">
  <?php if ($data['remaining'] > 0): ?>
    <div class="fsb-bar fsb-warning">
      FALTAM <?php echo wc_price($data['remaining']); ?> PARA PORTES GRÁTIS!
    </div>
  <?php else: ?>
    <div class="fsb-bar fsb-success">
      🎉 VOCÊ GANHOU FRETE GRÁTIS!
    </div>
  <?php endif; ?>
</div>