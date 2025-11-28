# WP Article Builder

WP Article Builder is a simple web tool that allows you to create WordPress draft posts directly from a custom frontend form. It is ideal for automating or simplifying content creation without using the WordPress editor.

## Features

- Create WordPress posts from a frontend form.
- Save posts as drafts for later review.
- Simple and lightweight interface.
- Compatible with any WordPress installation (requires access to `wp-load.php`).

## Installation

1. Copy the project files to a folder on your server.
2. Update the path to `wp-load.php` in the PHP script to point to your WordPress installation:

```php
require_once('/path/to/your/wp-load.php');
```

3. Access the form in your browser to start creating drafts.

## Usage

1. Fill in the form with the post title and content.
2. Click "Create Draft".
3. The draft will be created in WordPress and accessible from the dashboard.

## Requirements

- PHP 7.x or higher.
- WordPress installed and accessible from the server.
- Sufficient permissions to create posts via PHP.

## Example

```html
<form id="articleForm">
  <input type="text" name="title" placeholder="Post Title" required>
  <textarea name="content" placeholder="Post Content"></textarea>
  <button type="submit">Create Draft</button>
</form>
<script src="script.js"></script>
```

The `script.js` handles sending the data to the PHP script, which creates the draft post.

## Contributing

Contributions are welcome! Feel free to suggest improvements, add validation, or support more WordPress fields (categories, tags, etc.).


