<?php
// File: faculty.php
session_start();
require_once 'functions.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create_assessment') {
        $title = $_POST['title'];
        $questions = json_decode($_POST['questions'], true);
        $deadline = $_POST['deadline'];

        try {
            createAssessment($title, $questions, $deadline);
            $success = "Assessment created successfully!";
        } catch (Exception $e) {
            $error = "Error creating assessment: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Faculty Dashboard</h1>
        <div class="mb-4">
            <a href="index.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Home</a>
            <a href="faculty.php?action=create_assessment" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Create Assessment</a>
            <a href="faculty.php?action=manage_assessments" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Manage Assessments</a>
        </div>

        <?php if ($action === 'create_assessment'): ?>
            <!-- Create Assessment Form -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Create Assessment</h2>
                <form id="assessmentForm" method="POST" action="faculty.php?action=create_assessment">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Assessment Title</label>
                        <input type="text" id="title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div class="mb-4">
                        <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                        <input type="datetime-local" id="deadline" name="deadline" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2 text-gray-700">AI-Powered Question Generation</h3>
                        <div class="space-y-2 mb-2">
                            <input type="text" id="topic" placeholder="Enter topic for questions" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Questions</label>
                                    <input type="number" id="numQuestions" value="1" min="1" max="10" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Marks per Question</label>
                                    <input type="number" id="marksPerQuestion" value="1" min="1" max="10" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        <button type="button" id="generateQuestions" class="w-full bg-purple-600 text-white p-2 rounded-md hover:bg-purple-700 transition duration-300">
                            Generate Questions
                        </button>
                    </div>
                    <div id="questionsContainer"></div>
                    <button type="button" id="addQuestion" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Add Question</button>
                    <input type="hidden" id="questions" name="questions">
                    <button type="submit" class="mt-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Create Assessment</button>
                </form>
            </div>
        <?php elseif ($action === 'manage_assessments'): ?>
            <!-- Manage Assessments -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Manage Assessments</h2>
                <?php
                $assessments = getAssessments();
                foreach ($assessments as $assessment) {
                    echo "<div class='mb-4 p-4 border rounded'>";
                    echo "<h3 class='text-xl font-bold'>{$assessment['title']}</h3>";
                    echo "<p>Deadline: {$assessment['deadline']}</p>";
                    echo "<a href='faculty.php?action=view_results&id={$assessment['id']}' class='text-blue-500 hover:underline'>View Results</a>";
                    echo "</div>";
                }
                ?>
            </div>
        <?php elseif ($action === 'view_results'): ?>
            <!-- View Assessment Results -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Assessment Results</h2>
                <?php
                $assessmentId = $_GET['id'];
                $results = getAssessmentResults($assessmentId);
                foreach ($results as $result) {
                    echo "<div class='mb-4 p-4 border rounded'>";
                    echo "<h3 class='text-xl font-bold'>{$result['student_name']}</h3>";
                    echo "<p>Score: {$result['score']}</p>";
                    echo "</div>";
                }
                ?>
            </div>
        <?php else: ?>
            <!-- Faculty Home -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Welcome, Faculty!</h2>
                <p>Select an action from the menu above.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        let questionCount = 0;
        let isGenerating = false;

        function addQuestion(questionData = null) {
            questionCount++;
            const questionHtml = `
                <div class="question mb-4 p-4 border rounded">
                    <h3 class="text-lg font-semibold mb-2">Question ${questionCount}</h3>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" class="questionText p-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required value="${questionData ? questionData.questionText : ''}">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Options (comma-separated)</label>
                        <input type="text" class="options p-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required value="${questionData ? questionData.options.join(', ') : ''}">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                        <input type="text" class="correctAnswer p-1 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required value="${questionData ? questionData.correctAnswer : ''}">
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700">Marks</label>
                        <input type="number" class="marks mt-1 p-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required value="${questionData ? questionData.marks : ''}">
                    </div>
                </div>
            `;
            $('#questionsContainer').append(questionHtml);
        }

        // Add this line to bind the click event to the "Add Question" button
        $('#addQuestion').click(function() {
            addQuestion();
        });

        $('#assessmentForm').submit(function(e) {
            e.preventDefault();
            const questions = [];
            $('.question').each(function() {
                const question = {
                    questionText: $(this).find('.questionText').val(),
                    options: $(this).find('.options').val().split(',').map(option => option.trim()),
                    correctAnswer: $(this).find('.correctAnswer').val(),
                    marks: parseInt($(this).find('.marks').val())
                };
                questions.push(question);
            });
            $('#questions').val(JSON.stringify(questions));
            this.submit();
        });

        $('#generateQuestions').click(function() {
            if (isGenerating) return;

            const topic = $('#topic').val();
            const numQuestions = $('#numQuestions').val();
            const marksPerQuestion = $('#marksPerQuestion').val();

            if (!topic) {
                alert('Please enter a topic for question generation.');
                return;
            }

            isGenerating = true;
            const $button = $(this);
            const originalText = $button.text();
            $button.html('<span class="spinner"></span> Generating...');
            $button.prop('disabled', true);

            $.ajax({
                url: 'api/generate_questions.php',
                method: 'POST',
                data: {
                    topic: topic,
                    numQuestions: numQuestions,
                    marksPerQuestion: marksPerQuestion
                },
                dataType: 'json',
                success: function(data) {
                    data.forEach(question => addQuestion(question));
                },
                error: function(xhr, status, error) {
                    alert('Error generating questions: ' + error);
                },
                complete: function() {
                    isGenerating = false;
                    $button.html(originalText);
                    $button.prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>
