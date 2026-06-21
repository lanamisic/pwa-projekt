<?php
include 'connect.php';
include 'helpers.php';

$category = $_GET['id'] ?? 'Sport';
$allowed_categories = ["Sport", "Politik"];

if (!in_array($category, $allowed_categories, true)) {
    $category = "Sport";
}

page_header($category, $category);
?>

<main class="page-shell">
    <section class="news-section">
        <h1><?php echo h($category); ?></h1>

        <?php
        $stmt = mysqli_prepare($dbc, "SELECT * FROM vijesti WHERE arhiva = 0 AND kategorija = ? ORDER BY datum DESC, id DESC");
        mysqli_stmt_bind_param($stmt, "s", $category);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 0) {
            echo '<p class="empty-state">Nema objavljenih vijesti za odabranu kategoriju.</p>';
        }

        while ($row = mysqli_fetch_array($result)) {
            render_news_card($row);
        }
        ?>
    </section>
</main>

<?php
mysqli_close($dbc);
page_footer();
?>
