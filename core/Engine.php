<?php
class Engine
{
    private TextCleaner $cleaner;
    private InputNormalizer $inputNormalizer;
    private Tokenizer $tokenizer;
    private Stemmer $stemmer;
    private LanguageDetector $languageDetector;
    private Supervised $supervised;
    private Unsupervised $unsupervised;
    private Predictor $predictor;
    private CodeComposer $codeComposer;
    private MathEvaluator $mathEvaluator;
    private NeuralNetwork $neuralNetwork;
    private Backpropagation $backpropagation;
    private ShortTermMemory $shortTermMemory;
    private LongTermMemory $longTermMemory;
    private ContextManager $contextManager;
    private ProfileMemory $profileMemory;
    private TopicGraphMemory $topicGraphMemory;
    private MemorySafety $memorySafety;
    private MemoryRanker $memoryRanker;
    private IntentDetector $intentDetector;
    private ResponseBuilder $responseBuilder;
    private ConversationalFallback $conversationalFallback;
    private IntentNormalizer $intentNormalizer;
    private ResponseRanker $responseRanker;
    private KnowledgeBase $knowledgeBase;
    private FileMemoryStore $fileMemoryStore;
    private LocalKnowledgeFallback $localKnowledgeFallback;
    private LangMainDictionary $langMainDictionary;
    private QuizGenerator $quizGenerator;
    private TableGenerator $tableGenerator;
    private ?WebKnowledgeClient $webKnowledgeClient = null;
    private UserKnowledgeLearner $userKnowledgeLearner;
    private UrlContentIngestor $urlContentIngestor;
    private TaskPlanner $taskPlanner;
    private ?ToolRouter $toolRouter = null;
    private ExecutionLog $executionLog;
    private Verifier $verifier;
    private ?DomainUpgradeGateway $domainUpgradeGateway = null;
    private ?VisionIngestor $visionIngestor = null;

    public function __construct()
    {
        global $config;
        $this->cleaner = new TextCleaner();
        $this->inputNormalizer = new InputNormalizer();
        $this->tokenizer = new Tokenizer();
        $this->stemmer = new Stemmer();
        $this->languageDetector = new LanguageDetector();
        $this->supervised = new Supervised();
        $this->unsupervised = new Unsupervised();
        $this->predictor = new Predictor();
        $this->codeComposer = new CodeComposer();
        $this->mathEvaluator = new MathEvaluator();
        $this->neuralNetwork = new NeuralNetwork();
        $this->backpropagation = new Backpropagation();
        $this->shortTermMemory = new ShortTermMemory();
        $this->longTermMemory = new LongTermMemory();
        $this->contextManager = new ContextManager();
        $this->profileMemory = new ProfileMemory();
        $this->topicGraphMemory = new TopicGraphMemory();
        $this->memorySafety = new MemorySafety();
        $this->memoryRanker = new MemoryRanker();
        $this->intentDetector = new IntentDetector();
        $this->responseBuilder = new ResponseBuilder();
        $this->conversationalFallback = new ConversationalFallback();
        $this->intentNormalizer = new IntentNormalizer();
        $this->responseRanker = new ResponseRanker();
        $this->knowledgeBase = new KnowledgeBase();
        $this->fileMemoryStore = new FileMemoryStore();
        $this->localKnowledgeFallback = new LocalKnowledgeFallback();
        $this->langMainDictionary = new LangMainDictionary();
        $this->quizGenerator = new QuizGenerator();
        $this->tableGenerator = new TableGenerator();
        $this->userKnowledgeLearner = new UserKnowledgeLearner();
        $this->urlContentIngestor = new UrlContentIngestor();
        $this->taskPlanner = new TaskPlanner();
        $this->executionLog = new ExecutionLog();
        $this->verifier = new Verifier();
        if (class_exists('VisionIngestor')) {
            $this->visionIngestor = new VisionIngestor();
        }
        if (class_exists('DomainUpgradeGateway')) {
            $this->domainUpgradeGateway = new DomainUpgradeGateway();
        }
        if (!class_exists('WebKnowledgeClient')) {
            $wk = __DIR__ . '/ML/WebKnowledgeClient.php';
            if (is_file($wk)) {
                require_once $wk;
            }
        }
        if (class_exists('WebKnowledgeClient')) {
            $this->webKnowledgeClient = new WebKnowledgeClient((bool) ($config['web_knowledge_enabled'] ?? true), (int) ($config['web_timeout_ms'] ?? 5000));
        }
        $this->toolRouter = new ToolRouter(
            $this->webKnowledgeClient,
            $this->mathEvaluator,
            $this->codeComposer,
            $this->quizGenerator,
            $this->tableGenerator,
            $this->urlContentIngestor
        );
    }

