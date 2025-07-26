<?php
/**
 * Code Game - Tutorials Page
 *
 * This page serves as the main hub for all educational content, offering
 * tutorials for various programming languages (HTML, CSS, Bootstrap, JavaScript,
 * Java, Python, C++). It also provides descriptive tutorials for each game mode
 * (Quiz, Challenges, Mini-Game) to guide users on how to play and learn.
 *
 * Users can track their progress through topics/modules, ensuring a structured
 * and engaging learning path. Future enhancements will focus on user engagement
 * and a highly user-friendly experience.
 *
 * Dependencies:
 * - includes/Database.php: For fetching programming languages and topics.
 * - includes/Auth.php: For user authentication status and personalized content.
 * - includes/header.php: Centralized header for consistent navigation and styling.
 * - includes/footer.php: Centralized footer for consistent layout.
 * 
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 */

// Include required files
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

// Initialize core components
$db = Database::getInstance();
$auth = Auth::getInstance();

// Get programming languages
$languages = $db->getProgrammingLanguages();

// Define topics for each language directly in PHP
$topicsConfig = [
    'html' => [
        ['id' => 'html-1', 'title' => 'HTML Fundamentals', 'description' => 'Learn the basic structure and elements of HTML documents.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'html-2', 'title' => 'HTML Elements & Attributes', 'description' => 'Explore common HTML elements and their attributes.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'html-3', 'title' => 'Links & Images', 'description' => 'How to add links and images to your web pages.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'html-4', 'title' => 'Lists & Tables', 'description' => 'Create lists and tables for structured content.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'html-5', 'title' => 'Forms & Input', 'description' => 'Master HTML forms and input types.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'html-6', 'title' => 'Semantic HTML', 'description' => 'Use semantic tags for better accessibility and SEO.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'html-7', 'title' => 'Media Elements', 'description' => 'Embed audio, video, and other media.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'html-8', 'title' => 'HTML APIs', 'description' => 'Introduction to HTML5 APIs.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'html-9', 'title' => 'Accessibility', 'description' => 'Make your web pages accessible to all users.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'html-10', 'title' => 'Best Practices', 'description' => 'Tips and tricks for writing clean HTML.', 'difficulty' => 'expert', 'content' => ''],
    ],
    'css' => [
        ['id' => 'css-1', 'title' => 'CSS Basics', 'description' => 'Introduction to CSS syntax and selectors.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'css-2', 'title' => 'Colors & Backgrounds', 'description' => 'Styling backgrounds and using color.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'css-3', 'title' => 'Text & Fonts', 'description' => 'Control typography and font styles.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'css-4', 'title' => 'Box Model', 'description' => 'Understand margin, border, padding, and content.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'css-5', 'title' => 'Flexbox', 'description' => 'Modern layout with CSS Flexbox.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'css-6', 'title' => 'Grid Layout', 'description' => 'Advanced layouts with CSS Grid.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'css-7', 'title' => 'Transitions & Animations', 'description' => 'Add motion to your web pages.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'css-8', 'title' => 'Responsive Design', 'description' => 'Make your site look great on any device.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'css-9', 'title' => 'CSS Variables', 'description' => 'Reusable values with custom properties.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'css-10', 'title' => 'CSS Best Practices', 'description' => 'Write maintainable and scalable CSS.', 'difficulty' => 'expert', 'content' => ''],
    ],
    'bootstrap' => [
        ['id' => 'bootstrap-1', 'title' => 'Topic 1: Bootstrap Introduction', 'description' => 'What is Bootstrap and why use it?', 'difficulty' => 'beginner',
         'content' => '
         <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
           <i class="bx bxl-bootstrap" style="font-size: 2.2rem; color: #7952b3;"></i>
           <span style="font-size: 1.3rem; font-weight: 600; color: #7952b3;">Bootstrap</span>
         </div>
         <p><strong>Bootstrap</strong> is a popular open-source CSS framework for developing responsive and mobile-first websites. It provides ready-to-use components, a grid system, and powerful utilities to speed up web development.</p>
         <p>Bootstrap was originally created by <strong>Mark Otto</strong> and <strong>Jacob Thornton</strong> at Twitter in mid-2010, and released as an open-source project in August 2011. Its goal was to unify and simplify the front-end development process at Twitter, but it quickly became the world’s most popular front-end toolkit.</p>
         <div style="display: flex; gap: 18px; align-items: flex-start; margin: 1.2rem 0;">
           <img src="https://getbootstrap.com/docs/5.3/assets/brand/bootstrap-logo-shadow.png" alt="Bootstrap Logo" style="width: 80px; height: 80px; object-fit: contain; border-radius: 12px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
           <div>
             <p>Today, Bootstrap is used by millions of websites and is maintained by a large community of developers. It supports all modern browsers and is constantly updated with new features and improvements.</p>
           </div>
         </div>
         <ul>
           <li><i class="bx bx-check-circle" style="color: #28a745;"></i> <strong>Easy to use:</strong> Start with just a single CSS file.</li>
           <li><i class="bx bx-mobile-alt" style="color: #00bfff;"></i> <strong>Responsive by default:</strong> Adapts to any device size.</li>
           <li><i class="bx bx-cog" style="color: #ffc107;"></i> <strong>Customizable:</strong> Use Sass variables and mixins to make it your own.</li>
           <li><i class="bx bx-layer" style="color: #7952b3;"></i> <strong>Component-rich:</strong> Includes navbars, modals, carousels, and more.</li>
         </ul>
         <h5 style="margin-top: 1.5rem; color: #00bfff;"><i class="bx bx-star"></i> Fun Facts</h5>
         <ul>
           <li>Bootstrap was originally named "Twitter Blueprint".</li>
           <li>It’s one of the most starred projects on GitHub.</li>
           <li>Major sites like NASA, Spotify, and Vogue have used Bootstrap.</li>
           <li>Bootstrap 5 dropped jQuery for a more modern, vanilla JS approach.</li>
         </ul>
         <p style="margin-top: 1.5rem;">Learn more at <a href="https://getbootstrap.com/" target="_blank">getbootstrap.com</a>.</p>'
    ],
        ['id' => 'bootstrap-2', 'title' => 'Topic 2: Bootstrap Grid', 'description' => 'Responsive layouts with the grid system.', 'difficulty' => 'beginner',
        'content' => '
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <i class="bx bxl-bootstrap" style="font-size: 2.2rem; color: #7952b3;"></i>
            <span style="font-size: 1.3rem; font-weight: 600; color: #7952b3;">Bootstrap</span>
        </div>
        <p><strong>Bootstrap Grid</strong> is a responsive, mobile-first grid system that allows you to create complex layouts with ease. It provides a flexible and intuitive way to structure your content, ensuring it looks great on any device.</p>
        <p>The grid system is based on a 12-column layout, which means you can create layouts with up to 12 columns. You can use the grid to create responsive layouts that adapt to different screen sizes, from small mobile devices to large desktop screens.</p>
        <p>The grid system is built with flexbox, which means it provides a more efficient and flexible way to create layouts. It also provides a set of utility classes that you can use to quickly style your content, such as padding and margin classes.</p>
        <p>The grid system is a powerful tool that can help you create responsive and mobile-first websites. It is a great way to ensure that your content looks great on any device, and it is a great way to create complex layouts with ease.</p>
        <p>Learn more at <a href="https://getbootstrap.com/docs/5.3/layout/grid/" target="_blank">getbootstrap.com/docs/5.3/layout/grid/</a>.</p>'
    ],
        ['id' => 'bootstrap-3', 'title' => 'Topic 3: Bootstrap Components', 'description' => 'Buttons, cards, navbars, and more.', 'difficulty' => 'beginner',
        'content' => '
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
    <i class="bx bxl-bootstrap" style="font-size: 2.2rem; color: #7952b3;"></i>
    <span style="font-size: 1.3rem; font-weight: 600; color: #7952b3;">Bootstrap</span>
</div>

<h2>Understanding Bootstrap Components</h2>
<p><strong>Bootstrap Components</strong> are pre-built, reusable UI elements that simplify web development. They include buttons, cards, navbars, forms, alerts, modals, and more. These components are designed to be responsive and work seamlessly across different devices, making it easier to create modern, mobile-first websites.</p>
<p>In this tutorial, we\'ll explore some of the most commonly used Bootstrap Components and learn how to implement them in your projects. To use these components, include Bootstrap 5’s CSS and JavaScript in your HTML file:</p>
<pre><code>&lt;link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"&gt;
&lt;script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"&gt;&lt;/script&gt;</code></pre>

<h3>Buttons</h3>
<p>Bootstrap provides a variety of button styles and sizes to suit different needs. You can create buttons with different colors, sizes, and states (like active or disabled).</p>
<p>Here\'s an example of basic buttons:</p>
<pre><code>&lt;button type="button" class="btn btn-primary"&gt;Primary&lt;/button&gt;
&lt;button type="button" class="btn btn-secondary"&gt;Secondary&lt;/button&gt;
&lt;button type="button" class="btn btn-success"&gt;Success&lt;/button&gt;
&lt;button type="button" class="btn btn-danger"&gt;Danger&lt;/button&gt;
&lt;button type="button" class="btn btn-warning"&gt;Warning&lt;/button&gt;
&lt;button type="button" class="btn btn-info"&gt;Info&lt;/button&gt;
&lt;button type="button" class="btn btn-light"&gt;Light&lt;/button&gt;
&lt;button type="button" class="btn btn-dark"&gt;Dark&lt;/button&gt;</code></pre>
<p>You can also create outline buttons, which have a border but no background color, or adjust sizes with <code>btn-lg</code> or <code>btn-sm</code>:</p>
<pre><code>&lt;button type="button" class="btn btn-outline-primary"&gt;Outline Primary&lt;/button&gt;
&lt;button type="button" class="btn btn-primary btn-lg"&gt;Large Button&lt;/button&gt;
&lt;button type="button" class="btn btn-primary btn-sm"&gt;Small Button&lt;/button&gt;</code></pre>

<h3>Cards</h3>
<p>Cards are versatile components that can hold text, images, buttons, and more. They are ideal for displaying content like blog posts or product details.</p>
<p>Here\'s a basic card example:</p>
<pre><code>&lt;div class="card" style="width: 18rem;"&gt;
  &lt;img src="..." class="card-img-top" alt="..."&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Card Title&lt;/h5&gt;
    &lt;p class="card-text"&gt;Some quick example text to build on the card title and make up the bulk of the card\'s content.&lt;/p&gt;
    &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
<p>You can add headers or lists for more complex cards:</p>
<pre><code>&lt;div class="card" style="width: 18rem;"&gt;
  &lt;div class="card-header"&gt;Featured&lt;/div&gt;
  &lt;ul class="list-group list-group-flush"&gt;
    &lt;li class="list-group-item"&gt;Item 1&lt;/li&gt;
    &lt;li class="list-group-item"&gt;Item 2&lt;/li&gt;
    &lt;li class="list-group-item"&gt;Item 3&lt;/li&gt;
  &lt;/ul&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Special Title&lt;/h5&gt;
    &lt;p class="card-text"&gt;Additional content here.&lt;/p&gt;
    &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>

<h3>Navbars</h3>
<p>Navbars are navigation headers that include links, forms, and dropdowns, typically used at the top of a webpage.</p>
<p>Here\'s a responsive navbar example:</p>
<pre><code>&lt;nav class="navbar navbar-expand-lg navbar-light bg-light"&gt;
  &lt;div class="container-fluid"&gt;
    &lt;a class="navbar-brand" href="#"&gt;Navbar&lt;/a&gt;
    &lt;button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"&gt;
      &lt;span class="navbar-toggler-icon"&gt;&lt;/span&gt;
    &lt;/button&gt;
    &lt;div class="collapse navbar-collapse" id="navbarSupportedContent"&gt;
      &lt;ul class="navbar-nav me-auto mb-2 mb-lg-0"&gt;
        &lt;li class="nav-item"&gt;
          &lt;a class="nav-link active" aria-current="page" href="#"&gt;Home&lt;/a&gt;
        &lt;/li&gt;
        &lt;li class="nav-item"&gt;
          &lt;a class="nav-link" href="#"&gt;Link&lt;/a&gt;
        &lt;/li&gt;
        &lt;li class="nav-item dropdown"&gt;
          &lt;a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"&gt;
            Dropdown
          &lt;/a&gt;
          &lt;ul class="dropdown-menu" aria-labelledby="navbarDropdown"&gt;
            &lt;li&gt;&lt;a class="dropdown-item" href="#"&gt;Action&lt;/a&gt;&lt;/li&gt;
            &lt;li&gt;&lt;a class="dropdown-item" href="#"&gt;Another action&lt;/a&gt;&lt;/li&gt;
            &lt;li&gt;&lt;hr class="dropdown-divider"&gt;&lt;/li&gt;
            &lt;li&gt;&lt;a class="dropdown-item" href="#"&gt;Something else here&lt;/a&gt;&lt;/li&gt;
          &lt;/ul&gt;
        &lt;/li&gt;
        &lt;li class="nav-item"&gt;
          &lt;a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"&gt;Disabled&lt;/a&gt;
        &lt;/li&gt;
      &lt;/ul&gt;
      &lt;form class="d-flex"&gt;
        &lt;input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"&gt;
        &lt;button class="btn btn-outline-success" type="submit"&gt;Search&lt;/button&gt;
      &lt;/form&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/nav&gt;</code></pre>
<p>This navbar collapses on small screens and includes a toggler for mobile navigation.</p>

<h3>Forms</h3>
<p>Bootstrap provides classes to style form elements like inputs, labels, and buttons, making forms user-friendly.</p>
<p>Here\'s a basic form example:</p>
<pre><code>&lt;form&gt;
  &lt;div class="mb-3"&gt;
    &lt;label for="exampleInputEmail1" class="form-label"&gt;Email address&lt;/label&gt;
    &lt;input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"&gt;
    &lt;div id="emailHelp" class="form-text"&gt;We\'ll never share your email with anyone else.&lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="mb-3"&gt;
    &lt;label for="exampleInputPassword1" class="form-label"&gt;Password&lt;/label&gt;
    &lt;input type="password" class="form-control" id="exampleInputPassword1"&gt;
  &lt;/div&gt;
  &lt;div class="mb-3 form-check"&gt;
    &lt;input type="checkbox" class="form-check-input" id="exampleCheck1"&gt;
    &lt;label class="form-check-label" for="exampleCheck1"&gt;Check me out&lt;/label&gt;
  &lt;/div&gt;
  &lt;button type="submit" class="btn btn-primary"&gt;Submit&lt;/button&gt;
&lt;/form&gt;</code></pre>
<p>Use classes like <code>form-control</code> for inputs and <code>form-check</code> for checkboxes.</p>

<h3>Alerts</h3>
<p>Alerts provide feedback messages for user actions, such as success or error notifications.</p>
<p>Here\'s an example of different alert types:</p>
<pre><code>&lt;div class="alert alert-primary" role="alert"&gt;
  A simple primary alert—check it out!
&lt;/div&gt;
&lt;div class="alert alert-success" role="alert"&gt;
  A simple success alert—check it out!
&lt;/div&gt;
&lt;div class="alert alert-danger" role="alert"&gt;
  A simple danger alert—check it out!
&lt;/div&gt;
&lt;div class="alert alert-warning" role="alert"&gt;
  A simple warning alert—check it out!
&lt;/div&gt;</code></pre>
<p>For dismissible alerts, add a close button:</p>
<pre><code>&lt;div class="alert alert-warning alert-dismissible fade show" role="alert"&gt;
  &lt;strong&gt;Holy guacamole!&lt;/strong&gt; You should check in on some of those fields below.
  &lt;button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"&gt;&lt;/button&gt;
&lt;/div&gt;</code></pre>

<h3>Modals</h3>
<p>Modals are dialog boxes for displaying content or forms, triggered by buttons or other elements.</p>
<p>Here\'s a modal example:</p>
<pre><code>&lt;button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"&gt;
  Launch demo modal
&lt;/button&gt;
&lt;div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"&gt;
  &lt;div class="modal-dialog"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h5 class="modal-title" id="exampleModalLabel"&gt;Modal title&lt;/h5&gt;
        &lt;button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"&gt;&lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        &lt;p&gt;Modal body text goes here.&lt;/p&gt;
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-bs-dismiss="modal"&gt;Close&lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
<p>Ensure Bootstrap’s JavaScript is included for modals to function.</p>

<h3>Conclusion</h3>
<p>Bootstrap Components make it easy to create responsive, professional-looking websites. By mastering buttons, cards, navbars, forms, alerts, and modals, you can build engaging user interfaces. Explore more components and advanced features in the official documentation:</p>
<ul>
  <li><a href="https://getbootstrap.com/docs/5.3/components/" target="_blank">Bootstrap 5 Components</a></li>
  <li><a href="https://www.w3schools.com/bootstrap5/" target="_blank">W3Schools Bootstrap 5 Tutorial</a></li>
</ul>'
    ],
        ['id' => 'bootstrap-4', 'title' => 'Topic 4: Utilities & Helpers', 'description' => 'Quickly style elements with utility classes.', 'difficulty' => 'beginner', 
        'content' => ''],
        ['id' => 'bootstrap-5', 'title' => 'Topic 5: Customizing Bootstrap', 'description' => 'Override and extend Bootstrap styles.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'bootstrap-6', 'title' => 'Topic 6: Bootstrap JS Plugins', 'description' => 'Add interactivity with Bootstrap plugins.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'bootstrap-7', 'title' => 'Topic 7: Forms & Validation', 'description' => 'Build and validate forms with Bootstrap.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'bootstrap-8', 'title' => 'Topic 8: Advanced Components', 'description' => 'Carousels, modals, and more.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'bootstrap-9', 'title' => 'Topic 9: Accessibility in Bootstrap', 'description' => 'Make Bootstrap sites accessible.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'bootstrap-10', 'title' => 'Topic 10: Bootstrap Best Practices', 'description' => 'Tips for scalable Bootstrap projects.', 'difficulty' => 'expert', 'content' => ''],
    ],
    'javascript' => [
        ['id' => 'js-1', 'title' => 'JS Fundamentals', 'description' => 'Variables, data types, and operators.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'js-2', 'title' => 'Control Flow', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'js-3', 'title' => 'Functions', 'description' => 'Defining and using functions.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'js-4', 'title' => 'DOM Basics', 'description' => 'Manipulate the web page with JavaScript.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'js-5', 'title' => 'Events', 'description' => 'Respond to user actions.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'js-6', 'title' => 'Objects & Arrays', 'description' => 'Work with complex data structures.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'js-7', 'title' => 'ES6+ Features', 'description' => 'Modern JavaScript syntax and features.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'js-8', 'title' => 'Async JS', 'description' => 'Promises, async/await, and AJAX.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'js-9', 'title' => 'Modules & Tooling', 'description' => 'Organize and build JS projects.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'js-10', 'title' => 'JS Best Practices', 'description' => 'Write clean and efficient JavaScript.', 'difficulty' => 'expert', 'content' => ''],
    ],
    'python' => [
        ['id' => 'python-1', 'title' => 'Python Basics', 'description' => 'Syntax, variables, and data types.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'python-2', 'title' => 'Control Structures', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'python-3', 'title' => 'Functions', 'description' => 'Defining and using functions.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'python-4', 'title' => 'Data Structures', 'description' => 'Lists, tuples, sets, and dictionaries.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'python-5', 'title' => 'OOP in Python', 'description' => 'Classes and objects in Python.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'python-6', 'title' => 'Modules & Packages', 'description' => 'Organize and reuse code.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'python-7', 'title' => 'File I/O', 'description' => 'Read and write files in Python.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'python-8', 'title' => 'Error Handling', 'description' => 'Exceptions and debugging.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'python-9', 'title' => 'Advanced Topics', 'description' => 'Decorators, generators, and more.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'python-10', 'title' => 'Python Best Practices', 'description' => 'Tips for writing great Python code.', 'difficulty' => 'expert', 'content' => ''],
    ],
    'java' => [
        ['id' => 'java-1', 'title' => 'Java Basics', 'description' => 'Syntax, variables, and data types.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'java-2', 'title' => 'Control Flow', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'java-3', 'title' => 'Methods', 'description' => 'Defining and using methods.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'java-4', 'title' => 'OOP Concepts', 'description' => 'Classes, objects, and inheritance.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'java-5', 'title' => 'Collections', 'description' => 'Lists, sets, and maps.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'java-6', 'title' => 'Exception Handling', 'description' => 'Try-catch and error management.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'java-7', 'title' => 'File I/O', 'description' => 'Read and write files in Java.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'java-8', 'title' => 'Multithreading', 'description' => 'Concurrency in Java.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'java-9', 'title' => 'Java Streams', 'description' => 'Functional programming with streams.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'java-10', 'title' => 'Java Best Practices', 'description' => 'Tips for writing robust Java code.', 'difficulty' => 'expert', 'content' => ''],
    ],
    'cpp' => [
        ['id' => 'cpp-1', 'title' => 'C++ Basics', 'description' => 'Syntax, variables, and data types.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'cpp-2', 'title' => 'Control Flow', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'cpp-3', 'title' => 'Functions', 'description' => 'Defining and using functions.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'cpp-4', 'title' => 'OOP in C++', 'description' => 'Classes, objects, and inheritance.', 'difficulty' => 'beginner', 'content' => ''],
        ['id' => 'cpp-5', 'title' => 'Pointers & Memory', 'description' => 'Pointers and memory management.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'cpp-6', 'title' => 'STL', 'description' => 'Standard Template Library.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'cpp-7', 'title' => 'File I/O', 'description' => 'Read and write files in C++.', 'difficulty' => 'intermediate', 'content' => ''],
        ['id' => 'cpp-8', 'title' => 'Templates', 'description' => 'Generic programming with templates.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'cpp-9', 'title' => 'Advanced Topics', 'description' => 'Move semantics, smart pointers, etc.', 'difficulty' => 'expert', 'content' => ''],
        ['id' => 'cpp-10', 'title' => 'C++ Best Practices', 'description' => 'Tips for writing efficient C++ code.', 'difficulty' => 'expert', 'content' => ''],
    ],
];

// Set page title for the header
$pageTitle = "Tutorials";

// Helper function for difficulty badges
function getDifficultyBadgeClass($difficulty) {
    return match($difficulty) {
        'beginner' => 'success',
        'intermediate' => 'warning',
        'expert' => 'danger',
        default => 'secondary'
    };
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/tutorial-style.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<main class="tutorial-main">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <nav class="tutorial-sidebar" id="tutorial-nav">
                    <ul class="tutorial-nav-list">
                        <li class="tutorial-nav-item">
                            <h5 class="text-light mb-3">Game Modes</h5>
                            <ul class="tutorial-nav-list">
                                <li class="tutorial-nav-item">
                                    <a href="#mini-game" class="tutorial-nav-link">
                                        <i class='bx bx-joystick'></i>
                                        Mini-Game Mode
                                    </a>
                                </li>
                                <li class="tutorial-nav-item">
                                    <a href="#quiz" class="tutorial-nav-link">
                                        <i class='bx bx-question-mark'></i>
                                        Quiz Mode
                                    </a>
                                </li>
                                <li class="tutorial-nav-item">
                                    <a href="#challenge" class="tutorial-nav-link">
                                        <i class='bx bx-trophy'></i>
                                        Challenge Mode
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="tutorial-nav-item mt-4">
                            <h5 class="text-light mb-3">Programming</h5>
                            <ul class="tutorial-nav-list">
                                <?php foreach ($languages as $language): ?>
                                <li class="tutorial-nav-item">
                                    <a href="#" class="tutorial-nav-link language-select" data-language-id="<?php echo htmlspecialchars($language['id']); ?>">
                                        <span class="language-icon"><?php echo htmlspecialchars($language['icon']); ?></span>
                                        <?php echo htmlspecialchars($language['name']); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- Mobile Toggle -->
                <button class="sidebar-toggle d-lg-none">
                    <i class='bx bx-menu'></i>
                </button>
            </div>

            <!-- Main Content: Only one language at a time -->
            <div class="col-lg-9">
                <!-- Restore Game Mode Tutorials Section -->
                <section class="tutorial-window mb-4" id="game-modes">
                    <div class="window-header">
                        <div class="window-title">
                            <i class='bx bx-game'></i>
                            Game Mode Tutorials
                        </div>
                        <div class="window-controls">
                            <span class="window-control close"></span>
                            <span class="window-control minimize"></span>
                            <span class="window-control maximize"></span>
                        </div>
                    </div>
                    <div class="window-content">
                        <div class="row g-4">
                            <!-- Mini-Game Tutorial -->
                            <div class="col-md-6 col-lg-4">
                                <div class="game-mode-card" id="mini-game">
                                    <div class="game-mode-icon">
                                        <i class='bx bx-joystick'></i>
                                    </div>
                                    <h5>Mini-Game Mode</h5>
                                    <p>Learn through interactive mini-games. Master coding concepts while having fun!</p>
                                    <button class="nav-button tutorial-trigger" data-mode="mini-game">
                                        Start Tutorial
                                    </button>
                                </div>
                            </div>
                            <!-- Quiz Tutorial -->
                            <div class="col-md-6 col-lg-4">
                                <div class="game-mode-card" id="quiz">
                                    <div class="game-mode-icon">
                                        <i class='bx bx-question-mark'></i>
                                    </div>
                                    <h5>Quiz Mode</h5>
                                    <p>Test your knowledge with our comprehensive quizzes on various programming topics.</p>
                                    <button class="nav-button tutorial-trigger" data-mode="quiz">
                                        Start Tutorial
                                    </button>
                                </div>
                            </div>
                            <!-- Challenge Tutorial -->
                            <div class="col-md-6 col-lg-4">
                                <div class="game-mode-card" id="challenge">
                                    <div class="game-mode-icon">
                                        <i class='bx bx-trophy'></i>
                                    </div>
                                    <h5>Challenge Mode</h5>
                                    <p>Take on coding challenges and prove your skills in real-world scenarios.</p>
                                    <button class="nav-button tutorial-trigger" data-mode="challenge">
                                        Start Tutorial
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- End Game Mode Tutorials Section -->

                <section class="tutorial-window" id="programming-language-section">
                    <div class="window-header">
                        <div class="window-title" id="selected-language-title">
                            <i class='bx bx-code-alt'></i>
                            <span>Select a Programming Language</span>
                        </div>
                        <div class="window-controls">
                            <span class="window-control close"></span>
                            <span class="window-control minimize"></span>
                            <span class="window-control maximize"></span>
                        </div>
                    </div>
                    <div class="window-content">
                        <!-- Add search bar above topic dropdown -->
                        <div id="language-topic-container" class="d-none">
                            <div class="mb-3">
                                <div class="search-bar mb-3">
                                    <i class='bx bx-search'></i>
                                    <input type="text" id="topicSearch" class="form-control" placeholder="Search topics...">
                                </div>
                                <label for="topicDropdown" class="form-label">Choose a Topic:</label>
                                <select id="topicDropdown" class="form-select"></select>
                            </div>
                            <div id="topicDetails" class="mb-4"></div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button class="btn btn-outline-info" id="currentlyReadingBtn">Currently Reading</button>
                                <button class="btn btn-success" id="doneReadingBtn">Done Reading</button>
                            </div>
                        </div>
                        <div id="selectPrompt" class="text-center text-muted py-5">
                            <i class='bx bx-pointer bx-lg'></i>
                            <p>Please select a programming language from the sidebar to begin.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- Congratulation Modal -->
    <div class="modal fade" id="congratsModal" tabindex="-1" aria-labelledby="congratsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="congratsModalLabel">Congratulations!</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <i class='bx bx-party bx-tada bx-lg mb-3'></i>
            <p class="lead">You've completed this topic! Keep up the great work and continue your learning journey!</p>
          </div>
        </div>
      </div>
    </div>
    <!-- Game Mode Tutorial Modal/Popup -->
    <div class="tutorial-modal-overlay" id="tutorialModalOverlay"></div>
    <div class="tutorial-popup" id="tutorialPopup">
        <button class="popup-close" id="popupCloseBtn" aria-label="Close">&times;</button>
        <div class="popup-progress">
            <div class="progress-bar" style="width: 0%"></div>
        </div>
        <div class="popup-content">
            <!-- Content will be loaded dynamically -->
            <div class="spinner-container">
                <div class="spinner"></div>
            </div>
        </div>
        <div class="popup-navigation">
            <button class="nav-button" id="prevBtn" disabled>Previous</button>
            <button class="nav-button" id="nextBtn">Next</button>
        </div>
    </div>
</main>
<script>
    window.topicsConfig = <?php echo json_encode($topicsConfig); ?>;
    window.languages = <?php echo json_encode($languages); ?>;
  </script>
<script src="assets/js/tutorial.js"></script>
<?php include 'includes/footer.php'; ?> 