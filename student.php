<?php
// File: student.php
session_start();
require_once 'functions.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'submit_assessment') {
        $assessmentId = $_POST['assessment_id'];
        $answers = isset($_POST['answers']) ? $_POST['answers'] : array();
        
        if (empty($answers)) {
            $error = "Please answer at least one question before submitting.";
        } else {
            // Get or create student (you might want to use a login system in a real application)
            $studentId = getOrCreateStudent("John Doe", "john@example.com");
            
            $score = submitAssessment($assessmentId, $answers, $studentId);
            $success = "Assessment submitted successfully! Your score: $score";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Student Dashboard</h1>
        <div class="mb-4">
            <a href="index.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Home</a>
            <a href="student.php?action=view_assessments" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">View Assessments</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <h3 class="text-xl font-bold">🎉 Congratulations! 🎉</h3>
                <p><?php echo $success; ?></p>
            </div>
            <div id="confetti-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>
            <script>
                // Trigger confetti
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });

                // Animate the success message
                const successMessage = document.getElementById('successMessage');
                successMessage.style.animation = '5s infinite';

                // Add a CSS keyframe animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes pulse {
                        0% { transform: scale(1); }
                        50% { transform: scale(1.05); }
                        100% { transform: scale(1); }
                    }
                `;
                document.head.appendChild(style);
            </script>
        <?php endif; ?>

        <?php if ($action === 'view_assessments'): ?>
            <!-- View Available Assessments -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Available Assessments</h2>
                <?php
                $assessments = getAvailableAssessments();
                if (empty($assessments)) {
                    echo "<p>No assessments available at the moment.</p>";
                } else {
                    foreach ($assessments as $assessment) {
                        echo "<div class='mb-4 p-4 border rounded'>";
                        echo "<h3 class='text-xl font-bold'>{$assessment['title']}</h3>";
                        echo "<p>Deadline: {$assessment['deadline']}</p>";
                        echo "<a href='student.php?action=take_assessment&id={$assessment['id']}' class='text-blue-500 hover:underline'>Take Assessment</a>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        <?php elseif ($action === 'take_assessment'): ?>
            <!-- Take Assessment -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Take Assessment</h2>
                <?php
                $assessmentId = $_GET['id'];
                $assessment = getAssessment($assessmentId);
                if ($assessment): ?>
                    <form method="POST" action="student.php?action=submit_assessment">
                        <input type="hidden" name="assessment_id" value="<?php echo $assessmentId; ?>">
                        <h3 class="text-xl font-bold mb-4"><?php echo $assessment['title']; ?></h3>
                        <?php foreach ($assessment['questions'] as $index => $question): ?>
                            <div class="mb-4">
                                <p class="font-bold"><?php echo $question['question_text']; ?></p>
                                <?php foreach (json_decode($question['options']) as $option): ?>
                                    <label class="block">
                                        <input type="radio" name="answers[<?php echo $index; ?>]" value="<?php echo $option; ?>">
                                        <?php echo $option; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" id="submitBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Submit Assessment
                        </button>
                        <script>
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const submitBtn = document.getElementById('submitBtn');
                                submitBtn.innerHTML = 'Submitting...';
                                submitBtn.disabled = true;
                            });
                        </script>
                    </form>
                <?php else: ?>
                    <p>Assessment not found or no longer available.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Student Home -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Welcome, Student!</h2>
                <p>Select an action from the menu above.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
