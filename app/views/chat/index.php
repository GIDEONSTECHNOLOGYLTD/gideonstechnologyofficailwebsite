<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-header">
            <h2>Customer Support</h2>
            <div class="chat-stats">
                <span class="online-status">Online</span>
                <span class="response-time">Avg Response: 1 min</span>
            </div>
        </div>
        
        <div class="chat-options">
            <button class="option-btn" data-option="translate">
                <i class="fas fa-globe"></i> Translate
            </button>
            <button class="option-btn" data-option="recommend">
                <i class="fas fa-star"></i> Recommendations
            </button>
            <button class="option-btn" data-option="faq">
                <i class="fas fa-question-circle"></i> FAQ
            </button>
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be inserted here -->
        </div>
        
        <div class="chat-input">
            <div class="input-container">
                <input type="text" id="chatInput" placeholder="Type your message..." autocomplete="off">
                <button id="sendButton" class="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="input-options">
                <button class="option-btn" data-option="emoji">
                    <i class="fas fa-smile"></i>
                </button>
                <button class="option-btn" data-option="file">
                    <i class="fas fa-paperclip"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    display: flex;
    height: 100vh;
    background: #f5f7fa;
}

.chat-sidebar {
    width: 300px;
    background: #fff;
    border-right: 1px solid #e1e8ed;
    padding: 20px;
}

.chat-header {
    margin-bottom: 30px;
}

.chat-header h2 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.chat-stats {
    display: flex;
    gap: 20px;
    font-size: 0.9em;
    color: #666;
}

.online-status {
    color: #2ecc71;
}

.chat-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.option-btn {
    background: #f8f9fa;
    border: none;
    padding: 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: background 0.2s;
}

.option-btn:hover {
    background: #e9ecef;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message {
    max-width: 80%;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 10px;
}

.message.user {
    background: #3498db;
    color: white;
    align-self: flex-end;
}

.message.assistant {
    background: #f8f9fa;
    color: #2c3e50;
    align-self: flex-start;
}

.message .timestamp {
    font-size: 0.7em;
    color: #666;
    margin-top: 5px;
}

.chat-input {
    padding: 20px;
    background: #fff;
    border-top: 1px solid #e1e8ed;
}

.input-container {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

#chatInput {
    flex: 1;
    padding: 10px;
    border: 1px solid #e1e8ed;
    border-radius: 5px;
    font-size: 1em;
}

.send-btn {
    background: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s;
}

.send-btn:hover {
    background: #2980b9;
}

.input-options {
    display: flex;
    gap: 10px;
}

@media (max-width: 768px) {
    .chat-container {
        flex-direction: column;
    }
    
    .chat-sidebar {
        width: 100%;
        height: 200px;
    }
    
    .chat-main {
        height: calc(100vh - 200px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.getElementById('chatInput');
    const sendButton = document.getElementById('sendButton');
    const chatMessages = document.getElementById('chatMessages');
    let currentContext = [];

    function addMessage(message, isUser) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'assistant'}`;
        messageDiv.innerHTML = `
            <p>${message}</p>
            <span class="timestamp">${new Date().toLocaleTimeString()}</span>
        `;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async function sendMessage(message) {
        try {
            addMessage(message, true);
            
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    message: message,
                    context: currentContext
                })
            });

            const data = await response.json();
            if (data.success) {
                addMessage(data.response, false);
                currentContext.push({ role: 'user', content: message });
                currentContext.push({ role: 'assistant', content: data.response });
                
                // Keep context size manageable
                if (currentContext.length > 10) {
                    currentContext = currentContext.slice(-10);
                }
            } else {
                addMessage('Error: ' + data.message, false);
            }
        } catch (error) {
            addMessage('Error: Failed to send message', false);
        }
    }

    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (chatInput.value.trim()) {
                sendMessage(chatInput.value);
                chatInput.value = '';
            }
        }
    });

    sendButton.addEventListener('click', function() {
        if (chatInput.value.trim()) {
            sendMessage(chatInput.value);
            chatInput.value = '';
        }
    });

    // Initialize chat options
    document.querySelectorAll('.option-btn').forEach(button => {
        button.addEventListener('click', function() {
            const option = this.dataset.option;
            
            switch (option) {
                case 'translate':
                    translateMessage();
                    break;
                case 'recommend':
                    getRecommendations();
                    break;
                case 'faq':
                    getFAQ();
                    break;
            }
        });
    });

    async function translateMessage() {
        const message = prompt('Enter message to translate:');
        if (message) {
            const targetLanguage = prompt('Enter target language (e.g., "es" for Spanish):');
            if (targetLanguage) {
                try {
                    const response = await fetch('/api/chat/translate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message: message,
                            targetLanguage: targetLanguage
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        addMessage(`Translation to ${targetLanguage}: ${data.translation}`, false);
                    } else {
                        addMessage('Error: ' + data.message, false);
                    }
                } catch (error) {
                    addMessage('Error: Failed to translate message', false);
                }
            }
        }
    }

    async function getRecommendations() {
        try {
            const preferences = {
                category: prompt('Enter preferred category:'),
                budget: prompt('Enter budget range:'),
                preferences: prompt('Enter any specific preferences:')
            };

            const response = await fetch('/api/chat/recommend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(preferences)
            });

            const data = await response.json();
            if (data.success) {
                addMessage('Product Recommendations:', false);
                data.recommendations.forEach(product => {
                    addMessage(`- ${product.name}: ${product.description}`, false);
                });
            } else {
                addMessage('Error: ' + data.message, false);
            }
        } catch (error) {
            addMessage('Error: Failed to get recommendations', false);
        }
    }

    async function getFAQ() {
        try {
            const topic = prompt('Enter topic for FAQ:');
            if (topic) {
                const response = await fetch('/api/chat/faq', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ topic: topic })
                });

                const data = await response.json();
                if (data.success) {
                    addMessage('FAQ:', false);
                    data.faq.forEach(item => {
                        addMessage(`Q: ${item.question}`, false);
                        addMessage(`A: ${item.answer}`, false);
                    });
                } else {
                    addMessage('Error: ' + data.message, false);
                }
            }
        } catch (error) {
            addMessage('Error: Failed to get FAQ', false);
        }
    }
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
