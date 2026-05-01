<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jr Student System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <img src="../images/northhub.svg" id="logo"></img>
        <button class="navbarbuttons" onclick="showSection('create')"> Create </button>
        <button class="navbarbuttons" onclick="showSection('read')"> Read </button>
        <button class="navbarbuttons" onclick="showSection('update')"> Update </button>
        <button class="navbarbuttons" onclick="showSection('delete')"> Delete </button>
    </nav>

    <section id="home" class="homecontent">
        <h1 class="splash">Welcome to Student Management System</h1>
        <h2 class="splash">A Project in Integrative Programming Technologies</h2>
    </section>

    <?php require_once '../includes/db.php'; ?>

    <?php
    $activeSection = 'home';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_action'])) {
        $surname    = trim($_POST['surname']);
        $name       = trim($_POST['name']);
        $middlename = trim($_POST['middlename']);
        $address    = trim($_POST['address']);
        $contact    = trim($_POST['contact']);

        $stmt = $pdo->prepare("INSERT INTO students (name, surname, middlename, address, contact_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $surname, $middlename, $address, $contact]);
        $activeSection = 'create';
        $createSuccess = true;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_action'])) {
        $id         = $_POST['student_id'];
        $surname    = trim($_POST['surname']);
        $name       = trim($_POST['name']);
        $middlename = trim($_POST['middlename']);
        $address    = trim($_POST['address']);
        $contact    = trim($_POST['contact']);

        $stmt = $pdo->prepare("UPDATE students SET surname=?, name=?, middlename=?, address=?, contact_number=? WHERE id=?");
        $stmt->execute([$surname, $name, $middlename, $address, $contact, $id]);
        $activeSection = 'update';
        $updateSuccess = true;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_action'])) {
        $id   = $_POST['student_id'];
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $activeSection = 'delete';
        $deleteSuccess = true;
    }

    if (isset($_GET['edit_id'])) {
        $activeSection = 'update';
    }
    ?>

    <section id="create" class="content">
        <h1 class="contentitle"> Insert New Student </h1>

        <?php if (!empty($createSuccess)): ?>
            <div class="successmsg">✅ Student saved successfully!</div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="create_action" value="1">

            <label class="label">Surname</label>
            <input type="text" name="surname" id="surname" class="field" required><br/>

            <label class="label">Name</label>
            <input type="text" name="name" id="name" class="field" required><br/>

            <label class="label">Middle name</label>
            <input type="text" name="middlename" id="middlename" class="field"><br/>

            <label class="label">Address</label>
            <input type="text" name="address" id="address" class="field"><br/>

            <label class="label">Mobile Number</label>
            <input type="text" name="contact" id="contact" class="field"><br/>

            <div id="btncontainer">
                <button type="button" id="clrbtn" class="btns">Clear Fields</button>
                <button type="submit" id="savebtn" class="btns">Save</button>
            </div>
        </form>
    </section>

    <section id="read" class="content">
        <h1 class="contentitle">View Students</h1>
        <table class="studenttable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Surname</th>
                    <th>Name</th>
                    <th>Middle Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM students ORDER BY id ASC");
                $students = $stmt->fetchAll();
                if (count($students) === 0) {
                    echo '<tr><td colspan="6" style="text-align:center;padding:20px;color:#888;">No students found.</td></tr>';
                } else {
                    foreach ($students as $s) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($s['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($s['surname']) . '</td>';
                        echo '<td>' . htmlspecialchars($s['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($s['middlename'] ?? '—') . '</td>';
                        echo '<td>' . htmlspecialchars($s['address'] ?? '—') . '</td>';
                        echo '<td>' . htmlspecialchars($s['contact_number'] ?? '—') . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </section>

    <section id="update" class="content">
        <h1 class="contentitle">Update Student Records</h1>

        <?php if (!empty($updateSuccess)): ?>
            <div class="successmsg">✅ Student updated successfully!</div>
        <?php endif; ?>

        <?php
        $all = $pdo->query("SELECT id, surname, name FROM students ORDER BY id ASC")->fetchAll();
        ?>

        <form method="GET">
            <label class="label">Select Student to Edit:</label><br/>
            <select name="edit_id" class="field" onchange="this.form.submit()">
                <option value="">-- Choose a student --</option>
                <?php foreach ($all as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (isset($_GET['edit_id']) && $_GET['edit_id'] == $s['id']) ? 'selected' : '' ?>>
                        #<?= $s['id'] ?> — <?= htmlspecialchars($s['surname'] . ', ' . $s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php
        if (isset($_GET['edit_id']) && $_GET['edit_id'] !== '') {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$_GET['edit_id']]);
            $editStudent = $stmt->fetch();
            if ($editStudent): ?>
            <form method="POST" style="margin-top:20px;">
                <input type="hidden" name="update_action" value="1">
                <input type="hidden" name="student_id" value="<?= $editStudent['id'] ?>">

                <label class="label">Surname</label>
                <input type="text" name="surname" class="field" value="<?= htmlspecialchars($editStudent['surname']) ?>" required><br/>

                <label class="label">Name</label>
                <input type="text" name="name" class="field" value="<?= htmlspecialchars($editStudent['name']) ?>" required><br/>

                <label class="label">Middle Name</label>
                <input type="text" name="middlename" class="field" value="<?= htmlspecialchars($editStudent['middlename'] ?? '') ?>"><br/>

                <label class="label">Address</label>
                <input type="text" name="address" class="field" value="<?= htmlspecialchars($editStudent['address'] ?? '') ?>"><br/>

                <label class="label">Mobile Number</label>
                <input type="text" name="contact" class="field" value="<?= htmlspecialchars($editStudent['contact_number'] ?? '') ?>"><br/>

                <div id="btncontainer">
                    <button type="submit" class="btns">Save Changes</button>
                </div>
            </form>
            <?php endif;
        } else {
            echo '<p style="color:#888;margin-top:12px;">Select a student above to edit their details.</p>';
        }
        ?>
    </section>

    <section id="delete" class="content">
        <h1 class="contentitle">Remove Student Records</h1>

        <?php if (!empty($deleteSuccess)): ?>
            <div class="successmsg">🗑️ Student deleted successfully!</div>
        <?php endif; ?>

        <?php
        $all = $pdo->query("SELECT id, surname, name FROM students ORDER BY id ASC")->fetchAll();
        ?>

        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this student?')">
            <input type="hidden" name="delete_action" value="1">

            <label class="label">Select Student to Delete:</label><br/>
            <select name="student_id" class="field" required>
                <option value="">-- Choose a student --</option>
                <?php foreach ($all as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        #<?= $s['id'] ?> — <?= htmlspecialchars($s['surname'] . ', ' . $s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br/><br/>

            <div id="btncontainer">
                <button type="submit" class="btns" style="background-color:rgba(220,50,50,0.3);">Delete Student</button>
            </div>
        </form>
    </section>

    <script>
        var startSection = "<?= $activeSection ?>";
    </script>
    <script src="script.js"></script>
</body>
</html>