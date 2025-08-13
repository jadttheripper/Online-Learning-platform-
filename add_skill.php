<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$userId = $_SESSION['user_id'];

// Handle quick-add skill via card click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_add_skill_id'])) {
    $quickSkillId = intval($_POST['quick_add_skill_id']);

    // Skill limit check
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_skill WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetchColumn() >= 3) {
        $_SESSION['error_message'] = "You cannot add more than 3 skills.";
        header("Location: dashboard.php");
        exit();
    }

    // Prevent duplicate skill
    $stmt = $conn->prepare("SELECT 1 FROM user_skill WHERE user_id = ? AND skill_id = ?");
    $stmt->execute([$userId, $quickSkillId]);
    if (!$stmt->fetch()) {
        $stmt = $conn->prepare("INSERT INTO user_skill (user_id, skill_id) VALUES (?, ?)");
        $stmt->execute([$userId, $quickSkillId]);
    }

    header("Location: profile.php");
    exit();
}

// Category => image mapping
$categoryImageMap = [
    'Technology_Programming' => 'image/Technology_Programming.png',
    'Design_Creativity'      => 'image/Design_Creativity.png',
    'Business_Marketing'     => 'image/Business_Marketing.png',
    'Finance_Accounting'     => 'image/Finance_Accounting.png',
    'Communication_Writing'  => 'image/Communication_Writing.png',
    'Music'                  => 'image/Music.png',
    'Sports'                 => 'image/Sports.png',
    'Math_Science'           => 'image/Math_Science.png'
];

