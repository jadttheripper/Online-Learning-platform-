🌟 Online Learning Platform (SkillSwap) 🌟
A Modern, Interactive Learning Hub Where Users Can Learn & Teach Skills in Real-Time!

🚀 Discover, Teach, and Track Progress – A full-stack PHP-based platform with real-time messaging, course sharing, progress tracking, and a thriving learning community.

🔥 Key Features
✔ User Accounts – Secure login/signup with profile editing.
✔ Skill Search & Filters – Find Skills by  category.
✔ Teach & Learn – Users can add/remove skills and create/edit courses for the skills.
✔ Real-Time Chat – Instant messaging with translation support (powered by APIs).
✔ Course Progress Tracking – Monitor learning milestones.
✔ Reviews & Ratings – Rate the overall website.
✔ Community Engagement – Post, like, and discuss in the community section.
✔ Wishlist/Favorites – Save courses for later.
✔ Admin & User Password Reset – Secure account recovery.
✔ Skill Recommendations – Personalized suggestions based on interests.
✔ Delete and report User  – for moderation purposes.

🛠 Tech Stack
Frontend: HTML, CSS, JavaScript,Alpine Js,Bootstrap, Tailwind CSS

Backend: PHP, MySQL(Maria DB) 

Real-Time Chat: AJAX 

Optional Translation: Google Translate API 

Server: XAMPP (Apache, MySQL, PHP)

i already hosted the project online for you to immediately test it from your phone even! the link is in the About section of this repo. But in case you wanna run it locally, here are a set of instructions: 
🚀 How to Run This Project Locally
Prerequisites
XAMPP (or WAMP/MAMP) 

Git – Install Git

Web Browser (Chrome/Firefox recommended)

Setup Instructions
1. Clone the Repository
bash
git clone https://github.com/jadttheripper/Online-Learning-Platform.git
cd Online-Learning-Platform
2. Set Up XAMPP
Move the cloned folder to htdocs (XAMPP) or www (WAMP).

Example path:

text
C:\xampp\htdocs\SkillSwap
3. Import the Database
Open phpMyAdmin (http://localhost/phpmyadmin).

Create a new database named SkillSwap.

Import the provided SQL file (Skillswap.sql).

4. Configure Database Connection
Open connection.php and update:

php
$host = "localhost";
$user = "root";  // Default XAMPP username
$pass = "";      // Default XAMPP password (blank)
$db   = "Skillswap";
5. Start the Server
Launch XAMPP Control Panel → Start Apache & MySQL.

Open browser → Visit:

text
http://localhost/SkillSwap
6. User test: SkillSwap/index.php
Admin test: SkillSwap/Admin/panel.php

📸 Screenshots 
<img width="1344" height="652" alt="home1" src="https://github.com/user-attachments/assets/341f50c1-0d11-4e3a-9f0a-b53bb19f3b56" />  <img width="1348" height="641" alt="loginpage" src="https://github.com/user-attachments/assets/3aa8ffc5-96d4-4bc1-970a-00e1627a414a" /> <img width="703" height="645" alt="addskill" src="https://github.com/user-attachments/assets/a5e18053-dbba-4829-b8a7-6f89133e58be" /> <img width="500" height="623" alt="addskill1" src="https://github.com/user-attachments/assets/d46424c0-63fb-4194-924d-c0def828ac6f" />
 <img width="1304" height="649" alt="search1" src="https://github.com/user-attachments/assets/52b53d8c-5a7c-4c21-a852-1b2a699e3404" /> <img width="1290" height="648" alt="messaging1" src="https://github.com/user-attachments/assets/c6346666-0ccc-402c-95ef-5ec3c6b75db0" /> 
<img width="795" height="593" alt="sendcourse" src="https://github.com/user-attachments/assets/835d6713-fd20-4064-902a-0447400d5d70" />
 <img width="905" height="590" alt="viewskillusers" src="https://github.com/user-attachments/assets/63a254fe-598b-4360-8d3f-5c77e3e53166" />  
<img width="468" height="615" alt="addlesson" src="https://github.com/user-attachments/assets/e32ef978-a04e-436f-9a25-03a1159d82ea" /> <img width="1298" height="645" alt="profile1" src="https://github.com/user-attachments/assets/37b7e1ec-3192-440e-93be-7087e8fcefe9" /> <img width="472" height="612" alt="profile4" src="https://github.com/user-attachments/assets/f243fdde-8931-4e96-acf5-6286197eb405" /> <img width="458" height="540" alt="markasdone2" src="https://github.com/user-attachments/assets/9325cb41-6770-47a2-bcfa-91309fd9bf14" /> <img width="938" height="650" alt="viewcourseuser" src="https://github.com/user-attachments/assets/1cfbd655-9cca-4620-8f2f-bad855ae2aa6" />  <img width="1335" height="650" alt="adminpanel" src="https://github.com/user-attachments/assets/99ef3ec1-30e5-4062-bc6b-c9c0b4df5a2d" /> <img width="471" height="613" alt="adminpanel2" src="https://github.com/user-attachments/assets/c1d9e55b-75ef-4d79-87f2-67769f76b6f5" />



🌍 Contribute & Feedback
🔹 Found a bug? Open an Issue!
🔹 Want to improve something? Submit a Pull Request!
🔹 Need help? Email me at your-email@example.com.


🚀 Ready to Dive In?
Clone the repo, fire up XAMPP, and start learning (or teaching) today!

💬 Let’s build the future of education—together!

⭐ Star this repo if you like it! ⭐

