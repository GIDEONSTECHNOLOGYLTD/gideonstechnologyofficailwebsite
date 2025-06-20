<?php

namespace Slim\Flash;

/**
 * Flash messages for Slim Framework
 */
class Messages
{
    /**
     * Add a flash message for a key
     * 
     * @param string $key The key for the message
     * @param string $message The message text
     * @return void
     */
    public function addMessage(string $key, string $message): void
    {
        // Implementation
    }
    
    /**
     * Get flash messages for a key
     * 
     * @param string $key The key for the messages
     * @return array The messages
     */
    public function getMessages(string $key): array
    {
        // Implementation
        return [];
    }
    
    /**
     * Get all flash messages
     * 
     * @return array All messages
     */
    public function getFirstMessage(string $key): ?string
    {
        // Implementation
        return null;
    }
}