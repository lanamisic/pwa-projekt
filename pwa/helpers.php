<?php
define('UPLPATH', 'images/');

function h($value)
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function nav_link($href, $label, $active = false)
{
    $class = $active ? ' class="active"' : '';
    return '<a' . $class . ' href="' . h($href) . '">' . h($label) . '</a>';
}

function page_header($title, $active = '')
{
    echo '<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . h($title) . '</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="index.php" aria-label="RP Online početna">
            <span>RP</span> Online
        </a>
        <nav class="main-nav" aria-label="Glavna navigacija">
            ' . nav_link('index.php', 'Home', $active === 'home') . '
            ' . nav_link('kategorija.php?id=Sport', 'Sport', $active === 'Sport') . '
            ' . nav_link('kategorija.php?id=Politik', 'Politik', $active === 'Politik') . '
            ' . nav_link('administrator.php', 'Administracija', $active === 'admin') . '
            ' . nav_link('unos.php', 'Unos vijesti', $active === 'unos') . '
            ' . nav_link('registracija.php', 'Registracija', $active === 'registracija') . '
        </nav>
    </div>
</header>';
}

function page_footer()
{
    echo '<footer class="site-footer">
    <p>&copy; RP Digital GmbH | Lana Mišić | lmisic@tvz.hr | 2026</p>
    <small>Content Management by InterRed</small>
</footer>
</body>
</html>';
}

function upload_image($field_name, $current_image = '')
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] === UPLOAD_ERR_NO_FILE) {
        return $current_image;
    }

    if ($_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return $current_image;
    }

    $original_name = basename($_FILES[$field_name]['name']);
    $safe_name = preg_replace("/[^a-zA-Z0-9._-]/", "-", $original_name);
    $image = time() . "-" . $safe_name;
    $target_path = UPLPATH . $image;

    if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $target_path)) {
        return $image;
    }

    return $current_image;
}

function render_news_card($row)
{
    echo '<article class="news-card">
        <a class="news-image" href="clanak.php?id=' . (int)$row['id'] . '">
            <img src="' . UPLPATH . h($row['slika']) . '" alt="' . h($row['naslov']) . '">
        </a>
        <div class="news-copy">
            <h2><a href="clanak.php?id=' . (int)$row['id'] . '">' . h($row['naslov']) . '</a></h2>
            <p>' . h($row['sazetak']) . ' <span>' . h(date("d.m.Y.", strtotime($row['datum']))) . '</span></p>
        </div>
    </article>';
}
?>
