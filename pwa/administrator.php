<?php
session_start();
include 'connect.php';
include 'helpers.php';

$message = "";
$login_message = "";
$successful_login = false;
$is_admin = isset($_SESSION['username']) && (int)($_SESSION['level'] ?? 0) === 1;
$is_user = isset($_SESSION['username']) && (int)($_SESSION['level'] ?? 0) === 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prijava'])) {
    $username = trim($_POST['username'] ?? "");
    $password = $_POST['lozinka'] ?? "";

    $stmt = mysqli_prepare($dbc, "SELECT korisnicko_ime, lozinka, razina FROM korisnik WHERE korisnicko_ime = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $db_username, $db_password, $db_level);
    mysqli_stmt_fetch($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0 && password_verify($password, $db_password)) {
        $successful_login = true;
        $_SESSION['username'] = $db_username;
        $_SESSION['level'] = (int)$db_level;
        $is_admin = (int)$db_level === 1;
        $is_user = (int)$db_level === 0;
    } else {
        $login_message = 'Neispravni podaci. Prvo se registrirajte ili provjerite korisničko ime i lozinku.';
    }
}

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($dbc, "DELETE FROM vijesti WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $message = "Vijest je izbrisana.";
}

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? "");
    $about = trim($_POST['about'] ?? "");
    $content = trim($_POST['content'] ?? "");
    $category = trim($_POST['category'] ?? "");
    $archive = isset($_POST['archive']) ? 1 : 0;
    $current_image = trim($_POST['current_image'] ?? "");
    $image = upload_image('pphoto', $current_image);

    $stmt = mysqli_prepare($dbc, "UPDATE vijesti SET naslov = ?, sazetak = ?, tekst = ?, slika = ?, kategorija = ?, arhiva = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssssii", $title, $about, $content, $image, $category, $archive, $id);
    mysqli_stmt_execute($stmt);
    $message = "Vijest je ažurirana.";
}

page_header("Administracija", "admin");
?>

<main class="page-shell">
    <section class="news-section admin-section">
        <h1>Administracija vijesti</h1>

        <?php if ($message !== ""): ?>
            <p class="form-message"><?php echo h($message); ?></p>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <div class="admin-toolbar">
                <p>Prijavljeni ste kao <?php echo h($_SESSION['username']); ?>.</p>
                <a href="odjava.php">Odjava</a>
            </div>

            <?php
            $query = "SELECT * FROM vijesti ORDER BY id DESC";
            $result = mysqli_query($dbc, $query);

            if (mysqli_num_rows($result) === 0) {
                echo '<p class="empty-state">Još nema spremljenih vijesti.</p>';
            }

            while ($row = mysqli_fetch_array($result)):
            ?>
                <form class="news-form admin-form" enctype="multipart/form-data" action="administrator.php" method="POST">
                    <div class="admin-form-header">
                        <strong>#<?php echo (int)$row['id']; ?></strong>
                        <span><?php echo $row['arhiva'] ? 'Arhivirano' : 'Objavljeno'; ?></span>
                    </div>

                    <div class="form-item">
                        <label for="title-<?php echo (int)$row['id']; ?>">Naslov vijesti</label>
                        <input class="form-field-textual" type="text" name="title" id="title-<?php echo (int)$row['id']; ?>" value="<?php echo h($row['naslov']); ?>" required>
                    </div>

                    <div class="form-item">
                        <label for="about-<?php echo (int)$row['id']; ?>">Kratki sažetak</label>
                        <textarea class="form-field-textual" name="about" id="about-<?php echo (int)$row['id']; ?>" rows="4" required><?php echo h($row['sazetak']); ?></textarea>
                    </div>

                    <div class="form-item">
                        <label for="content-<?php echo (int)$row['id']; ?>">Sadržaj vijesti</label>
                        <textarea class="form-field-textual" name="content" id="content-<?php echo (int)$row['id']; ?>" rows="8" required><?php echo h($row['tekst']); ?></textarea>
                    </div>

                    <div class="form-item">
                        <label for="category-<?php echo (int)$row['id']; ?>">Kategorija</label>
                        <select class="form-field-textual" name="category" id="category-<?php echo (int)$row['id']; ?>" required>
                            <option value="Sport" <?php echo $row['kategorija'] === 'Sport' ? 'selected' : ''; ?>>Sport</option>
                            <option value="Politik" <?php echo $row['kategorija'] === 'Politik' ? 'selected' : ''; ?>>Politik</option>
                        </select>
                    </div>

                    <div class="form-item">
                        <label for="pphoto-<?php echo (int)$row['id']; ?>">Slika</label>
                        <div class="admin-image-row">
                            <img src="<?php echo UPLPATH . h($row['slika']); ?>" alt="<?php echo h($row['naslov']); ?>">
                            <input class="input-text" type="file" name="pphoto" id="pphoto-<?php echo (int)$row['id']; ?>" accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                    </div>

                    <div class="form-item checkbox-item">
                        <input type="checkbox" name="archive" id="archive-<?php echo (int)$row['id']; ?>" value="1" <?php echo $row['arhiva'] ? 'checked' : ''; ?>>
                        <label for="archive-<?php echo (int)$row['id']; ?>">Spremiti u arhivu</label>
                    </div>

                    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                    <input type="hidden" name="current_image" value="<?php echo h($row['slika']); ?>">

                    <div class="form-actions">
                        <button type="submit" name="update" value="1">Izmijeni</button>
                        <button class="danger-button" type="submit" name="delete" value="1">Izbriši</button>
                    </div>
                </form>
            <?php endwhile; ?>
        <?php elseif ($is_user): ?>
            <p class="form-message">Bok <?php echo h($_SESSION['username']); ?>! Uspješno ste prijavljeni, ali nemate administratorska prava.</p>
            <p class="empty-state"><a href="odjava.php">Odjava</a></p>
        <?php else: ?>
            <?php if ($successful_login === false && $login_message !== ""): ?>
                <p class="form-message"><?php echo h($login_message); ?> <a href="registracija.php">Registracija</a></p>
            <?php endif; ?>

            <form class="news-form auth-form" action="administrator.php" method="POST" id="login-form" novalidate>
                <div class="form-item">
                    <span class="validation-message" id="porukaUsername"></span>
                    <label for="username">Korisničko ime</label>
                    <input class="form-field-textual" type="text" name="username" id="username" required autofocus>
                </div>

                <div class="form-item">
                    <span class="validation-message" id="porukaLozinka"></span>
                    <label for="lozinka">Lozinka</label>
                    <input class="form-field-textual" type="password" name="lozinka" id="lozinka" required>
                </div>

                <div class="form-actions">
                    <a class="text-button" href="registracija.php">Registracija</a>
                    <button type="submit" name="prijava" value="1">Prijava</button>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<script>
document.getElementById("login-form")?.addEventListener("submit", function(event) {
    var valid = true;
    var username = document.getElementById("username");
    var password = document.getElementById("lozinka");

    if (username.value.trim().length === 0) {
        valid = false;
        username.classList.add("field-error");
        document.getElementById("porukaUsername").textContent = "Unesite korisničko ime.";
    }

    if (password.value.trim().length === 0) {
        valid = false;
        password.classList.add("field-error");
        document.getElementById("porukaLozinka").textContent = "Unesite lozinku.";
    }

    if (!valid) {
        event.preventDefault();
    }
});
</script>

<?php
mysqli_close($dbc);
page_footer();
?>
