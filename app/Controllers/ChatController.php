<?php
namespace App\Controllers;

class ChatController extends BaseController {
    private $aiChatModel;
    private $authModel;
    private $productsModel;

    public function __construct() {
        $this->aiChatModel = new \App\Models\AIChat();
        $this->authModel = new \App\Models\Auth();
        $this->productsModel = new \App\Models\Products();
    }

    public function index() {
        if (!$this->authModel->isLoggedIn()) {
            return redirect('/login');
            exit;
        }

        require APP_PATH . '/views/chat/index.php';
    }

    public function sendMessage() {
        try {
            if (!$this->authModel->isLoggedIn()) {
                throw new \Exception('Not authenticated');
            }

            $message = $_POST['message'] ?? '';
            $context = $_POST['context'] ?? [];

            if (!$message) {
                throw new \Exception('Message cannot be empty');
            }

            // Analyze sentiment
            $sentiment = $this->aiChatModel->analyzeSentiment($message);
            
            // Generate response
            $response = $this->aiChatModel->generateResponse($message, $context);

            // Save chat history
            $this->saveChatHistory($message, $response, $sentiment);

            echo json_encode([
                'success' => true,
                'response' => $response,
                'sentiment' => $sentiment
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function translateMessage() {
        try {
            if (!$this->authModel->isLoggedIn()) {
                throw new \Exception('Not authenticated');
            }

            $message = $_POST['message'] ?? '';
            $targetLanguage = $_POST['targetLanguage'] ?? '';

            if (!$message || !$targetLanguage) {
                throw new \Exception('Missing required parameters');
            }

            $translation = $this->aiChatModel->translateMessage($message, $targetLanguage);
            echo json_encode([
                'success' => true,
                'translation' => $translation
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getRecommendations() {
        try {
            if (!$this->authModel->isLoggedIn()) {
                throw new \Exception('Not authenticated');
            }

            $preferences = $_POST['preferences'] ?? [];
            
            // Get product recommendations
            $recommendations = $this->aiChatModel->generateProductRecommendations($preferences);
            
            // Enhance recommendations with actual product data
            foreach ($recommendations as &$recommendation) {
                $product = $this->productsModel->getProductById($recommendation['id']);
                if ($product) {
                    $recommendation = array_merge($recommendation, $product);
                }
            }

            echo json_encode([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFAQ() {
        try {
            if (!$this->authModel->isLoggedIn()) {
                throw new \Exception('Not authenticated');
            }

            $topic = $_POST['topic'] ?? '';
            if (!$topic) {
                throw new \Exception('Topic is required');
            }

            $faq = $this->aiChatModel->generateFAQ($topic);
            
            // Parse FAQ into array format
            $faqArray = $this->parseFAQ($faq);

            echo json_encode([
                'success' => true,
                'faq' => $faqArray
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function saveChatHistory($message, $response, $sentiment) {
        try {
            $userId = $_SESSION['user_id'];
            $stmt = $this->db->prepare("
                INSERT INTO chat_history (user_id, message, response, sentiment, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $message, $response, $sentiment]);
        } catch (\Exception $e) {
            // Log error but don't fail the chat interaction
            error_log("Failed to save chat history: " . $e->getMessage());
        }
    }

    private function parseFAQ($faqText) {
        $faqArray = [];
        $lines = explode("\n", $faqText);
        
        for ($i = 0; $i < count($lines); $i += 2) {
            $question = trim($lines[$i]);
            $answer = isset($lines[$i + 1]) ? trim($lines[$i + 1]) : '';
            
            if ($question && $answer) {
                $faqArray[] = [
                    'question' => $question,
                    'answer' => $answer
                ];
            }
        }
        
        return $faqArray;
    }

    public function analyzeFeedback() {
        try {
            if (!$this->authModel->isLoggedIn()) {
                throw new \Exception('Not authenticated');
            }

            $feedback = $_POST['feedback'] ?? '';
            if (!$feedback) {
                throw new \Exception('Feedback is required');
            }

            $analysis = $this->aiChatModel->analyzeCustomerFeedback($feedback);
            echo json_encode([
                'success' => true,
                'analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getChatHistory() {
        try {
            if (!$this->authModel->isLoggedIn()) {
                throw new \Exception('Not authenticated');
            }

            $userId = $_SESSION['user_id'];
            $stmt = $this->db->prepare("
                SELECT message, response, sentiment, created_at
                FROM chat_history
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$userId]);
            $history = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