    public function run(string $input): array
    {
        $normalizedInput = $this->inputNormalizer->normalize($input);
        $recent = $this->shortTermMemory->recent(6);
        $userTurn = $normalizedInput !== '' ? $normalizedInput : $input;
        $queryTurn = $this->resolveContextualInput($userTurn, $recent);
        $inputLanguage = $this->languageDetector->detect($queryTurn);
        $responseLanguage = $this->languageDetector->preferredResponseLanguage($inputLanguage);
        $this->profileMemory->inferFromInput($input, $responseLanguage);
        $preferredLanguage = $this->profileMemory->getPreferredLanguage();
        if (in_array($preferredLanguage, ['en', 'hi', 'bilingual'], true)) {
            $responseLanguage = $preferredLanguage;
        }
        $this->userKnowledgeLearner->ingest($input, $responseLanguage);

        $clean = $this->cleaner->clean($queryTurn);
        $tokens = $this->tokenizer->tokenize($clean);
        $tokens = StopWords::remove($tokens);
        $tokens = $this->stemmer->stemTokens($tokens);
        $this->topicGraphMemory->rememberTopics($tokens);

        $ruleIntentCandidates = $this->intentDetector->detectMany($queryTurn);
        $ruleIntent = $ruleIntentCandidates[0]['intent'] ?? 'general';
        $ruleIntentScore = (float) ($ruleIntentCandidates[0]['score'] ?? 0.0);
        $prediction = $this->predictor->predict($tokens, $this->supervised, $this->unsupervised);
        $predictionIntent = $this->intentNormalizer->normalize((string) ($prediction['intent'] ?? 'general'));
        $routedIntent = $this->responseRanker->pickBestIntent(
            $queryTurn,
            $predictionIntent,
            (float) ($prediction['confidence'] ?? 0.0),
            $ruleIntentCandidates
        );

        if ($ruleIntentScore >= 0.88 && $ruleIntent !== 'general') {
            $prediction['intent'] = $this->intentNormalizer->normalize($ruleIntent);
        } elseif (
            $ruleIntent === 'general'
            && ($prediction['confidence'] ?? 0.0) >= 0.62
            && $predictionIntent !== 'unknown'
            && ($predictionIntent !== 'code_generate' || $this->isLikelyCodePrompt($input))
        ) {
            $prediction['intent'] = $predictionIntent;
        } else {
            $prediction['intent'] = $this->intentNormalizer->normalize($routedIntent);
        }

        $knowledgeMatch = null;
        $policyMatch = null;
        $effectiveInput = $queryTurn;

        if ($this->isTeachMePrompt($effectiveInput)) {
            $prediction['intent'] = 'normal_chat';
            $prediction['chat_reply'] = $responseLanguage === 'hi'
                ? 'Bilkul. Aap text, notes, ya link bhejo. Main seekh kar bataunga ki maine kya sikha.'
                : 'Sure. Send text, notes, or a link. I will learn and tell you what I learned.';
            $prediction['confidence'] = max($prediction['confidence'], 0.9);
        } elseif (str_starts_with($input, '__image_file__:')) {
            $path = trim(substr($input, strlen('__image_file__:')));
            $img = $this->visionIngestor?->analyzeFile($path);
            if ($img) {
                $ocr = trim((string) ($img['ocr_text'] ?? ''));
                $summary = 'Image analyzed: ' . ($img['mime'] ?? 'unknown')
                    . ', size=' . (int) ($img['width'] ?? 0) . 'x' . (int) ($img['height'] ?? 0);
                if ($ocr !== '') {
                    $summary .= "\nOCR: " . $ocr;
                }
                $prediction['intent'] = 'knowledge_answer';
                $prediction['knowledge_answer'] = $summary;
                $prediction['confidence'] = max($prediction['confidence'], 0.93);
            } else {
                $prediction['intent'] = 'normal_chat';
                $prediction['chat_reply'] = $responseLanguage === 'hi'
                    ? 'Image process nahi ho paayi. JPG/PNG file try karo.'
                    : 'Image could not be processed. Try JPG/PNG.';
            }
        } elseif ($prediction['intent'] === 'task_mode') {
            $taskResponse = $this->runTaskMode($input, $responseLanguage);
            $prediction['intent'] = 'knowledge_answer';
            $prediction['knowledge_answer'] = $taskResponse;
            $prediction['confidence'] = max($prediction['confidence'], 0.93);
        } elseif ($this->isLikelyKnowledgeDocument($input) || $this->isLikelyDefinitionStatement($input)) {
            $prediction['intent'] = 'knowledge_answer';
            $prediction['knowledge_answer'] = $this->buildLearningAck($input, $responseLanguage);
            $prediction['confidence'] = max($prediction['confidence'], 0.95);
        } elseif ($prediction['intent'] === 'number_query') {
            $prediction['number'] = trim($input);
            $prediction['confidence'] = max($prediction['confidence'], 0.96);
        } elseif (in_array($prediction['intent'], ['code_generate', 'engineering_plan', 'debug_help'], true)) {
            $code = $this->codeComposer->compose($input, $responseLanguage);
            $prediction['intent'] = $this->intentNormalizer->normalize((string) ($code['intent'] ?? 'code_generate'));
            $prediction['code_title'] = $code['title'] ?? '';
            $prediction['code_body'] = $code['body'] ?? '';
            $prediction['plan_body'] = $code['body'] ?? '';
            $prediction['debug_body'] = $code['body'] ?? '';
            $prediction['confidence'] = max($prediction['confidence'], 0.95);
        } elseif ($prediction['intent'] === 'math_query') {
            $mathResult = $this->mathEvaluator->evaluateMathQuery($input);
            if ($mathResult !== null) {
                $prediction['math_result'] = is_float($mathResult) ? round($mathResult, 6) : (string) $mathResult;
                $prediction['confidence'] = max($prediction['confidence'], 0.97);
            } else {
                $prediction['intent'] = $this->intentNormalizer->normalize('general');
            }
        } elseif ($prediction['intent'] === 'quiz_request') {
            $prediction['quiz_body'] = $this->quizGenerator->generate($input, $responseLanguage);
            $prediction['confidence'] = max($prediction['confidence'], 0.96);
        } elseif ($prediction['intent'] === 'table_request') {
            $table = $this->tableGenerator->generate($input, $responseLanguage);
            if ($table !== null) {
                $prediction['intent'] = 'knowledge_answer';
                $prediction['knowledge_answer'] = $table;
                $prediction['confidence'] = max($prediction['confidence'], 0.96);
            } else {
                $prediction['intent'] = 'normal_chat';
                $prediction['chat_reply'] = $responseLanguage === 'hi'
                    ? 'à¤•à¤¿à¤¸ à¤¸à¤‚à¤–à¥à¤¯à¤¾ à¤•à¤¾ à¤ªà¤¹à¤¾à¤¡à¤¼à¤¾ à¤šà¤¾à¤¹à¤¿à¤? à¤‰à¤¦à¤¾à¤¹à¤°à¤£: 5 à¤•à¤¾ à¤ªà¤¹à¤¾à¤¡à¤¼à¤¾à¥¤'
                    : 'Which number table do you need? Example: table of 5.';
            }
        } elseif ($prediction['intent'] === 'capital_query') {
            $capital = $this->webKnowledgeClient?->answerCapitalQuery($queryTurn);
            if ($capital !== null) {
                $prediction['intent'] = 'knowledge_answer';
                $prediction['knowledge_answer'] = $capital;
                $prediction['confidence'] = max($prediction['confidence'], 0.97);
            } else {
                $prediction['intent'] = 'normal_chat';
                $prediction['chat_reply'] = $responseLanguage === 'hi'
                    ? 'à¤•à¤¿à¤¸ à¤¦à¥‡à¤¶ à¤•à¥€ à¤°à¤¾à¤œà¤§à¤¾à¤¨à¥€ à¤ªà¥‚à¤›à¤¨à¥€ à¤¹à¥ˆ? à¤‰à¤¦à¤¾à¤¹à¤°à¤£: capital of Japan.'
                    : 'Which country capital do you need? Example: capital of Japan.';
            }
        } elseif ($prediction['intent'] === 'url_ingest') {
            $url = $this->extractFirstUrl($input);
            if ($url !== null) {
                $ingested = $this->urlContentIngestor->ingest($url);
                if ($ingested) {
                    $title = trim((string) ($ingested['title'] ?? 'link'));
                    $summary = (string) ($ingested['summary'] ?? '');
                    $this->userKnowledgeLearner->ingest(($title !== '' ? ($title . ': ') : '') . $summary, $responseLanguage);
                    $prediction['intent'] = 'knowledge_answer';
                    $prediction['knowledge_answer'] = $responseLanguage === 'hi'
                        ? ('à¤²à¤¿à¤‚à¤• à¤ªà¤¢à¤¼ à¤²à¤¿à¤¯à¤¾à¥¤ à¤¶à¥€à¤°à¥à¤·à¤•: ' . ($title !== '' ? $title : 'Unknown') . 'à¥¤ à¤¡à¥‡à¤Ÿà¤¾ à¤¸à¥€à¤– à¤²à¤¿à¤¯à¤¾, à¤…à¤¬ à¤†à¤ª à¤¸à¤µà¤¾à¤² à¤ªà¥‚à¤› à¤¸à¤•à¤¤à¥‡ à¤¹à¥ˆà¤‚à¥¤')
                        : ('Link parsed. Title: ' . ($title !== '' ? $title : 'Unknown') . '. I learned the content; ask me questions now.');
                    $prediction['confidence'] = max($prediction['confidence'], 0.95);
                } else {
                    $prediction['intent'] = 'normal_chat';
                    $prediction['chat_reply'] = $responseLanguage === 'hi'
                        ? 'à¤²à¤¿à¤‚à¤• à¤–à¥à¤² à¤¨à¤¹à¥€à¤‚ à¤ªà¤¾à¤¯à¤¾à¥¤ à¤¦à¥‚à¤¸à¤°à¤¾ à¤²à¤¿à¤‚à¤• à¤­à¥‡à¤œà¥‡à¤‚ à¤¯à¤¾ plain text à¤­à¥‡à¤œà¥‡à¤‚à¥¤'
                        : 'Could not open the link. Send another URL or paste text directly.';
                }
            }
        } elseif ($prediction['intent'] === 'memory_store') {
            $fact = $this->extractMemoryFact($input);
            if ($fact) {
                if ($this->memorySafety->canStore($fact['key'], $fact['value'])) {
                    $this->knowledgeBase->upsertUserFact($fact['key'], $fact['value'], $responseLanguage);
                    $this->fileMemoryStore->remember($fact['key'], $fact['value'], $responseLanguage);
                    $prediction['memory_key'] = $fact['key'];
                    $prediction['memory_value'] = $fact['value'];
                    $prediction['confidence'] = max($prediction['confidence'], 0.93);
                } else {
                    $prediction['intent'] = 'memory_blocked';
                    $prediction['memory_key'] = $fact['key'];
                    $prediction['memory_value'] = $this->memorySafety->safeValue($fact['value']);
                    $prediction['confidence'] = max($prediction['confidence'], 0.95);
                }
            } else {
                $prediction['intent'] = $this->intentNormalizer->normalize('general');
            }
        } elseif ($prediction['intent'] === 'memory_recall') {
            $candidates = [];
            $fact = $this->knowledgeBase->findUserFact($input);
            if ($fact) {
                $candidates[] = $fact;
            }
            $fileFact = $this->fileMemoryStore->recall($input);
            if ($fileFact) {
                $candidates[] = $fileFact;
            }
            if ($fileFact && $this->isDirectFactQuery($input, (string) ($fileFact['key'] ?? ''))) {
                $fact = $fileFact;
            } else {
                $fact = $this->memoryRanker->pickBest($input, $candidates);
            }
            if ($fact) {
                $prediction['memory_key'] = $fact['key'];
                $prediction['memory_value'] = $fact['value'];
                $prediction['confidence'] = max($prediction['confidence'], 0.9);
            } else {
                $prediction['intent'] = $this->intentNormalizer->normalize('general');
            }
        }

        if (in_array($prediction['intent'], ['general', 'question', 'help', 'greeting', 'normal_chat'], true)) {
            $policyMatch = $this->knowledgeBase->findResponsePolicy($queryTurn);
            if ($policyMatch && !empty($policyMatch['response'])) {
                $prediction['intent'] = 'custom_policy';
                $prediction['policy_response'] = (string) $policyMatch['response'];
                $prediction['confidence'] = max($prediction['confidence'], 0.92);
            } else {
                $webQuery = $queryTurn;
                if ($this->isTeachMePrompt($webQuery)) {
                    $prediction['intent'] = 'normal_chat';
                    $prediction['chat_reply'] = $responseLanguage === 'hi'
                        ? 'Bilkul. Aap text, notes, ya link bhejo. Main seekh kar bataunga ki maine kya sikha.'
                        : 'Sure. Send text, notes, or a link. I will learn and tell you what I learned.';
                    $prediction['confidence'] = max($prediction['confidence'], 0.9);
                } elseif ($this->isQuizContinuationPrompt($webQuery, $recent)) {
                    $topicHint = $this->findRecentTopicHint($recent);
                    $prediction['intent'] = 'quiz_request';
                    $prediction['quiz_body'] = $this->quizGenerator->generate($topicHint !== '' ? $topicHint : $input, $responseLanguage);
                    $prediction['confidence'] = max($prediction['confidence'], 0.9);
                } elseif ($this->isCasualPrompt($webQuery)) {
                    $prediction['intent'] = 'normal_chat';
                    $prediction['chat_reply'] = $this->conversationalFallback->reply($webQuery, $responseLanguage, $recent);
                    $prediction['confidence'] = max($prediction['confidence'], 0.86);
                } else {
                $domainAnswer = $this->domainUpgradeGateway?->handle($webQuery, $responseLanguage);
                if (is_string($domainAnswer) && trim($domainAnswer) !== '') {
                    $prediction['intent'] = 'knowledge_answer';
                    $prediction['knowledge_answer'] = $domainAnswer;
                    $prediction['confidence'] = max($prediction['confidence'], 0.88);
                } else {
                $web = null;
                if ($this->isLikelyWebKnowledgePrompt($webQuery)) {
                    $web = $this->webKnowledgeClient?->answerCapitalQuery($webQuery) ?? $this->webKnowledgeClient?->answerWebSnippet($webQuery);
                }
                if ($web !== null && $this->isReasonableKnowledgeAnswer($web)) {
                    $prediction['intent'] = 'knowledge_answer';
                    $prediction['knowledge_answer'] = $web;
                    $prediction['confidence'] = max($prediction['confidence'], 0.84);
                } else {
                    $knowledgeMatch = $this->knowledgeBase->findBestQA($webQuery);
                    if (!$knowledgeMatch) {
                        $knowledgeMatch = $this->localKnowledgeFallback->findBestQA($webQuery, $responseLanguage);
                    }
                    if (
                        $knowledgeMatch
                        && $this->isReasonableKnowledgeAnswer((string) ($knowledgeMatch['answer'] ?? ''))
                        && $this->isRelevantKnowledgeMatch($webQuery, $knowledgeMatch)
                    ) {
                        $prediction['intent'] = 'knowledge_answer';
                        $prediction['knowledge_answer'] = $knowledgeMatch['answer'];
                        $prediction['confidence'] = max($prediction['confidence'], (float) $knowledgeMatch['score']);
                    } elseif ($this->looksLikeTranslatePrompt($input)) {
                        $sourceText = $this->extractQuotedText($input);
                        if ($sourceText !== '') {
                            $target = $responseLanguage === 'hi' ? 'hi' : 'en';
                            $mapped = $this->langMainDictionary->lookup($sourceText, $target);
                            if ($mapped !== null) {
                                $prediction['intent'] = 'knowledge_answer';
                                $prediction['knowledge_answer'] = $mapped;
                                $prediction['confidence'] = max($prediction['confidence'], 0.84);
                            }
                        }
                    } elseif (in_array($prediction['intent'], ['general', 'question', 'normal_chat'], true)) {
                        $prediction['intent'] = 'normal_chat';
                        if ($this->shouldAskToTeach($webQuery, $recent)) {
                            $prediction['chat_reply'] = $this->buildTeachRequest($responseLanguage);
                            $prediction['confidence'] = max($prediction['confidence'], 0.78);
                        } else {
                            $prediction['chat_reply'] = $this->conversationalFallback->reply(
                                $queryTurn,
                                $responseLanguage,
                                $recent
                            );
                            $prediction['confidence'] = max($prediction['confidence'], 0.7);
                        }
                    }
                }
                }
                }
            }
        }

        $nnScore = $this->neuralNetwork->score($prediction['vector']);
        $finalConfidence = round(($prediction['confidence'] * 0.7) + ($nnScore * 0.3), 4);
        $prediction['confidence'] = $finalConfidence;
        $prediction['intent'] = $this->intentNormalizer->normalize((string) $prediction['intent']);

        $expected = in_array($prediction['intent'], ['general', 'question', 'normal_chat'], true) ? 0.55 : 0.85;
        $error = $this->backpropagation->adjust($this->neuralNetwork, $expected, $nnScore);
        if ($finalConfidence >= 0.75 && ($prediction['intent'] !== 'code_generate' || $this->isLikelyCodePrompt($input))) {
            $this->supervised->train($prediction['intent'], $tokens);
        }
        $this->longTermMemory->remember($prediction['intent'], $tokens);

        $context = $this->contextManager->buildContext(
            $tokens,
            $recent,
            $this->longTermMemory->recall($prediction['intent'])
        );
        $context['profile'] = $this->profileMemory->getProfile();
        $context['topic_focus'] = $this->topicGraphMemory->topTopics(8);

        $response = $this->responseBuilder->build($prediction['intent'], array_merge($prediction, [
            'language' => $responseLanguage,
        ]));

        $record = [
            'input' => $input,
            'normalized_input' => $normalizedInput,
            'tokens' => $tokens,
            'intent' => $prediction['intent'],
            'intent_candidates' => $ruleIntentCandidates,
            'confidence' => $finalConfidence,
            'neural_score' => $nnScore,
            'error' => round($error, 4),
            'input_language' => $inputLanguage,
            'response_language' => $responseLanguage,
            'response' => $response,
            'memory_stats' => $this->topicGraphMemory->stats(),
            'knowledge_match' => $knowledgeMatch,
            'policy_match' => $policyMatch,
            'time' => date('c'),
        ];

        $this->shortTermMemory->add($record);

        return [
            'response' => $response,
            'analysis' => $record,
            'context' => $context,
        ];
    }

