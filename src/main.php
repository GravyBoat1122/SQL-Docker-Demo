<?php include('main_h.php'); ?>
<!DOCTYPE html>
<html>

<head>
    <title>User Form</title>
    <style>
        <?= $css_extra ?>
    </style>
</head>

<body>
    <h1>Add a User</h1>
    <form method="post" action="">
        <label>
            Name:
            <input type="text" name="name" required>
        </label><br><br>

        <label>
            Surname:
            <input type="text" name="surname">
        </label><br><br>

        <label>
            Is Parent:
            <input type="checkbox" name="is_parent" id="is_parent_checkbox" onchange="toggleChildSelector()">
        </label><br><br>

        <div id="child_selector" style="display:none;">
            <label>Search Children:</label><br>
            <input type="text" id="child_search" oninput="filterChildren()" placeholder="Search by name or surname">
            <ul id="child_options">
                <?php foreach ($allUsers as $u): ?>
                    <li class="child-option" onclick="addChild(<?= $u['id'] ?>, '<?= htmlspecialchars($u['name'] . ' ' . $u['surname']) ?>')">
                        <?= htmlspecialchars($u['name'] . ' ' . $u['surname']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p>Selected Children:</p>
            <ul id="selected_children" class="child-list"></ul>
            <input type="hidden" name="selected_children" id="selected_children_input">
        </div>

        <br><button type="submit">Add User</button>
    </form>

    <hr>
    <h2>All Users</h2>
    <table border="1" cellpadding="6">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Is Parent</th>
            <th>Parents</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['surname']) ?></td>
                <td><?= $user['is_parent'] ? 'Yes' : 'No' ?></td>
                <td><?= $user['parent_names'] ?: '-' ?></td>
                <td><?= $user['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        <?= $js ?>
    </script>
</body>

</html>