<?php
namespace Core\Engine;

use Core\NLP\NLPPipeline;
use Core\Memory\Storage\OnlineMemoryBridge;
use Core\Memory\RAG\LocalRAG;
use Core\Evaluation\ConfidenceScorer;
use Core\Training\Feedback\LocalTeacherBridge;
use Core\Training\Feedback\FeedbackLoop;
use Core\SQLGenerator\SQLGenerator;

/**
 * HRITIK AI - MAIN ENGINE (SYSTEMIC REFACTOR)
 * The central orchestrator now moved into the Engine folder.
 */
class MainEngine {
    private $nlp;
    private $memory;
    private $mood;
    private $tools;
    private $agenticCore;
    private $moduleIntegrator;
    private $sqlGen;
    private LocalRAG $rag;
    private ConfidenceScorer $confidenceScorer;
    private LocalTeacherBridge $localTeacher;
    private FeedbackLoop $feedbackLoop;
    private $personalMemory;
    
    private array $history = [];

    public function __construct() {
        $this->nlp = new NLPPipeline();
        $this->memory = new OnlineMemoryBridge();
        $this->mood = new \Core\NLP\NeuralMoodAnalyzer();
        $this->tools = new \Core\Tools\ToolRegistry();
        $this->agenticCore = new AgenticCore();
        $this->moduleIntegrator = new ModuleIntegrator();
        $this->sqlGen = new SQLGenerator();
        $this->rag = new LocalRAG();
        $this->confidenceScorer = new ConfidenceScorer();
        $this->localTeacher = new LocalTeacherBridge();
        $this->feedbackLoop = new FeedbackLoop($this->rag);
        $this->personalMemory = new \Core\Memory\Storage\PersonalMemoryBridge();
    }

    public function processPrompt(string $prompt, string $sessionId = 'default', $onToken = null): array {
        $prompt = trim((string)preg_replace('/^(?:\xEF\xBB\xBF|\x{FEFF})/u', '', $prompt));
        if (empty($prompt)) {
            return ['status' => 'success', 'response' => '', 'intent' => 'empty'];
        }

        $analysis = $this->nlp->process($prompt);
        $intent = $analysis['intent'] ?? 'general';
        $response = null;
        $source = 'none';
        $evidence = [];

        $response = $this->basicConversation($prompt);
        if ($response !== null) {
            $source = 'built_in_conversation';
        }

        if (!$response) {
            $routed = $this->routeByIntent($intent, $prompt);
            if ($routed !== null) {
                $response = $routed;
                $source = 'intent_router';
            }
        }

        // 0. Permanent Memory Recall (Personal Context)
        $personalContext = $this->personalMemory->recall($prompt);
        if ($personalContext) {
            $prompt = "[User Context: " . trim($personalContext) . "] " . $prompt;
        }

        // 1. Check SQL Knowledge (Dynamic)
        $sql = $this->sqlGen->generate('search_knowledge', ['query' => $prompt]);
        if (!$response && $sql) {
            $dbRes = $this->queryDB($sql);
            if (!empty($dbRes)) {
                $potentialResponse = $dbRes[0]['k_value'];
                // Safeguard against raw code dumping on conversational queries
                $isCode = str_contains($potentialResponse, '<?php') || str_contains($potentialResponse, 'namespace ');
                $askedForCode = preg_match('/(code|script|file|function|class)/i', $prompt);
                
                if (!$isCode || $askedForCode) {
                    $response = $potentialResponse;
                    $source = 'dynamic_sql_knowledge';
                }
            }
        }

        // 2. Check RAG
        if (!$response) {
            $ragRes = $this->rag->answer($prompt);
            if ($ragRes) {
                $response = $ragRes['answer'];
                $source = 'rag';
            }
        }

        // 3. Fallback to Memory
        if (!$response) {
            $sql = $this->sqlGen->generate('get_memory', ['prompt' => $prompt]);
            $dbRes = $this->queryDB($sql);
            if (!empty($dbRes)) {
                $response = $dbRes[0]['response'];
                $source = 'dynamic_sql_memory';
            }
        }

        // 4. Online Search (Wikipedia / DuckDuckGo)
        if (!$response || $this->confidenceScorer->score($prompt, $response, $source) < 0.4) {
            require_once dirname(__DIR__) . '/API/ExternalIntelligence.php';
            $external = new \Core\API\ExternalIntelligence();
            $searchRes = $external->search($prompt);
            
            if ($searchRes && !str_contains($searchRes, "I searched the neural networks")) {
                $response = $searchRes;
                $source = 'online_search_api';
            }
        }

        // 5. Neural Reasoning & Teacher Refinement (Filter/Think)
        $confidence = $this->confidenceScorer->score($prompt, $response, $source);
        if ($confidence < 0.7 || !$response || $response === "N/A") {
            $teacherRes = $this->localTeacher->evaluate($prompt, (string)$response, $this->history, $confidence);
            if (($teacherRes['status'] ?? '') === 'success' && !empty($teacherRes['final_answer'])) {
                $response = $teacherRes['final_answer'];
                $source = 'neural_local_teacher';
            }
        }

        // 6. Absolute Final Attempt (Forced Neural Creative Generation)
        if (!$response || $response === "N/A" || strlen($response) < 5) {
            $teacherRes = $this->localTeacher->evaluate($prompt, "FORCE_GENERATE", $this->history, 0);
            if (($teacherRes['status'] ?? '') === 'success' && !empty($teacherRes['final_answer'])) {
                $response = $teacherRes['final_answer'];
                $source = 'forced_neural_generation';
            } else {
                // LAST RESORT: If Teacher is DEAD/CRASHING, use Creativity Core
                $creativity = new \Core\Virtual\NeuralCreativityCore();
                $response = $creativity->imagine();
                $source = 'creativity_fallback';
            }
        }

        // Ensure we NEVER send a generic "Maaf kijiye" unless the API is dead
        if (!$response) {
            $response = "[Neural Link Error] Thinking..."; 
        }

        // Finalize
        $this->feedbackLoop->record($prompt, $prompt, $response, ['source' => $source]);
        if ($source === 'neural_local_teacher' || $source === 'forced_neural_generation') {
            // Check if it's a JSON project plan
            $decoded = json_decode($response, true);
            if (is_array($decoded) && !empty($decoded)) {
                $architect = new \Core\Tools\Intelligence\ProjectArchitect();
                $response = $architect->build($decoded);
                $source = 'neural_project_architect';
            }
        }

        $this->history[] = ['prompt' => $prompt, 'response' => $response];
        // Rolling Context Window: Keep only the 10 most recent interactions to prevent memory overload
        if (count($this->history) > 10) {
            array_shift($this->history);
        }
        
        // Log to History via SQL Gen
        $logSql = $this->sqlGen->generate('save_history', [
            'session_id' => $sessionId,
            'prompt' => $prompt,
            'response' => $response,
            'intent' => $intent
        ]);
        $this->queryDB($logSql);

        // 7. Permanent Learning (Save Personal Facts)
        $this->personalMemory->learn($prompt, $response);

        return [
            'status' => 'success',
            'response' => $response,
            'intent' => $intent,
            'source' => $source,
            'confidence' => $confidence
        ];
    }

