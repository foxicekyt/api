<?php
session_start();

// 1. Kontrola, či je používateľ prihlásený (ak nie, pošleme ho na login)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit();
}

// 2. Pripojenie k databáze
$conn = new mysqli("localhost", "uzivatel", "heslo", "smartydev_db");

if ($conn->connect_error) {
    die("Chyba pripojenia: " . $conn->connect_error);
}

// 3. Získanie serverov prihláseného používateľa
$u_id = $_SESSION['user_id'];
$u_name = $_SESSION['user_name'];
$u_avatar = $_SESSION['user_avatar']; // Ak ukladáš z Google/GitHubu

$sql = "SELECT * FROM servers WHERE user_id = '$u_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartyDev | Administrácia</title>
    <style>
        :root {
            --bg: #0a0b10;
            --card: #141621;
            --primary: #3b82f6;
            --text: #ffffff;
            --text-dim: #94a3b8;
            --border: #2d324a;
            --success: #10b981;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        /* Navigácia */
        .navbar {
            background: var(--card);
            padding: 1rem 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid var(--primary);
        }

        /* Hlavný obsah */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* Mriežka a Karty */
        .server-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: 0.3s;
        }

        .card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .status-badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            font-weight: bold;
        }

        .btn {
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            border: none;
        }

        .btn-manage { background: var(--primary); color: white; width: 100%; text-align: center; }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            background: var(--card);
            border-radius: 15px;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div style="font-size: 20px; font-weight: 800;">Smarty<span style="color:var(--primary)">Dev</span></div>
        <div class="user-profile">
            <span><?php echo htmlspecialchars($u_name); ?></span>
            <?php if($u_avatar): ?>
                <img src="<?php echo $u_avatar; ?>" alt="Avatar">
            <?php endif; ?>
            <a href="logout.php" style="color: #ef4444; text-decoration: none; font-size: 14px; margin-left: 10px;">Odhlásiť</a>
        </div>
    </nav>

    <div class="container">
        <div class="header-flex">
            <h1>Moje Minecraft servery</h1>
            <a href="create_server.php" class="btn" style="background: white; color: black;">+ Nový server</a>
        </div>

        <div class="server-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                            <div>
                                <h3 style="margin: 0;"><?php echo htmlspecialchars($row['server_name']); ?></h3>
                                <small style="color: var(--text-dim); font-family: monospace;">
                                    <?php echo $row['ip_address'] . ":" . $row['port']; ?>
                                </small>
                            </div>
                            <span class="status-badge"><?php echo strtoupper($row['status']); ?></span>
                        </div>

                        <div style="display: flex; gap: 20px; margin: 20px 0; font-size: 14px;">
                            <div>
                                <div style="color: var(--text-dim); font-size: 11px; text-transform: uppercase;">RAM</div>
                                <strong><?php echo $row['ram']; ?></strong>
                            </div>
                            <div>
                                <div style="color: var(--text-dim); font-size: 11px; text-transform: uppercase;">Typ</div>
                                <strong>Paper MC</strong>
                            </div>
                        </div>

                        <a href="manage.php?id=<?php echo $row['id']; ?>" class="btn btn-manage">Otvoriť konzolu</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Zatiaľ tu nemáte žiadne servery</h3>
                    <p style="color: var(--text-dim);">Vytvorte si svoj prvý server a začnite hrať!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>