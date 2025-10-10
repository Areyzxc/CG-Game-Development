<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * API Endpoint: Get Challenge
 * Purpose: Fetch a random challenge for the specified game type, language, and difficulty
 * Method: POST
 * Returns: JSON challenge data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/Database.php';

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    $gameType = $input['game_type'] ?? 'guess';
    $language = $input['language'] ?? 'javascript';
    $difficulty = $input['difficulty'] ?? 'beginner';
    
    // Log the request for debugging
    error_log("Request: game_type={$gameType}, language={$language}, difficulty={$difficulty}");
    
    // Hardcoded challenges (can be moved to database later)
    $challenges = [
        'typing' => [
            'javascript' => [
                'beginner' => [
                    ['code' => 'function greet(name) {\n  return `Hello, ${name}!`;\n}'],
                    ['code' => 'const numbers = [1, 2, 3];\nconst doubled = numbers.map(n => n * 2);'],
                    ['code' => 'if (true) {\n  console.log("This will run");\n}'],
                    ['code' => 'for (let i = 0; i < 3; i++) {\n  console.log(i);\n}']
                ],
                'intermediate' => [
                    ['code' => 'class Person {\n  constructor(name) {\n    this.name = name;\n  }\n  greet() {\n    return `Hello, ${this.name}!`;\n  }\n}'],
                    ['code' => 'const asyncFunction = async () => {\n  const result = await fetch(url);\n  return result.json();\n};']
                ],
                'expert' => [
                    ['code' => 'const memoize = (fn) => {\n  const cache = new Map();\n  return (...args) => {\n    const key = JSON.stringify(args);\n    return cache.has(key) ? cache.get(key) : cache.set(key, fn(...args)).get(key);\n  };\n};']
                ]
            ],
            'python' => [
                'beginner' => [
                    ['code' => 'def greet(name):\n    return f"Hello, {name}!"'],
                    ['code' => 'numbers = [1, 2, 3]\ndoubled = [n * 2 for n in numbers]']
                ],
                'intermediate' => [
                    ['code' => 'class Person:\n    def __init__(self, name):\n        self.name = name\n    \n    def greet(self):\n        return f"Hello, {self.name}!"']
                ]
            ],
            'html' => [
                'beginner' => [
                    ['code' => '<!DOCTYPE html>\n<html>\n<head>\n    <title>My Page</title>\n</head>\n<body>\n    <h1>Hello World</h1>\n</body>\n</html>'],
                    ['code' => '<div class="container">\n    <h2>Welcome</h2>\n    <p>This is a paragraph.</p>\n</div>'],
                    ['code' => '<form>\n    <input type="text" placeholder="Name">\n    <button type="submit">Submit</button>\n</form>'],
                    ['code' => '<ul>\n    <li>Item 1</li>\n    <li>Item 2</li>\n    <li>Item 3</li>\n</ul>']
                ],
                'intermediate' => [
                    ['code' => '<nav class="navbar">\n    <div class="nav-brand">Logo</div>\n    <ul class="nav-links">\n        <li><a href="#home">Home</a></li>\n        <li><a href="#about">About</a></li>\n    </ul>\n</nav>'],
                    ['code' => '<section class="hero">\n    <div class="hero-content">\n        <h1>Welcome to Our Site</h1>\n        <p>Discover amazing content here.</p>\n        <button class="cta-btn">Get Started</button>\n    </div>\n</section>']
                ],
                'expert' => [
                    ['code' => '<article class="blog-post">\n    <header>\n        <h1>Post Title</h1>\n        <time datetime="2025-01-01">January 1, 2025</time>\n    </header>\n    <main>\n        <p>Post content goes here...</p>\n    </main>\n    <footer>\n        <div class="tags">\n            <span class="tag">HTML</span>\n            <span class="tag">Web</span>\n        </div>\n    </footer>\n</article>']
                ]
            ],
            'css' => [
                'beginner' => [
                    ['code' => '.container {\n    max-width: 1200px;\n    margin: 0 auto;\n    padding: 20px;\n}'],
                    ['code' => 'h1 {\n    color: #333;\n    font-size: 2rem;\n    text-align: center;\n}'],
                    ['code' => '.button {\n    background: #007bff;\n    color: white;\n    padding: 10px 20px;\n    border: none;\n    border-radius: 4px;\n}'],
                    ['code' => '.card {\n    border: 1px solid #ddd;\n    border-radius: 8px;\n    padding: 16px;\n    box-shadow: 0 2px 4px rgba(0,0,0,0.1);\n}']
                ],
                'intermediate' => [
                    ['code' => '.navbar {\n    display: flex;\n    justify-content: space-between;\n    align-items: center;\n    padding: 1rem 2rem;\n    background: #fff;\n    box-shadow: 0 2px 4px rgba(0,0,0,0.1);\n}'],
                    ['code' => '.grid {\n    display: grid;\n    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));\n    gap: 2rem;\n    padding: 2rem;\n}'],
                    ['code' => '@media (max-width: 768px) {\n    .container {\n        padding: 1rem;\n    }\n    .grid {\n        grid-template-columns: 1fr;\n    }\n}']
                ],
                'expert' => [
                    ['code' => '.hero {\n    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\n    min-height: 100vh;\n    display: flex;\n    align-items: center;\n    justify-content: center;\n    position: relative;\n}\n.hero::before {\n    content: "";\n    position: absolute;\n    top: 0;\n    left: 0;\n    right: 0;\n    bottom: 0;\n    background: rgba(0,0,0,0.3);\n}']
                ]
            ],
            'bootstrap' => [
                'beginner' => [
                    ['code' => '<div class="container">\n    <div class="row">\n        <div class="col-md-6">\n            <div class="card">\n                <div class="card-body">\n                    <h5 class="card-title">Card Title</h5>\n                    <p class="card-text">Some quick example text.</p>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>'],
                    ['code' => '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">\n    <div class="container">\n        <a class="navbar-brand" href="#">Brand</a>\n        <button class="navbar-toggler" type="button">\n            <span class="navbar-toggler-icon"></span>\n        </button>\n    </div>\n</nav>'],
                    ['code' => '<div class="alert alert-success" role="alert">\n    <h4 class="alert-heading">Well done!</h4>\n    <p>You successfully completed the task.</p>\n    <hr>\n    <p class="mb-0">Keep up the great work!</p>\n</div>']
                ],
                'intermediate' => [
                    ['code' => '<div class="modal fade" id="exampleModal">\n    <div class="modal-dialog">\n        <div class="modal-content">\n            <div class="modal-header">\n                <h5 class="modal-title">Modal Title</h5>\n                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>\n            </div>\n            <div class="modal-body">\n                <p>Modal body text goes here.</p>\n            </div>\n        </div>\n    </div>\n</div>'],
                    ['code' => '<form class="needs-validation" novalidate>\n    <div class="mb-3">\n        <label for="email" class="form-label">Email</label>\n        <input type="email" class="form-control" id="email" required>\n        <div class="invalid-feedback">Please provide a valid email.</div>\n    </div>\n    <button class="btn btn-primary" type="submit">Submit</button>\n</form>']
                ],
                'expert' => [
                    ['code' => '<div class="accordion" id="accordionExample">\n    <div class="accordion-item">\n        <h2 class="accordion-header" id="headingOne">\n            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">\n                Accordion Item #1\n            </button>\n        </h2>\n        <div id="collapseOne" class="accordion-collapse collapse show">\n            <div class="accordion-body">\n                This is the first item\'s accordion body.\n            </div>\n        </div>\n    </div>\n</div>']
                ]
            ]
        ],
        'guess' => [
            'javascript' => [
                'beginner' => [
                    ['code' => 'console.log(2 + "2");', 'answer' => '22', 'explanation' => 'String concatenation: 2 becomes "2"'],
                    ['code' => 'console.log([1, 2, 3].length);', 'answer' => '3', 'explanation' => 'Array length property'],
                    ['code' => 'console.log(typeof "hello");', 'answer' => 'string', 'explanation' => 'typeof returns data type'],
                    ['code' => 'console.log(5 > 3);', 'answer' => 'true', 'explanation' => 'Boolean comparison'],
                    ['code' => 'console.log(Math.max(1, 5, 3));', 'answer' => '5', 'explanation' => 'Math.max returns largest number'],
                    ['code' => 'console.log("Hello".toUpperCase());', 'answer' => 'HELLO', 'explanation' => 'toUpperCase converts to uppercase'],
                    ['code' => 'console.log(10 % 3);', 'answer' => '1', 'explanation' => 'Modulo operator returns remainder'],
                    ['code' => 'console.log(Boolean(0));', 'answer' => 'false', 'explanation' => '0 is falsy in JavaScript'],
                    ['code' => 'console.log("5" - 3);', 'answer' => '2', 'explanation' => 'Subtraction converts string to number'],
                    ['code' => 'console.log([].length);', 'answer' => '0', 'explanation' => 'Empty array has length 0'],
                    ['code' => 'console.log(Math.round(4.7));', 'answer' => '5', 'explanation' => 'Math.round rounds to nearest integer'],
                    ['code' => 'console.log(true + false);', 'answer' => '1', 'explanation' => 'true=1, false=0 in arithmetic'],
                    ['code' => 'console.log("5" + 3);', 'answer' => '53', 'explanation' => 'String concatenation with + operator'],
                    ['code' => 'console.log(parseInt("10px"));', 'answer' => '10', 'explanation' => 'parseInt extracts numbers from string start'],
                    ['code' => 'console.log([1, 2, 3].includes(2));', 'answer' => 'true', 'explanation' => 'Array includes() checks for value existence'],
                    ['code' => 'console.log("hello".length);', 'answer' => '5', 'explanation' => 'String length property'],
                    ['code' => 'console.log(3 * "3");', 'answer' => '9', 'explanation' => 'Multiplication converts string to number'],
                    ['code' => 'console.log(0.1 + 0.2 === 0.3);', 'answer' => 'false', 'explanation' => 'Floating point precision issue'],
                    ['code' => 'console.log("5" == 5);', 'answer' => 'true', 'explanation' => 'Loose equality with type coercion'],
                    ['code' => 'console.log("5" === 5);', 'answer' => 'false', 'explanation' => 'Strict equality compares type and value']
                ],
                'intermediate' => [
                    ['code' => 'console.log([1, 2, 3].map(x => x * 2));', 'answer' => '[2, 4, 6]', 'explanation' => 'map() creates a new array with the results of calling a function for every array element.'],
                    ['code' => 'const arr = [1, 2, 3]; const [a, , b] = arr; console.log(a, b);', 'answer' => '1 3', 'explanation' => 'Array destructuring with skipped elements using commas'],
                    ['code' => 'const obj = { x: 1, y: 2 }; const { x: newX } = obj; console.log(newX);', 'answer' => '1', 'explanation' => 'Object destructuring with property renaming'],
                    ['code' => 'const arr = [1, 2, 3]; console.log([...arr, 4, 5]);', 'answer' => '[1, 2, 3, 4, 5]', 'explanation' => 'Spread operator to expand array elements'],
                    ['code' => 'const sum = (a, b, ...rest) => a + b + rest.length; console.log(sum(1, 2, 3, 4));', 'answer' => '5', 'explanation' => 'Rest parameters collect remaining arguments into an array'],
                    ['code' => 'const delay = () => { return new Promise(resolve => setTimeout(resolve, 1000, "Done!")); }; delay().then(console.log);', 'answer' => 'Done! (after 1 second)', 'explanation' => 'Promise with setTimeout and resolve value'],
                    ['code' => 'const obj = { a: 1 }; Object.defineProperty(obj, "b", { value: 2, enumerable: false }); console.log(Object.keys(obj).includes("b"));', 'answer' => 'false', 'explanation' => 'Non-enumerable properties are not included in Object.keys()'],
                    ['code' => 'const map = new Map([["a", 1], ["b", 2]]); console.log([...map.entries()].flat());', 'answer' => '["a", 1, "b", 2]', 'explanation' => 'Converting Map entries to array and flattening'],
                    ['code' => 'const set = new Set([1, 2, 2, 3]); console.log([...set].filter(x => x > 1));', 'answer' => '[2, 3]', 'explanation' => 'Set removes duplicates, then filter keeps values > 1'],
                    ['code' => 'const obj = { a: 1, b: 2 }; const { a, ...rest } = obj; console.log(rest);', 'answer' => '{ b: 2 }', 'explanation' => 'Rest properties in object destructuring'],
                    ['code' => 'console.log(typeof typeof 1);', 'answer' => 'string', 'explanation' => 'typeof 1 returns "number", then typeof "number" returns "string".'],
                    ['code' => 'console.log([..."hello"].length);', 'answer' => '5', 'explanation' => 'Spread operator converts string to array of characters, then we get the length.'],
                    ['code' => 'console.log([1, 2, 3].map(x => x * 2));', 'answer' => '[2, 4, 6]', 'explanation' => 'map() creates a new array with the results of calling a function for every array element.'],
                    ['code' => 'console.log([1, 2, 3].filter(x => x > 1));', 'answer' => '[2, 3]', 'explanation' => 'filter() creates a new array with all elements that pass the test implemented by the provided function.'],
                    ['code' => 'console.log([1, 2, 3].reduce((a, b) => a + b, 0));', 'answer' => '6', 'explanation' => 'reduce() accumulates array values starting from 0: 0+1+2+3 = 6.']
                ],
                'expert' => [
                    ['code' => 'console.log(new Set([1, 2, 2, 3, 3, 3]).size);', 'answer' => '3', 'explanation' => 'Set removes duplicates, so we have [1, 2, 3] with size 3.'],
                    ['code' => 'const createCounter = () => {\n  let count = 0;\n  return {\n    increment: () => ++count,\n    getCount: () => count\n  };\n};\nconst counter = createCounter();\ncounter.increment();\nconsole.log(counter.getCount());', 'answer' => '1', 'explanation' => 'Closure maintains private state (count)'],
                    ['code' => 'const delay = (ms) => new Promise(res => setTimeout(res, ms));\n\nasync function* asyncGenerator() {\n  yield await Promise.resolve(1);\n  await delay(100);\n  yield 2;\n  yield 3;\n}\n\n(async () => {\n  for await (const value of asyncGenerator()) {\n    console.log(value);\n  }\n})();', 'answer' => '1\n2\n3', 'explanation' => 'Async generator with for-await-of loop and delays'],
                    ['code' => 'const obj = {\n  _value: 0,\n  get value() { return this._value; },\n  increment() {\n    this._value++;\n    return this;\n  },\n  add(x) {\n    this._value += x;\n    return this;\n  }\n};\n\nconsole.log(obj.increment().add(2).value);', 'answer' => '3', 'explanation' => 'Method chaining with getter/setter and private property'],
                    ['code' => 'const memoize = (fn) => {\n  const cache = new Map();\n  return (...args) => {\n    const key = JSON.stringify(args);\n    if (!cache.has(key)) {\n      cache.set(key, fn(...args));\n    }\n    return cache.get(key);\n  };\n};\n\nconst fibonacci = memoize((n) =>\n  n <= 1 ? n : fibonacci(n - 1) + fibonacci(n - 2)\n);\n\nconsole.log(fibonacci(5));', 'answer' => '5', 'explanation' => 'Memoization of recursive Fibonacci function'],
                    ['code' => 'class Observable {\n  constructor() {\n    this.observers = [];\n  }\n  subscribe(fn) {\n    this.observers.push(fn);\n    return () => {\n      this.observers = this.observers.filter(observer => observer !== fn);\n    };\n  }\n  notify(data) {\n    this.observers.forEach(observer => observer(data));\n  }\n}\n\nconst observable = new Observable();\nconst unsubscribe = observable.subscribe(console.log);\nobservable.notify("Hello");\nunsubscribe();\nobservable.notify("World");', 'answer' => 'Hello', 'explanation' => 'Simple Observable pattern implementation with unsubscribe functionality'],
                    ['code' => 'const debounce = (fn, delay) => {\n  let timeoutId;\n  return function(...args) {\n    clearTimeout(timeoutId);\n    timeoutId = setTimeout(() => fn.apply(this, args), delay);\n  };\n};\n\nconst log = () => console.log("Debounced!");\nconst debouncedLog = debounce(log, 100);\n\ndebouncedLog();\ndebouncedLog();\nsetTimeout(debouncedLog, 150);', 'answer' => 'Debounced!\nDebounced!', 'explanation' => 'Debounce implementation with multiple calls'],
                    ['code' => 'const createLazy = (fn) => {\n  let result;\n  let executed = false;\n  return () => {\n    if (!executed) {\n      result = fn();\n      executed = true;\n    }\n    return result;\n  };\n};\n\nconst lazyValue = createLazy(() => {\n  console.log("Calculating...");\n  return 42;\n});\n\nconsole.log(lazyValue() + lazyValue());', 'answer' => 'Calculating...\n84', 'explanation' => 'Lazy evaluation with caching (memoization)'],
                    ['code' => 'const deepEqual = (a, b) => {\n  if (a === b) return true;\n  if (a == null || b == null || typeof a !== \'object\' || typeof b !== \'object\') return false;\n  \n  const keysA = Object.keys(a), keysB = Object.keys(b);\n  if (keysA.length !== keysB.length) return false;\n  \n  return keysA.every(key => {\n    if (!keysB.includes(key)) return false;\n    return deepEqual(a[key], b[key]);\n  });\n};\n\nconst obj1 = { a: 1, b: { c: 2 } };\nconst obj2 = { b: { c: 2 }, a: 1 };\nconsole.log(deepEqual(obj1, obj2));', 'answer' => 'true', 'explanation' => 'Deep equality check for objects with nested structures'],
                    ['code' => 'const createToggler = (initial = false) => {\n  let state = initial;\n  return [\n    () => state,\n    () => state = !state,\n    (newState) => state = newState\n  ];\n};\n\nconst [get, toggle, set] = createToggler();\ntoggle();\nset(true);\nconsole.log(get());', 'answer' => 'true', 'explanation' => 'Toggle state management with closure and array destructuring']
                ]
            ],
            'python' => [
                'beginner' => [
                    ['code' => 'print(len("hello"))', 'answer' => '5', 'explanation' => 'len() returns the length of the string "hello".'],
                    ['code' => 'print(3 ** 2)', 'answer' => '9', 'explanation' => '** is the exponentiation operator: 3 to the power of 2 equals 9.'],
                    ['code' => 'print("Python"[::-1])', 'answer' => 'nohtyP', 'explanation' => '[::-1] slices the string in reverse order.'],
                    ['code' => 'print("A" * 5)', 'answer' => 'AAAAA', 'explanation' => 'Multiplying a string in Python repeats it.'],
                    ['code' => 'print(bool(""))', 'answer' => 'False', 'explanation' => 'Empty strings are considered False.'],
                    ['code' => 'print(10//3)', 'answer' => '3', 'explanation' => '// performs integer (floor) division.'],
                    ['code' => 'print(7%2)', 'answer' => '1', 'explanation' => '% returns the remainder of the division.'],
                    ['code' => 'print(abs(-10))', 'answer' => '10', 'explanation' => 'abs() gives the absolute value.'],
                    ['code' => 'print([1, 2, 3][1])', 'answer' => '2', 'explanation' => 'Indexing starts at 0, so is 2.'],
                    ['code' => 'print("2" + "3")', 'answer' => '23', 'explanation' => 'Adding two strings concatenates them.'],
                    ['code' => 'print(4.5 // 2)', 'answer' => '2.0', 'explanation' => 'Floor division returns float when used with a float.'],
                    ['code' => 'print("A B C".split())', 'answer' => "['A', 'B', 'C']", 'explanation' => "split() separates words by spaces."],
                    ['code' => 'print(min(5, 2, 9))', 'answer' => '2', 'explanation' => 'min() returns the smallest value.'],
                    ['code' => 'print(",".join(["a", "b"]))', 'answer' => 'a,b', 'explanation' => 'join() concatenates list items with commas.'],
                    ['code' => 'print(list("hello"))', 'answer' => "['h', 'e', 'l', 'l', 'o']", 'explanation' => 'list() converts a string to a list of characters.'],
                    ['code' => 'print(set([1, 2, 2, 3, 3, 3]))', 'answer' => "{'1', '2', '3'}", 'explanation' => 'set() removes duplicates from a list.'],
                    ['code' => 'print(dict(a=1, b=2))', 'answer' => "{'a': 1, 'b': 2}", 'explanation' => 'dict() creates a dictionary from key-value pairs.'],
                    ['code' => 'print({"a": 1, "b": 2}.keys())', 'answer' => "['a', 'b']", 'explanation' => 'keys() returns a list of dictionary keys.'],
                    ['code' => 'print("Goodbye".replace("o", "a"))', 'answer' => 'Gaadbye', 'explanation' => 'replace substitutes characters in a string.'],
                    ['code' => 'print(7 == "7")', 'answer' => 'False', 'explanation' => '7 (int) is not equal to "7" (str).'],
                    ['code' => 'print(None == 0)', 'answer' => 'False', 'explanation' => 'None is a special type, different from 0.']
                ],
                'intermediate' => [
                    ['code' => 'print([x * 2 for x in [1, 2, 3]])', 'answer' => '[2, 4, 6]', 'explanation' => 'List comprehension creates a new list with each element multiplied by 2.'],
                    ['code' => 'print([(x, x ** 2) for x in range(1,4)])', 'answer' => '[(1, 1), (2, 4), (3, 9)]', 'explanation' => 'Pairs each number from 1 to 3 with its square.'],
                    ['code' => 'print([x.upper() for x in ["apple", "orange"]])', 'answer' => "['APPLE', 'ORANGE']", 'explanation' => 'Converts each string to uppercase.'],
                    ['code' => 'print([x for x in range(10) if x % 2 == 0])', 'answer' => '[0, 2, 4, 6, 8]', 'explanation' => 'List comprehension with a condition to filter even numbers.'],
                    ['code' => 'print(list(map(lambda x: x * 2, [1, 2, 3])))', 'answer' => '[2, 4, 6]', 'explanation' => 'map() applies a lambda function to each element in the list.'],
                    ['code' => 'print(list(filter(lambda x: x % 2 == 0, [1, 2, 3])))', 'answer' => '[2]', 'explanation' => 'filter() with a lambda function to filter even numbers.'],
                    ['code' => 'print([x for x in ["apple", "banana", "cherry"] if "a" in x])', 'answer' => "['apple', 'banana']", 'explanation' => 'Selects items with "a" from the list.'],
                    ['code' => 'print(list(zip([1, 2, 3], ["a", "b", "c"])))', 'answer' => "[(1, 'a'), (2, 'b'), (3, 'c')]", 'explanation' => 'zip() pairs elements from two lists.'],
                    ['code' => 'print({"a": 1, "b": 2}.get("a"))', 'answer' => '1', 'explanation' => 'get() retrieves a value from a dictionary.'],
                    ['code' => 'print("hello".count("l"))', 'answer' => '2', 'explanation' => 'count() counts occurrences of a character in a string.'],
                    ['code' => 'print("hello".replace("l", "x"))', 'answer' => 'hexxo', 'explanation' => 'replace() substitutes characters in a string.'],
                    ['code' => 'print("hello".split("l"))', 'answer' => "['he', 'o']", 'explanation' => 'split() splits a string into a list of substrings.'],
                    ['code' => 'print(" ".join([str(x) for x in range(3)]))', 'answer' => '0 1 2', 'explanation' => 'Joins string representations of 0-2 with spaces.'],
                    ['code' => 'print(sum([i for i in range(6)]))', 'answer' => '15', 'explanation' => 'Sum of numbers from 0 to 5: 0+1+2+3+4+5=15.'],
                    ['code' => 'print(list({x: x**2 for x in range(5)}))', 'answer' => "{'0': 0, '1': 1, '2': 4, '3': 9, '4': 16}", 'explanation' => 'Dictionary comprehension creates a dictionary with squares of numbers from 0 to 4.'],
                    ['code' => 'print([x for x in "hello" if x in "aeiou"])', 'answer' => "['e', 'o']", 'explanation' => 'Selects vowels in the string "hello".']
                ],
                'expert' => [
                    ['code' => 'print(sum(x for x in range(5) if x % 2 == 0))', 'answer' => '6', 'explanation' => 'Generator expression sums even numbers from 0 to 4: 0+2+4 = 6.'],
                    ['code' => 'print(list((x**2 for x in range(5))))', 'answer' => '[0, 1, 4, 9, 16]', 'explanation' => 'Generates squares of 0 to 4 using generator expression.'],
                    ['code' => 'print(sum(i if i % 2 == 0 else 0 for i in range(10)))', 'answer' => '20', 'explanation' => 'Sum of even numbers from 0 to 9 using conditional generator.'],
                    ['code' => 'gen = (i/2 for i in ); print(list(gen))', 'answer' => '[0.0, 4.5, 10.5, 16.0]', 'explanation' => 'Generator expression producing floats by division.'],
                    ['code' => 'gen = ((i, i2, i3) for i in range(4)); print(next(gen))', 'answer' => '(0, 0, 0)', 'explanation' => 'Generator yields tuples with number, square, cube.'],
                    ['code' => 'print(sum(x*y for x, y in [(1,2), (3,4), (5,6)]))', 'answer' => '44', 'explanation' => 'Sum of product of pairs using generator expression.'],
                    ['code' => 'print(all(x > 0 for x in [1, 2, 3, -1]))', 'answer' => 'False', 'explanation' => 'all() returns False because -1 is not greater than 0.'],
                    ['code' => 'def g():\n for i in range(2):\n yield i\n yield i*i\nprint(list(g()))', 'answer' => '[0, 0, 1, 1]', 'explanation' => 'Generator function yielding numbers and their squares.'],
                    ['code' => 'def gen():\n yield from range(3)\nprint(list(gen()))', 'answer' => '[0, 1, 2]', 'explanation' => 'yield from delegates generator iteration.']
                ]
        ],
        'java' => [
                 'beginner' => [
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println("Hello, World!");\n    }\n}', 'answer' => 'Hello, World!', 'explanation' => 'Simple Java program to print "Hello, World!".'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println(10 + 5);\n    }\n}', 'answer' => '15', 'explanation' => 'Adds two integers and prints the result.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println("Hello" + "World");\n    }\n}', 'answer' => 'HelloWorld', 'explanation' => 'Concatenates two strings and prints the result.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println(7 / 2);\n    }\n}', 'answer' => '3', 'explanation' => 'Integer division discards decimal part.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println(7.0 / 2);\n    }\n}', 'answer' => '3.5', 'explanation' => 'Double division displays decimal values.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println("A" + 3);\n    }\n}', 'answer' => 'A3', 'explanation' => 'Concatenates string "A" with int 3, producing "A3".'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println("Java".length());\n    }\n}', 'answer' => '4', 'explanation' => 'The length() method counts string characters.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println(2 * 5);\n    }\n}', 'answer' => '10', 'explanation' => 'Multiplies two integers.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println(true);\n    }\n}', 'answer' => 'true', 'explanation' => 'Boolean literal is printed directly.'],
                    ['code' => 'public class Main {\n    public static void main(String[] args) {\n        System.out.println(5 > 3);\n    }\n}', 'answer' => 'true', 'explanation' => 'The comparison 5 > 3 evaluates to true.']
                ],
                 'intermediate' => [
                ['code' => 'public class Main {\n    public static void main(String[] args) {\n        int[] numbers = {1, 2, 3};\n        for (int number : numbers) {\n            System.out.println(number);\n        }\n    }\n}', 'answer' => '[1, 2, 3]', 'explanation' => 'Java program using enhanced for loop to print array elements.'],
                ],
                 'expert' => [
                ['code' => 'public class Main {\n    public static void main(String[] args) {\n        List<String> names = Arrays.asList("Alice", "Bob", "Charlie");\n        names.stream().filter(name -> name.startsWith("A")).forEach(System.out::println);\n    }\n}', 'answer' => '[Alice]', 'explanation' => 'Java program using streams to filter and print names starting with "A".'],
            ]
        ]
    ]
];

    // Get challenges for the specified parameters
    if (!isset($challenges[$gameType])) {
        throw new Exception("Invalid game type: {$gameType}. Available types: " . implode(', ', array_keys($challenges)));
    }
    if (!isset($challenges[$gameType][$language])) {
        throw new Exception("Invalid language '{$language}' for game type '{$gameType}'. Available languages: " . 
            implode(', ', array_keys($challenges[$gameType])));
    }
    if (!isset($challenges[$gameType][$language][$difficulty])) {
        throw new Exception("Invalid difficulty '{$difficulty}' for game type '{$gameType}' and language '{$language}'. " . 
            "Available difficulties: " . implode(', ', array_keys($challenges[$gameType][$language])));
    }
    
    $gameChallenges = $challenges[$gameType][$language][$difficulty];
    error_log("Found " . count($gameChallenges) . " challenges for {$gameType}/{$language}/{$difficulty}");
    
    if (empty($gameChallenges)) {
        throw new Exception("No challenges available for {$gameType}/{$language}/{$difficulty}");
    }
    
    // Select random challenge
    $randomIndex = array_rand($gameChallenges);
    $challenge = $gameChallenges[$randomIndex];
    
    // Format response based on game type
    if ($gameType === 'guess') {
        // For guess type, ensure all required fields exist
        if (!is_array($challenge) || !isset($challenge['code']) || !isset($challenge['answer'])) {
            throw new Exception("Invalid challenge format for guess type");
        }
        
        $response = [
            'success' => true,
            'challenge' => [
                'code' => $challenge['code'],
                'answer' => $challenge['answer'],
                'explanation' => $challenge['explanation'] ?? '',
                'type' => 'guess'
            ]
        ];
    } else {
        // For typing challenges, handle both string and array formats
        $challengeContent = is_string($challenge) ? $challenge : (is_array($challenge) ? ($challenge['code'] ?? json_encode($challenge)) : json_encode($challenge));
        
        // Ensure we have a string value for the challenge
        $challengeContent = (string)$challengeContent;
        
        $response = [
            'success' => true,
            'challenge' => [
                'code' => $challengeContent,
                'content' => $challengeContent,
                'type' => 'typing'
            ]
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Log the full error with trace
    error_log("Error in get-challenge.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
