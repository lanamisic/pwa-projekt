<?php
header('Content-Type: text/html; charset=utf-8');

$basename = "pwa";
mysqli_report(MYSQLI_REPORT_OFF);

$connections = [
    ["127.0.0.1", "root", "root", 8889],
    ["localhost", "root", "root", 8889],
    ["127.0.0.1", "root", "", 3306],
    ["localhost", "root", "", 3306],
];

$dbc = false;
$last_error = "";

foreach ($connections as $connection) {
    $dbc = @mysqli_connect($connection[0], $connection[1], $connection[2], "", $connection[3]);

    if ($dbc) {
        break;
    }

    $last_error = mysqli_connect_error();
}

if (!$dbc) {
    die("Greška pri spajanju na bazu: " . $last_error);
}

mysqli_set_charset($dbc, "utf8mb4");

mysqli_query($dbc, "CREATE DATABASE IF NOT EXISTS $basename CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

if (!mysqli_select_db($dbc, $basename)) {
    die("Baza pwa nije dostupna.");
}

$create_table = "CREATE TABLE IF NOT EXISTS vijesti (
    id INT NOT NULL AUTO_INCREMENT,
    datum DATE NOT NULL,
    naslov VARCHAR(255) NOT NULL,
    sazetak TEXT NOT NULL,
    tekst TEXT NOT NULL,
    slika VARCHAR(255) NOT NULL,
    kategorija VARCHAR(50) NOT NULL,
    arhiva TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (!mysqli_query($dbc, $create_table)) {
    die("Tablica vijesti nije dostupna: " . mysqli_error($dbc));
}

$create_users = "CREATE TABLE IF NOT EXISTS korisnik (
    id INT NOT NULL AUTO_INCREMENT,
    ime VARCHAR(100) NOT NULL,
    prezime VARCHAR(100) NOT NULL,
    korisnicko_ime VARCHAR(100) NOT NULL,
    lozinka VARCHAR(255) NOT NULL,
    razina INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY korisnicko_ime (korisnicko_ime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (!mysqli_query($dbc, $create_users)) {
    die("Tablica korisnik nije dostupna: " . mysqli_error($dbc));
}

$default_news = [
    [
        "datum" => "2019-05-17",
        "naslov" => "Dortmund und Bayern üben sich in Psychospielchen",
        "sazetak" => "Im Fernduell mit den Münchnern um die Meisterschaft redet sich der BVB stark. Er beansprucht die Position des Teams, das alles zu gewinnen hat.",
        "tekst" => "Im Fernduell mit den Münchnern um die Meisterschaft redet sich der BVB stark. Die Psychospielchen zwischen den beiden führenden deutschen Klubs werden mit deutlichen Worten ausgetragen. Hans-Joachim Watzke erinnert daran, dass sein Team am letzten Spieltag nichts zu verlieren, dafür aber alles zu gewinnen hat.",
        "slika" => "sport-dortmund.jpg",
        "kategorija" => "Sport",
        "arhiva" => 0,
    ],
    [
        "datum" => "2019-05-17",
        "naslov" => "Cacau hält mildes Urteil nach Rassismus-Eklat für „sehr bitter“",
        "sazetak" => "Drei Männer haben Nationalspieler bei einem Länderspiel rassistisch beleidigt. Der DFB-Integrationsbeauftragte Cacau reagierte enttäuscht.",
        "tekst" => "Nach einem rassistischen Vorfall bei einem Länderspiel äußerte sich DFB-Integrationsbeauftragter Cacau enttäuscht über die aus seiner Sicht milden Strafen. Der Fall hat die Debatte über Verantwortung, den Schutz von Spielern und deutlichere Reaktionen der Sportinstitutionen erneut entfacht.",
        "slika" => "sport-cacau.jpg",
        "kategorija" => "Sport",
        "arhiva" => 0,
    ],
    [
        "datum" => "2019-05-17",
        "naslov" => "Max Kruse verlässt Werder Bremen",
        "sazetak" => "Mannschaftskapitän Max Kruse verlässt nach der Saison Werder Bremen. Der Verein bestätigte die Nachricht am Freitag.",
        "tekst" => "Mannschaftskapitän Max Kruse wird Werder Bremen nach der Saison verlassen. Der Vertrag des ehemaligen Nationalspielers läuft am 30. Juni aus, und der Verein hatte bis zuletzt auf eine Verlängerung gehofft. Der Abschied des Kapitäns stellt eine bedeutende Veränderung für die Mannschaft dar.",
        "slika" => "sport-kruse.jpg",
        "kategorija" => "Sport",
        "arhiva" => 0,
    ],
    [
        "datum" => "2019-05-17",
        "naslov" => "USA heben Zölle gegen Mexiko und Kanada auf",
        "sazetak" => "US-Präsident Donald Trump kündigte die Aufhebung der Zölle an und rief den Kongress auf, einen neuen Handelspakt zu billigen.",
        "tekst" => "Die Entscheidung betrifft die Beziehungen zwischen den USA, Mexiko und Kanada und wurde als Schritt hin zu einer stabileren Handelszusammenarbeit vorgestellt. Die Regierung erwartet, dass das neue Abkommen den bisherigen Rahmen des Nafta-Abkommens ersetzen wird.",
        "slika" => "politik-trump.jpg",
        "kategorija" => "Politik",
        "arhiva" => 0,
    ],
    [
        "datum" => "2019-05-17",
        "naslov" => "Zahlreiche EU-Diplomaten sind sauer auf Rumänien",
        "sazetak" => "Rumänien führt derzeit den EU-Vorsitz, stößt dabei jedoch auf Kritik aus diplomatischen Kreisen.",
        "tekst" => "Rumänien hat derzeit den Vorsitz der Europäischen Union inne, doch einige Diplomaten kritisieren die Art und Weise, wie das Land die Treffen der Finanzminister gestaltet. Die Kritik richtet sich vor allem gegen die gesetzten Prioritäten und die politischen Botschaften, die die europäischen Diskussionen begleiten.",
        "slika" => "politik-romania.jpg",
        "kategorija" => "Politik",
        "arhiva" => 0,
    ],
    [
        "datum" => "2019-05-17",
        "naslov" => "Labour erklärt Brexit-Gespräche mit Regierung für gescheitert",
        "sazetak" => "Wochenlang wurde verhandelt, doch am Ende konnte keine Einigung erzielt werden.",
        "tekst" => "Nach mehrtägigen Verhandlungen erklärte die Labour-Partei die Gespräche mit der Regierung über den Brexit für gescheitert. Für Theresa May erschwert dies zusätzlich den Versuch, die notwendige Unterstützung im Parlament für das Austrittsabkommen mit der Europäischen Union zu erhalten.",
        "slika" => "politik-brexit.jpg",
        "kategorija" => "Politik",
        "arhiva" => 0,
    ],
];
foreach ($default_news as $news) {
    $stmt = mysqli_prepare($dbc, "SELECT id FROM vijesti WHERE naslov = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $news["naslov"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 0) {
        $insert = mysqli_prepare($dbc, "INSERT INTO vijesti (datum, naslov, sazetak, tekst, slika, kategorija, arhiva) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insert, "ssssssi", $news["datum"], $news["naslov"], $news["sazetak"], $news["tekst"], $news["slika"], $news["kategorija"], $news["arhiva"]);
        mysqli_stmt_execute($insert);
    }
}
?>