    private function extractMemoryFact(string $input): ?array
    {
        $patterns = [
            '/my name is\s+(.+)$/i' => 'name',
            '/mera naam\s+(.+)$/i' => 'name',
            '/à¤®à¥‡à¤°à¤¾ à¤¨à¤¾à¤®\s+(.+)$/u' => 'name',
            '/my friend name is\s+(.+)$/i' => 'friend_name',
            '/mere friend ka naam\s+(.+)$/i' => 'friend_name',
            '/i live in\s+(.+)$/i' => 'city',
            '/main rehta hun\s+(.+)$/i' => 'city',
            '/main rehti hun\s+(.+)$/i' => 'city',
            '/remember (?:that|this)\s+(.+?)\s+is\s+(.+)$/i' => null,
            '/yaad rakho\s+(.+?)\s+(.+)$/i' => null,
        ];

        foreach ($patterns as $regex => $fixedKey) {
            if (preg_match($regex, trim($input), $m) !== 1) {
                continue;
            }
            if ($fixedKey !== null) {
                $value = trim((string) $m[1]);
                $lv = mb_strtolower($value, 'UTF-8');
                if (str_contains($lv, 'kya') || str_contains($lv, '?') || str_contains($lv, 'what')) {
                    return null;
                }
                return ['key' => $fixedKey, 'value' => $value];
            }
            if (count($m) >= 3) {
                return ['key' => trim($m[1]), 'value' => trim($m[2])];
            }
        }
        return null;
    }

