# 🚀 Project Setup Guide

Welcome to the project! Follow these steps to get your environment up and running.

**CodeIgniter version 3.1.11**

**Requirements:**

- Docker: 27.4.0 or higher
- Docker Compose: 2.31.0 or higher
- Node.js: 16.x or higher

**Versions installed on the containers:**

- PHP: 7.3
- MariaDB: 10.11.10

## 📥 Step 1: Clone the Repository

First, clone the repository from the provided URL:

```bash
git clone https://github.com/your-repo/your-project.git
```

## 📂 Step 2: Configure the Database

1. **Copy the Example Configuration Files:**

   Navigate to the `application/config` directory and copy the `database.example.php`, `constants.example.php` and `config.example.php` files:

   ```bash
   cp application/config/database.example.php application/config/database.php
   cp application/config/constants.example.php application/config/constants.php
   cp application/config/config.example.php application/config/config.php
   ```

   Navigate to the `api/application/config` directory and copy the `database.example.php`, `constants.example.php` file:

   ```bash
   cp api/application/config/database.example.php api/application/config/database.php
   cp api/application/config/constants.example.php api/application/config/constants.php
   cp api/application/config/config.example.php api/application/config/config.php
   ```

2. **Edit the Configuration Files:**
   
   - Open `application/config/database.php` and update the credentials to connect to your database, please contact the admin to obtain the credentials.
   - If necessary, adjust any constants in `application/config/constants.php` to suit your environment, please contact the admin to obtain the constants.
   - If necessary, adjust any constants in `application/config/config.php` to suit your environment, please contact the admin to obtain the constants.

   - Open `api/application/config/database.php` and update the credentials to connect to your database, please contact the admin to obtain the credentials.
   - If necessary, adjust any constants in `api/application/config/constants.php` to suit your environment, please contact the admin to obtain the constants.
   - If necessary, adjust any constants in `api/application/config/config.php` to suit your environment, please contact the admin to obtain the constants.

## 🗄️ Step 3: Initialize the Database

Copy the SQL initialization file into the `api/scripts` directory. The file should be named `u275597130_viaggi.sql`. Please contact the admin to obtain this file.

## 📁 Step 4: (Optional) Media Files

If needed, copy the media files into the `api/media` directory. Again, please contact the admin to obtain these files.

## 📁 Step 5: Assets Files

Copy the assets files into the `assets` directory. please contact the admin to obtain these files.

## 📁 Step 6: Libraries Files

Copy the libraries files into the `api/libs` directory. please contact the admin to obtain these files.

## 📁 Step 7: Backoffice Vendors Files

Copy the vendors files into the `client/public/assets/vendors` directory. please contact the admin to obtain these files.

## 🛠️ Step 8: Vue js compilation

Start the Vue js project in `client/` folder

```bash
cd client
npm install
npm run serve
```

## Optional: Compile the Vue js project for production

Compile the Vue js project in `client/` folder and copy the `dist` folder into the `backoffice/` directory.

```bash
cd client
npm install
npm run build
cp -r dist/* ../backoffice/
```

## 🐳 Step 9: Start the Docker Environment

Finally, build and start the Docker containers:

```bash
docker-compose up -d --build
```

This command will create and start the local environment for the project.

---

Feel free to reach out if you encounter any issues or have questions. Happy coding! 🎉