    private function queryDB(string $sql): array {
        global $db;
        if (!isset($db) || $db === null) return [];
        $res = $db->query($sql);
        return $res['data'] ?? [];
    }

    private function basicConversation(string $prompt): ?string {
        $text = strtolower(trim($prompt));

        if (preg_match('/^(hi|hello|hey|hlo|helo|namaste|salam)$/i', $text)) {
            return "Namaste bhai! Main Hritik AI ready hoon. Aap bolo, kya karna hai?";
        }

        if (preg_match('/(kaise ho|kese ho|how are you|kya haal)/i', $text)) {
            return "Main theek hoon bhai, ab console bhi stable chal raha hai. Aap apna kaam batao.";
        }

        if (preg_match('/(tumhara naam|tera naam|who are you|kaun ho|kaun hai tu|hritik ai)/i', $text)) {
            return "Main Hritik AI hoon, aapka local PHP assistant. Main project files, commands, debugging aur coding tasks mein help kar sakta hoon.";
        }
        
        if (preg_match('/^(php kya h|what is php)/i', $text)) {
            return "PHP (Hypertext Preprocessor) ek popular open-source server-side scripting language hai jo mukhya roop se web development ke liye use hoti hai.";
        }

        if (preg_match('/(bye|goodbye|alvida|shukria|thank you|thanks)$/i', $text)) {
            return "Alvida bhai! Jab bhi zarurat ho, main yahin hoon.";
        }

        return null;
    }

    private function routeByIntent(string $intent, string $prompt): ?string {
        $text = strtolower(trim($prompt));

        // Direct keywords routing to AgenticCore to enable Command Pattern
        if (preg_match('/^(calculate|solve|calc|run command|execute command|create file|show map|show tree|project structure|debug|fix the bug|audit file|audit code)/i', $text) ||
            preg_match('/^[0-9+\-*\/^().\s]+$/', $text) ||
            preg_match('/[0-9]+\s*[\+\-\*\/^]\s*[0-9]+/', $text)) {
            return $this->agenticCore->solve($prompt);
        }

        switch ($intent) {
            case 'training':
                return "Training ke liye pehle dataset import/train karo:\n" .
                    "1. H:\\xampp\\php\\php.exe datasetrun.php --file=hinglish_conversations.csv\n" .
                    "2. H:\\xampp\\php\\php.exe train_intents.php --file=storage/datasets/hinglish_intents.csv --epochs=3\n" .
                    "3. H:\\xampp\\php\\php.exe scanandlearn.php --path=H:\\xampp\\htdocs\\hritik_ai --limit=500";

            case 'tool_use':
            case 'generate_code':
                return $this->agenticCore->solve($prompt);

            case 'coding':
                // Check if it's a specific debug or audit command, otherwise solve via AgenticCore
                if (preg_match('/(debug|error|fix|audit|check|generate|write)/i', $prompt)) {
                    return $this->agenticCore->solve($prompt);
                }
                return null;

            default:
                return null;
        }
    }
}
