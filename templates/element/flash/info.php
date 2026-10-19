<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-info" role="alert" style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
    <div><?= $message ?></div>
    <button type="button" aria-label="Cerrar" onclick="this.parentNode.style.display='none'" style="background:none;border:0;font-size:1.4rem;line-height:1;cursor:pointer;color:inherit;">&times;</button>
</div>
