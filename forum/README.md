# Learning Forum Web Application

A web forum application similar to Reddit/Facebook communities but focused on learning topics. Users can create posts, comment, upvote/downvote, and follow topic spaces.

## Features

- User authentication (login/register)
- User roles (regular users and moderators)
- Home page showing posts from followed topics
- Search page to find topic spaces
- Topic spaces with related posts
- Create posts with text, images, or videos
- Comment on posts with threaded replies
- Edit posts and comments
- Upvote/downvote system
- Follow/unfollow topic spaces
- Moderator capabilities (create topic spaces, moderate content)

## Technologies Used

- Frontend: HTML, CSS, JavaScript, Bootstrap 5
- Backend: PHP
- Database: MySQL

## Installation

1. Clone this repository to your XAMPP htdocs folder
2. Import the database schema from `database/forum_db.sql`
3. Configure database connection in `config/database.php`
4. Access the application through `http://localhost/forum`

## Project Structure

```
forum/
├── assets/           # CSS, JS, images
├── config/           # Configuration files
├── database/         # Database schema
├── includes/         # PHP includes
├── js/               # JavaScript files
├── pages/            # Page templates
├── uploads/          # User uploaded content
├── index.php         # Main entry point
└── README.md         # This file
```

## License

MIT
