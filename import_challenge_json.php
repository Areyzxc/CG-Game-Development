<?php
/**
 * ==========================================================
 * File: import_challenge_json.php
 * 
 * Description:
 *   - Admin tool for importing challenge questions from a JSON file
 *   - Features:
 *       • Validates admin access before import
 *       • Accepts JSON file upload via POST
 *       • Parses and inserts challenge questions and answers into database
 *       • Supports multiple question types (fill_blank, output, case_study, code)
 *       • Displays import summary and error messages
 *       • Shows example JSON format for reference
 *       • Responsive, dark-themed UI
 * 
 * Usage:
 *   - Accessible only to logged-in admins
 *   - Used to bulk import challenge questions and answers
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['challenge_json'])) {
    $file = $_FILES['challenge_json']['tmp_name'];
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    if (!is_array($data)) {
        $errors[] = 'Invalid JSON format.';
    } else {
        $db = Database::getInstance()->getConnection();
        foreach ($data as $q) {
            try {
                $stmt = $db->prepare("INSERT INTO challenge_questions (type, title, description, starter_code, expected_output, points) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $q['type'],
                    $q['title'],
                    $q['description'],
                    $q['starter_code'] ?? '',
                    $q['expected_output'] ?? '',
                    $q['points'] ?? 30
                ]);
                $qid = $db->lastInsertId();
                // Insert answers if present (for fill_blank, output, case_study)
                if (!empty($q['answers']) && is_array($q['answers'])) {
                    foreach ($q['answers'] as $a) {
                        $stmt2 = $db->prepare("INSERT INTO challenge_answers (question_id, answer_text, is_correct, explanation) VALUES (?, ?, ?, ?)");
                        $stmt2->execute([
                            $qid,
                            $a['answer_text'],
                            isset($a['is_correct']) ? (int)$a['is_correct'] : 1,
                            $a['explanation'] ?? null
                        ]);
                    }
                } elseif (!empty($q['expected_output'])) {
                    // If only expected_output is present, insert as answer
                    $stmt2 = $db->prepare("INSERT INTO challenge_answers (question_id, answer_text, is_correct) VALUES (?, ?, 1)");
                    $stmt2->execute([$qid, $q['expected_output']]);
                }
                $imported++;
            } catch (Exception $e) {
                $errors[] = 'Error importing question: ' . htmlspecialchars($q['title']) . ' - ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Challenge Questions (JSON)</title>
    <style>
        body { background: #23272e; color: #f5f5f5; font-family: monospace; }
        .container { max-width: 600px; margin: 3rem auto; background: #181a1b; border-radius: 10px; padding: 2rem; box-shadow: 0 2px 12px #000a; }
        h1 { color: #ffe066; text-align: center; }
        input[type=file], button { font-size: 1.1rem; margin: 1rem 0; }
        .success { color: #2ecc71; }
        .error { color: #ff6fa1; }
        .summary { margin-top: 1.5rem; }
        pre { background: #23272e; color: #fff; padding: 1rem; border-radius: 6px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Import Challenge Questions (JSON)</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="challenge_json">Select JSON file:</label><br>
        <input type="file" name="challenge_json" id="challenge_json" accept="application/json" required><br>
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
    <pre>[
  {
    "type": "fill_blank",
    "title": "Complete the Loop",
    "description": "Fill in the blank to complete this for loop...",
    "starter_code": "",
    "expected_output": "i++",   
    "points": 30,
    "answers": [
    { "answer_text": "i++", "is_correct": 1, "explanation": "This is the standard increment operator in C-style for loops." },
    { "answer_text": "++i", "is_correct": 1, "explanation": "++i also increments the value, and is valid in this context." },
    { "answer_text": "i--", "is_correct": 0, "explanation": "i-- would decrement, not increment, so it is incorrect here." }
    ]
  },
  {
    "type": "output",
    "title": "Predict the Output",
    "description": "What will be the output of this code?",
    "starter_code": "",
    "expected_output": "[1, 2, 3, 4]",
    "points": 30,
    "answers": [
      { "answer_text": "[1, 2, 3, 4]", "is_correct": 1 }
    ]
  },
  {
    "type": "case_study",
    "title": "Database Optimization",
    "description": "A web application is experiencing slow query performance...",
    "starter_code": "",
    "expected_output": "Add indexes on frequently queried columns",
    "points": 30,
    "answers": [
      { "answer_text": "Add indexes on frequently queried columns", "is_correct": 1 }
    ]
  },
  {
    "type": "code",
    "title": "Reverse String",
    "description": "Write a function that reverses a string...",
    "starter_code": "function reverseString(str) {\n    // Your code here\n}",
    "expected_output": "",
    "points": 30
  }
]
</pre>
</div>
</body>
</html>