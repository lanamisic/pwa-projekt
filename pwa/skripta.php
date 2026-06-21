<?php
function clean_text($value)
{
    return htmlspecialchars(trim($value ?? ""), ENT_QUOTES, "UTF-8");
}

$title = clean_text($_POST['title'] ?? "");
$about = clean_text($_POST['about'] ?? "");
$content = clean_text($_POST['content'] ?? "");
$category = clean_text($_POST['category'] ?? "");
$archive = isset($_POST['archive']) ? "DA" : "NE";

$image = "";
$image_error = "";

if (isset($_FILES['pphoto']) && $_FILES['pphoto']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = "images/";
    $original_name = basename($_FILES['pphoto']['name']);
    $safe_name = preg_replace("/[^a-zA-Z0-9._-]/", "-", $original_name);
    $image = time() . "-" . $safe_name;
    $target_path = $upload_dir . $image;

    if (!move_uploaded_file($_FILES['pphoto']['tmp_name'], $target_path)) {
        $image = "";
        $image_error = "Slika nije spremljena. Provjerite dozvole za mapu images.";
    }
} elseif (isset($_FILES['pphoto']) && $_FILES['pphoto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $image_error = "Došlo je do pogreške prilikom učitavanja slike.";
}

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title !== "" ? $title : "Pregled vijesti"; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="index.php">
            <span>RP</span> Online
        </a>

        <nav class="main-nav" aria-label="Glavna navigacija">
            <a href="index.php">Home</a>
            <a href="kategorija.php?id=Sport">Sport</a>
            <a href="kategorija.php?id=Politik">Politik</a>
            <a href="administrator.php">Administracija</a>
            <a href="unos.php">Unos vijesti</a>
        </nav>
    </div>
</header>

<main class="article-shell">

<article class="article-page submitted-article">
    <header class="article-header">
        <p class="article-category"><?php echo $category; ?></p>

        <h1>
            <?php echo $title; ?>
        </h1>

        <p class="article-meta">AUTOR: Lana Mišić | OBJAVLJENO: <?php echo date("d.m.Y."); ?></p>
    </header>

    <?php if ($image !== ""): ?>
        <figure class="uploaded-image">
            <img src="images/<?php echo rawurlencode($image); ?>" alt="Slika vijesti">
        </figure>
    <?php elseif ($image_error !== ""): ?>
        <p class="form-message"><?php echo clean_text($image_error); ?></p>
    <?php endif; ?>

    <section class="about">
        <p class="lead">
            <?php echo nl2br($about); ?>
        </p>
    </section>

    <section class="sadrzaj">
        <p>
            <?php echo nl2br($content); ?>
        </p>
    </section>

    <section class="sadrzaj article-status">
        <p>
            Prikazati na stranici: <?php echo $archive; ?>
        </p>
    </section>

</article>

</main>

<footer class="site-footer">
    <p>&copy; RP Digital GmbH | Lana Mišić | 2026</p>
    <small>Content Management by InterRed</small>
</footer>

</body>
</html>
