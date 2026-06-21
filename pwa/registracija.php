<?php
include 'connect.php';
include 'helpers.php';

$message = "";
$registered = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? "");
    $prezime = trim($_POST['prezime'] ?? "");
    $username = trim($_POST['username'] ?? "");
    $pass = $_POST['pass'] ?? "";
    $pass_rep = $_POST['passRep'] ?? "";
    $razina = 0;

    if ($ime === "" || $prezime === "" || $username === "" || $pass === "" || $pass_rep === "") {
        $message = "Ispunite sva polja.";
    } elseif ($pass !== $pass_rep) {
        $message = "Lozinke se ne podudaraju.";
    } else {
        $stmt = mysqli_prepare($dbc, "SELECT id FROM korisnik WHERE korisnicko_ime = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = "Korisničko ime već postoji.";
        } else {
            $hashed_password = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($dbc, "INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka, razina) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssi", $ime, $prezime, $username, $hashed_password, $razina);

            if (mysqli_stmt_execute($stmt)) {
                $registered = true;
                $message = "Korisnik je uspješno registriran. Sada se možete prijaviti.";
            } else {
                $message = "Registracija nije uspjela.";
            }
        }
    }
}

page_header("Registracija", "registracija");
?>

<main class="page-shell">
    <section class="news-section">
        <h1>Registracija korisnika</h1>

        <?php if ($message !== ""): ?>
            <p class="form-message"><?php echo h($message); ?></p>
        <?php endif; ?>

        <?php if ($registered): ?>
            <p class="empty-state"><a href="administrator.php">Prijavite se u administraciju</a>.</p>
        <?php else: ?>
            <form class="news-form" action="registracija.php" method="POST" id="registration-form" novalidate>
                <div class="form-item">
                    <span class="validation-message" id="porukaIme"></span>
                    <label for="ime">Ime</label>
                    <input class="form-field-textual" type="text" name="ime" id="ime" required>
                </div>

                <div class="form-item">
                    <span class="validation-message" id="porukaPrezime"></span>
                    <label for="prezime">Prezime</label>
                    <input class="form-field-textual" type="text" name="prezime" id="prezime" required>
                </div>

                <div class="form-item">
                    <span class="validation-message" id="porukaUsername"></span>
                    <label for="username">Korisničko ime</label>
                    <input class="form-field-textual" type="text" name="username" id="username" required>
                </div>

                <div class="form-item">
                    <span class="validation-message" id="porukaPass"></span>
                    <label for="pass">Lozinka</label>
                    <input class="form-field-textual" type="password" name="pass" id="pass" required>
                </div>

                <div class="form-item">
                    <span class="validation-message" id="porukaPassRep"></span>
                    <label for="passRep">Ponovite lozinku</label>
                    <input class="form-field-textual" type="password" name="passRep" id="passRep" required>
                </div>

                <div class="form-actions">
                    <button type="submit" id="slanje">Registriraj se</button>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<script>
document.getElementById("registration-form")?.addEventListener("submit", function(event) {
    var valid = true;

    function requireValue(id, messageId, message) {
        var field = document.getElementById(id);
        var note = document.getElementById(messageId);

        if (field.value.trim().length === 0) {
            valid = false;
            field.classList.add("field-error");
            note.textContent = message;
        } else {
            field.classList.remove("field-error");
            note.textContent = "";
        }
    }

    requireValue("ime", "porukaIme", "Unesite ime.");
    requireValue("prezime", "porukaPrezime", "Unesite prezime.");
    requireValue("username", "porukaUsername", "Unesite korisničko ime.");
    requireValue("pass", "porukaPass", "Unesite lozinku.");
    requireValue("passRep", "porukaPassRep", "Ponovite lozinku.");

    var pass = document.getElementById("pass");
    var passRep = document.getElementById("passRep");

    if (pass.value !== passRep.value) {
        valid = false;
        pass.classList.add("field-error");
        passRep.classList.add("field-error");
        document.getElementById("porukaPass").textContent = "Lozinke nisu iste.";
        document.getElementById("porukaPassRep").textContent = "Lozinke nisu iste.";
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