    private function looksLikeTranslatePrompt(string $input): bool
    {
        $x = mb_strtolower($input, 'UTF-8');
        return str_contains($x, 'translate') || str_contains($x, 'translation') || str_contains($x, 'matlab');
    }

    private function extractQuotedText(string $input): string
    {
        if (preg_match('/["\'](.+?)["\']/u', $input, $m) === 1) {
            return trim((string) $m[1]);
        }
        return trim($input);
    }

    private function isLikelyCodePrompt(string $input): bool
    {
        $x = mb_strtolower($input, 'UTF-8');
        $signals = ['code', 'coding', 'php', 'javascript', 'html', 'css', 'sql', 'api', 'function', 'class', 'bug', 'debug'];
        foreach ($signals as $signal) {
            if (str_contains($x, $signal)) {
                return true;
            }
        }
        return false;
    }

    private function isLikelyWebKnowledgePrompt(string $input): bool
    {
        $x = mb_strtolower($input, 'UTF-8');
        if (in_array(trim($x), ['next', 'continue', 'agla', 'aage', 'php', 'java', 'javascript', 'js', 'json'], true)) {
            return false;
        }
        if (
            preg_match('/capi[a-z]*\s+of\s+[a-z\.\- ]{2,60}/iu', $x) === 1
            || preg_match('/[a-z\.\- ]{2,60}\s+capi[a-z]*/iu', $x) === 1
        ) {
            return true;
        }
        return str_contains($x, 'what is')
            || str_contains($x, '?')
            || str_contains($x, 'who is')
            || str_contains($x, 'where is')
            || str_contains($x, 'tell me about')
            || str_starts_with($x, 'about ')
            || str_contains($x, ' ke baare me');
    }

