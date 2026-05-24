<?php
namespace Core\Response;

/**
 * HRITIK AI - CONVERSATIONAL FALLBACK (CLEAN)
 * All fallbacks are now handled by the Neural Teacher.
 */
class ConversationalFallback {
    /**
     * Returns null to force the engine to escalate to the Neural Teacher.
     */
    public function getFallback(): ?string {
        return null;
    }
}
