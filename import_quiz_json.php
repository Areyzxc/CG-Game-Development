<?php
/**
 * ==========================================================
 * File: import_quiz_json.php
 * 
 * Description:
 *   - Admin tool for importing quiz questions from a JSON file
 *   - Features:
 *       • Validates admin access before import
 *       • Accepts JSON file upload via POST
 *       • Parses and inserts questions and answers into database
 *       • Displays import summary and error messages
 *       • Shows example JSON format for reference
 *       • Responsive, dark-themed UI
 * 
 * Usage:
 *   - Accessible only to logged-in admins
 *   - Used to bulk import quiz questions and choices
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

$auth = Auth::getInstance();
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    die('Access denied. Admins only.');
}

$imported = 0;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['quiz_json'])) {
    $file = $_FILES['quiz_json']['tmp_name'];
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    if (!is_array($data)) {
        $errors[] = 'Invalid JSON format.';
    } else {
        $db = Database::getInstance()->getConnection();
        foreach ($data as $q) {
            try {
                $stmt = $db->prepare("INSERT INTO quiz_questions (topic_id, question, question_type, difficulty) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $q['topic_id'],
                    $q['question'],
                    $q['question_type'],
                    $q['difficulty']
                ]);
                $qid = $db->lastInsertId();
                $choices = $q['choices'] ?? [];
                foreach ($choices as $c) {
                    $stmt2 = $db->prepare("INSERT INTO quiz_answers (question_id, answer, is_correct) VALUES (?, ?, ?)");
                    $stmt2->execute([$qid, $c['answer'], $c['is_correct'] ? 1 : 0]);
                }
                $imported++;
            } catch (Exception $e) {
                $errors[] = 'Error importing question: ' . htmlspecialchars($q['question']) . ' - ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Quiz Questions (JSON)</title>
    <style>
        body { background: #23272e; color: #f5f5f5; font-family: monospace; }
        .container { max-width: 600px; margin: 3rem auto; background: #181a1b; border-radius: 10px; padding: 2rem; box-shadow: 0 2px 12px #000a; }
        h1 { color: #aaffee; text-align: center; }
        input[type=file], button { font-size: 1.1rem; margin: 1rem 0; }
        .success { color: #2ecc71; }
        .error { color: #ff6fa1; }
        .summary { margin-top: 1.5rem; }
        pre { background: #23272e; color: #fff; padding: 1rem; border-radius: 6px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Import Quiz Questions (JSON)</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="quiz_json">Select JSON file:</label><br>
        <input type="file" name="quiz_json" id="quiz_json" accept="application/json" required><br>
        <button type="submit">Import</button>
    </form>
    <div class="summary">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if ($imported): ?>
                <div class="success">Imported <?= $imported ?> questions successfully.</div>
            <?php endif; ?>
            <?php if ($errors): ?>
                <div class="error">
                    <strong>Errors:</strong>
                    <ul>
                        <?php foreach ($errors as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <h3>JSON Format Example:</h3>
    <pre>[{
  "topic_id": "html",
  "question": "What does HTML stand for?",
  "question_type": "multiple_choice",
  "difficulty": "beginner",
  "choices": [
    {"answer": "HyperText Markup Language", "is_correct": 1},
    {"answer": "Home Tool Markup Language", "is_correct": 0},
    {"answer": "Hyperlinks and Text Markup Language", "is_correct": 0},
    {"answer": "Hyperlinking Text Management Language", "is_correct": 0}
  ]
}]
</pre>
</div>
</body>
</html>