    private function resolveContextualInput(string $input, array $recent): string
    {
        if (str_starts_with($input, '__image_file__:')) {
            return $input;
        }
        $x = trim($input);
        $low = mb_strtolower($x, 'UTF-8');
        if ($x === '') {
            return $x;
        }
        $needsContext = mb_strlen($low, 'UTF-8') <= 28
            || preg_match('/\b(it|this|that|about it|about this|uska|iske|uske|isko|iske bare|uske bare|aur|next)\b/u', $low) === 1;
        if (!$needsContext) {
            return $x;
        }
        $topic = $this->extractRecentUserTopic($recent);
        if ($topic === '' || str_contains($low, mb_strtolower($topic, 'UTF-8'))) {
            return $x;
        }
        if (preg_match('/^(about it|about this|iske bare|uske bare)/u', $low) === 1) {
            return 'about ' . $topic;
        }
        if (preg_match('/\bcapital\b/u', $low) === 1 && !str_contains($low, 'capital of')) {
            return 'capital of ' . $topic;
        }
        if (preg_match('/^(about|tell me about|iske bare|uske bare)/u', $low) === 1) {
            return $x . ' ' . $topic;
        }
        return $x . ' (context: ' . $topic . ')';
    }

    private function extractRecentUserTopic(array $recent): string
    {
        for ($i = count($recent) - 1; $i >= 0; $i--) {
            $resp = trim((string) ($recent[$i]['response'] ?? ''));
            if ($resp !== '' && preg_match('/capital of\s+([a-z\.\- ]{2,60})\s+is/iu', mb_strtolower($resp, 'UTF-8'), $m) === 1) {
                return trim((string) $m[1]);
            }
            $q = trim((string) ($recent[$i]['input'] ?? ''));
            if ($q === '' || str_starts_with($q, '__image_file__:')) {
                continue;
            }
            $low = mb_strtolower($q, 'UTF-8');
            if (preg_match('/capital of\s+([a-z\.\- ]{2,60})/iu', $low, $m) === 1) {
                return trim((string) $m[1]);
            }
            if (preg_match('/about\s+([a-z\.\- ]{2,60})/iu', $low, $m) === 1) {
                return trim((string) $m[1]);
            }
            if (mb_strlen($low, 'UTF-8') < 5) {
                continue;
            }
            if (in_array($low, ['hi', 'hello', 'hlo', 'ok', 'next', 'continue'], true)) {
                continue;
            }
            if (preg_match('/\b(uski|iske|uske|this|that|it)\b/u', $low) === 1) {
                continue;
            }
            return mb_substr($q, 0, 80, 'UTF-8');
        }
        return '';
    }

