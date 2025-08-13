<?php
session_start();
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$userId = $_SESSION['user_id'];
$userLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? 'Guest';
$profilePic = $_SESSION['profile_pic'] ?? 'image/defaultavatar.jpg';




$category = $_GET['skill_category'] ?? null;

if ($category) {
    // Show all skills in the recommended category
    $sql = "SELECT * FROM skill WHERE skill_category = :skill_category ORDER BY skill_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':skill_category', $category, PDO::PARAM_STR);
} else {
    // Default: Show latest 3 skills
    $sql = "SELECT * FROM skill ORDER BY skill_id DESC LIMIT 3";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.js" defer></script>
    <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<nav class="navbar">
  <div class="navbar-container">
    <!-- Logo or User Info -->
    <div class="navbar-left">
      <?php if ($userLoggedIn): ?>
        <div class="user-info cursor-pointer" onclick="window.location.href='profile.php';">
  <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="profile-pic" onerror="this.src='image/defaultavatar.jpg'">
  <div class="user-name"><?= htmlspecialchars($userName) ?></div>
</div>
      <?php else: ?>
        <h1 class="logo text-xl font-bold text-blue-600">SkillSwap</h1>
      <?php endif; ?>
    </div>

    <!-- Desktop Links -->
    <?php if ($userLoggedIn): ?>
      <div class="navbar-links left-links desktop-only">
        <a href="dashboard.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Dashboard</a>
        <a href="search.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
        <a href="index.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
        <a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About Us</a>
        <a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>
      </div>
      <div class="navbar-links right-icons desktop-only">
        <a href="messages.php" title="Messages"><i class="fas fa-comments"></i></a>
        <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
      </div>
    <?php else: ?>
      <div class="navbar-links left-links desktop-only">
        <a href="search.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Search</a>
        <a href="index.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Home</a>
        <a href="aboutus.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">About</a>
        <a href="contact.php" class="px-3 py-2 rounded transition hover:bg-blue-100 hover:text-blue-700 focus:bg-blue-200 focus:text-blue-800 active:bg-blue-300 active:text-blue-900">Contact</a>
      </div>
      <div class="navbar-links right-icons desktop-only gap-2">
        <a href="login.php" class="px-4 py-1 border border-blue-600 text-blue-600 rounded hover:bg-green-600 hover:text-white transition">Login</a>
        <a href="signupuser.php" class="px-4 py-1 border border-green-600 text-green-600 rounded hover:bg-blue-700 hover:text-white transition">Sign Up</a>
      </div>
    <?php endif; ?>

    <!-- Hamburger Icon -->
    <button class="hamburger" onclick="toggleMenu()">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="navLinks">
    <?php if ($userLoggedIn): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="search.php">Search</a>
      <a href="index.php">Home</a>
      <a href="aboutus.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="messages.php"><i class="fas fa-comments"></i> Messages</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
      <a href="search.php">Search</a>
      <a href="index.php">Home</a>
      <a href="aboutus.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="login.php" class="text-blue-600 font-semibold hover:underline">Login</a>
      <a href="signupuser.php" class="text-white bg-blue-600 px-4 py-2 rounded hover:bg-white-700 transition">Sign Up</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Navbar Styles -->
<style>
  .navbar {
    background: white;
    color: #333;
    padding: 1rem;
    position: fixed;
    top: 0; /* Ensure it's at the top of the page */
    width: 100%;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  body {
    padding-top: 4rem; /* Make sure content doesn't hide behind the navbar */
}

  .navbar-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: auto;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-right: 2rem;
  }

  .profile-pic {
    width: 36px;
    height: 36px;
    border-radius: 50%;
  }

  .user-name {
    font-size: 0.9rem;
    font-weight: 600;
  }

  .navbar-links {
    display: flex;
    gap: 1rem;
    align-items: center;
  }

  .left-links {
    flex: 1;
    margin-left: 2rem;
  }

  .right-icons {
    display: flex;
    gap: 1rem;
    align-items: center;
  }

  .hamburger {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
  }

  .mobile-menu {
    display: none;
    flex-direction: column;
    background: white;
    padding: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }

  .mobile-menu.open {
    display: flex;
  }

  .mobile-menu a {
    display: block;
    margin-bottom: 0.75rem;
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #333;
    border-radius: 5px;
    transition: background-color 0.2s, color 0.2s;
  }

  .mobile-menu a:hover {
    background-color: #f0f0f0;
  }

  @media (max-width: 1024px) {
    .desktop-only {
      display: none;
    }

    .hamburger {
      display: block;
    }
  }
</style>

<!-- Navbar Toggle Script -->
<script>
  function toggleMenu() {
    const nav = document.getElementById('navLinks');
    nav.classList.toggle('open');
}

// Automatically close the mobile menu if window is resized above a certain width (e.g., 768px)
window.addEventListener('resize', () => {
    const nav = document.getElementById('navLinks');
    if (window.innerWidth > 768) {
        nav.classList.remove('open');
    }
});

</script>
<body class="bg-gray-100 min-h-screen pt-24 px-4">

<!-- Welcome Toast -->
<div id="welcomeToast" class="fixed top-20 left-5 bg-white shadow-lg border rounded-md px-6 py-4 z-50 flex items-start justify-between w-72">
  <span class="font-medium text-gray-800">Welcome “<?= htmlspecialchars($userName) ?>”</span>
  <button onclick="document.getElementById('welcomeToast').style.display='none'" class="ml-4 text-gray-500 hover:text-red-600 font-bold text-xl leading-none">&times;</button>
</div>


<!-- Recommendation Box -->
<div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-6 mb-10 text-center" id="ai-recommendation-box"> 
  <h2 class="text-xl font-bold text-gray-800 mb-4">let us reccomend a skill</h2>
  <div id="question-progress" class="text-xl font-bold mb-4"></div>
  <p class="text-red-600 text-lg mb-4" id="question-text">Loading question...</p>
  <div class="space-x-4">
    <button onclick="submitAnswer(1)" id="option1" class="px-4 py-2 bg-blue-800 text-white rounded hover:bg-gray-300">Option 1</button>
    <button onclick="submitAnswer(2)" id="option2" class="px-4 py-2 bg-green-800 text-white rounded hover:bg-gray-700">Option 2</button>
  </div>
</div>
<div class="max-w-xl mx-auto text-center" id="recommendation-result"></div>


<!-- Skills Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto mb-12">
<?php
$recommendedCategory = $_GET['skill_category'] ?? null;
$filteredSkills = $recommendedCategory
    ? array_filter($skills, fn($s) => $s['skill_category'] === $recommendedCategory)
    : $skills;
?>

<?php foreach ($filteredSkills as $skill): ?>

        <a href="viewSkillUsers.php?skill_id=<?= urlencode($skill['skill_id']) ?>" class="block">
        <img src="<?= htmlspecialchars($skill['image_url'] ?? 'image/default.png') ?>" alt="Skill Image" class="w-full h-40 object-cover  rounded">
            <div class="skill-card border rounded-lg shadow-md p-4 hover:shadow-lg transition">
              
                <h3 class="text-xl font-semibold"><?= htmlspecialchars($skill['title']) ?></h3>
                <p class="text-sm text-red-600"><?= htmlspecialchars($skill['description']) ?></p>
                
            </div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Add New Skill Section -->
<div class="max-w-md mx-auto bg-white shadow-lg p-6 rounded-lg text-center">
  <p class="mb-4">excited to teach a new skill? </p>
  <a href="add_skill.php" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block">Add new Skill</a>
</div>
<?php if (isset($_SESSION['error_message'])): ?>
  <div class="max-w-md mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-6" role="alert">
    <strong class="font-bold">Error: </strong>
    <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error_message']) ?></span>
    <button onclick="this.parentElement.style.display='none';" class="absolute top-0 bottom-0 right-0 px-4 py-3">
      <span class="text-2xl">&times;</span>
    </button>
  </div>
  <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>


<footer class="bg-gray-900 text-white py-10 mt-10">
  <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-center">
      <div class="mb-6 md:mb-0 text-center md:text-left">
        <h2 class="text-xl font-semibold mb-2">SkillSwap</h2>
        <p class="text-gray-400 text-sm">© <?= date('Y') ?> SkillSwap. All rights reserved.</p>
      </div>
      <div class="flex space-x-6 text-xl">
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
        <a href="mailto:contact@skillswap.com" class="text-gray-400 hover:text-white transition"><i class="fas fa-envelope"></i></a>
      </div>
    </div>
  </div>
</footer>
<script>
let currentQuestion = 0;
let answers = [];

// Full list of diverse and balanced questions (48 total originally)
let questions = [
  {
    text: "Do you enjoy composing music or solving equations?",
    options: ["Composing music", "Solving equations"]
  },
  {
    text: "Would you rather play a competitive sport or write a story?",
    options: ["Play a sport", "Write a story"]
  },
  {
    text: "Do you prefer building an app or managing a budget?",
    options: ["Building an app", "Managing a budget"]
  },
  {
    text: "Are you more interested in scientific research or graphic design?",
    options: ["Scientific research", "Graphic design"]
  },
  {
    text: "Would you rather pitch a product or code a solution?",
    options: ["Pitch a product", "Code a solution"]
  },
  {
    text: "Do you enjoy leading a team or creating visual content?",
    options: ["Leading a team", "Creating visual content"]
  },
  {
    text: "Do you prefer spreadsheets and financial models or creative brainstorming?",
    options: ["Spreadsheets and models", "Creative brainstorming"]
  },
  {
    text: "Would you rather perform music on stage or develop a new tech tool?",
    options: ["Perform music", "Develop a tech tool"]
  },
  {
    text: "Do you enjoy physical activity and training or editing videos?",
    options: ["Physical activity", "Editing videos"]
  },
  {
    text: "Are you more excited about data analysis or public speaking?",
    options: ["Data analysis", "Public speaking"]
  },
  {
    text: "Would you rather coach a team or analyze statistics?",
    options: ["Coach a team", "Analyze statistics"]
  },
  {
    text: "Do you enjoy playing an instrument or planning a marketing campaign?",
    options: ["Playing an instrument", "Planning marketing"]
  },
  {
    text: "Would you rather design a website or write a novel?",
    options: ["Design a website", "Write a novel"]
  },
  {
    text: "Do you prefer conducting experiments or presenting business strategies?",
    options: ["Conduct experiments", "Present business strategies"]
  },
  {
    text: "Are you more interested in animation or investment analysis?",
    options: ["Animation", "Investment analysis"]
  },
  {
    text: "Would you rather write a blog or debug code?",
    options: ["Write a blog", "Debug code"]
  },
  {
    text: "Do you enjoy improvising music or designing a product?",
    options: ["Improvising music", "Designing a product"]
  },
  {
    text: "Would you rather handle accounting reports or film a short movie?",
    options: ["Accounting reports", "Film a movie"]
  },
  {
    text: "Are you more into learning physics or playing football?",
    options: ["Learning physics", "Playing football"]
  },
  {
    text: "Would you rather organize a community event or paint a digital portrait?",
    options: ["Organize event", "Paint digital portrait"]
  },
  {
    text: "Would you rather direct a short film or work on robotics?",
    options: ["Direct a film", "Work on robotics"]
  },
  {
    text: "Do you enjoy crafting marketing slogans or optimizing databases?",
    options: ["Marketing slogans", "Optimizing databases"]
  },
  {
    text: "Would you rather design an infographic or reconcile financial records?",
    options: ["Design infographic", "Reconcile records"]
  },
  {
    text: "Do you prefer a coding bootcamp or entrepreneurship workshop?",
    options: ["Coding bootcamp", "Entrepreneurship workshop"]
  },
  {
    text: "Would you rather join a choir or play chess competitively?",
    options: ["Join a choir", "Play chess"]
  },
  {
    text: "Are you more excited about UX design or quantitative analysis?",
    options: ["UX design", "Quantitative analysis"]
  },
  {
    text: "Do you enjoy making beats or creating spreadsheets?",
    options: ["Making beats", "Creating spreadsheets"]
  },
  {
    text: "Would you rather referee a sports game or analyze lab results?",
    options: ["Referee game", "Analyze lab results"]
  },
  {
    text: "Would you rather take a public speaking class or attend a logic seminar?",
    options: ["Public speaking", "Logic seminar"]
  },
  {
    text: "Would you prefer working on a documentary or doing economic forecasts?",
    options: ["Work on documentary", "Economic forecasts"]
  },
  {
    text: "Do you like creating mood boards or running simulations?",
    options: ["Mood boards", "Running simulations"]
  },
  {
    text: "Would you rather write fiction or manage a team project?",
    options: ["Write fiction", "Manage team project"]
  },
  {
    text: "Would you rather develop a physics model or design a new logo?",
    options: ["Develop physics model", "Design a logo"]
  },
  {
    text: "Do you enjoy training others in fitness or reviewing literature?",
    options: ["Fitness training", "Reviewing literature"]
  },
  {
    text: "Would you prefer performing in a band or conducting research?",
    options: ["Perform in a band", "Conduct research"]
  },
  {
    text: "Would you rather edit a podcast or manage a stock portfolio?",
    options: ["Edit podcast", "Manage portfolio"]
  },
  {
    text: "Do you enjoy debating topics or writing program code?",
    options: ["Debating topics", "Writing code"]
  },
  {
    text: "Would you rather illustrate a children’s book or balance a business budget?",
    options: ["Illustrate book", "Balance budget"]
  },
  {
    text: "Do you enjoy playing team sports or solving math problems?",
    options: ["Team sports", "Math problems"]
  },
  {
    text: "Would you prefer starting a business or creating concept art?",
    options: ["Starting business", "Creating art"]
  }
];

// Category tracker
const categories = {
  'Design_Creativity': 0,
  'Technology_Programming': 0,
  'Business_Marketing': 0,
  'Finance_Accounting': 0,
  'Communication_Writing': 0,
  'Music': 0,
  'Sports': 0,
  'Math_Science': 0
};

// 40 mappings corresponding to questions
let questionMappings = [
  [{ Music: 1 }, { Math_Science: 1 }],
  [{ Sports: 1 }, { Communication_Writing: 1 }],
  [{ Technology_Programming: 1 }, { Finance_Accounting: 1 }],
  [{ Math_Science: 1 }, { Design_Creativity: 1 }],
  [{ Business_Marketing: 1 }, { Technology_Programming: 1 }],
  [{ Business_Marketing: 1 }, { Design_Creativity: 1 }],
  [{ Finance_Accounting: 1 }, { Design_Creativity: 1 }],
  [{ Music: 1 }, { Technology_Programming: 1 }],
  [{ Sports: 1 }, { Design_Creativity: 1 }],
  [{ Math_Science: 1 }, { Communication_Writing: 1 }],
  [{ Sports: 1 }, { Math_Science: 1 }],
  [{ Music: 1 }, { Business_Marketing: 1 }],
  [{ Design_Creativity: 1 }, { Communication_Writing: 1 }],
  [{ Math_Science: 1 }, { Business_Marketing: 1 }],
  [{ Design_Creativity: 1 }, { Finance_Accounting: 1 }],
  [{ Communication_Writing: 1 }, { Technology_Programming: 1 }],
  [{ Music: 1 }, { Design_Creativity: 1 }],
  [{ Finance_Accounting: 1 }, { Design_Creativity: 1 }],
  [{ Math_Science: 1 }, { Sports: 1 }],
  [{ Business_Marketing: 1 }, { Design_Creativity: 1 }],
  [{ Design_Creativity: 1 }, { Technology_Programming: 1 }],
  [{ Business_Marketing: 1 }, { Technology_Programming: 1 }],
  [{ Design_Creativity: 1 }, { Finance_Accounting: 1 }],
  [{ Technology_Programming: 1 }, { Business_Marketing: 1 }],
  [{ Music: 1 }, { Math_Science: 1 }],
  [{ Design_Creativity: 1 }, { Math_Science: 1 }],
  [{ Music: 1 }, { Finance_Accounting: 1 }],
  [{ Sports: 1 }, { Math_Science: 1 }],
  [{ Communication_Writing: 1 }, { Math_Science: 1 }],
  [{ Design_Creativity: 1 }, { Finance_Accounting: 1 }],
  [{ Design_Creativity: 1 }, { Communication_Writing: 1 }],
  [{ Communication_Writing: 1 }, { Business_Marketing: 1 }],
  [{ Math_Science: 1 }, { Design_Creativity: 1 }],
  [{ Sports: 1 }, { Communication_Writing: 1 }],
  [{ Music: 1 }, { Math_Science: 1 }],
  [{ Music: 1 }, { Finance_Accounting: 1 }],
  [{ Communication_Writing: 1 }, { Technology_Programming: 1 }],
  [{ Design_Creativity: 1 }, { Finance_Accounting: 1 }],
  [{ Sports: 1 }, { Math_Science: 1 }],
  [{ Business_Marketing: 1 }, { Design_Creativity: 1 }],
  [{ Technology_Programming: 1 }, { Sports: 1 }],
  [{ Music: 1 }, { Communication_Writing: 1 }],
  [{ Finance_Accounting: 1 }, { Communication_Writing: 1 }],
  [{ Design_Creativity: 1 }, { Sports: 1 }],
  [{ Business_Marketing: 1 }, { Math_Science: 1 }],
  [{ Music: 1 }, { Design_Creativity: 1 }],
  [{ Technology_Programming: 1 }, { Finance_Accounting: 1 }]
];

//  Trim both arrays to first 40 items
questions = questions.slice(0, 40);
questionMappings = questionMappings.slice(0, 40);

//  Shuffle question-mapping pairs together
let combined = questions.map((q, i) => ({
  question: q,
  mapping: questionMappings[i]
}));

combined = combined.sort(() => Math.random() - 0.5);

questions = combined.map(item => item.question);
questionMappings = combined.map(item => item.mapping);

// DOM Elements
const questionText = document.getElementById("question-text");
const resultDiv = document.getElementById("recommendation-result");
const progressLabel = document.getElementById("question-progress");
const option1Btn = document.getElementById("option1");
const option2Btn = document.getElementById("option2");

let totalQuestions = questions.length;

function renderQuestion() {
  const q = questions[currentQuestion];
  questionText.textContent = q.text;
  option1Btn.textContent = q.options[0];
  option2Btn.textContent = q.options[1];
  updateProgressLabel();
}

//  Handle answer
function submitAnswer(option) {
  const mapping = questionMappings[currentQuestion][option - 1];
  for (let cat in mapping) {
    categories[cat] += mapping[cat];
  }

  currentQuestion++;

  if (currentQuestion < totalQuestions) {
    renderQuestion();
    updateProgressLabel();
  }else {
    fetch("recommend.php", {
      method: "POST",
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ categoryScores: categories })
    })
    .then(response => response.json())
    .then(data => {
      const recommendationBox = document.getElementById("ai-recommendation-box");
      recommendationBox.innerHTML = `
        <h2 class="text-xl font-bold text-gray-800 mb-4">Didn't feel satisfied with our recommendation? Try again!</h2>
        <div class="text-center">
          <button onclick="retryRecommendation()" class="px-4 py-2 bg-blue-800 text-white rounded hover:bg-gray-300">
            <i class="fas fa-redo-alt"></i> Retry
          </button>
        </div>
      `;

      // Load new skills via AJAX based on the recommended category
      const skillGrid = document.querySelector(".grid.max-w-6xl"); // the skills section

      //  show loading spinner or message
      skillGrid.innerHTML = `
        <div class="col-span-full text-center text-gray-500">Loading recommended skills...</div>
      `;

      // Fetch filtered skills from PHP
      fetch(`fetch_dashboard.php?skill_category=${encodeURIComponent(data.skill)}`)
        .then(response => response.json())
        .then(skills => {
          if (skills.length === 0) {
            skillGrid.innerHTML = `
              <div class="col-span-full text-center text-red-500">No skills found for this category.</div>
            `;
          } else {
            skillGrid.innerHTML = ""; // Clear placeholder

            skills.forEach(skill => {
              skillGrid.innerHTML += `
                <a href="viewSkillUsers.php?skill_id=${encodeURIComponent(skill.skill_id)}" class="block">
                  <img src="${skill.image_url ?? 'image/default.png'}" alt="Skill Image" class="w-full h-40 object-cover rounded">
                  <div class="skill-card border rounded-lg shadow-md p-4 hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold">${skill.title}</h3>
                    <p class="text-sm text-red-600">By ${skill.description}</p>
                  </div>
                </a>
              `;
            });
          }
        });
    });
  }
}

// Update progress
function updateProgressLabel() {
  progressLabel.textContent = `Question ${currentQuestion + 1} of ${totalQuestions}`;
}


//Initialize
document.addEventListener("DOMContentLoaded", () => {
  renderQuestion();
});

function retryRecommendation() {
     // Redirect to dashboard.php
  window.location.href = 'dashboard.php'; // This will redirect to the dashboard page
}


</script>


</body>
</html>
