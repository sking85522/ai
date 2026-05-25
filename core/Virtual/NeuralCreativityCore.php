<?php
namespace Core\Virtual;

use Core\SQLGenerator\SQLGenerator;

/**
 * HRITIK AI - NEURAL CREATIVITY CORE
 * Now fully database-driven. Generates original ideas by querying the Knowledge Base.
 */
class NeuralCreativityCore {
    
    private SQLGenerator $sqlGen;

    public function __construct() {
        $this->sqlGen = new SQLGenerator();
    }

    /**
     * Imagines a new, original idea for the user using DB knowledge.
     */
    public function imagine(): string {
        global $db;
        if (!isset($db) || $db === null) {
            return "[IMAGINATION] My creative database is offline. Please try again later.";
        }

        // Fetch a random creative seed from the DB
        $sql = $this->sqlGen->generate('search_knowledge', ['query' => 'creative_concept']);
        $res = $db->query($sql);
        
        if (!empty($res['data'])) {
            $concept = $res['data'][array_rand($res['data'])]['k_value'];
            return "[IMAGINATION] maine ek naya concept socha hai:\n" . $concept . "\n\nIske bare mein aapka kya khayal hai?";
        }

        return "[IMAGINATION] main kuch naya sochne ki koshish kar raha hoon, par mere pas abhi koi seeds nahi hain database mein.";
    }
}