    private function isTeachMePrompt(string $input): bool
    {
        $x = mb_strtolower(trim($input), 'UTF-8');
        return str_contains($x, 'mujhse siko')
            || str_contains($x, 'mujse siko')
            || str_contains($x, 'mujhse seekho')
            || str_contains($x, 'mujse seekho')
            || str_contains($x, 'learn from me');
    }

    private function shouldAskToTeach(string $input, array $recent): bool
    {
        $x = mb_strtolower(trim($input), 'UTF-8');
        if (mb_strlen($x, 'UTF-8') < 3) {
            return false;
        }
        if (preg_match('/^[a-z]{8,}$/u', $x) === 1) {
            return true;
        }
        if (!str_contains($x, '?') && mb_strlen($x, 'UTF-8') <= 18) {
            return true;
        }
        if (!empty($recent)) {
            $last = end($recent);
            $lastIntent = (string) ($last['intent'] ?? '');
            if (in_array($lastIntent, ['normal_chat', 'general'], true) && mb_strlen($x, 'UTF-8') <= 30) {
                return true;
            }
        }
        return false;
    }

    private function buildTeachRequest(string $language): string
    {
        return match ($language) {
            'hi' => 'Mujhe is topic ka sahi data nahi mila. Aap 2-4 line me samjhao, main turant seekh kar next message me answer dunga.',
            'en' => 'I do not have reliable data on this yet. Teach me in 2-4 lines and I will answer it in the next message.',
            default => 'Mujhe iske bare me reliable data nahi mila. Aap 2-4 lines me sikhao, main next message me sahi answer dunga.',
        };
    }

