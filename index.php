<?php
$db_path = "C:/xampp/htdocs/Blogum/db/blogum.db";

try {
    $conn = new PDO("sqlite:$db_path");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'contact') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO contact (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
    $success = "Mesajınız gönderildi!";
}

// Hero arka planlarını çek
$bg_images = [];
$stmt = $conn->query("SELECT file_path FROM hero_backgrounds");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $bg_images[] = $row['file_path'];
}
$random_bg = !empty($bg_images) ? $bg_images[array_rand($bg_images)] : 'images/default-bg.jpg';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kişisel blog ve portföy sitem, hikayelerim ve projelerimle tanışın.">
    <meta property="og:title" content="Blogum">
    <meta property="og:image" content="images/og-image.jpg">
    <title>Blogum - Kendimi Tanıtıyorum</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=9">
    <style>
        .hero::before {
            background-image: url('<?php echo $random_bg; ?>');
        }
    </style>
</head>
<body>
    <header class="sticky">
        <nav>
            <div class="logo">Blogum</div>
            <ul class="nav-menu">
                <?php
                $stmt = $conn->query("SELECT * FROM pages");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li><a href='?page={$row['slug']}'>{$row['title']}</a>";
                    if ($row['slug'] === 'about' || $row['slug'] === 'blog' || $row['slug'] === 'gallery') {
                        echo "<ul class='submenu'>";
                        $sub_stmt = $conn->prepare("SELECT * FROM subpages WHERE page_id = ?");
                        $sub_stmt->execute([$row['id']]);
                        while ($sub_row = $sub_stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<li><a href='?page={$row['slug']}&subpage={$sub_row['slug']}'>{$sub_row['title']}</a></li>";
                        }
                        echo "</ul>";
                    }
                    echo "</li>";
                }
                ?>
            </ul>
            <div class="hamburger">☰</div>
        </nav>
    </header>

    <main>
        <?php if ($page === 'home') { ?>
            <section class="hero" id="hero">
                <div class="hero-content">
                    <h1 class="animate-stagger">Hoş Geldiniz!</h1>
                    <p class="animate-stagger">Ben Emre, burada hikayemi, düşüncelerimi ve deneyimlerimi paylaşıyorum.</p>
                    <a href="?page=about&subpage=biography" class="cta-button animate-stagger">Hakkımda Daha Fazla</a>
                </div>
                <div class="scroll-down" id="scroll-down">↓</div>
            </section>
            <?php
            $stmt = $conn->query("SELECT * FROM subpages WHERE page_id = (SELECT id FROM pages WHERE slug='home')");
            $first_section = true;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $section_id = $first_section ? 'first-section' : '';
                // Güvenli HTML ve yeni satırları işle
                $content = nl2br(htmlspecialchars_decode($row['content']));
                echo "<section class='content-section animate' id='$section_id'><h2 class='animate-stagger'>{$row['title']}</h2><div class='content-text animate-stagger'>{$content}</div></section>";
                $first_section = false;
            }
            $stmt = $conn->query("SELECT * FROM posts ORDER BY RANDOM() LIMIT 3");
            echo "<section class='content-section animate' id='blog-section'><h2 class='animate-stagger'>Rastgele Blog Yazıları</h2><div class='blog-grid'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Blog özetinde de yeni satırları işle
                $content = nl2br(htmlspecialchars_decode(substr($row['content'], 0, 100))) . "...";
                echo "<article class='blog-card animate-stagger'><h3>{$row['title']}</h3><div class='content-text'>{$content}</div><a href='?page=blog&subpage=personal'>Devamını Oku</a></article>";
            }
            echo "</div></section>";
        } elseif ($page === 'faq') { ?>
            <section class="content-section animate" id="first-section">
                <h2 class="animate-stagger">Sıkça Sorulan Sorular</h2>
                <?php
                $stmt = $conn->query("SELECT * FROM faq");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // SSS cevaplarında yeni satırları işle
                    $answer = nl2br(htmlspecialchars_decode($row['answer']));
                    echo "<div class='faq-item animate-stagger'><h3>{$row['question']}</h3><div class='content-text'>{$answer}</div></div>";
                }
                ?>
            </section>
        <?php } elseif ($page === 'contact') { ?>
            <section class="content-section animate" id="first-section">
                <h2 class="animate-stagger">İletişim</h2>
                <?php if (isset($success)) echo "<p class='success animate-stagger'>$success</p>"; ?>
                <form method="post" class="contact-form">
                    <label class="animate-stagger">Ad Soyad:</label><input type="text" name="name" required class="animate-stagger">
                    <label class="animate-stagger">E-posta:</label><input type="email" name="email" required class="animate-stagger">
                    <label class="animate-stagger">Mesaj:</label><textarea name="message" required class="animate-stagger"></textarea>
                    <button type="submit" class="cta-button animate-stagger">Gönder</button>
                </form>
                <h3 class="animate-stagger">Sosyal Medya</h3>
                <ul class="social-links">
                    <?php
                    $stmt = $conn->query("SELECT * FROM social_media");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<li class='animate-stagger'><a href='{$row['url']}' target='_blank'>{$row['platform']}</a></li>";
                    }
                    ?>
                </ul>
            </section>
        <?php } elseif ($subpage) {
            $stmt = $conn->prepare("SELECT * FROM subpages WHERE slug = ?");
            $stmt->execute([$subpage]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) { ?>
                <section class="content-section animate" id="first-section">
                    <h2 class="animate-stagger"><?php echo $row['title']; ?></h2>
                    <?php
                    // Alt sayfa içeriğinde yeni satırları ve HTML'yi işle
                    $content = nl2br(htmlspecialchars_decode($row['content']));
                    echo "<div class='content-text animate-stagger'>{$content}</div>";
                    ?>
                    <?php
                    if ($page === 'blog') {
                        $stmt_posts = $conn->prepare("SELECT * FROM posts WHERE subpage_id = ?");
                        $stmt_posts->execute([$row['id']]);
                        echo "<div class='blog-grid'>";
                        while ($post = $stmt_posts->fetch(PDO::FETCH_ASSOC)) {
                            // Blog yazılarında yeni satırları ve HTML'yi işle
                            $post_content = nl2br(htmlspecialchars_decode($post['content']));
                            echo "<article class='blog-card animate-stagger'><h3>{$post['title']}</h3><div class='content-text'>{$post_content}</div></article>";
                        }
                        echo "</div>";
                    } elseif ($page === 'gallery') {
                        $stmt_gallery = $conn->prepare("SELECT * FROM gallery WHERE subpage_id = ?");
                        $stmt_gallery->execute([$row['id']]);
                        echo "<div class='gallery-controls'><button class='gallery-prev'>←</button><div class='gallery-grid'>";
                        while ($item = $stmt_gallery->fetch(PDO::FETCH_ASSOC)) {
                            echo "<div class='gallery-item animate-stagger'>";
                            if ($item['type'] === 'video') {
                                echo "<video src='{$item['file_path']}' controls loading='lazy' data-src='{$item['file_path']}' draggable='false'></video>";
                            } else {
                                echo "<img src='{$item['file_path']}' alt='{$item['title']}' loading='lazy' data-src='{$item['file_path']}' draggable='false'>";
                            }
                            echo "<h3>{$item['title']}</h3></div>";
                        }
                        echo "</div><button class='gallery-next'>→</button></div>";
                    }
                    ?>
                </section>
            <?php }
        } ?>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> Blogum. Tüm hakları saklıdır.</p>
    </footer>

    <script src="js/script.js?v=9"></script>
</body>
</html>
<?php $conn = null; ?>