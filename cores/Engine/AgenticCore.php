<?php
namespace Core\Engine;

use Core\Commands\CommandInterface;
use Core\Commands\CreateProjectCommand;
use Core\Commands\ShowMapCommand;
use Core\Commands\CalculateCommand;
use Core\Commands\RunCommandCommand;
use Core\Commands\DebugCommand;
use Core\Commands\AuditFileCommand;
use Core\Tools\FileSystem\FileEditor;
use Core\Tools\Terminal\ShellExecutor;
use Core\NLU\PersonalBrain;
use Core\NLU\EntityExtractor;

/**
 * HRITIK AI - ADVANCED AGENTIC CORE (PROJECT MANAGER)
 * This class uses a Personal ML Brain instead of Regex.
 */
class AgenticCore {
    
    private FileEditor $fileSystem;
    private ShellExecutor $terminal;
    private \Core\Tools\Safety\NeuralSafetyGuard $safetyGuard;
    private \Core\Tools\Intelligence\TranslatorTool $translator;
    private \Core\Tools\Intelligence\DataConverterTool $converter;
    private \Core\Evolution\SelfRepairCore $repairCore;
    private \Core\GenerativeAI\GenerativeAIAssistant $generativeAI;
    
    /** @var CommandInterface[] */
    private array $commands = [];

    private PersonalBrain $personalBrain;
    private EntityExtractor $extractor;

    public function __construct()
    {
        $this->personalBrain = new PersonalBrain();
        $this->extractor = new EntityExtractor();
        
        $this->fileSystem = new FileEditor();
        $this->terminal = new ShellExecutor();
        $this->safetyGuard = new \Core\Tools\Safety\NeuralSafetyGuard();
        $this->translator = new \Core\Tools\Intelligence\TranslatorTool();
        $this->converter = new \Core\Tools\Intelligence\DataConverterTool();
        $this->repairCore = new \Core\Evolution\SelfRepairCore();
        $this->generativeAI = new \Core\GenerativeAI\GenerativeAIAssistant();

        $this->registerCommands();
    }

