<?php
include 'connect.php';
include 'helpers.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? "");
    $about = trim($_POST['about'] ?? "");
    $content = trim($_POST['content'] ?? "");
    $category = trim($_POST['category'] ?? "");
    $archive = isset($_POST['archive']) ? 1 : 0;
    $date = date('Y-m-d');
    $image = upload_image('pphoto');

    if ($title !== "" && $about !== "" && $content !== "" && $category !== "" && $image !== "") {
        $stmt = mysqli_prepare($dbc, "INSERT INTO vijesti (datum, naslov, sazetak, tekst, slika, kategorija, arhiva) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $date, $title, $about, $content, $image, $category, $archive);

            if (mysqli_stmt_execute($stmt)) {
                $new_id = mysqli_insert_id($dbc);
                header("Location: clanak.php?id=" . $new_id);
                exit;
            }
        }

        $message = "Vijest nije spremljena u bazu: " . mysqli_error($dbc);
    } else {
        $message = "Ispunite sva polja i odaberite sliku.";
    }
}

page_header("Unos vijesti", "unos");
?>

<main class="page-shell">
    <section class="news-section">
        <h1>Unos nove vijesti</h1>

        <?php if ($message !== ""): ?>
            <p class="form-message"><?php echo h($message); ?></p>
        <?php endif; ?>

        <form class="news-form" name="unos-vijesti" action="unos.php" method="POST" enctype="multipart/form-data" autocomplete="on">
            <div class="form-item">
                <label for="title">Naslov vijesti</label>
                <input class="form-field-textual" type="text" name="title" id="title" required autofocus>
            </div>

            <div class="form-item">
                <label for="about">Kratki sažetak</label>
                <textarea class="form-field-textual" name="about" id="about" rows="5" maxlength="180" required></textarea>
            </div>

            <div class="form-item">
                <label for="content">Sadržaj vijesti</label>
                <textarea class="form-field-textual" name="content" id="content" rows="10" required></textarea>
            </div>

            <div class="form-item">
                <label for="category">Kategorija</label>
                <select class="form-field-textual" name="category" id="category" required>
                    <option value="Sport">Sport</option>
                    <option value="Politik">Politik</option>
                </select>
            </div>

            <div class="form-item">
                <label for="pphoto">Odabir slike</label>
                <input class="input-text" type="file" name="pphoto" id="pphoto" accept="image/jpeg,image/png,image/gif,image/webp" required>
            </div>

            <div class="form-item checkbox-item">
                <input type="checkbox" name="archive" id="archive" value="1">
                <label for="archive">Spremiti u arhivu</label>
            </div>

            <div class="form-actions">
                <button type="reset">Poništi</button>
                <button type="submit">Spremi vijest</button>
            </div>
        </form>
    </section>
</main>

<?php
mysqli_close($dbc);
page_footer();
?>
