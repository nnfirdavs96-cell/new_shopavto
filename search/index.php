<?php
require_once dirname(__DIR__) . '/config/config.php';

$q     = trim($_GET['q'] ?? '');
$parts = [];
$total = 0;

if (mb_strlen($q) >= 2) {
    $db   = getDB();
    $like = '%' . $q . '%';
    $countStmt = $db->prepare("SELECT COUNT(*) FROM parts p WHERE p.is_active = 1 AND (p.part_number LIKE ? OR p.name LIKE ?)");
    $countStmt->execute([$like, $like]);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $db->prepare(
        "SELECT p.*, b.name AS brand_name, c.name AS category_name
         FROM parts p
         LEFT JOIN brands b ON b.id = p.brand_id
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.is_active = 1 AND (p.part_number LIKE ? OR p.name LIKE ?)
         ORDER BY CASE WHEN p.part_number = ? THEN 0 WHEN p.part_number LIKE ? THEN 1 ELSE 2 END, p.part_number
         LIMIT 50"
    );
    $stmt->execute([$like, $like, $q, $q.'%']);
    $parts = $stmt->fetchAll();
}

$pageTitle = $q ? 'Поиск: '.$q : 'Поиск запчастей';
require_once dirname(__DIR__) . '/includes/header.php';

function highlightSearch(string $text, string $q): string {
    if (!$q) return sanitize($text);
    $escaped = sanitize($text);
    $qEsc    = preg_quote(sanitize($q), '/');
    return preg_replace('/('.$qEsc.')/iu', '<mark style="background:#fff3cd;color:#856404;">$1</mark>', $escaped);
}
?>

<!--breadcrumb area start-->
<div class="breadcrumb_area">
  <div class="container">
    <div class="breadcrumb_content">
      <h2>Поиск запчастей</h2>
      <ul>
        <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
        <li>Поиск</li>
      </ul>
    </div>
  </div>
</div>
<!--breadcrumb area end-->

<div class="shop_area" style="padding:40px 0;">
  <div class="container">

    <!-- Search form -->
    <form method="get" action="" style="display:flex;gap:10px;margin-bottom:36px;max-width:700px;">
      <input type="text" name="q" class="form-control" value="<?= sanitize($q) ?>"
             placeholder="Введите номер детали или название..." autofocus
             style="flex:1;padding:12px 16px;border:1px solid #ddd;border-radius:2px;font-size:0.9rem;">
      <button type="submit" class="button" style="padding:12px 24px;font-size:0.9rem;">НАЙТИ</button>
    </form>

    <?php if ($q): ?>
    <div style="margin-bottom:20px;">
      <strong><?= $total ?></strong> результатов по запросу
      <span style="background:#fff3cd;color:#856404;padding:2px 8px;border-radius:2px;"><?= sanitize($q) ?></span>
    </div>

    <?php if (empty($parts)): ?>
      <div class="az-no-results">
        <div class="az-no-results-icon">🔍</div>
        <p>По запросу «<?= sanitize($q) ?>» ничего не найдено.</p>
        <p style="margin-top:8px;font-size:0.8rem;color:#999;">Проверьте правильность написания номера детали.</p>
      </div>
    <?php else: ?>
      <div class="az-table-wrap">
        <table class="az-data-table">
          <thead>
            <tr>
              <th>Номер детали</th>
              <th>Название</th>
              <th>Бренд</th>
              <th>Категория</th>
              <th style="text-align:right;">Цена</th>
              <th>Наличие</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($parts as $part):
              $stock = getStockStatus((int)$part['stock']);
            ?>
            <tr>
              <td style="font-family:monospace;color:#ff6600;font-weight:600;">
                <?= highlightSearch($part['part_number'], $q) ?>
              </td>
              <td>
                <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $part['id'] ?>" style="color:#333;font-size:0.875rem;">
                  <?= highlightSearch($part['name'], $q) ?>
                </a>
              </td>
              <td style="font-size:0.8rem;color:#555;"><?= sanitize($part['brand_name']) ?></td>
              <td style="font-size:0.8rem;color:#888;"><?= sanitize($part['category_name']) ?></td>
              <td style="text-align:right;font-weight:700;color:#ff6600;"><?= formatPrice($part['price']) ?></td>
              <td><span class="az-badge az-badge-<?= $stock['class'] ?>"><?= $stock['label'] ?></span></td>
              <td><a href="<?= APP_URL ?>/catalog/part.php?id=<?= $part['id'] ?>" class="button" style="padding:6px 14px;font-size:0.78rem;">Подробнее</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
    <?php endif; ?>

  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
