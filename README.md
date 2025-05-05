# Mini CRM for Prospect Management

A complete web application built with native PHP, MySQL, and jQuery/AJAX for managing prospects and customer relationships. This application follows OOP principles and provides a clean, structured codebase.

## Features

### Authentication
- Secure login with password hashing
- Session management
- Protected routes requiring authentication

### Prospect Management
- Complete CRUD operations (Create, Read, Update, Delete)
- Status tracking (new, contacted, in negotiation, won, lost)
- Dynamic filtering by status using AJAX
- Pagination for prospect lists

### Prospect Details
- Comprehensive prospect information display
- Add and view notes for each prospect
- Upload, download, and delete documents (PDF, DOCX, JPG, PNG)
- Real-time updates with AJAX

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Modern web browser with JavaScript enabled

## Installation

1. **Clone or download the repository**

   \`\`\`bash
   git clone https://github.com/zied-snoussi/mini-crm.git
   cd mini-crm
   \`\`\`

2. **Create the database**

   Create a MySQL database for the application:

   \`\`\`sql
   CREATE DATABASE mini_crm;
   \`\`\`

3. **Import the database schema**

   Import the database structure and sample data:

   \`\`\`bash
   mysql -u username -p mini_crm < database.sql
   \`\`\`

4. **Configure environment variables**

   Copy the example environment file and update it with your configuration:

   \`\`\`bash
   cp .env.example .env
   \`\`\`

   Edit the `.env` file with your database credentials and other settings.

5. **Set up the uploads directory**

   Create an uploads directory and ensure it's writable by the web server:

   \`\`\`bash
   mkdir uploads
   chmod 755 uploads
   \`\`\`

6. **Configure your web server**

   Point your web server to the project directory. For Apache, you might need to update your virtual host configuration or .htaccess file.

## Configuration

### Environment Variables

The application uses environment variables for configuration. These are stored in the `.env` file:

- `DB_HOST`: Database host (default: localhost)
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password
- `DB_DATABASE`: Database name (default: mini_crm)
- `APP_NAME`: Application name
- `APP_URL`: Base URL for the application
- `APP_DEBUG`: Enable/disable debug mode (true/false)
- `UPLOAD_MAX_SIZE`: Maximum file upload size in bytes
- `ALLOWED_EXTENSIONS`: Comma-separated list of allowed file extensions

### Database Configuration

Database connection settings are managed in `config/database.php`. This file reads from the environment variables.

## Usage

1. **Access the application**

   Open your web browser and navigate to the application URL (e.g., http://localhost/mini-crm).

2. **Login**

   Use the default admin account:
   - Username: `admin`
   - Password: `admin123`

3. **Managing Prospects**

   - View all prospects on the dashboard
   - Filter prospects by status
   - Add new prospects using the "Add New Prospect" button
   - View, edit, or delete prospects using the action buttons

4. **Working with Prospect Details**

   - Click "View" on a prospect to see detailed information
   - Add notes to track interactions
   - Upload documents related to the prospect
   - View and download documents

## Security Considerations

This application implements several security measures:

- Password hashing using PHP's `password_hash()` function
- Input sanitization to prevent XSS attacks
- Prepared statements to prevent SQL injection
- Session-based authentication
- File type validation for uploads

For production use, consider implementing:

- HTTPS for secure data transmission
- Rate limiting for login attempts
- Additional user roles and permissions
- Regular security audits

## File Structure

\`\`\`
mini-crm/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── dashboard.js
│       └── prospect_detail.js
├── classes/
│   ├── Document.php
│   ├── Note.php
│   ├── Prospect.php
│   ├── Session.php
│   └── User.php
├── config/
│   ├── database.php
│   └── env.php
├── controllers/
│   ├── document_controller.php
│   ├── note_controller.php
│   └── prospect_controller.php
├── uploads/
│   └── (uploaded files)
├── views/
│   ├── documents_list.php
│   ├── header.php
│   ├── notes_list.php
│   └── prospects_table.php
├── .env
├── .env.example
├── dashboard.php
├── database.sql
├── index.php
├── login.php
├── logout.php
├── prospect_detail.php
├── prospect_form.php
└── README.md
\`\`\`

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details.