    private function buildLearningAck(string $input, string $language): string
    {
        $topics = $this->extractLearningTopics($input);
        $topicText = $topics ? implode(', ', array_slice($topics, 0, 5)) : 'general topic';

        return match ($language) {
            'hi' => 'Maine text se ye topics sikhe: ' . $topicText . '. Ab aap inse jude sawal puch sakte ho.',
            'en' => 'I learned these topics from your text: ' . $topicText . '. You can now ask related questions.',
            default => 'Maine text se ye sikha: ' . $topicText . '. Ab aap related questions puch sakte ho.',
        };
    }

    /** @return string[] */
    private function extractLearningTopics(string $input): array
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', mb_strtolower($input, 'UTF-8')) ?? '';
        $tokens = preg_split('/\s+/u', trim($clean)) ?: [];
        $stop = [
            'the', 'and', 'for', 'with', 'this', 'that', 'from', 'into', 'your', 'about',
            'hai', 'aur', 'se', 'ki', 'ka', 'ke', 'mein', 'main', 'kya', 'is', 'are', 'was', 'were',
        ];
        $freq = [];
        foreach ($tokens as $token) {
            if (mb_strlen($token, 'UTF-8') < 4 || in_array($token, $stop, true)) {
                continue;
            }
            $freq[$token] = ($freq[$token] ?? 0) + 1;
        }
        arsort($freq);
        return array_keys(array_slice($freq, 0, 8, true));
    }

    private function isReasonableKnowledgeAnswer(string $answer): bool
    {
        $answer = trim($answer);
        if ($answer === '' || mb_strlen($answer, 'UTF-8') > 380) {
            return false;
        }
        $badHints = ['organization:', 'newsgroup', 'repost from', 'writes:', 'subject: re:'];
        $low = mb_strtolower($answer, 'UTF-8');
        foreach ($badHints as $hint) {
            if (str_contains($low, $hint)) {
                return false;
            }
        }
        return true;
    }

    private function isRelevantKnowledgeMatch(string $query, array $knowledgeMatch): bool
    {
        $q = mb_strtolower(trim($query), 'UTF-8');
        $question = mb_strtolower(trim((string) ($knowledgeMatch['question'] ?? '')), 'UTF-8');
        $answer = mb_strtolower(trim((string) ($knowledgeMatch['answer'] ?? '')), 'UTF-8');

        $stop = ['what', 'which', 'when', 'where', 'about', 'tell', 'capital', 'kya', 'ka', 'ki', 'hai', 'is', 'the'];
        $qTokens = array_values(array_filter(
            explode(' ', preg_replace('/\s+/', ' ', $q) ?? ''),
            static fn($t) => strlen($t) >= 4 && !in_array($t, $stop, true)
        ));
        if (!$qTokens) {
            return true;
        }

        $hits = 0;
        foreach (array_slice($qTokens, 0, 6) as $t) {
            if (str_contains($question, $t) || str_contains($answer, $t)) {
                $hits++;
            }
        }
        return $hits >= 1;
    }

    private function isDirectFactQuery(string $input, string $key): bool
    {
        $x = mb_strtolower($input, 'UTF-8');
        return match ($key) {
            'name' => str_contains($x, 'my name') || str_contains($x, 'mera naam') || str_contains($x, 'à¤¨à¤¾à¤®'),
            'city' => str_contains($x, 'my city') || str_contains($x, 'i live') || str_contains($x, 'kahan'),
            'friend_name' => str_contains($x, 'friend name') || str_contains($x, 'friend ka naam'),
            'phone' => str_contains($x, 'phone') || str_contains($x, 'number'),
            default => false,
        };
    }

    private function isLikelyKnowledgeDocument(string $input): bool
    {
        $x = trim($input);
        if (mb_strlen($x, 'UTF-8') < 120) {
            return false;
        }
        if (str_contains($x, '?')) {
            return false;
        }
        return preg_match('/\b(is|are|includes|explains|theory|system|framework|language|physics|math|php|javascript|json)\b/i', $x) === 1;
    }

    private function isLikelyDefinitionStatement(string $input): bool
    {
        $x = trim($input);
        if (mb_strlen($x, 'UTF-8') < 20 || mb_strlen($x, 'UTF-8') > 180) {
            return false;
        }
        if (str_contains($x, '?')) {
            return false;
        }
        $low = mb_strtolower($x, 'UTF-8');
        if (preg_match('/^(what|who|where|when|why|how)\b/u', $low) === 1) {
            return false;
        }
        return preg_match('/^[a-z][a-z0-9 \-]{2,40}\s*(=|is)\s+.+$/iu', $x) === 1;
    }

    private function isCasualPrompt(string $input): bool
    {
        $x = mb_strtolower(trim($input), 'UTF-8');
        if ($x === '') {
            return true;
        }
        $casual = [
            'tum kese ho', 'tum kaise ho', 'kaise ho', 'kya haal h', 'kya haal hai',
            'kya kya kr skte ho', 'kya kar sakte ho', 'kya tuko mujese kuch sikhna h',
            'mujese kuch sikhna', 'ek joke', 'joke', 'google', 'next'
        ];
        foreach ($casual as $c) {
            if (str_contains($x, $c)) {
                return true;
            }
        }
        return false;
    }

    private function extractFirstUrl(string $input): ?string
    {
        if (preg_match('/https?:\/\/[^\s]+/i', $input, $m) === 1) {
            return trim((string) $m[0]);
        }
        return null;
    }

    private function isQuizContinuationPrompt(string $input, array $recent): bool
    {
        $x = mb_strtolower(trim($input), 'UTF-8');
        if (!in_array($x, ['next', 'continue', 'next quiz', 'agla', 'aage'], true)) {
            return false;
        }
        $last = end($recent);
        return is_array($last) && (($last['intent'] ?? '') === 'quiz_request' || str_contains((string) ($last['response'] ?? ''), 'à¤•à¥à¤µà¤¿à¤œ') || str_contains((string) ($last['response'] ?? ''), 'Quiz'));
    }

    private function findRecentTopicHint(array $recent): string
    {
        for ($i = count($recent) - 1; $i >= 0; $i--) {
            $msg = mb_strtolower((string) ($recent[$i]['input'] ?? ''), 'UTF-8');
            if (str_contains($msg, 'physics') || str_contains($msg, 'à¤­à¥Œà¤¤à¤¿à¤•')) {
                return 'physics quiz';
            }
            if (str_contains($msg, 'math') || str_contains($msg, 'à¤—à¤£à¤¿à¤¤')) {
                return 'math quiz';
            }
        }
        return '';
    }

    private function runTaskMode(string $input, string $language): string
    {
        $plan = $this->taskPlanner->makePlan($input);
        $outputs = [];
        $steps = [];

        foreach ($plan as $idx => $item) {
            $tool = (string) ($item['tool'] ?? 'unknown');
            if ($tool === 'verify') {
                continue;
            }
            $out = $this->toolRouter?->run($tool, $input, $language);
            $outputs[$tool] = $out;
            $steps[] = ($idx + 1) . '. ' . ($item['label'] ?? $tool) . ': ' . (is_string($out) && trim($out) !== '' ? 'done' : 'skip');
            $this->executionLog->append([
                'input' => $input,
                'tool' => $tool,
                'ok' => is_string($out) && trim($out) !== '',
                'preview' => is_string($out) ? mb_substr($out, 0, 200, 'UTF-8') : '',
            ]);
        }

        $verify = $this->verifier->verify($input, $outputs);
        $parts = array_values(array_filter($outputs, static fn($x) => is_string($x) && trim($x) !== ''));
        $body = $parts ? implode("\n\n", array_slice($parts, 0, 3)) : ($language === 'hi' ? 'à¤…à¤­à¥€ à¤ªà¤°à¥à¤¯à¤¾à¤ªà¥à¤¤ à¤¡à¥‡à¤Ÿà¤¾ à¤¨à¤¹à¥€à¤‚ à¤®à¤¿à¤²à¤¾à¥¤' : 'No reliable data found yet.');

        return "Task Mode Report\n\nPlan:\n" . implode("\n", $steps) . "\n\nResult:\n" . $body . "\n\nVerification: " . ($verify['ok'] ? 'pass' : 'weak') . ' | confidence=' . $verify['confidence'];
    }
}

