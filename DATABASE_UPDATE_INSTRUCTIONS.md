# Database Update Instructions

To add the address field to your users table, run the following SQL command in your MySQL database:

```sql
ALTER TABLE users ADD COLUMN address TEXT AFTER phone;
```

## How to run this:

### Option 1: phpMyAdmin

1. Open phpMyAdmin in your browser (usually http://localhost/phpmyadmin)
2. Select your `hungerhub` database
3. Click on the "SQL" tab
4. Paste the SQL command above
5. Click "Go"

### Option 2: MySQL Command Line

1. Open Command Prompt/Terminal
2. Run: `mysql -u root -p`
3. Enter your MySQL password (usually empty for XAMPP)
4. Run: `USE hungerhub;`
5. Run: `ALTER TABLE users ADD COLUMN address TEXT AFTER phone;`

### Option 3: Run the SQL file

1. You can also import the `update_users_table.sql` file directly through phpMyAdmin

After running this, all new user registrations will include an address field, and existing users can update their address in their profile.
