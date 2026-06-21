<?php
include 'connect.php';
include 'helpers.php';

page_header("RP Online", "home");
?>

<main class="page-shell">
    <?php
    $categories = ["Sport", "Politik"];

    foreach ($categories as $category) {
        echo '<section class="news-section" id="' . h(strtolower($category)) . '">';
        echo '<h1>' . h($category) . '</h1>';

        $stmt = mysqli_prepare($dbc, "SELECT * FROM vijesti WHERE arhiva = 0 AND kategorija = ? ORDER BY datum DESC, id DESC LIMIT 4");
        mysqli_stmt_bind_param($stmt, "s", $category);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 0) {
            echo '<p class="empty-state">Još nema objavljenih vijesti u ovoj kategoriji.</p>';
        }

        while ($row = mysqli_fetch_array($result)) {
            render_news_card($row);
        }

        echo '</section>';
    }
    ?>
</main>

<?php
mysqli_close($dbc);
page_footer();
?>
