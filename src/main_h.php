<?php
// Database connection
$host = 'mysql';
$db   = 'devdb';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

//  Auto-create 'users' table if it doesn't exist
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  surname VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_parent BOOLEAN DEFAULT FALSE
);
");

//  Auto-create 'user_parents' table if it doesn't exist
$pdo->exec("
CREATE TABLE IF NOT EXISTS user_parents (
  child_id INT NOT NULL,
  parent_id INT NOT NULL,
  PRIMARY KEY (child_id, parent_id),
  FOREIGN KEY (child_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE
);
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $is_parent = isset($_POST['is_parent']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO users (name, surname, is_parent) VALUES (?, ?, ?)");
    $stmt->execute([$name, $surname, $is_parent]);

    $parentId = $pdo->lastInsertId();

    // Assign children if selected
    if ($is_parent && !empty($_POST['selected_children'])) {
        $children = json_decode($_POST['selected_children'], true);
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO user_parents (child_id, parent_id) VALUES (?, ?)");
        foreach ($children as $childId) {
            $insertStmt->execute([$childId, $parentId]);
        }
    }


    header("Location: main.php");
    exit;
}

// Fetch users for selection (non-parents only)
$allUsers = $pdo->query("SELECT id, name, surname FROM users WHERE is_parent = 0")->fetchAll();
$users = $pdo->query("
    SELECT 
        u.id, 
        u.name, 
        u.surname, 
        u.is_parent, 
        u.created_at,
        GROUP_CONCAT(CONCAT(p.name, ' ', p.surname) SEPARATOR ', ') AS parent_names
    FROM users u
    LEFT JOIN user_parents up ON u.id = up.child_id
    LEFT JOIN users p ON up.parent_id = p.id
    GROUP BY u.id
")->fetchAll();


// CSS to inject
$css_extra = '
    .greeting { font-size: 1.5em; color: #007acc; margin-top: 20px; }
    .child-list { margin-top: 10px; padding-left: 0; }
    .child-list li { list-style: none; padding: 5px 0; }
    .child-entry { display: flex; justify-content: space-between; }
    .child-entry button { background-color: red; color: white; border: none; cursor: pointer; }
';

// JS to inject
$js = '
function toggleChildSelector() {
    const box = document.getElementById("is_parent_checkbox");
    document.getElementById("child_selector").style.display = box.checked ? "block" : "none";
}

function filterChildren() {
    const query = document.getElementById("child_search").value.toLowerCase();
    const items = document.querySelectorAll(".child-option");

    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.includes(query) ? "block" : "none";
    });
}

function addChild(id, name) {
    const selected = document.getElementById("selected_children");
    const existing = document.getElementById("child_" + id);
    if (existing) return;

    const li = document.createElement("li");
    li.className = "child-entry";
    li.id = "child_" + id;
    li.innerHTML = `
        <span>${name}</span>
        <button onclick="removeChild(${id})">X</button>
    `;
    selected.appendChild(li);

    updateHiddenInput();
}

function removeChild(id) {
    const li = document.getElementById("child_" + id);
    if (li) li.remove();
    updateHiddenInput();
}

function updateHiddenInput() {
    const children = document.querySelectorAll("#selected_children .child-entry");
    const ids = Array.from(children).map(li => li.id.replace("child_", ""));
    document.getElementById("selected_children_input").value = JSON.stringify(ids);
}

document.addEventListener("DOMContentLoaded", () => {
    toggleChildSelector();
});
';