// Handle full form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['quick_add_skill_id'])) {
    // ✅ Skill limit check added
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_skill WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetchColumn() >= 3) {
        $error = "You cannot add more than 3 skills.";
    } else {
        $selectedSkillId = $_POST['selected_skill_id'] ?? null;
        $title = trim(strip_tags($_POST['title']));
        $description = trim(strip_tags($_POST['description']));
        $category = $_POST['category'] ?? '';

        if (($selectedSkillId && !is_numeric($selectedSkillId)) || $category === '') {
            $error = "Invalid form input.";
        } else {
            try {
                if ($selectedSkillId) {
                    $stmt = $conn->prepare("SELECT * FROM skill WHERE skill_id = ?");
                    $stmt->execute([$selectedSkillId]);
                    $existingSkill = $stmt->fetch();

                    if (!$existingSkill) throw new Exception("Selected skill not found.");

                    $skillId = $existingSkill['skill_id'];
                    $title = $existingSkill['title'];
                    $userSkillDescription = ($description !== $existingSkill['description']) ? $description : null;
                } else {
                    if ($title === '') {
                        $error = "Title is required.";
                        return;
                    }

                    $imageUrl = $categoryImageMap[$category] ?? '';

                    $stmt = $conn->prepare("SELECT skill_id, description FROM skill WHERE title = ?");
                    $stmt->execute([$title]);
                    $existingSkill = $stmt->fetch();

                    if (!$existingSkill) {
                        $stmt = $conn->prepare("INSERT INTO skill (title, description, skill_category, image_url) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$title, $description, $category, $imageUrl]);
                        $skillId = $conn->lastInsertId();
                        $userSkillDescription = null;
                    } else {
                        $skillId = $existingSkill['skill_id'];
                        $userSkillDescription = ($description !== $existingSkill['description']) ? $description : null;
                    }
                }

                $stmt = $conn->prepare("SELECT 1 FROM user_skill WHERE user_id = ? AND skill_id = ?");
                $stmt->execute([$userId, $skillId]);
                if (!$stmt->fetch()) {
                    $stmt = $conn->prepare("INSERT INTO user_skill (user_id, skill_id, user_skill_description) VALUES (?, ?, ?)");
                    $stmt->execute([$userId, $skillId, $userSkillDescription]);
                }

                header("Location: profile.php");
                exit();
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<?php if (isset($error)): ?>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const modal = document.getElementById("errorModal");
      const message = document.getElementById("modalMessage");
      message.textContent = "<?php echo addslashes($error); ?>";
      modal.classList.remove("hidden");
    });
  </script>
<?php endif; ?>

<?php
$stmt = $conn->query("SELECT skill_id, title, image_url FROM skill ORDER BY title ASC");
$existingSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Skill</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // JavaScript to handle the active state
    document.addEventListener("DOMContentLoaded", function() {
      const badges = document.querySelectorAll('.badge');
      badges.forEach(badge => {
        badge.addEventListener('click', function() {
          badges.forEach(b => b.classList.remove('bg-opacity-80', 'border-2', 'border-blue-500'));
          badge.classList.add('bg-opacity-80', 'border-2', 'border-blue-500'); // Set active state
          badge.previousElementSibling.checked = true; // Check the corresponding radio input
        });
      });
    });
  </script>
  <style>
    /* Styling the badges for better design */
    .badge {
      transition: all 0.3s ease;
    }

    .badge:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .badge.selected {
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
 
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
  <div class="bg-white p-8 rounded shadow-lg w-full max-w-lg space-y-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-blue-600">Add a New Skill</h1>

    <form action="add_skill.php" method="POST" class="space-y-5">
  <div>
    

    <label class="block text-sm font-medium text-gray-700 mt-4">Choose an Existing Skill</label>
    <div id="existingSkills" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-2">
      <?php foreach ($existingSkills as $skill): ?>
        <div
          class="skill-card cursor-pointer border border-gray-300 rounded-lg p-4 hover:shadow-lg transition-all flex items-center gap-3 bg-white"
          data-skill-title="<?php echo htmlspecialchars($skill['title']); ?>"
          data-skill-id="<?php echo $skill['skill_id']; ?>"
        >
          <img src="<?php echo htmlspecialchars($skill['image_url']); ?>" alt="" class="w-10 h-10 object-cover rounded-full">
          <span class="font-medium text-sm"><?php echo htmlspecialchars($skill['title']); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="selected_skill_id" id="selectedSkillId">
    <input type="text" name="title" id="titleInput" required
           class="mt-3 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
           placeholder="Or type a new skill title...">
  </div>

  <div>
    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
    <textarea name="description" id="description" rows="4"
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
              placeholder="Optional description about your skill..."></textarea>
  </div>

  <!-- Static Skill Categories -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Select Skill Category</label>
    <div class="flex flex-wrap gap-4 mt-2">
      <?php
      $categories = [
          "Technology_Programming" => "Technology & Programming",
          "Design_Creativity" => "Design & Creativity",
          "Business_Marketing" => "Business & Marketing",
          "Finance_Accounting" => "Finance & Accounting",
          "Communication_Writing" => "Communication & Writing",
          "Music" => "Music",
          "Sports" => "Sports",
          "Math_Science" => "Math & Science"
      ];
      $badgeColors = [
          "Technology_Programming" => "from-blue-400 via-blue-500 to-blue-600",
          "Design_Creativity" => "from-yellow-400 via-yellow-500 to-yellow-600",
          "Business_Marketing" => "from-green-400 via-green-500 to-green-600",
          "Finance_Accounting" => "from-purple-400 via-purple-500 to-purple-600",
          "Communication_Writing" => "from-teal-400 via-teal-500 to-teal-600",
          "Music" => "from-indigo-400 via-indigo-500 to-indigo-600",
          "Sports" => "from-orange-400 via-orange-500 to-orange-600",
          "Math_Science" => "from-red-400 via-red-500 to-red-600"
      ];
      foreach ($categories as $value => $label): ?>
        <label class="flex items-center space-x-2 cursor-pointer">
          <input type="radio" name="category" value="<?= $value ?>" class="hidden" required>
          <span class="badge px-6 py-3 bg-gradient-to-r <?= $badgeColors[$value] ?> text-white text-sm rounded-full hover:bg-opacity-80 transition-all"><?= $label ?></span>
        </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="flex justify-between">
    <a href="profile.php" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-all">
      Add Skill
    </button>
  </div>
</form>
  </div>
  <script>
document.addEventListener("DOMContentLoaded", () => {
    const skillCards = document.querySelectorAll(".skill-card");
    const selectedSkillIdInput = document.getElementById("selectedSkillId");
    const titleInput = document.getElementById("titleInput");

    skillCards.forEach(card => {
      card.addEventListener("click", () => {
        const skillId = card.dataset.skillId;
        const skillTitle = card.dataset.skillTitle;

        if (!confirm(`Add "${skillTitle}" to your profile?`)) return;

        // Disable inputs during request
        skillCards.forEach(c => c.classList.remove("ring", "ring-blue-400"));
        card.classList.add("ring", "ring-blue-400");

        const formData = new FormData();
        formData.append('quick_add_skill_id', skillId);

        fetch('add_skill.php', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (response.redirected) {
            window.location.href = response.url;
          } else {
            alert("Error adding skill.");
            card.classList.remove("ring", "ring-blue-400");
          }
        })
        .catch(() => {
          alert("Network error.");
          card.classList.remove("ring", "ring-blue-400");
        });
      });
    });

    // Clear selection when typing a new skill
    titleInput.addEventListener("input", () => {
      selectedSkillIdInput.value = '';
      skillCards.forEach(c => c.classList.remove("ring", "ring-blue-400"));
    });
  });
  </script>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
  <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full text-center">
    <h2 class="text-xl font-semibold text-red-600 mb-4">Error</h2>
    <p id="modalMessage" class="text-gray-700"></p>
    <button onclick="document.getElementById('errorModal').classList.add('hidden')"
            class="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
      Close
    </button>
  </div>
</div>



</body>

</html>
