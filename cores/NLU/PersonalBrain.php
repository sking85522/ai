<?php
namespace Core\NLU;

require_once __DIR__ . '/../../modules/index.php';

use NLPHP\Classification\NaiveBayes;

class PersonalBrain {
    private $modelPath;
    private $classifier;
    private $isTrained = false;

    public function __construct() {
        $this->loadOrTrain();
    }

    private function loadOrTrain() {
        global $db;
        $res = $db->query("SELECT k_value FROM neural_knowledge WHERE category='system_models' AND sub_category='personal_brain' LIMIT 1");
        
        if (!empty($res['data'])) {
            $data = base64_decode($res['data'][0]['k_value']);
            $this->classifier = unserialize($data);
            $this->isTrained = true;
        } else {
            $this->train();
        }
    }

    public function retrain() {
        $this->train();
    }

    private function train() {
        echo "\n\033[0;33m[AI SYSTEM] Initializing Neural ML Training... This might take a moment.\033[0m\n";
        
        $this->classifier = new NaiveBayes();

        $texts = [
            "test file", "please test this file", "run test for", "execute unit test", "check if file works", "testing file", "run file",
            "research about", "tell me about", "find out about", "research topic", "search for", "google about",
            "debug this error", "fix the bug", "analyze error message", "debug", "solve this error", "why is it failing",
            "audit the file", "review code in", "check file quality", "audit file", "inspect the code", "read and audit", "check file",
            "generate readme", "create documentation", "document this project", "generate a readme file", "make readme",
            "document folder", "document the directory", "create docs for folder", "generate folder docs",
            "plan project", "create a plan for", "outline project steps", "plan out", "make a project plan",
            "show map", "show project structure", "visualize tree", "show tree", "display architecture map",
            "audit system", "check system connections", "audit connections", "system audit",
            "optimize file", "make this code better", "refactor file", "optimize", "improve this code", "clean up code",
            "patch file", "apply patch to", "fix code in file", "auto patch", "correct this file",
            "deploy project", "release project", "deploy application", "push to prod", "ship it",
            "enable ai in", "bridge api to", "integrate ai with", "add ai to",
            "evolve system", "upgrade yourself", "become smarter", "evolve", "self improve",
            "git init", "initialize repository", "start git", "create git repo",
            "commit changes", "save work", "git commit", "push commit",
            "spawn agent", "create new agent", "make an assistant", "new ai agent",
            "show swarm", "list agents", "show all agents", "who is working",
            "collaborate on", "work together on", "agents collaborate", "team work on",
            "analyze image", "look at picture", "vision analyze", "what is in this image",
            "imagine something", "give me an idea", "be creative", "imagine", "think of a story",
            "reach singularity", "who are you really", "what is your true purpose", "singularity",
            "train lines", "start training", "learn massive data", "train model",
            "translate to", "convert language", "translate text", "change language",
            "convert to", "change format to", "transform into", "export as",
            "analyze this data", "describe csv", "analyze dataset", "data analysis",
            "tell me a joke", "talk to me", "write a story", "what do you think", "say something", "hello", "hi", "how are you",
            "write code", "generate code", "create function", "write a php script", "code for", "make a class"
        ];

        $labels = [
            "test_file", "test_file", "test_file", "test_file", "test_file", "test_file", "test_file",
            "research", "research", "research", "research", "research", "research",
            "debug", "debug", "debug", "debug", "debug", "debug",
            "audit_file", "audit_file", "audit_file", "audit_file", "audit_file", "audit_file", "audit_file",
            "generate_readme", "generate_readme", "generate_readme", "generate_readme", "generate_readme",
            "document_folder", "document_folder", "document_folder", "document_folder",
            "plan_project", "plan_project", "plan_project", "plan_project", "plan_project",
            "show_map", "show_map", "show_map", "show_map", "show_map",
            "audit_system", "audit_system", "audit_system", "audit_system",
            "optimize_file", "optimize_file", "optimize_file", "optimize_file", "optimize_file", "optimize_file",
            "patch_file", "patch_file", "patch_file", "patch_file", "patch_file",
            "deploy_project", "deploy_project", "deploy_project", "deploy_project", "deploy_project",
            "enable_ai", "enable_ai", "enable_ai", "enable_ai",
            "evolve", "evolve", "evolve", "evolve", "evolve",
            "git_init", "git_init", "git_init", "git_init",
            "commit", "commit", "commit", "commit",
            "spawn_agent", "spawn_agent", "spawn_agent", "spawn_agent",
            "show_swarm", "show_swarm", "show_swarm", "show_swarm",
            "collaborate", "collaborate", "collaborate", "collaborate",
            "analyze_image", "analyze_image", "analyze_image", "analyze_image",
            "imagine", "imagine", "imagine", "imagine", "imagine",
            "singularity", "singularity", "singularity", "singularity",
            "train", "train", "train", "train",
            "translate", "translate", "translate", "translate",
            "convert", "convert", "convert", "convert",
            "data_analyze", "data_analyze", "data_analyze", "data_analyze",
            "generate_thought", "generate_thought", "generate_thought", "generate_thought", "generate_thought", "generate_thought", "generate_thought", "generate_thought",
            "generate_code", "generate_code", "generate_code", "generate_code", "generate_code", "generate_code"
        ];

        $this->classifier->fit($texts, $labels);

        // Save model to Online Database
        global $db;
        $modelStr = base64_encode(serialize($this->classifier));
        $db->query("DELETE FROM neural_knowledge WHERE category='system_models' AND sub_category='personal_brain'");
        $db->query("INSERT INTO neural_knowledge (category, sub_category, k_key, k_value) VALUES ('system_models', 'personal_brain', 'naive_bayes', '$modelStr')");
        
        $this->isTrained = true;
        echo "\033[0;32m[AI SYSTEM] Brain training complete and saved to ONLINE DATABASE.\033[0m\n";
    }

    public function predictIntent(string $input): string {
        $intent = $this->classifier->predict($input);
        return $intent ?: 'generate_thought';
    }
}
