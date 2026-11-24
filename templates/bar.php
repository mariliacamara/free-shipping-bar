<?php if (!$data): ?>
<div id="fsb-bar" class="fsb-bar-wrapper"></div>
<?php return; endif; ?>

<div id="fsb-bar" class="fsb-bar-wrapper">
  <?php if ($data['remaining'] > 0): ?>
    <div class="fsb-bar fsb-warning">
      FALTAM <strong><?php echo wc_price($data['remaining']); ?></strong> PARA PORTES GRÁTIS!
    </div>
  <?php else: ?>
    <div class="fsb-bar fsb-success">
      🎉 GANHOU ENVIO GRÁTIS!
    </div>
  <?php endif; ?>
</div>
