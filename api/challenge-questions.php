<?php
/**
 * File: api/challenge-questions.php
 * Purpose: API endpoint for retrieving 20 random expert challenge questions for CodeGaming, with fallback and sample generation.
 * Features:
 *   - Fetches 20 random expert questions from challenge_questions or code_challenges tables.
 *   - If no questions found, generates sample questions for fallback.
 *   - Returns question data in JSON format for frontend use.
 * Usage:
 *   - Called via AJAX from challenge.js to load challenge questions for the expert mode.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

header('Content-Type: application/json');
require_once '../includes/Database.php';

try {
    $db = Database::getInstance();
    
    // Fetch 20 random challenge questions (Expert only)
    $stmt = $db->prepare("
        SELECT 
            cq.id,
            cq.type,
            cq.title,
            cq.description,
            cq.starter_code,
            cq.expected_output,
            cq.points
        FROM challenge_questions cq
        WHERE cq.difficulty = 'expert'
        ORDER BY RAND()
        LIMIT 20
    ");
    
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($questions)) {
        // If no questions in challenge_questions table, fall back to code_challenges
        $stmt = $db->prepare("
            SELECT 
                id,
                'code' as type,
                title,
                description,
                starter_code,
                '' as expected_output,
                points
            FROM code_challenges
            WHERE difficulty = 'expert'
            ORDER BY RAND()
            LIMIT 20
        ");
        
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // If still no questions, return sample questions
    if (empty($questions)) {
        $questions = generateSampleQuestions();
    }
    
    echo json_encode([
        'success' => true,
        'questions' => $questions
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load questions: ' . $e->getMessage()
    ]);
}

function generateSampleQuestions() {
    return [
        [
            'id' => 1,
            'type' => 'fill_blank',
            'title' => 'Complete the Loop',
            'description' => 'Fill in the blank to complete this for loop that prints numbers 1 to 10:\n\nfor (int i = 1; i <= 10; ___) {\n    System.out.println(i);\n}',
            'starter_code' => '',
            'expected_output' => 'i++',
            'points' => 30
        ],
        [
            'id' => 2,
            'type' => 'output',
            'title' => 'Predict the Output',
            'description' => 'What will be the output of this Python code?\n\nx = [1, 2, 3]\ny = x\ny.append(4)\nprint(x)',
            'starter_code' => '',
            'expected_output' => '[1, 2, 3, 4]',
            'points' => 30
        ],
        [
            'id' => 3,
            'type' => 'code',
            'title' => 'Reverse String',
            'description' => 'Write a function that reverses a string without using any built-in reverse methods.',
            'starter_code' => 'function reverseString(str) {\n    // Your code here\n}',
            'expected_output' => '',
            'points' => 30
        ],
        [
            'id' => 4,
            'type' => 'case_study',
            'title' => 'Database Optimization',
            'description' => 'A web application is experiencing slow query performance. The main table has 1 million records. What would be the most effective first step to optimize performance?',
            'starter_code' => '',
            'expected_output' => 'Add indexes on frequently queried columns',
            'points' => 30
        ],
        [
            'id' => 5,
            'type' => 'fill_blank',
            'title' => 'JavaScript Array Method',
            'description' => 'Complete this JavaScript code to filter out even numbers:\n\nconst numbers = [1, 2, 3, 4, 5, 6];\nconst oddNumbers = numbers.____(num => num % 2 !== 0);',
            'starter_code' => '',
            'expected_output' => 'filter',
            'points' => 30
        ],
        [
            'id' => 6,
            'type' => 'output',
            'title' => 'Java Inheritance',
            'description' => 'What will be printed?\n\nclass Parent {\n    void show() { System.out.println("Parent"); }\n}\nclass Child extends Parent {\n    void show() { System.out.println("Child"); }\n}\nParent p = new Child();\np.show();',
            'starter_code' => '',
            'expected_output' => 'Child',
            'points' => 30
        ],
        [
            'id' => 7,
            'type' => 'code',
            'title' => 'Palindrome Check',
            'description' => 'Write a function that checks if a string is a palindrome (reads the same forwards and backwards).',
            'starter_code' => 'function isPalindrome(str) {\n    // Your code here\n}',
            'expected_output' => '',
            'points' => 30
        ],
        [
            'id' => 8,
            'type' => 'case_study',
            'title' => 'Memory Leak',
            'description' => 'A Java application is experiencing memory leaks. The heap usage keeps growing over time. What is the most likely cause?',
            'starter_code' => '',
            'expected_output' => 'Objects not being garbage collected due to circular references',
            'points' => 30
        ],
        [
            'id' => 9,
            'type' => 'fill_blank',
            'title' => 'CSS Flexbox',
            'description' => 'Complete this CSS to center an element both horizontally and vertically using flexbox:\n\n.container {\n    display: flex;\n    justify-content: ____;\n    align-items: ____;\n}',
            'starter_code' => '',
            'expected_output' => 'center center',
            'points' => 30
        ],
        [
            'id' => 10,
            'type' => 'output',
            'title' => 'SQL Query Result',
            'description' => 'Given this table:\nUsers(id, name, age)\n1, "Alice", 25\n2, "Bob", 30\n3, "Charlie", 25\n\nWhat will this query return?\nSELECT COUNT(*) FROM Users WHERE age = 25;',
            'starter_code' => '',
            'expected_output' => '2',
            'points' => 30
        ],
        [
            'id' => 11,
            'type' => 'code',
            'title' => 'Binary Search',
            'description' => 'Implement a binary search algorithm to find a target value in a sorted array.',
            'starter_code' => 'function binarySearch(arr, target) {\n    // Your code here\n}',
            'expected_output' => '',
            'points' => 30
        ],
        [
            'id' => 12,
            'type' => 'case_study',
            'title' => 'API Security',
            'description' => 'A REST API is vulnerable to unauthorized access. What is the most critical security measure to implement first?',
            'starter_code' => '',
            'expected_output' => 'Implement proper authentication and authorization',
            'points' => 30
        ],
        [
            'id' => 13,
            'type' => 'fill_blank',
            'title' => 'Python List Comprehension',
            'description' => 'Complete this Python list comprehension to create a list of squares for even numbers only:\n\nnumbers = [1, 2, 3, 4, 5, 6]\nsquares = [x**2 for x in numbers if ____]',
            'starter_code' => '',
            'expected_output' => 'x % 2 == 0',
            'points' => 30
        ],
        [
            'id' => 14,
            'type' => 'output',
            'title' => 'C++ Pointer',
            'description' => 'What will be the output?\n\nint x = 5;\nint *ptr = &x;\n*ptr = 10;\ncout << x;',
            'starter_code' => '',
            'expected_output' => '10',
            'points' => 30
        ],
        [
            'id' => 15,
            'type' => 'code',
            'title' => 'Fibonacci Sequence',
            'description' => 'Write a function to generate the nth Fibonacci number using recursion.',
            'starter_code' => 'function fibonacci(n) {\n    // Your code here\n}',
            'expected_output' => '',
            'points' => 30
        ],
        [
            'id' => 16,
            'type' => 'case_study',
            'title' => 'Load Balancing',
            'description' => 'A web application needs to handle 10,000 concurrent users. What is the most effective scaling strategy?',
            'starter_code' => '',
            'expected_output' => 'Implement horizontal scaling with load balancers',
            'points' => 30
        ],
        [
            'id' => 17,
            'type' => 'fill_blank',
            'title' => 'React Hook',
            'description' => 'Complete this React hook to manage state:\n\nconst [count, ____] = useState(0);',
            'starter_code' => '',
            'expected_output' => 'setCount',
            'points' => 30
        ],
        [
            'id' => 18,
            'type' => 'output',
            'title' => 'Git Command',
            'description' => 'What will this Git command do?\n\ngit reset --hard HEAD~1',
            'starter_code' => '',
            'expected_output' => 'Reset to the previous commit and discard all changes',
            'points' => 30
        ],
        [
            'id' => 19,
            'type' => 'code',
            'title' => 'Sort Algorithm',
            'description' => 'Implement a bubble sort algorithm to sort an array in ascending order.',
            'starter_code' => 'function bubbleSort(arr) {\n    // Your code here\n}',
            'expected_output' => '',
            'points' => 30
        ],
        [
            'id' => 20,
            'type' => 'case_study',
            'title' => 'Microservices',
            'description' => 'A monolithic application is being migrated to microservices. What is the biggest challenge to address first?',
            'starter_code' => '',
            'expected_output' => 'Data consistency and transaction management across services',
            'points' => 30
        ]
    ];
}
?>