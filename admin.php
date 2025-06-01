<?php
session_start();
$db_path = "C:/xampp/htdocs/Blogum/db/blogum.db";

try {
    $conn = new PDO("sqlite:$db_path");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['loggedin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Geçersiz kullanıcı adı veya şifre.";
        }
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_faq'])) {
            $stmt = $conn->prepare("INSERT INTO faq (question, answer) VALUES (?, ?)");
            $stmt->execute([$_POST['question'], $_POST['answer']]);
            $success = "SSS eklendi.";
        } elseif (isset($_POST['edit_faq'])) {
            $stmt = $conn->prepare("UPDATE faq SET question = ?, answer = ? WHERE id = ?");
            $stmt->execute([$_POST['question'], $_POST['answer'], $_POST['id']]);
            $success = "SSS güncellendi.";
        } elseif (isset($_POST['delete_faq'])) {
            $stmt = $conn->prepare("DELETE FROM faq WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "SSS silindi.";
        } elseif (isset($_POST['add_post'])) {
            $stmt = $conn->prepare("INSERT INTO posts (subpage_id, title, content) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['subpage_id'], $_POST['title'], $_POST['content']]);
            $success = "Blog yazısı eklendi.";
        } elseif (isset($_POST['edit_post'])) {
            $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['content'], $_POST['id']]);
            $success = "Blog yazısı güncellendi.";
        } elseif (isset($_POST['delete_post'])) {
            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Blog yazısı silindi.";
        } elseif (isset($_POST['add_social'])) {
            $stmt = $conn->prepare("INSERT INTO social_media (platform, url) VALUES (?, ?)");
            $stmt->execute([$_POST['platform'], $_POST['url']]);
            $success = "Sosyal medya linki eklendi.";
        } elseif (isset($_POST['edit_social'])) {
            $stmt = $conn->prepare("UPDATE social_media SET platform = ?, url = ? WHERE id = ?");
            $stmt->execute([$_POST['platform'], $_POST['url'], $_POST['id']]);
            $success = "Sosyal medya linki güncellendi.";
        } elseif (isset($_POST['delete_social'])) {
            $stmt = $conn->prepare("DELETE FROM social_media WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Sosyal medya linki silindi.";
        } elseif (isset($_POST['update_subpage'])) {
            $stmt = $conn->prepare("UPDATE subpages SET content = ? WHERE id = ?");
            $stmt->execute([$_POST['content'], $_POST['id']]);
            $success = "Alt sayfa güncellendi.";
        } elseif (isset($_POST['add_gallery'])) {
            $target_dir = "images/";
            $file = $_FILES['file'];
            $file_name = basename($file['name']);
            $target_file = $target_dir . time() . "_" . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'mp4'];
            $type = in_array($file_type, ['mp4']) ? 'video' : 'photo';

            if (in_array($file_type, $allowed_types) && $file['size'] < 5000000) {
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO gallery (subpage_id, title, file_path, type) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_POST['subpage_id'], $_POST['title'], $target_file, $type]);
                    $success = "Galeri öğesi eklendi.";
                } else {
                    $error = "Dosya yüklenemedi.";
                }
            } else {
                $error = "Geçersiz dosya türü veya boyutu.";
            }
        } elseif (isset($_POST['delete_gallery'])) {
            $stmt = $conn->prepare("SELECT file_path FROM gallery WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && file_exists($row['file_path'])) {
                unlink($row['file_path']);
            }
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Galeri öğesi silindi.";
        } elseif (isset($_POST['add_hero_bg'])) {
            $target_dir = "images/";
            $file = $_FILES['file'];
            $file_name = basename($file['name']);
            $target_file = $target_dir . time() . "_" . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png'];

            if (in_array($file_type, $allowed_types) && $file['size'] < 5000000) {
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO hero_backgrounds (file_path) VALUES (?)");
                    $stmt->execute([$target_file]);
                    $success = "Hero arka planı eklendi.";
                } else {
                    $error = "Dosya yüklenemedi.";
                }
            } else {
                $error = "Geçersiz dosya türü veya boyutu.";
            }
        } elseif (isset($_POST['delete_hero_bg'])) {
            $stmt = $conn->prepare("SELECT file_path FROM hero_backgrounds WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && file_exists($row['file_path'])) {
                unlink($row['file_path']);
            }
            $stmt = $conn->prepare("DELETE FROM hero_backgrounds WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Hero arka planı silindi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=8">
</head>
<body>
    <header class="sticky">
        <nav>
            <div class="logo">Yönetim Paneli</div>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                <a href="?logout=1" class="cta-button">Çıkış Yap</a>
            <?php } ?>
        </nav>
    </header>

    <main>
        <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { ?>
            <section class="content-section animate">
                <h2 class="animate-stagger">Giriş Yap</h2>
                <?php if (isset($error)) echo "<p class='error animate-stagger'>$error</p>"; ?>
                <form method="post" class="contact-form">
                    <input type="hidden" name="login" value="1">
                    <label class="animate-stagger">Kullanıcı Adı:</label><input type="text" name="username" required class="animate-stagger">
                    <label class="animate-stagger">Şifre:</label><input type="password" name="password" required class="animate-stagger">
                    <button type="submit" class="cta-button animate-stagger">Giriş Yap</button>
                </form>
            </section>
        <?php } else { ?>
            <?php if (isset($success)) echo "<p class='success animate-stagger'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='error animate-stagger'>$error</p>"; ?>
            <section class="content-section animate">
                <h2 class="animate-stagger">Hero Arka Planları</h2>
                <form method="post" class="admin-form animate-stagger" enctype="multipart/form-data">
                    <h3>Yeni Arka Plan</h3>
                    <label>Dosya (JPG, PNG):</label><input type="file" name="file" required accept=".jpg,.jpeg,.png">
                    <button type="submit" name="add_hero_bg" class="cta-button">Yükle</button>
                </form>
                <h3>Mevcut Arka Planlar</h3>
                <div class="gallery-grid">
                    <?php
                    $stmt = $conn->query("SELECT * FROM hero_backgrounds");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='gallery-item animate-stagger'>
                            <img src='{$row['file_path']}' alt='Hero Background' width='200'>
                            <form method='post' class='admin-form'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit' name='delete_hero_bg' class='cta-button delete'>Sil</button>
                            </form>
                        </div>";
                    }
                    ?>
                </div>
            </section>
            <section class="content-section animate">
                <h2 class="animate-stagger">Alt Sayfaları Düzenle</h2>
                <?php
                $stmt = $conn->query("SELECT s.*, p.slug as page_slug FROM subpages s JOIN pages p ON s.page_id = p.id");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<form method='post' class='admin-form animate-stagger'>
                        <h3>{$row['title']} ({$row['page_slug']})</h3>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <textarea name='content'>{$row['content']}</textarea>
                        <button type='submit' name='update_subpage' class='cta-button'>Güncelle</button>
                    </form>";
                }
                ?>
            </section>
            <section class="content-section animate">
                <h2 class="animate-stagger">Blog Yazıları</h2>
                <form method="post" class="admin-form animate-stagger">
                    <h3>Yeni Blog Yazısı</h3>
                    <label>Alt Sayfa:</label>
                    <select name="subpage_id">
                        <?php
                        $stmt = $conn->query("SELECT * FROM subpages WHERE page_id = (SELECT id FROM pages WHERE slug='blog')");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['title']}</option>";
                        }
                        ?>
                    </select>
                    <label>Başlık:</label><input type="text" name="title" required>
                    <label>İçerik:</label><textarea name="content" required></textarea>
                    <button type="submit" name="add_post" class="cta-button">Ekle</button>
                </form>
                <h3>Mevcut Yazılar</h3>
                <?php
                $stmt = $conn->query("SELECT p.*, s.title as subpage_title FROM posts p JOIN subpages s ON p.subpage_id = s.id");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<form method='post' class='admin-form animate-stagger'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <label>Başlık:</label><input type='text' name='title' value='{$row['title']}' required>
                        <label>İçerik:</label><textarea name='content' required>{$row['content']}</textarea>
                        <button type='submit' name='edit_post' class='cta-button'>Düzenle</button>
                        <button type='submit' name='delete_post' class='cta-button delete'>Sil</button>
                    </form>";
                }
                ?>
            </section>
            <section class="content-section animate">
                <h2 class="animate-stagger">Galeri</h2>
                <form method="post" class="admin-form animate-stagger" enctype="multipart/form-data">
                    <h3>Yeni Görsel/Video</h3>
                    <label>Alt Sayfa:</label>
                    <select name="subpage_id">
                        <?php
                        $stmt = $conn->query("SELECT * FROM subpages WHERE page_id = (SELECT id FROM pages WHERE slug='gallery')");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['title']}</option>";
                        }
                        ?>
                    </select>
                    <label>Başlık:</label><input type="text" name="title" required>
                    <label>Dosya (JPG, PNG, MP4):</label><input type="file" name="file" required accept=".jpg,.jpeg,.png,.mp4">
                    <button type="submit" name="add_gallery" class="cta-button">Yükle</button>
                </form>
                <h3>Mevcut Görseller/Videolar</h3>
                <div class="gallery-grid">
                    <?php
                    $stmt = $conn->query("SELECT g.*, s.title as subpage_title FROM gallery g JOIN subpages s ON g.subpage_id = s.id");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='gallery-item animate-stagger'>
                            <h3>{$row['title']} ({$row['subpage_title']})</h3>";
                        if ($row['type'] === 'video') {
                            echo "<video src='{$row['file_path']}' controls width='200'></video>";
                        } else {
                            echo "<img src='{$row['file_path']}' alt='{$row['title']}' width='200'>";
                        }
                        echo "<form method='post' class='admin-form'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete_gallery' class='cta-button delete'>Sil</button>
                        </form></div>";
                    }
                    ?>
                </div>
            </section>
            <section class="content-section animate">
                <h2 class="animate-stagger">Sıkça Sorulan Sorular</h2>
                <form method="post" class="admin-form animate-stagger">
                    <h3>Yeni SSS</h3>
                    <label>Soru:</label><input type="text" name="question" required>
                    <label>Cevap:</label><textarea name="answer" required></textarea>
                    <button type="submit" name="add_faq" class="cta-button">Ekle</button>
                </form>
                <h3>Mevcut SSS</h3>
                <?php
                $stmt = $conn->query("SELECT * FROM faq");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<form method='post' class='admin-form animate-stagger'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <label>Soru:</label><input type='text' name='question' value='{$row['question']}' required>
                        <label>Cevap:</label><textarea name='answer' required>{$row['answer']}</textarea>
                        <button type='submit' name='edit_faq' class='cta-button'>Düzenle</button>
                        <button type='submit' name='delete_faq' class='cta-button delete'>Sil</button>
                    </form>";
                }
                ?>
            </section>
            <section class="content-section animate">
                <h2 class="animate-stagger">Sosyal Medya Linkleri</h2>
                <form method="post" class="admin-form animate-stagger">
                    <h3>Yeni Link</h3>
                    <label>Platform:</label><input type="text" name="platform" required>
                    <label>URL:</label><input type="text" name="url" required>
                    <button type="submit" name="add_social" class="cta-button">Ekle</button>
                </form>
                <h3>Mevcut Linkler</h3>
                <?php
                $stmt = $conn->query("SELECT * FROM social_media");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<form method='post' class='admin-form animate-stagger'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <label>Platform:</label><input type='text' name='platform' value='{$row['platform']}' required>
                        <label>URL:</label><input type='text' name='url' value='{$row['url']}' required>
                        <button type='submit' name='edit_social' class='cta-button'>Düzenle</button>
                        <button type='submit' name='delete_social' class='cta-button delete'>Sil</button>
                    </form>";
                }
                ?>
            </section>
        <?php } ?>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> Blogum Yönetim. Tüm hakları saklıdır.</p>
    </footer>

    <script src="js/script.js?v=8"></script>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
}
$conn = null;
?>