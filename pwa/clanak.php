<?php
include 'connect.php';
include 'helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($dbc, "SELECT * FROM vijesti WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);

page_header($row ? $row['naslov'] : "Članak", "");
?>

<main class="article-shell">
    <?php if (!$row): ?>
        <section class="article-page">
            <div class="article-header">
                <h1>Članak nije pronađen</h1>
            </div>
            <p class="empty-state">Provjerite poveznicu ili se vratite na početnu stranicu.</p>
        </section>
    <?php else: ?>
        <article class="article-page submitted-article">
            <header class="article-header">
                <p class="article-category"><?php echo h($row['kategorija']); ?></p>
                <h1><?php echo h($row['naslov']); ?></h1>
                <p class="article-meta">AUTOR: Lana Mišić | OBJAVLJENO: <?php echo h(date("d.m.Y.", strtotime($row['datum']))); ?></p>
            </header>

            <figure class="uploaded-image">
                <img src="<?php echo UPLPATH . h($row['slika']); ?>" alt="<?php echo h($row['naslov']); ?>">
            </figure>

            <section class="about">
                <p class="lead"><?php echo nl2br(h($row['sazetak'])); ?></p>
            </section>

            <section class="sadrzaj">
                <p><?php echo nl2br(h($row['tekst'])); ?></p>
            </section>
        </article>
    <?php endif; ?>
</main>

<?php
mysqli_close($dbc);
page_footer();
?>
