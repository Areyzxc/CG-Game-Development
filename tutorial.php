<?php
// Include visitor tracking
require_once 'includes/track_visitor.php';

/**
 * ==========================================================
 * File: tutorial.php
 * 
 * Description:
 *   - Tutorial page for Code Gaming platform
 *   - Features:
 *       • Sidebar navigation for tutorial categories
 *       • Main content area for tutorial display
 *       • Interactive code examples
 *       • Progress tracking
 *       • Responsive design for all devices
 *
 * Dependencies:
 * - includes/Database.php: For fetching programming languages and topics.
 * - includes/Auth.php: For user authentication status and personalized content.
 * - includes/header.php: Centralized header for consistent navigation and styling.
 * - includes/footer.php: Centralized footer for consistent layout.
 * 
 * @author [Santiago]
 * @version 1.0.0
 * @last_updated 2025-07-22
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
        ['id' => 'html-1', 'title' => 'Topic #1: HTML Fundamentals', 'description' => 'Learn the basic structure and elements of HTML documents.', 'difficulty' => 'beginner', 
    'content' => '
<h3>What is HTML?</h3>
<p>HTML (HyperText Markup Language) is the backbone of every web page. It provides the basic structure using elements (tags) that browsers understand and render visually.</p>
<h4>Basic HTML Structure</h4>
<pre><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
  &lt;head&gt;
    &lt;title&gt;My First Page&lt;/title&gt;
  &lt;/head&gt;
  &lt;body&gt;
    &lt;h1&gt;Hello, World!&lt;/h1&gt;
    &lt;p&gt;Welcome to HTML fundamentals.&lt;/p&gt;
  &lt;/body&gt;
&lt;/html&gt;</code></pre>
<ul>
  <li><strong>&lt;html&gt;</strong>: Root element of the page.</li>
  <li><strong>&lt;head&gt;</strong>: Metadata (title, links to CSS, etc.).</li>
  <li><strong>&lt;body&gt;</strong>: Main content visible on the page.</li>
</ul>
'],
        ['id' => 'html-2', 'title' => 'Topic #2: HTML Elements & Attributes', 'description' => 'Explore common HTML elements and their attributes.', 'difficulty' => 'beginner', 
    'content' => '
<h3>HTML Elements</h3>
<p>Elements are the building blocks of HTML, defined by tags such as <code>&lt;h1&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;div&gt;</code>, etc.</p>
<pre><code>&lt;h1&gt;This is a heading&lt;/h1&gt;
&lt;p&gt;This is a paragraph.&lt;/p&gt;
</code></pre>
<h3>Attributes</h3>
<p>Attributes provide extra information about elements. They go inside the opening tag.</p>
<pre><code>&lt;a href="https://example.com"&gt;Visit Example&lt;/a&gt;</code></pre>
<ul>
  <li><strong>href</strong>: Specifies the URL for a link.</li>
  <li><strong>src</strong>: Specifies the image source for <code>&lt;img&gt;</code> tags.</li>
</ul>
'],
        ['id' => 'html-3', 'title' => 'Topic #3: Links & Images', 'description' => 'How to add links and images to your web pages.', 'difficulty' => 'beginner', 
    'content' => '
<h3>Hyperlinks</h3>
<p>Use <code>&lt;a&gt;</code> to create hyperlinks to other pages or sites.</p>
<pre><code>&lt;a href="https://www.google.com"&gt;Go to Google&lt;/a&gt;</code></pre>
<h3>Images</h3>
<p>Use <code>&lt;img&gt;</code> to display images.</p>
<pre><code>&lt;img src="logo.png" alt="Website Logo"&gt;</code></pre>
<ul>
  <li><strong>src</strong>: Path to the image file.</li>
  <li><strong>alt</strong>: Alternative text for accessibility.</li>
</ul>
'],
        ['id' => 'html-4', 'title' => 'Topic #4: Lists & Tables', 'description' => 'Create lists and tables for structured content.', 'difficulty' => 'beginner', 
    'content' => '
<h3>Lists</h3>
<p>There are two basic types: ordered (<code>&lt;ol&gt;</code>) and unordered (<code>&lt;ul&gt;</code>).</p>
<pre><code>
&lt;ul&gt;
  &lt;li&gt;Apple&lt;/li&gt;
  &lt;li&gt;Banana&lt;/li&gt;
&lt;/ul&gt;

&lt;ol&gt;
  &lt;li&gt;First&lt;/li&gt;
  &lt;li&gt;Second&lt;/li&gt;
&lt;/ol&gt;
</code></pre>
<h3>Tables</h3>
<p>Tables organize data into rows and columns.</p>
<pre><code>
&lt;table&gt;
  &lt;tr&gt;
    &lt;th&gt;Name&lt;/th&gt;
    &lt;th&gt;Age&lt;/th&gt;
  &lt;/tr&gt;
  &lt;tr&gt;
    &lt;td&gt;Alice&lt;/td&gt;
    &lt;td&gt;21&lt;/td&gt;
  &lt;/tr&gt;
&lt;/table&gt;
</code></pre>
'],
        ['id' => 'html-5', 'title' => 'Topic #5: Forms & Input', 'description' => 'Master HTML forms and input types.', 'difficulty' => 'intermediate', 
    'content' => '
<h3>HTML Forms</h3>
<p>Forms collect user input and send it to a server. Basic form elements include <code>&lt;input&gt;</code>, <code>&lt;textarea&gt;</code>, <code>&lt;button&gt;</code>, and <code>&lt;select&gt;</code>.</p>
<pre><code>
&lt;form&gt;
  &lt;label&gt;Name:&lt;/label&gt;
  &lt;input type="text" name="username"&gt;
  &lt;input type="submit" value="Submit"&gt;
&lt;/form&gt;
</code></pre>
<ul>
  <li><strong>type</strong>: Specifies the input type (text, password, email, etc.).</li>
  <li><strong>name</strong>: The name for the data submitted.</li>
</ul>
'],
        ['id' => 'html-6', 'title' => 'Topic #6: Semantic HTML', 'description' => 'Use semantic tags for better accessibility and SEO.', 'difficulty' => 'intermediate', 
    'content' => '
<h3>Semantic Elements</h3>
<p>Semantic tags clearly describe their meaning and structure (for both browsers and developers).</p>
<ul>
  <li><code>&lt;header&gt;</code>, <code>&lt;nav&gt;</code>, <code>&lt;main&gt;</code>, <code>&lt;section&gt;</code>, <code>&lt;article&gt;</code>, <code>&lt;footer&gt;</code></li>
</ul>
<p>Example:</p>
<pre><code>
&lt;header&gt;Site Header&lt;/header&gt;
&lt;nav&gt;Main Navigation&lt;/nav&gt;
&lt;main&gt;
  &lt;article&gt;Blog Post&lt;/article&gt;
&lt;/main&gt;
&lt;footer&gt;Site Footer&lt;/footer&gt;
</code></pre>
'],
      ['id' => 'html-7', 'title' => 'Topic #7: Media Elements', 'description' => 'Embed audio, video, and other media.', 'difficulty' => 'intermediate',
    'content' => '
<h3>Embedding Media</h3>
<p>HTML lets you add audio, video, and other multimedia to your website.</p>
<h4>Audio</h4>
<pre><code>
&lt;audio controls&gt;
  &lt;source src="audio.mp3" type="audio/mpeg"&gt;
  Your browser does not support the audio element.
&lt;/audio&gt;
</code></pre>
<h4>Video</h4>
<pre><code>
&lt;video width="320" height="240" controls&gt;
  &lt;source src="movie.mp4" type="video/mp4"&gt;
  Your browser does not support the video tag.
&lt;/video&gt;
</code></pre>
'],
        ['id' => 'html-8', 'title' => 'Topic #8: HTML APIs', 'description' => 'Introduction to HTML5 APIs.', 'difficulty' => 'expert', 
    'content' => '
<h3>HTML5 APIs</h3>
<p>HTML5 introduced powerful APIs for modern web applications:</p>
<ul>
  <li><strong>Canvas API</strong>: Draw graphics using JavaScript.</li>
  <li><strong>Geolocation API</strong>: Get user location.</li>
  <li><strong>Local Storage</strong>: Store data in the browser.</li>
</ul>
<p>Example: Using Local Storage</p>
<pre><code>
&lt;script&gt;
  localStorage.setItem("username", "Alice");
  const name = localStorage.getItem("username");
&lt;/script&gt;
</code></pre>
'],
        ['id' => 'html-9', 'title' => 'Topic #9: Accessibility', 'description' => 'Make your web pages accessible to all users.', 'difficulty' => 'expert', 
    'content' => '
<h3>Accessible HTML</h3>
<p>Accessibility ensures your site works for everyone, including users with disabilities.</p>
<ul>
  <li>Use <strong>alt</strong> text for images.</li>
  <li>Ensure proper heading structure (<code>&lt;h1&gt; - &lt;h6&gt;</code>).</li>
  <li>Use semantic tags.</li>
  <li>Add <code>aria</code> attributes where necessary.</li>
</ul>
<p>Example:</p>
<pre><code>
&lt;img src="logo.png" alt="Company Logo"&gt;
&lt;button aria-label="Close"&gt;X&lt;/button&gt;
</code></pre>
'],
        ['id' => 'html-10', 'title' => 'Topic #10: Best Practices', 'description' => 'Tips and tricks for writing clean HTML.', 'difficulty' => 'expert', 
    'content' => '
<h3>HTML Best Practices</h3>
<ul>
  <li>Use semantic elements for clarity and SEO.</li>
  <li>Always include <code>alt</code> attributes for images.</li>
  <li>Keep your code clean and well-indented.</li>
  <li>Validate your HTML using tools like <a href="https://validator.w3.org/" target="_blank">W3C Validator</a>.</li>
  <li>Test your pages on multiple browsers and devices.</li>
</ul>
'],
  ],
    'css' => [
        ['id' => 'css-1', 'title' => 'Topic #1: CSS Basics', 'description' => 'Introduction to CSS syntax and selectors.', 'difficulty' => 'beginner', 
    'content' => '
<h3>What is CSS?</h3>
<p>CSS (Cascading Style Sheets) is used to style and layout web pages. It controls colors, fonts, spacing, and positioning of HTML elements.</p>
<h4>Basic Syntax</h4>
<pre><code>
selector {
  property: value;
}
</code></pre>
<p>Example:</p>
<pre><code>
p {
  color: blue;
  font-size: 16px;
}
</code></pre>
<ul>
  <li><strong>Selectors</strong> target HTML elements.</li>
  <li><strong>Properties</strong> define what to change.</li>
  <li><strong>Values</strong> set the property’s value.</li>
</ul>
'],
        ['id' => 'css-2', 'title' => 'Topic #2: Colors & Backgrounds', 'description' => 'Styling backgrounds and using color.', 'difficulty' => 'beginner', 
    'content' => '
<h3>Colors</h3>
<p>Set colors using names, HEX, RGB, or HSL.</p>
<pre><code>
body {
  background-color: #f0f0f0;
  color: rgb(40, 40, 40);
}
</code></pre>
<h3>Backgrounds</h3>
<p>Control backgrounds with <code>background</code> properties.</p>
<pre><code>
div {
  background-image: url("image.jpg");
  background-repeat: no-repeat;
  background-size: cover;
}
</code></pre>
<ul>
  <li><strong>background-color</strong>: Sets background color.</li>
  <li><strong>background-image</strong>: Adds images behind content.</li>
</ul>
'],
        ['id' => 'css-3', 'title' => 'Topic #3: Text & Fonts', 'description' => 'Control typography and font styles.', 'difficulty' => 'beginner',
    'content' => '
<h3>Text Styling</h3>
<p>Change font, size, weight, and style with CSS.</p>
<pre><code>
h1 {
  font-family: "Arial", sans-serif;
  font-size: 2em;
  font-weight: bold;
  color: #333;
}
</code></pre>
<h3>Font Properties</h3>
<ul>
  <li><strong>font-family</strong>: Sets the typeface.</li>
  <li><strong>font-size</strong>: Adjusts text size.</li>
  <li><strong>font-weight</strong>: Bold, normal, etc.</li>
  <li><strong>color</strong>: Sets text color.</li>
</ul>
'],
        ['id' => 'css-4', 'title' => 'Topic #4: Box Model', 'description' => 'Understand margin, border, padding, and content.', 'difficulty' => 'beginner', 
    'content' => '
<h3>The CSS Box Model</h3>
<p>Every HTML element is a box with four parts:</p>
<ul>
  <li><strong>Content</strong>: The actual text or image.</li>
  <li><strong>Padding</strong>: Space inside the border.</li>
  <li><strong>Border</strong>: The line around the box.</li>
  <li><strong>Margin</strong>: Space outside the border.</li>
</ul>
<pre><code>
div {
  margin: 20px;
  padding: 10px;
  border: 2px solid #888;
}
</code></pre>
<p>Understanding the box model helps with layout and spacing.</p>
'],
        ['id' => 'css-5', 'title' => 'Topic #5: Flexbox', 'description' => 'Modern layout with CSS Flexbox.', 'difficulty' => 'intermediate', 
    'content' => '
<h3>CSS Flexbox</h3>
<p>Flexbox is a modern layout tool for arranging items in rows or columns.</p>
<pre><code>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
}
</code></pre>
<ul>
  <li><strong>display: flex</strong>: Enables flexbox.</li>
  <li><strong>justify-content</strong>: Controls horizontal alignment.</li>
  <li><strong>align-items</strong>: Controls vertical alignment.</li>
</ul>
<p>Flexbox makes it easy to build responsive layouts!</p>
'],
        ['id' => 'css-6', 'title' => 'Topic #6:  Grid Layout', 'description' => 'Advanced layouts with CSS Grid.', 'difficulty' => 'intermediate', 
    'content' => '
<h3>CSS Grid Layout</h3>
<p>Grid is the most powerful CSS layout system for structuring content in rows and columns.</p>
<pre><code>
.grid-container {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 10px;
}
</code></pre>
<ul>
  <li><strong>display: grid</strong>: Enables grid layout.</li>
  <li><strong>grid-template-columns</strong>: Defines columns.</li>
  <li><strong>gap</strong>: Sets spacing between items.</li>
</ul>
'],
        ['id' => 'css-7', 'title' => 'Topic #7: Transitions & Animations', 'description' => 'Add motion to your web pages.', 'difficulty' => 'intermediate', 
    'content' => '
<h3>CSS Transitions</h3>
<p>Transitions create smooth changes between property values.</p>
<pre><code>
button {
  background: #007bff;
  transition: background 0.3s;
}
button:hover {
  background: #0056b3;
}
</code></pre>
<h3>CSS Animations</h3>
<p>Animations let you animate multiple properties.</p>
<pre><code>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
div {
  animation: fadeIn 1s ease-in;
}
</code></pre>
'],
        ['id' => 'css-8', 'title' => 'Topic #8: Responsive Design', 'description' => 'Make your site look great on any device.', 'difficulty' => 'expert', 
    'content' => '
<h3>Responsive Design</h3>
<p>Make your site look good on all devices using media queries.</p>
<pre><code>
@media (max-width: 600px) {
  body {
    font-size: 14px;
  }
  .menu {
    flex-direction: column;
  }
}
</code></pre>
<ul>
  <li><strong>Media queries</strong> adapt styles for different screen sizes.</li>
  <li>Use relative units (%, em, rem) for flexible layouts.</li>
</ul>
'],
        ['id' => 'css-9', 'title' => 'Topic #9: CSS Variables', 'description' => 'Reusable values with custom properties.', 'difficulty' => 'expert', 
    'content' => '
<h3>CSS Variables (Custom Properties)</h3>
<p>Variables help you reuse values and make your styles easier to update.</p>
<pre><code>
:root {
  --main-color: #ff6600;
}
h1 {
  color: var(--main-color);
}
</code></pre>
<ul>
  <li>Define variables inside <code>:root</code> for global scope.</li>
  <li>Use <code>var(--variable-name)</code> to apply them.</li>
</ul>
'],
        ['id' => 'css-10', 'title' => 'Topic #10: CSS Best Practices', 'description' => 'Write maintainable and scalable CSS.', 'difficulty' => 'expert', 
    'content' => '
<h3>CSS Best Practices</h3>
<ul>
  <li>Keep your CSS organized and well-commented.</li>
  <li>Use classes for styling instead of element selectors.</li>
  <li>Use CSS variables for consistent colors and spacing.</li>
  <li>Test your styles on different browsers and devices.</li>
  <li>Minimize repetition by grouping shared styles.</li>
</ul>
'],
    ],
    'bootstrap' => [
        ['id' => 'bootstrap-1', 'title' => 'Topic #1: Bootstrap Introduction', 'description' => 'What is Bootstrap and why use it?', 'difficulty' => 'beginner',
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
        ['id' => 'bootstrap-2', 'title' => 'Topic #2: Bootstrap Grid', 'description' => 'Responsive layouts with the grid system.', 'difficulty' => 'beginner',
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
        ['id' => 'bootstrap-3', 'title' => 'Topic #3: Bootstrap Components', 'description' => 'Buttons, cards, navbars, and more.', 'difficulty' => 'beginner',
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
        'content' => '
<h3>Bootstrap Utilities & Helpers</h3>
<p>Bootstrap comes with hundreds of utility classes that help you style elements quickly without writing custom CSS.</p>
<ul>
  <li><strong>Spacing:</strong> <code>.m-2</code> (margin), <code>.p-3</code> (padding)</li>
  <li><strong>Text:</strong> <code>.text-center</code>, <code>.text-danger</code></li>
  <li><strong>Display:</strong> <code>.d-flex</code>, <code>.d-none</code></li>
  <li><strong>Colors:</strong> <code>.bg-primary</code>, <code>.text-success</code></li>
  <li><strong>Borders:</strong> <code>.border</code>, <code>.rounded</code></li>
</ul>
<pre><code>
&lt;div class="bg-warning p-3 text-center rounded"&gt;
  Quick styled box!
&lt;/div&gt;
</code></pre>
<p>For a full list of utilities, see <a href="https://getbootstrap.com/docs/5.3/utilities/" target="_blank">Bootstrap Utilities</a>.</p>
'],
        ['id' => 'bootstrap-5', 'title' => 'Topic 5: Customizing Bootstrap', 'description' => 'Override and extend Bootstrap styles.', 'difficulty' => 'intermediate', 
    'content' => '
<h3>Customizing Bootstrap</h3>
<p>Override Bootstrap’s default styles to match your brand or design needs.</p>
<ul>
  <li><strong>Use your own CSS:</strong> Write custom styles after the Bootstrap CSS link.</li>
  <li><strong>Customize Sass variables:</strong> Change colors, fonts, and breakpoints by editing Bootstrap’s source Sass files.</li>
</ul>
<pre><code>
/* Example: Override primary color */
:root {
  --bs-primary: #ff6600;
}
</code></pre>
<p>Learn more about customization at <a href="https://getbootstrap.com/docs/5.3/customize/" target="_blank">Bootstrap Customize</a>.</p>
'],
        ['id' => 'bootstrap-6', 'title' => 'Topic 6: Bootstrap JS Plugins', 'description' => 'Add interactivity with Bootstrap plugins.', 'difficulty' => 'intermediate', 
    'content' => '<h3>Bootstrap JavaScript Plugins</h3>
<p>Bootstrap includes interactive plugins powered by JavaScript, such as dropdowns, modals, and tooltips.</p>
<ul>
  <li><strong>Modals</strong>: Pop-up windows for dialogs.</li>
  <li><strong>Tooltips</strong>: Small pop-up info boxes.</li>
  <li><strong>Dropdowns</strong>: Menus for navigation or actions.</li>
  <li><strong>Collapse</strong>: Hide/show content panels.</li>
</ul>
<p>To use plugins, add Bootstrap’s JS bundle:</p>
<pre><code>
&lt;script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"&gt;&lt;/script&gt;
</code></pre>
<p>Example: Trigger a tooltip</p>
<pre><code>
&lt;button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" title="Tooltip text"&gt;
  Hover me
&lt;/button&gt;
</code></pre>'],
        ['id' => 'bootstrap-7', 'title' => 'Topic 7: Forms & Validation', 'description' => 'Build and validate forms with Bootstrap.', 'difficulty' => 'intermediate', 
    'content' => '<h3>Bootstrap Forms & Validation</h3>
<p>Build beautiful forms and validate inputs easily with Bootstrap classes.</p>
<ul>
  <li><strong>Form controls:</strong> <code>.form-control</code> for inputs, <code>.form-check</code> for checkboxes.</li>
  <li><strong>Layout:</strong> Use <code>.row</code> and <code>.col</code> for grid forms.</li>
  <li><strong>Validation:</strong> Add <code>.is-valid</code> or <code>.is-invalid</code> classes for feedback.</li>
</ul>
<pre><code>
&lt;form&gt;
  &lt;input type="text" class="form-control is-valid" placeholder="Valid input"&gt;
  &lt;input type="text" class="form-control is-invalid" placeholder="Invalid input"&gt;
  &lt;div class="valid-feedback"&gt;Looks good!&lt;/div&gt;
  &lt;div class="invalid-feedback"&gt;Please enter a valid value.&lt;/div&gt;
&lt;/form&gt;
</code></pre>
<p>Read more at <a href="https://getbootstrap.com/docs/5.3/forms/validation/" target="_blank">Bootstrap Form Validation</a>.</p>'],
        ['id' => 'bootstrap-8', 'title' => 'Topic 8: Advanced Components', 'description' => 'Carousels, modals, and more.', 'difficulty' => 'expert', 
    'content' => '<h3>Bootstrap Advanced Components</h3>
<p>Go beyond basics with advanced UI components:</p>
<ul>
  <li><strong>Carousel</strong>: Create image or content sliders.</li>
  <li><strong>Accordion</strong>: Expand/collapse sections for FAQs or content.</li>
  <li><strong>Toast</strong>: Show notifications in small pop-up boxes.</li>
</ul>
<pre><code>
&lt;div id="carouselExample" class="carousel slide"&gt;
  &lt;div class="carousel-inner"&gt;
    &lt;div class="carousel-item active"&gt;
      &lt;img src="img1.jpg" class="d-block w-100" alt="..."&gt;
    &lt;/div&gt;
    &lt;div class="carousel-item"&gt;
      &lt;img src="img2.jpg" class="d-block w-100" alt="..."&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev"&gt;
    &lt;span class="carousel-control-prev-icon" aria-hidden="true"&gt;&lt;/span&gt;
    &lt;span class="visually-hidden"&gt;Previous&lt;/span&gt;
  &lt;/button&gt;
  &lt;button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next"&gt;
    &lt;span class="carousel-control-next-icon" aria-hidden="true"&gt;&lt;/span&gt;
    &lt;span class="visually-hidden"&gt;Next&lt;/span&gt;
  &lt;/button&gt;
&lt;/div&gt;
</code></pre>
<p>See all advanced components at <a href="https://getbootstrap.com/docs/5.3/components/" target="_blank">Bootstrap Components</a>.</p>'],
        ['id' => 'bootstrap-9', 'title' => 'Topic 9: Accessibility in Bootstrap', 'description' => 'Make Bootstrap sites accessible.', 'difficulty' => 'expert', 
    'content' => '<h3>Accessibility in Bootstrap</h3>
<p>Bootstrap helps make sites accessible by default, but you can improve further:</p>
<ul>
  <li>Use semantic HTML and Bootstrap’s accessible components.</li>
  <li>Add <code>aria-*</code> attributes for screen readers (e.g., <code>aria-label</code>, <code>aria-expanded</code>).</li>
  <li>Ensure colors and contrasts meet accessibility standards.</li>
</ul>
<p>Example:</p>
<pre><code>
&lt;button class="btn btn-primary" aria-label="Close"&gt;X&lt;/button&gt;
</code></pre>
<p>Learn more at <a href="https://getbootstrap.com/docs/5.3/accessibility/" target="_blank">Bootstrap Accessibility</a>.</p>'],
        ['id' => 'bootstrap-10', 'title' => 'Topic 10: Bootstrap Best Practices', 'description' => 'Tips for scalable Bootstrap projects.', 'difficulty' => 'expert', 
    'content' => '<h3>Bootstrap Best Practices</h3>
<ul>
  <li>Use utility classes for quick styling and less custom CSS.</li>
  <li>Keep your markup clean and organized.</li>
  <li>Customize variables or use your own CSS for brand consistency.</li>
  <li>Test responsiveness on different devices.</li>
  <li>Update to the latest Bootstrap version for new features and security.</li>
</ul>'],
        ],
    'javascript' => [
        ['id' => 'js-1', 'title' => 'Topic #1: JS Fundamentals', 'description' => 'Variables, data types, and operators.', 'difficulty' => 'beginner', 
        'content' => '<h3>JavaScript Fundamentals</h3>
<p>JavaScript is a programming language for web development. It lets you add interactivity, logic, and dynamic content to your pages.</p>
<h4>Variables</h4>
<pre><code>
let name = "Alice";
const age = 21;
var isStudent = true;
</code></pre>
<ul>
  <li><strong>let</strong>: Block-scoped variable.</li>
  <li><strong>const</strong>: Block-scoped constant (cannot be reassigned).</li>
  <li><strong>var</strong>: Function-scoped variable (legacy, use let/const instead).</li>
</ul>
<h4>Data Types</h4>
<ul>
  <li>Number, String, Boolean, Object, Array, Null, Undefined</li>
</ul>
<h4>Operators</h4>
<pre><code>
let sum = 3 + 5;
let isEqual = (a === b);
</code></pre>'],
        ['id' => 'js-2', 'title' => 'Topic #2: Control Flow', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 
        'content' => '<h3>Control Flow</h3>
<p>Control the logic of your programs using if statements, loops, and conditions.</p>
<h4>If Statements</h4>
<pre><code>
if (score &gt; 80) {
  alert("Great job!");
} else {
  alert("Keep practicing!");
}
</code></pre>
<h4>Loops</h4>
<pre><code>
for (let i = 0; i &lt; 5; i++) {
  console.log(i);
}

let n = 0;
while (n &lt; 3) {
  console.log(n);
  n++;
}
</code></pre>
<h4>Logic</h4>
<p>Use <code>&amp;&amp;</code> (and), <code>||</code> (or), <code>!</code> (not) to build complex conditions.</p>'],
        ['id' => 'js-3', 'title' => 'Topic #3: Functions', 'description' => 'Defining and using functions.', 'difficulty' => 'beginner', 
        'content' => '<h3>Functions</h3>
<p>Functions group code to reuse and organize logic.</p>
<pre><code>
function greet(name) {
  return "Hello, " + name + "!";
}
console.log(greet("Alice"));
</code></pre>
<h4>Arrow Functions (ES6)</h4>
<pre><code>
const add = (a, b) =&gt; a + b;
console.log(add(2, 3));
</code></pre>'],
        ['id' => 'js-4', 'title' => 'Topic #4: DOM Basics', 'description' => 'Manipulate the web page with JavaScript.', 'difficulty' => 'beginner', 
        'content' => '<h3>DOM Basics</h3>
<p>The DOM (Document Object Model) lets JavaScript interact with HTML elements.</p>
<pre><code>
const heading = document.getElementById("main-title");
heading.textContent = "New Title!";
</code></pre>
<ul>
  <li><code>getElementById</code>, <code>querySelector</code>: Select elements.</li>
  <li><code>textContent</code>, <code>innerHTML</code>: Change content.</li>
  <li><code>style</code>: Change CSS.</li>
</ul>
<pre><code>
document.body.style.backgroundColor = "lightblue";
</code></pre>'],
        ['id' => 'js-5', 'title' => 'Topic #5: Events', 'description' => 'Respond to user actions.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Events</h3>
<p>Respond to user actions like clicks, keypresses, and more.</p>
<pre><code>
document.getElementById("btn").addEventListener("click", function() {
  alert("Button clicked!");
});
</code></pre>
<ul>
  <li><code>addEventListener</code>: Attach event handlers.</li>
  <li>Common events: <code>click</code>, <code>mouseover</code>, <code>keydown</code>, <code>submit</code></li>
</ul>'],
        ['id' => 'js-6', 'title' => 'Topic #6: Objects & Arrays', 'description' => 'Work with complex data structures.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Objects</h3>
<pre><code>
const user = {
  name: "Bob",
  age: 20,
  isStudent: true
};
console.log(user.name);
</code></pre>
<h3>Arrays</h3>
<pre><code>
const fruits = ["apple", "banana", "cherry"];
console.log(fruits[1]); // "banana"
fruits.push("orange");
</code></pre>'],
        ['id' => 'js-7', 'title' => 'Topic #7:ES6+ Features', 'description' => 'Modern JavaScript syntax and features.', 'difficulty' => 'intermediate', 
        'content' => '<h3>ES6+ Modern JavaScript Features</h3>
<ul>
  <li><strong>let</strong> and <strong>const</strong> for variables</li>
  <li><strong>Arrow Functions</strong>: <code>(x) =&gt; x * 2</code></li>
  <li><strong>Template Literals</strong>: <code>`Hello, ${name}!`</code></li>
  <li><strong>Destructuring</strong>: <code>const [a, b] = arr;</code> or <code>const {name} = user;</code></li>
  <li><strong>Default Parameters</strong>: <code>function f(x = 1) {...}</code></li>
  <li><strong>Spread Operator</strong>: <code>const arr2 = [...arr1, 4]</code></li>
</ul>'],
        ['id' => 'js-8', 'title' => 'Topic #8: Async JS', 'description' => 'Promises, async/await, and AJAX.', 'difficulty' => 'expert', 
        'content' => '<h3>Async JavaScript</h3>
<p>Handle asynchronous operations like network requests and timers.</p>
<h4>Promises</h4>
<pre><code>
const promise = fetch("https://api.example.com/data");
promise.then(response =&gt; response.json()).then(data =&gt; {
  console.log(data);
});
</code></pre>
<h4>Async/Await</h4>
<pre><code>
async function getData() {
  const response = await fetch("https://api.example.com/data");
  const data = await response.json();
  console.log(data);
}
getData();
</code></pre>'],
        ['id' => 'js-9', 'title' => 'Topic #9: Modules & Tooling', 'description' => 'Organize and build JS projects.', 'difficulty' => 'expert', 
        'content' => '<h3>Modules & Tooling</h3>
<p>Organize code into reusable files and use tools for building projects.</p>
<h4>Modules (ES6)</h4>
<pre><code>
// math.js
export function add(a, b) {
  return a + b;
}

// app.js
import { add } from "./math.js";
console.log(add(2, 3));
</code></pre>
<h4>Tooling</h4>
<ul>
  <li>Use npm/yarn for package management.</li>
  <li>Use build tools like Webpack, Vite, or Parcel.</li>
</ul>'],
        ['id' => 'js-10', 'title' => 'Topic #10: JS Best Practices', 'description' => 'Write clean and efficient JavaScript.', 'difficulty' => 'expert', 
        'content' => '<h3>JavaScript Best Practices</h3>
<ul>
  <li>Use <code>let</code> and <code>const</code> for variables.</li>
  <li>Comment your code and keep it readable.</li>
  <li>Break logic into functions and modules.</li>
  <li>Test your code and handle errors.</li>
  <li>Keep up with modern JavaScript features.</li>
</ul>'],
        ],
    'python' => [
        ['id' => 'python-1', 'title' => 'Topic #1: Python Basics', 'description' => 'Syntax, variables, and data types.', 'difficulty' => 'beginner', 
        'content' => '<h3>Python Basics</h3>
<p>Python is a popular, beginner-friendly programming language known for its simple syntax and readability.</p>
<h4>Variables & Data Types</h4>
<pre><code>
name = "Alice"
age = 21
is_student = True
</code></pre>
<ul>
  <li>Common data types: <strong>int</strong>, <strong>float</strong>, <strong>str</strong>, <strong>bool</strong>, <strong>list</strong>, <strong>dict</strong></li>
</ul>
<h4>Printing</h4>
<pre><code>
print("Hello, World!")
</code></pre>
'],
        ['id' => 'python-2', 'title' => 'Topic #2: Control Structures', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 
        'content' => '<h3>Control Structures</h3>
<p>Control the flow of your programs using if statements and loops.</p>
<h4>If Statements</h4>
<pre><code>
if age &gt;= 18:
    print("Adult")
else:
    print("Minor")
</code></pre>
<h4>Loops</h4>
<pre><code>
for i in range(5):
    print(i)

n = 0
while n &lt; 3:
    print(n)
    n += 1
</code></pre>
'],
        ['id' => 'python-3', 'title' => 'Topic #3: Functions', 'description' => 'Defining and using functions.', 'difficulty' => 'beginner', 
        'content' => '<h3>Functions</h3>
<p>Functions let you organize code into reusable blocks.</p>
<pre><code>
def greet(name):
    return "Hello, " + name + "!"

print(greet("Alice"))
</code></pre>
<ul>
  <li>Use <code>def</code> to define a function.</li>
  <li>Return values using <code>return</code>.</li>
</ul>'],
        ['id' => 'python-4', 'title' => 'Topic #4: Data Structures', 'description' => 'Lists, tuples, sets, and dictionaries.', 'difficulty' => 'beginner', 
        'content' => '<h3>Data Structures</h3>
<ul>
  <li><strong>Lists</strong>: Ordered, mutable collections.</li>
  <li><strong>Tuples</strong>: Ordered, immutable collections.</li>
  <li><strong>Sets</strong>: Unordered collections of unique elements.</li>
  <li><strong>Dictionaries</strong>: Key-value pairs.</li>
</ul>
<pre><code>
fruits = ["apple", "banana", "cherry"]
info = {"name": "Alice", "age": 21}
</code></pre>'],
        ['id' => 'python-5', 'title' => 'Topic #5: OOP in Python', 'description' => 'Classes and objects in Python.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Object-Oriented Programming (OOP)</h3>
<p>Python supports OOP with classes and objects.</p>
<pre><code>
class Person:
    def __init__(self, name, age):
        self.name = name
        self.age = age

    def greet(self):
        print("Hello, my name is", self.name)

alice = Person("Alice", 21)
alice.greet()
</code></pre>
<ul>
  <li><code>__init__</code>: Constructor method.</li>
  <li><code>self</code>: Refers to the instance.</li>
</ul>'],
        ['id' => 'python-6', 'title' => 'Topic #6: Modules & Packages', 'description' => 'Organize and reuse code.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Modules & Packages</h3>
<p>Modules help you organize code, and packages group modules together.</p>
<h4>Importing Modules</h4>
<pre><code>
import math
print(math.sqrt(16))
</code></pre>
<h4>Creating Modules</h4>
<pre><code>
# mymodule.py
def add(a, b):
    return a + b

# main.py
import mymodule
print(mymodule.add(2, 3))
</code></pre>'],
        ['id' => 'python-7', 'title' => 'Topic #7: File I/O', 'description' => 'Read and write files in Python.', 'difficulty' => 'intermediate', 
        'content' => '<h3>File Input & Output</h3>
<p>Read from and write to files using Python’s built-in functions.</p>
<pre><code>
# Writing to a file
with open("output.txt", "w") as f:
    f.write("Hello, file!")

# Reading from a file
with open("output.txt", "r") as f:
    content = f.read()
    print(content)
</code></pre>'],
        ['id' => 'python-8', 'title' => 'Topic #8: Error Handling', 'description' => 'Exceptions and debugging.', 'difficulty' => 'expert', 
        'content' => '<h3>Error Handling</h3>
<p>Handle exceptions gracefully using <code>try</code> and <code>except</code>.</p>
<pre><code>
try:
    result = 10 / 0
except ZeroDivisionError:
    print("Cannot divide by zero!")
finally:
    print("This runs no matter what.")
</code></pre>
<p>Common exceptions: <code>ValueError</code>, <code>TypeError</code>, <code>IOError</code>, etc.</p>'],
        ['id' => 'python-9', 'title' => 'Topic #9: Advanced Topics', 'description' => 'Decorators, generators, and more.', 'difficulty' => 'expert', 
        'content' => '<h3>Advanced Python Topics</h3>
<ul>
  <li><strong>Decorators</strong>: Modify functions.</li>
  <li><strong>Generators</strong>: Yield items one at a time.</li>
  <li><strong>List Comprehensions</strong>: Compact ways to build lists.</li>
</ul>
<pre><code>
# Decorator
def logger(func):
    def wrapper():
        print("Calling function...")
        func()
    return wrapper

@logger
def say_hi():
    print("Hi!")

say_hi()

# Generator
def count_up(n):
    i = 0
    while i &lt; n:
        yield i
        i += 1

for num in count_up(3):
    print(num)

# List comprehension
squares = [x*x for x in range(5)]
print(squares)
</code></pre>'],
        ['id' => 'python-10', 'title' => 'Topic #10: Python Best Practices', 'description' => 'Tips for writing great Python code.', 'difficulty' => 'expert', 
        'content' => '<h3>Python Best Practices</h3>
<ul>
  <li>Use meaningful variable and function names.</li>
  <li>Follow PEP 8 style guide for formatting.</li>
  <li>Write modular, reusable code.</li>
  <li>Comment your code and use docstrings.</li>
  <li>Test your code and handle exceptions.</li>
</ul>'],
        ],
    'java' => [
        ['id' => 'java-1', 'title' => 'Topic #1: Java Basics', 'description' => 'Syntax, variables, and data types.', 'difficulty' => 'beginner', 
        'content' => '<h3>Java Basics</h3>

        <ul> <li><strong>Syntax:</strong> Java code is organized into classes and methods. Statements end with a semicolon (<code>;</code>).
        </li> <li><strong>Variables:</strong> Declare variables with a specific type before use, 
        for example: <code>int age = 25;</code> or <code>String name = "Alice";</code></li> <li><strong>Data 
        Types:</strong> Java has primitive types (<code>int</code>, <code>double</code>, <code>char</code>, <code>boolean</code>) and reference types (like <code>String</code> and arrays).
        </li> <li><strong>Operators:</strong> Use arithmetic (<code>+</code>, <code>-</code>, <code>*</code>, <code>/</code>), relational (<code>==</code>, <code>!=</code>, <code>&lt;</code>, 
        <code>&gt;</code>), and logical (<code>&&</code>, <code>||</code>, <code>!</code>) operators to work with data.</li> <li><strong>Comments:</strong> 
        Add single-line comments with <code>//</code> and multi-line comments with <code>/* ... */</code> to explain your code.</li> </ul> 
        // <pre><code>public class Main { public static void main(String[] args) { int age = 25; String name = "Alice"; boolean isJavaFun = true; System.out.println("Hello, " + name + "! Age: " + age); // Output: Hello, Alice! Age: 25 } } </code></pre>'],
        ['id' => 'java-2', 'title' => 'Topic #2: Control Flow', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 
        'content' => '<h3>Control Flow</h3>

<ul> <li><strong>If Statements:</strong> Use <code>if</code>, <code>else if</code>, and <code>else</code> to make decisions based on conditions.</li> <li><strong>Switch Statements:</strong> Use <code>switch</code> to select one of many code blocks to execute based on the value of a variable.</li> <li><strong>Loops:</strong> Implement <code>for</code>, <code>while</code>, and <code>do-while</code> loops to repeat code.</li> <li><strong>Break and Continue:</strong> Use <code>break</code> to exit a loop early, and <code>continue</code> to skip to the next iteration.</li> <li><strong>Logic:</strong> Combine conditions using <code>&&</code> (and), <code>||</code> (or), and <code>!</code> (not) to create complex conditions.</li> </ul> <pre><code>public class Main { public static void main(String[] args) { int number = 10; // If-else statement if (number > 0) { System.out.println("Number is positive"); } else if (number == 0) { System.out.println("Number is zero"); } else { System.out.println("Number is negative"); }
text
    // Switch statement
    int day = 2;
    switch (day) {
        case 1:
            System.out.println("Monday");
            break;
        case 2:
            System.out.println("Tuesday");
            break;
        default:
            System.out.println("Other day");
    }

    // For loop
    for (int i = 0; i < 5; i++) {
        if (i == 3) continue; // Skip 3
        System.out.println(i);
    }

    // While loop
    int count = 0;
    while (count < 3) {
        System.out.println("Count: " + count);
        count++;
    }

    // Do-while loop
    int x = 0;
    do {
        System.out.println("x is " + x);
        x++;
    } while (x < 2);
}
}
</code></pre>'],
        ['id' => 'java-3', 'title' => 'Topic #3: Methods', 'description' => 'Defining and using methods.', 'difficulty' => 'beginner', 
        'content' => '<h3>Methods</h3>

        <ul> <li><strong>Method Definition:</strong> Define methods with a return type, name, and parameters. 
        For example: <code>public int add(int a, int b) { return a + b; }</code></li> <li><strong>Method Invocation:</strong> Call methods by their name and pass arguments. For example: <code>int result = add(2, 3);</code></li> <li><strong>Method Overloading:</strong> Define multiple methods with the same name but different parameters to perform similar but distinct operations.</li> 
        <li><strong>Method Overriding:</strong> Implement methods from parent classes to provide specific behavior in child classes.</li> <li><strong>Static Methods:</strong> Use <code>static</code> to define methods that belong to the class rather than an instance.</li> </ul> <pre><code>public class Calculator { // Method Overloading public int add(int a, int b) { return a + b; } public double add(double a, double b) { return a + b; } // Static Method public static int multiply(int a, int b) { return a * b; } public static void main(String[] args) { Calculator calc = new Calculator(); int result1 = calc.add(2, 3); // Method invocation double result2 = calc.add(2.5, 3.5); // Overloaded method int result3 = Calculator.multiply(4, 5); // Static method invocation System.out.println("Result 1: " + result1); System.out.println("Result 2: " + result2); System.out.println("Result 3: " + result3); } } </code></pre>'],
        ['id' => 'java-4', 'title' => 'Topic #4: OOP Concepts', 'description' => 'Classes, objects, and inheritance.', 'difficulty' => 'beginner', 
        'content' => '<h3>OOP Concepts</h3> <ul> <li><strong>Classes:</strong> Blueprints for creating objects. Define properties (fields) and behaviors (methods). For example: <code>public class Car { ... }</code></li> <li><strong>Objects:</strong> Instances of classes. Each object has its own values for the class\'s fields. For example: <code>Car myCar = new Car();</code></li> <li><strong>Encapsulation:</strong> Bundling data (fields) and methods together, and restricting direct access to some components using <code>private</code>, <code>public</code>, or <code>protected</code> keywords.</li> <li><strong>Inheritance:</strong> Allows a class to inherit fields and methods from another class using the <code>extends</code> keyword. For example: <code>public class ElectricCar extends Car { ... }</code></li> <li><strong>Polymorphism:</strong> The ability for different classes to be treated as instances of the same parent class, often using method overriding.</li> <li><strong>Abstraction:</strong> Hiding complex implementation details and showing only the necessary features of an object.</li> </ul> <pre><code>// Example of classes, objects, and inheritance class Animal { String name; public void speak() { System.out.println("The animal makes a sound."); } }
class Dog extends Animal {
public void speak() { // Method overriding
System.out.println("Woof!");
}
}

public class Main {
public static void main(String[] args) {
Animal genericAnimal = new Animal();
Dog myDog = new Dog();
genericAnimal.speak(); // Output: The animal makes a sound.
myDog.speak(); // Output: Woof!
}
}
</code></pre>'],
        ['id' => 'java-5', 'title' => 'Topic #5: Collections', 'description' => 'Lists, sets, and maps.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Collections</h3>

        <ul> <li><strong>Lists:</strong> Ordered collections that allow duplicates. Common implementations include <code>ArrayList</code> and <code>LinkedList</code>. Use when you need indexed access.</li> <li><strong>Sets:</strong> Collections that do not allow duplicates. Common implementations include <code>HashSet</code> and <code>TreeSet</code>. Use when uniqueness is important.</li> <li><strong>Maps:</strong> Key-value pairs where each key maps to a value. Common implementations include <code>HashMap</code> and <code>TreeMap</code>. Use for fast lookup by key.</li> <li><strong>Iteration:</strong> Use enhanced <code>for</code> loops or iterators to traverse collections.</li> <li><strong>Generics:</strong> Collections use generics to specify the type of elements they contain, improving type safety.</li> </ul> <pre><code>import java.util.*;
        public class CollectionsExample {
        public static void main(String[] args) {
        // List example
        List<String> fruits = new ArrayList<>();
        fruits.add("Apple");
        fruits.add("Banana");
        fruits.add("Apple"); // Duplicates allowed
        System.out.println("List: " + fruits);
        
        text
            // Set example
            Set<String> uniqueFruits = new HashSet<>();
            uniqueFruits.add("Apple");
            uniqueFruits.add("Banana");
            uniqueFruits.add("Apple"); // Duplicate ignored
            System.out.println("Set: " + uniqueFruits);
        
            // Map example
            Map<String, Integer> fruitCounts = new HashMap<>();
            fruitCounts.put("Apple", 3);
            fruitCounts.put("Banana", 2);
            System.out.println("Map: " + fruitCounts);
        
            // Iterating over a list
            for (String fruit : fruits) {
                System.out.println("Fruit: " + fruit);
            }
        }
        }
        </code></pre>'],
        ['id' => 'java-6', 'title' => 'Topic #6: Exception Handling', 'description' => 'Try-catch and error management.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Exception Handling</h3>

<ul> <li><strong>Exceptions:</strong> Errors that occur during program execution. Java distinguishes between checked and unchecked exceptions.</li> <li><strong>Try-Catch:</strong> Use <code>try</code> and <code>catch</code> blocks to handle exceptions and prevent program crashes.</li> <li><strong>Finally:</strong> The <code>finally</code> block executes code after <code>try</code> and <code>catch</code>, regardless of whether an exception occurred.</li> <li><strong>Throwing Exceptions:</strong> Use <code>throw</code> to manually trigger an exception.</li> <li><strong>Custom Exceptions:</strong> Create your own exception classes by extending <code>Exception</code> or <code>RuntimeException</code>.</li> </ul> <pre><code>public class ExceptionExample { public static void main(String[] args) { try { int result = divide(10, 0); System.out.println("Result: " + result); } catch (ArithmeticException e) { System.out.println("Error: " + e.getMessage()); } finally { System.out.println("Operation complete."); } }
text
public static int divide(int a, int b) {
    if (b == 0) {
        throw new ArithmeticException("Cannot divide by zero");
    }
    return a / b;
}
}
</code></pre>'],
        ['id' => 'java-7', 'title' => 'Topic #7: File I/O', 'description' => 'Read and write files in Java.', 'difficulty' => 'intermediate', 
        'content' => '<h3>File I/O</h3>

<ul> <li><strong>Reading Files:</strong> Use classes like <code>FileReader</code>, <code>BufferedReader</code>, and <code>Scanner</code> to read data from files.</li> <li><strong>Writing Files:</strong> Use <code>FileWriter</code> and <code>BufferedWriter</code> to write data to files.</li> <li><strong>Try-with-Resources:</strong> Automatically closes file resources using the <code>try-with-resources</code> statement for safer file handling.</li> <li><strong>Exception Handling:</strong> Always handle <code>IOException</code> when working with files to manage errors gracefully.</li> </ul> <pre><code>import java.io.*;
public class FileIOExample {
public static void main(String[] args) {
// Writing to a file
try (BufferedWriter writer = new BufferedWriter(new FileWriter("output.txt"))) {
writer.write("Hello, Java File I/O!");
} catch (IOException e) {
System.out.println("Write error: " + e.getMessage());
}

text
    // Reading from a file
    try (BufferedReader reader = new BufferedReader(new FileReader("output.txt"))) {
        String line;
        while ((line = reader.readLine()) != null) {
            System.out.println("Read: " + line);
        }
    } catch (IOException e) {
        System.out.println("Read error: " + e.getMessage());
    }
}
}
</code></pre>'],
        ['id' => 'java-8', 'title' => 'Topic #8: Multithreading', 'description' => 'Concurrency in Java.', 'difficulty' => 'expert', 
        'content' => '<h3>Multithreading</h3>

<ul> <li><strong>Threads:</strong> A thread is a lightweight process. Java supports multithreading to perform multiple tasks simultaneously.</li> <li><strong>Creating Threads:</strong> Create threads by extending the <code>Thread</code> class or implementing the <code>Runnable</code> interface.</li> <li><strong>Starting Threads:</strong> Use the <code>start()</code> method to begin thread execution.</li> <li><strong>Synchronization:</strong> Use the <code>synchronized</code> keyword to control access to shared resources and prevent race conditions.</li> <li><strong>Thread Communication:</strong> Use methods like <code>wait()</code>, <code>notify()</code>, and <code>notifyAll()</code> for inter-thread communication.</li> <li><strong>Executors:</strong> Use the <code>ExecutorService</code> framework for advanced thread management and pooling.</li> </ul> <pre><code>// Creating a thread by implementing Runnable class MyRunnable implements Runnable { public void run() { System.out.println("Thread is running: " + Thread.currentThread().getName()); } }
public class MultithreadingExample {
public static void main(String[] args) {
Thread thread1 = new Thread(new MyRunnable());
Thread thread2 = new Thread(new MyRunnable());
thread1.start();
thread2.start();

text
    // Using synchronization
    synchronized (MultithreadingExample.class) {
        System.out.println("Synchronized block accessed by: " + Thread.currentThread().getName());
    }
}
}
</code></pre>'],
        ['id' => 'java-9', 'title' => 'Topic #9: Java Streams', 'description' => 'Functional programming with streams.', 'difficulty' => 'expert', 
        'content' => '<h3>Java Streams</h3>

<ul> <li><strong>Stream:</strong> A sequence of elements supporting sequential and parallel aggregate operations.</li> <li><strong>Stream Operations:</strong> Use <code>map</code>, <code>filter</code>, <code>reduce</code>, and other operations to process collections of data.</li> <li><strong>Intermediate Operations:</strong> Operations that return a stream (e.g., <code>filter</code>, <code>map</code>).</li> <li><strong>Terminal Operations:</strong> Operations that produce a result or a side effect (e.g., <code>forEach</code>, <code>collect</code>).</li> <li><strong>Parallel Streams:</strong> Use parallel streams to perform operations on multiple threads for better performance.</li> </ul> <pre><code>import java.util.List;
import java.util.stream.Collectors;
public class StreamExample {
public static void main(String[] args) {
List<String> names = List.of("Alice", "Bob", "Charlie", "David");

// Filtering and mapping names
List<String> filteredNames = names.stream()
    .filter(name -> name.startsWith("A"))
    .map(String::toUpperCase)
    .collect(Collectors.toList());

System.out.println("Filtered Names: " + filteredNames);
}
}
</code></pre>'],
        ['id' => 'java-10', 'title' => 'Topic #10: Java Best Practices', 'description' => 'Tips for writing robust Java code.', 'difficulty' => 'expert', 
        'content' => '<h3>Java Best Practices</h3>

<p> Writing robust Java code involves following established best practices that enhance readability, maintainability, and performance. Always use meaningful variable and method names to make your code self-explanatory. Adhere to Java naming conventions and consistently format your code for clarity. Modularize your code by breaking it into small, reusable methods and classes. Handle exceptions thoughtfully, using specific exception types and providing helpful error messages. Favor immutability where possible to reduce bugs and make your code thread-safe. Use Java’s built-in libraries and frameworks instead of reinventing the wheel, and always write unit tests to verify your code’s correctness. Document your code with comments and Javadoc to help others (and your future self) understand your logic. </p> <ul> <li>Use clear and descriptive names for variables, methods, and classes.</li> <li>Follow Java naming conventions and consistent code formatting.</li> <li>Write modular, reusable, and single-responsibility methods and classes.</li> <li>Handle exceptions properly and avoid catching generic exceptions.</li> <li>Favor immutability and thread safety when possible.</li> <li>Leverage standard libraries and frameworks.</li> <li>Write unit tests and use assertions to ensure code quality.</li> <li>Document your code with comments and Javadoc.</li> </ul> <p> Key points to discuss include the importance of code readability, the benefits of modular design, strategies for effective exception handling, and the role of testing and documentation in professional Java development. </p>'],
    ],
    'cpp' => [
        ['id' => 'cpp-1', 'title' => 'Topic #1: C++ Basics', 'description' => 'Syntax, variables, and data types.', 'difficulty' => 'beginner', 
        'content' => '<h3>C++ Basics</h3>

<ul> <li><strong>Syntax:</strong> C++ programs are structured with functions, most importantly <code>main()</code>, which serves as the entry point. Statements end with a semicolon (<code>;</code>).</li> <li><strong>Variables:</strong> Declare variables with a specific type before use, such as <code>int age = 25;</code> or <code>std::string name = "Alice";</code>.</li> <li><strong>Data Types:</strong> C++ supports primitive types (<code>int</code>, <code>double</code>, <code>char</code>, <code>bool</code>) and complex types like <code>std::string</code> and arrays.</li> <li><strong>Operators:</strong> Use arithmetic (<code>+</code>, <code>-</code>, <code>*</code>, <code>/</code>, <code>%</code>), relational (<code>==</code>, <code>!=</code>, <code>&lt;</code>, <code>&gt;</code>), and logical (<code>&&</code>, <code>||</code>, <code>!</code>) operators.</li> <li><strong>Comments:</strong> Add single-line comments with <code>//</code> and multi-line comments with <code>/* ... */</code> to explain your code.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;string&gt;
int main() {
int age = 25;
std::string name = "Alice";
bool isCppFun = true;
std::cout << "Hello, " << name << "! Age: " << age << std::endl;
// Output: Hello, Alice! Age: 25
return 0;
}
</code></pre>'],
        ['id' => 'cpp-2', 'title' => 'Topic #2: Control Flow', 'description' => 'If statements, loops, and logic.', 'difficulty' => 'beginner', 
        'content' => '<h3>Control Flow</h3>

<ul> <li><strong>If Statements:</strong> Use <code>if</code>, <code>else if</code>, and <code>else</code> to make decisions based on conditions.</li> <li><strong>Switch Statements:</strong> Use <code>switch</code> to select one of many code blocks to execute based on the value of a variable.</li> <li><strong>Loops:</strong> Implement <code>for</code>, <code>while</code>, and <code>do-while</code> loops to repeat code.</li> <li><strong>Break and Continue:</strong> Use <code>break</code> to exit a loop early, and <code>continue</code> to skip to the next iteration.</li> <li><strong>Logic:</strong> Combine conditions using <code>&&</code> (and), <code>||</code> (or), and <code>!</code> (not) to create complex conditions.</li> </ul> <pre><code>#include &lt;iostream&gt;
int main() {
int number = 10;
// If-else statement
if (number > 0) {
std::cout << "Number is positive" << std::endl;
} else if (number == 0) {
std::cout << "Number is zero" << std::endl;
} else {
std::cout << "Number is negative" << std::endl;
}

text
// Switch statement
int day = 2;
switch (day) {
    case 1:
        std::cout << "Monday" << std::endl;
        break;
    case 2:
        std::cout << "Tuesday" << std::endl;
        break;
    default:
        std::cout << "Other day" << std::endl;
}

// For loop
for (int i = 0; i < 5; i++) {
    if (i == 3) continue; // Skip 3
    std::cout << i << std::endl;
}

// While loop
int count = 0;
while (count < 3) {
    std::cout << "Count: " << count << std::endl;
    count++;
}

// Do-while loop
int x = 0;
do {
    std::cout << "x is " << x << std::endl;
    x++;
} while (x < 2);

return 0;
}
</code></pre>

<ul> <li><strong>If Statements:</strong> Use <code>if</code>, <code>else if</code>, and <code>else</code> to make decisions based on conditions.</li> <li><strong>Switch Statements:</strong> Use <code>switch</code> to select one of many code blocks to execute based on the value of a variable.</li> <li><strong>Loops:</strong> Implement <code>for</code>, <code>while</code>, and <code>do-while</code> loops to repeat code.</li> <li><strong>Break and Continue:</strong> Use <code>break</code> to exit a loop early, and <code>continue</code> to skip to the next iteration.</li> <li><strong>Logic:</strong> Combine conditions using <code>&&</code> (and), <code>||</code> (or), and <code>!</code> (not) to create complex conditions.</li> </ul> <pre><code>#include &lt;iostream&gt;
int main() {
int number = 10;
// If-else statement
if (number > 0) {
std::cout << "Number is positive" << std::endl;
} else if (number == 0) {
std::cout << "Number is zero" << std::endl;
} else {
std::cout << "Number is negative" << std::endl;
}

text
// Switch statement
int day = 2;
switch (day) {
    case 1:
        std::cout << "Monday" << std::endl;
        break;
    case 2:
        std::cout << "Tuesday" << std::endl;
        break;
    default:
        std::cout << "Other day" << std::endl;
}

// For loop
for (int i = 0; i < 5; i++) {
    if (i == 3) continue; // Skip 3
    std::cout << i << std::endl;
}

// While loop
int count = 0;
while (count < 3) {
    std::cout << "Count: " << count << std::endl;
    count++;
}

// Do-while loop
int x = 0;
do {
    std::cout << "x is " << x << std::endl;
    x++;
} while (x < 2);

return 0;
}
</code></pre>'],
        ['id' => 'cpp-3', 'title' => 'Topic #3: Functions', 'description' => 'Defining and using functions.', 'difficulty' => 'beginner', 
        'content' => '<h3>Functions</h3>

<ul> <li><strong>Function Definition:</strong> Define functions with a return type, name, and parameters. For example: <code>int add(int a, int b) { return a + b; }</code></li> <li><strong>Function Invocation:</strong> Call functions by their name and pass arguments. For example: <code>int result = add(2, 3);</code></li> <li><strong>Function Overloading:</strong> Define multiple functions with the same name but different parameters to perform similar but distinct operations.</li> <li><strong>Default Arguments:</strong> Provide default values for function parameters to make them optional.</li> </ul> <pre><code>#include &lt;iostream&gt;
// Function definition
int add(int a, int b) {
return a + b;
}

// Function overloading
double add(double a, double b) {
return a + b;
}

// Function with default argument
int multiply(int a, int b = 2) {
return a * b;
}

int main() {
int result1 = add(2, 3); // Function invocation
double result2 = add(2.5, 3.5); // Overloaded function
int result3 = multiply(4); // Uses default argument
std::cout << "Result 1: " << result1 << std::endl;
std::cout << "Result 2: " << result2 << std::endl;
std::cout << "Result 3: " << result3 << std::endl;
return 0;
}
</code></pre>

<ul> <li><strong>Function Definition:</strong> Define functions with a return type, name, and parameters. For example: <code>int add(int a, int b) { return a + b; }</code></li> <li><strong>Function Invocation:</strong> Call functions by their name and pass arguments. For example: <code>int result = add(2, 3);</code></li> <li><strong>Function Overloading:</strong> Define multiple functions with the same name but different parameters to perform similar but distinct operations.</li> <li><strong>Default Arguments:</strong> Provide default values for function parameters to make them optional.</li> </ul> <pre><code>#include &lt;iostream&gt;
// Function definition
int add(int a, int b) {
return a + b;
}

// Function overloading
double add(double a, double b) {
return a + b;
}

// Function with default argument
int multiply(int a, int b = 2) {
return a * b;
}

int main() {
int result1 = add(2, 3); // Function invocation
double result2 = add(2.5, 3.5); // Overloaded function
int result3 = multiply(4); // Uses default argument
std::cout << "Result 1: " << result1 << std::endl;
std::cout << "Result 2: " << result2 << std::endl;
std::cout << "Result 3: " << result3 << std::endl;
return 0;
}
</code></pre>'],
        ['id' => 'cpp-4', 'title' => 'Topic #4: OOP in C++', 'description' => 'Classes, objects, and inheritance.', 'difficulty' => 'beginner', 
        'content' => '<h3>OOP in C++</h3>

<p> Object-Oriented Programming (OOP) in C++ allows you to model real-world entities using classes and objects. A <strong>class</strong> is a blueprint that defines properties (data members) and behaviors (member functions). An <strong>object</strong> is an instance of a class with its own unique data. C++ supports key OOP principles such as encapsulation, inheritance, and polymorphism. Encapsulation hides internal details using access specifiers like <code>private</code> and <code>public</code>. Inheritance enables a class (derived class) to inherit attributes and methods from another class (base class), promoting code reuse. Polymorphism allows methods to behave differently based on the object’s actual type, often implemented via method overriding and virtual functions. </p> <ul> <li><strong>Classes and Objects:</strong> Define classes with data members and member functions; create objects to use them.</li> <li><strong>Encapsulation:</strong> Use access specifiers (<code>private</code>, <code>public</code>, <code>protected</code>) to control access.</li> <li><strong>Inheritance:</strong> Derive new classes from existing ones using the <code>:</code> syntax.</li> <li><strong>Polymorphism:</strong> Use virtual functions to override base class methods in derived classes.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;string&gt;
class Animal {
public:
std::string name;
Animal(std::string n) : name(n) {}
virtual void speak() {
std::cout << "The animal makes a sound." << std::endl;
}
};

class Dog : public Animal {
public:
Dog(std::string n) : Animal(n) {}
void speak() override {
std::cout << name << " says: Woof!" << std::endl;
}
};

int main() {
Animal genericAnimal("Generic");
Dog myDog("Buddy");
genericAnimal.speak(); // Output: The animal makes a sound.
myDog.speak(); // Output: Buddy says: Woof!
return 0;
}
</code></pre>'],
        ['id' => 'cpp-5', 'title' => 'Topic #5: Pointers & Memory', 'description' => 'Pointers and memory management.', 'difficulty' => 'intermediate', 
        'content' => '<h3>Pointers & Memory</h3>

<p> Pointers are variables that store memory addresses, allowing direct access and manipulation of memory in C++. Understanding pointers is essential for dynamic memory management and efficient programming. Use the <code>*</code> operator to declare a pointer and the <code>&amp;</code> operator to get the address of a variable. Dynamic memory allocation is performed using <code>new</code> and deallocation with <code>delete</code>. Proper memory management is crucial to avoid memory leaks and undefined behavior. </p> <ul> <li><strong>Pointer Declaration:</strong> Use <code>*</code> to declare a pointer, e.g., <code>int* ptr;</code></li> <li><strong>Address-of Operator:</strong> Use <code>&amp;</code> to get the address of a variable, e.g., <code>ptr = &amp;value;</code></li> <li><strong>Dereferencing:</strong> Use <code>*</code> to access the value pointed to by a pointer, e.g., <code>*ptr = 10;</code></li> <li><strong>Dynamic Memory:</strong> Allocate memory with <code>new</code> and free it with <code>delete</code>.</li> <li><strong>Null Pointers:</strong> Initialize pointers to <code>nullptr</code> to avoid dangling references.</li> </ul> <pre><code>#include &lt;iostream&gt;
int main() {
int value = 42;
int* ptr = &value; // Pointer declaration and initialization
std::cout << "Value: " << value << std::endl;
std::cout << "Pointer points to: " << *ptr << std::endl;

text
// Dynamic memory allocation
int* dynamicInt = new int(100);
std::cout << "Dynamically allocated value: " << *dynamicInt << std::endl;
delete dynamicInt; // Free memory

// Null pointer
ptr = nullptr;
if (ptr == nullptr) {
    std::cout << "Pointer is null." << std::endl;
}
return 0;
}
</code></pre>

<p> Pointers are variables that store memory addresses, allowing direct access and manipulation of memory in C++. Understanding pointers is essential for dynamic memory management and efficient programming. Use the <code>*</code> operator to declare a pointer and the <code>&amp;</code> operator to get the address of a variable. Dynamic memory allocation is performed using <code>new</code> and deallocation with <code>delete</code>. Proper memory management is crucial to avoid memory leaks and undefined behavior. </p> <ul> <li><strong>Pointer Declaration:</strong> Use <code>*</code> to declare a pointer, e.g., <code>int* ptr;</code></li> <li><strong>Address-of Operator:</strong> Use <code>&amp;</code> to get the address of a variable, e.g., <code>ptr = &amp;value;</code></li> <li><strong>Dereferencing:</strong> Use <code>*</code> to access the value pointed to by a pointer, e.g., <code>*ptr = 10;</code></li> <li><strong>Dynamic Memory:</strong> Allocate memory with <code>new</code> and free it with <code>delete</code>.</li> <li><strong>Null Pointers:</strong> Initialize pointers to <code>nullptr</code> to avoid dangling references.</li> </ul> <pre><code>#include &lt;iostream&gt;
int main() {
int value = 42;
int* ptr = &value; // Pointer declaration and initialization
std::cout << "Value: " << value << std::endl;
std::cout << "Pointer points to: " << *ptr << std::endl;

text
// Dynamic memory allocation
int* dynamicInt = new int(100);
std::cout << "Dynamically allocated value: " << *dynamicInt << std::endl;
delete dynamicInt; // Free memory

// Null pointer
ptr = nullptr;
if (ptr == nullptr) {
    std::cout << "Pointer is null." << std::endl;
}
return 0;
}
</code></pre>'],
        ['id' => 'cpp-6', 'title' => 'Topic #6: STL', 'description' => 'Standard Template Library.', 'difficulty' => 'intermediate', 
        'content' => '<h3>STL (Standard Template Library)</h3>

<p> The Standard Template Library (STL) in C++ provides a collection of powerful, reusable classes and functions for handling common data structures and algorithms. STL includes containers like <code>vector</code>, <code>list</code>, <code>set</code>, and <code>map</code>, as well as algorithms for sorting, searching, and manipulating data. Iterators are used to traverse elements in containers, and STL uses templates to support generic programming. </p> <ul> <li><strong>Containers:</strong> Store collections of data. Common containers include <code>vector</code> (dynamic array), <code>list</code> (doubly linked list), <code>set</code> (unique elements), and <code>map</code> (key-value pairs).</li> <li><strong>Iterators:</strong> Objects that point to elements in containers and allow traversal.</li> <li><strong>Algorithms:</strong> Functions for operations like <code>sort</code>, <code>find</code>, <code>count</code>, and <code>reverse</code>.</li> <li><strong>Generic Programming:</strong> STL uses templates to work with any data type.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;vector&gt; #include &lt;set&gt; #include &lt;map&gt; #include &lt;algorithm&gt;
int main() {
// Vector example
std::vector<int> numbers = {1, 2, 3, 4, 5};
numbers.push_back(6);
for (int n : numbers) {
std::cout << n << " ";
}
std::cout << std::endl;

text
// Set example
std::set&lt;std::string&gt; fruits = {"apple", "banana", "apple"};
for (const auto& fruit : fruits) {
    std::cout << fruit << " ";
}
std::cout << std::endl;

// Map example
std::map&lt;std::string, int&gt; ages;
ages["Alice"] = 30;
ages["Bob"] = 25;
for (const auto& pair : ages) {
    std::cout << pair.first << ": " << pair.second << std::endl;
}

// Algorithm example
std::sort(numbers.begin(), numbers.end(), std::greater&lt;int&gt;());
std::cout << "Sorted in descending order: ";
for (int n : numbers) {
    std::cout << n << " ";
}
std::cout << std::endl;

return 0;
}
</code></pre>

<p> The Standard Template Library (STL) in C++ provides a collection of powerful, reusable classes and functions for handling common data structures and algorithms. STL includes containers like <code>vector</code>, <code>list</code>, <code>set</code>, and <code>map</code>, as well as algorithms for sorting, searching, and manipulating data. Iterators are used to traverse elements in containers, and STL uses templates to support generic programming. </p> <ul> <li><strong>Containers:</strong> Store collections of data. Common containers include <code>vector</code> (dynamic array), <code>list</code> (doubly linked list), <code>set</code> (unique elements), and <code>map</code> (key-value pairs).</li> <li><strong>Iterators:</strong> Objects that point to elements in containers and allow traversal.</li> <li><strong>Algorithms:</strong> Functions for operations like <code>sort</code>, <code>find</code>, <code>count</code>, and <code>reverse</code>.</li> <li><strong>Generic Programming:</strong> STL uses templates to work with any data type.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;vector&gt; #include &lt;set&gt; #include &lt;map&gt; #include &lt;algorithm&gt;
int main() {
// Vector example
std::vector<int> numbers = {1, 2, 3, 4, 5};
numbers.push_back(6);
for (int n : numbers) {
std::cout << n << " ";
}
std::cout << std::endl;

text
// Set example
std::set&lt;std::string&gt; fruits = {"apple", "banana", "apple"};
for (const auto& fruit : fruits) {
    std::cout << fruit << " ";
}
std::cout << std::endl;

// Map example
std::map&lt;std::string, int&gt; ages;
ages["Alice"] = 30;
ages["Bob"] = 25;
for (const auto& pair : ages) {
    std::cout << pair.first << ": " << pair.second << std::endl;
}

// Algorithm example
std::sort(numbers.begin(), numbers.end(), std::greater&lt;int&gt;());
std::cout << "Sorted in descending order: ";
for (int n : numbers) {
    std::cout << n << " ";
}
std::cout << std::endl;

return 0;
}
</code></pre>'],
        ['id' => 'cpp-7', 'title' => 'Topic #7: File I/O', 'description' => 'Read and write files in C++.', 'difficulty' => 'intermediate', 
        'content' => '<h3>File I/O</h3>

<ul> <li><strong>Reading Files:</strong> Use <code>std::ifstream</code> to read data from files.</li> <li><strong>Writing Files:</strong> Use <code>std::ofstream</code> to write data to files.</li> <li><strong>File Streams:</strong> <code>std::fstream</code> can be used for both reading and writing.</li> <li><strong>Error Handling:</strong> Always check if the file was opened successfully before reading or writing.</li> <li><strong>Closing Files:</strong> Files are automatically closed when the stream object goes out of scope, but you can also close them manually using <code>close()</code>.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;fstream&gt; #include &lt;string&gt;
int main() {
// Writing to a file
std::ofstream outFile("output.txt");
if (outFile.is_open()) {
outFile << "Hello, C++ File I/O!" << std::endl;
outFile.close();
} else {
std::cout << "Unable to open file for writing." << std::endl;
}

text
// Reading from a file
std::ifstream inFile("output.txt");
std::string line;
if (inFile.is_open()) {
    while (std::getline(inFile, line)) {
        std::cout << "Read: " << line << std::endl;
    }
    inFile.close();
} else {
    std::cout << "Unable to open file for reading." << std::endl;
}

return 0;
}
</code></pre>

<ul> <li><strong>Reading Files:</strong> Use <code>std::ifstream</code> to read data from files.</li> <li><strong>Writing Files:</strong> Use <code>std::ofstream</code> to write data to files.</li> <li><strong>File Streams:</strong> <code>std::fstream</code> can be used for both reading and writing.</li> <li><strong>Error Handling:</strong> Always check if the file was opened successfully before reading or writing.</li> <li><strong>Closing Files:</strong> Files are automatically closed when the stream object goes out of scope, but you can also close them manually using <code>close()</code>.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;fstream&gt; #include &lt;string&gt;
int main() {
// Writing to a file
std::ofstream outFile("output.txt");
if (outFile.is_open()) {
outFile << "Hello, C++ File I/O!" << std::endl;
outFile.close();
} else {
std::cout << "Unable to open file for writing." << std::endl;
}

text
// Reading from a file
std::ifstream inFile("output.txt");
std::string line;
if (inFile.is_open()) {
    while (std::getline(inFile, line)) {
        std::cout << "Read: " << line << std::endl;
    }
    inFile.close();
} else {
    std::cout << "Unable to open file for reading." << std::endl;
}

return 0;
}
</code></pre>'],
        ['id' => 'cpp-8', 'title' => 'Topic #8: Templates', 'description' => 'Generic programming with templates.', 'difficulty' => 'expert', 
        'content' => '<h3>Templates</h3>

<p> Templates in C++ enable generic programming by allowing functions and classes to operate with any data type. This promotes code reuse and type safety. Function templates let you write a single function to work with different types, while class templates allow you to define classes that can handle various data types. </p> <ul> <li><strong>Function Templates:</strong> Define generic functions using the <code>template&lt;typename T&gt;</code> syntax.</li> <li><strong>Class Templates:</strong> Create generic classes that can store or process any data type.</li> <li><strong>Type Parameters:</strong> Use <code>typename</code> or <code>class</code> as template parameters.</li> <li><strong>Template Instantiation:</strong> The compiler generates specific versions of the template for each type used.</li> </ul> <pre><code>#include &lt;iostream&gt;
// Function template
template<typename T>
T add(T a, T b) {
return a + b;
}

// Class template
template<typename T>
class Box {
public:
T value;
Box(T v) : value(v) {}
void show() {
std::cout << "Value: " << value << std::endl;
}
};

int main() {
int sumInt = add(2, 3);
double sumDouble = add(2.5, 3.5);
std::cout << "Sum (int): " << sumInt << std::endl;
std::cout << "Sum (double): " << sumDouble << std::endl;

text
Box&lt;int&gt; intBox(100);
Box&lt;std::string&gt; strBox("Template");
intBox.show();
strBox.show();

return 0;
}
</code></pre>'],
        ['id' => 'cpp-9', 'title' => 'Topic #9: Advanced Topics', 'description' => 'Move semantics, smart pointers, etc.', 'difficulty' => 'expert', 
        'content' => '<h3>Advanced Topics</h3>

<p> Modern C++ introduces advanced features that improve performance, safety, and resource management. <strong>Move semantics</strong> allow efficient transfer of resources from one object to another, reducing unnecessary copying. <strong>Smart pointers</strong> like <code>std::unique_ptr</code>, <code>std::shared_ptr</code>, and <code>std::weak_ptr</code> automate memory management and help prevent memory leaks. <strong>Lambda expressions</strong> provide a concise way to define anonymous functions, often used with STL algorithms. <strong>RAII</strong> (Resource Acquisition Is Initialization) ensures resources are properly released when objects go out of scope. </p> <ul> <li><strong>Move Semantics:</strong> Use <code>std::move</code> and move constructors to transfer resources efficiently.</li> <li><strong>Smart Pointers:</strong> Manage dynamic memory safely with <code>std::unique_ptr</code>, <code>std::shared_ptr</code>, and <code>std::weak_ptr</code>.</li> <li><strong>Lambda Expressions:</strong> Define inline, anonymous functions for use with algorithms and callbacks.</li> <li><strong>RAII:</strong> Use constructors and destructors to manage resources automatically.</li> </ul> <pre><code>#include &lt;iostream&gt; #include &lt;memory&gt; #include &lt;vector&gt; #include &lt;algorithm&gt;
int main() {
// Move semantics
std::vector<int> v1 = {1, 2, 3};
std::vector<int> v2 = std::move(v1); // v1 is now empty

text
// Smart pointers
std::unique_ptr&lt;int&gt; ptr1 = std::make_unique&lt;int&gt;(42);
std::shared_ptr&lt;int&gt; ptr2 = std::make_shared&lt;int&gt;(100);

// Lambda expression
std::vector&lt;int&gt; nums = {1, 2, 3, 4, 5};
std::for_each(nums.begin(), nums.end(), [](int n) {
    std::cout << n << " ";
});
std::cout << std::endl;

// RAII example
struct FileWrapper {
    FILE* file;
    FileWrapper(const char* filename) { file = fopen(filename, "w"); }
    ~FileWrapper() { if (file) fclose(file); }
};
{
    FileWrapper fw("example.txt");
    // File is automatically closed when fw goes out of scope
}

return 0;
}
</code></pre>'],
        ['id' => 'cpp-10', 'title' => 'Topic #10: C++ Best Practices', 'description' => 'Tips for writing efficient C++ code.', 'difficulty' => 'expert', 
        'content' => '<h3>C++ Best Practices</h3>

<p> Writing efficient and maintainable C++ code requires following best practices that promote clarity, safety, and performance. Start by using meaningful and consistent naming conventions for variables, functions, and classes to improve code readability. Prefer modern C++ features such as smart pointers over raw pointers to manage memory safely and avoid leaks. Embrace the use of the Standard Template Library (STL) for common data structures and algorithms instead of reinventing the wheel. Use const-correctness to protect data from unintended modification and improve optimization opportunities. Avoid premature optimization; write clear code first, then profile and optimize bottlenecks. Leverage RAII (Resource Acquisition Is Initialization) to manage resources automatically and prevent resource leaks. When dealing with concurrency, use thread-safe constructs and avoid data races by properly synchronizing access to shared data. Write modular code by breaking functionality into small, reusable functions and classes that follow the single responsibility principle. Document your code thoroughly with comments and use tools like Doxygen for generating documentation. Finally, write unit tests to verify correctness and facilitate future maintenance. Following these practices will help you write robust, efficient, and maintainable C++ applications. </p>'],
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