    public function solve(string $task): string {
        // PRE-EXECUTION SAFETY CHECK
        if (!$this->safetyGuard->isCommandSafe($task)) {
            return "[AGENT_SAFETY] Blocked: The requested task contains potentially harmful commands.";
        }
        
        $task = strtolower($task);

        // 1. Parallel Sub-Task Checking first
        if (str_contains($task, ' and ')) {
            $tp = new \Core\Tools\Parallel\TaskParallelizer();
            $subTasks = $tp->splitGoal($task);
            return $tp->parallelize($subTasks);
        }

        // 2. Exact command matching for refactored commands
        foreach ($this->commands as $command) {
            if ($command->canProcess($task)) {
                return $command->process($task);
            }
        }

        // --- NLU INTENT PIPELINE (Machine Learning) ---
        $intent = $this->personalBrain->predictIntent($task);
        $entities = $this->extractor->extract($task);
        
        // Extract primary file or generic topic dynamically without regex
        $primaryFile = !empty($entities['files']) ? $entities['files'][0] : null;
        $primaryTopic = EntityExtractor::extractTopic($task, ['about', 'on', 'for', 'to', 'in', 'project', 'agent']);

        // Dynamic Tool Execution
        $toolRegistry = new \Core\Tools\ToolRegistry();
        $tool = $toolRegistry->getTool($intent);
        if ($tool !== null) {
            $inputs = ['task' => $task, 'intent' => $intent];
            
            // Map task to tool-specific inputs dynamically
            if ($intent === 'calculator') {
                $inputs['expression'] = $primaryTopic;
            } elseif ($intent === 'read_file' && $primaryFile) {
                $inputs['path'] = $primaryFile;
            } elseif ($intent === 'write_file' && $primaryFile) {
                $inputs['path'] = $primaryFile;
                // If it asks for content, we might need a separate extractor, but for now fallback to string manip
                preg_match('/(?:with content|text)\s+(.*)/i', $task, $m);
                $inputs['content'] = $m[1] ?? 'Default Content';
            } elseif ($intent === 'execute_command') {
                $inputs['command'] = str_replace(['run command', 'execute command', 'execute'], '', $task);
            } elseif ($intent === 'learn_fact') {
                $inputs['fact'] = str_replace(['learn fact', 'save fact', 'memorize'], '', $task);
            }

            if (!empty($inputs['path']) || !empty($inputs['command']) || !empty($inputs['fact']) || !empty($inputs['expression'])) {
                 $result = $tool->execute($inputs);
                 // Unify return keys
                 $resVal = $result['response'] ?? $result['payload'] ?? $result['result'] ?? $result['content'] ?? $result['message'] ?? null;
                 if ($resVal !== null) {
                     return $resVal;
                 }
            }
        }

        switch ($intent) {
            case 'test_file':
                if ($primaryFile) {
                    $output = $this->terminal->runPhp($primaryFile);
                    return "[AGENT] Test Results for $primaryFile:\n" . $output;
                }
                return "Which file should I test? I couldn't find a filename in your request.";

            case 'research':
                $search = new \Core\Tools\Search\WebProSearch();
                return $search->researchCode($primaryTopic);

            case 'audit_file':
                if ($primaryFile) {
                    $debugger = new \Core\Tools\Debugger\NeuralDebugger();
                    return $debugger->auditFile($primaryFile);
                }
                return "Which file should I audit?";

            case 'generate_readme':
                $scribe = new \Core\Tools\Documentation\AutoScribe();
                return $scribe->generateReadme();

            case 'document_folder':
                $folder = !empty($entities['names']) ? $entities['names'][0] : (explode(' ', $primaryTopic)[0] ?? 'core');
                $scribe = new \Core\Tools\Documentation\AutoScribe();
                return $scribe->documentFolder($folder);

            case 'plan_project':
                $planner = new \Core\Tools\Planning\ProjectPlanner();
                return $planner->plan($primaryTopic);

            case 'show_map':
                $mapper = new \Core\Tools\Visualization\ProjectMapper();
                return $mapper->generateTree();

            case 'audit_system':
                $mapper = new \Core\Tools\Visualization\ProjectMapper();
                return $mapper->auditConnections();

            case 'optimize_file':
                if ($primaryFile) {
                    $opt = new \Core\Tools\Optimization\SelfOptimizer();
                    return $opt->optimizeFile($primaryFile);
                }
                return "Which file should I optimize?";

            case 'patch_file':
                if ($primaryFile) {
                    $opt = new \Core\Tools\Optimization\SelfOptimizer();
                    return $opt->applyAutoPatch($primaryFile);
                }
                return "Which file should I patch?";

            case 'deploy_project':
                $deployer = new \Core\Tools\Deployment\AutoDeployer();
                return $deployer->deploy($primaryTopic);

            case 'enable_ai':
                $bridge = new \Core\Tools\Connectivity\APIBridgeGenerator();
                return $bridge->generateBridge($primaryTopic);

            case 'evolve':
                $evolver = new \Core\Evolution\RecursiveEvolver();
                return $evolver->evolve();

            case 'git_init':
                $path = $primaryFile ?? '.';
                $git = new \Core\Tools\Git\GitPro();
                return $git->init($path);

            case 'commit':
                $path = $primaryFile ?? '.';
                $git = new \Core\Tools\Git\GitPro();
                return $git->commitChanges($path);

            case 'spawn_agent':
                if (preg_match('/agent (.*) specialized in (.*)/', $task, $m)) {
                    $bootstrap = new \Core\Evolution\AIBootstrap();
                    return $bootstrap->spawn(trim($m[1]), trim($m[2]));
                }
                return "Provide agent name and specialization domain.";

            case 'show_swarm':
                $mapper = new \Core\Tools\Visualization\SwarmMapper();
                return $mapper->generateMap();

            case 'collaborate':
                if (preg_match('/collaborate (.*) on (.*)/', $task, $m)) {
                    $agents = explode(',', $m[1]);
                    $collab = new \Core\Evolution\CrossAgentCollab();
                    return $collab->executeCollaborativeTask(trim($m[2]), array_map('trim', $agents));
                }
                return "Provide agents and the task to collaborate on.";

            case 'analyze_image':
                if ($primaryFile) {
                    $vision = new \Core\Tools\Vision\NeuralEyeCore();
                    return $vision->analyzeImage($primaryFile);
                }
                return "Please provide a valid image path to analyze.";

            case 'imagine':
                $creativity = new \Core\Virtual\NeuralCreativityCore();
                return $creativity->imagine();

            case 'singularity':
                $singularity = new \Core\Virtual\NeuralSingularityCore();
                return $singularity->reachSingularity();

            case 'train':
                $lines = !empty($entities['numbers']) ? (int)$entities['numbers'][0] : 1000;
                $trainer = new \Core\Learning\MassiveTrainer();
                return $trainer->startTraining($lines);

            case 'translate':
                if (preg_match('/translate (.*) to (.*)/i', $task, $m)) {
                    return $this->translator->translate($m[1], $m[2]);
                }
                return "Provide text and target language.";

            case 'convert':
                if (preg_match('/convert (.*) to (json|csv|xml|uppercase)/i', $task, $m)) {
                    return $this->converter->convert($m[1], 'auto', $m[2]);
                }
                return "Provide text and format to convert to.";

            case 'generate_code':
                $synthesizer = new \Core\GenerativeAI\CodeSynthesizer();
                return $synthesizer->generate($task);

            case 'generate_thought':
                return $this->generativeAI->generateThought($task);

            default:
                return "Intent detected as: $intent, but no handler exists yet.";
        }
    }

    private function registerCommands(): void
    {
        $this->commands[] = new CreateProjectCommand($this->fileSystem);
        $this->commands[] = new ShowMapCommand();
        $this->commands[] = new CalculateCommand();
        $this->commands[] = new RunCommandCommand();
        $this->commands[] = new DebugCommand();
        $this->commands[] = new AuditFileCommand();
    }
}
