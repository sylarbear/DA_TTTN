<?php
/**
 * OpenAIService
 * Gọi OpenAI API cho Speaking evaluation + Chatbot
 * Fallback về local scoring nếu không có API key
 */
class OpenAIService {
    
    private static $apiKey = null;
    
    /**
     * Lấy API key từ file config
     */
    public static function getApiKey() {
        if (self::$apiKey !== null) return self::$apiKey;
        
        $keyFile = APP_PATH . '/config/openai_key.txt';
        if (file_exists($keyFile)) {
            self::$apiKey = trim(file_get_contents($keyFile));
        } else {
            self::$apiKey = '';
        }
        return self::$apiKey;
    }
    
    /**
     * Lưu API key vào file config
     */
    public static function saveApiKey($key) {
        $keyFile = APP_PATH . '/config/openai_key.txt';
        file_put_contents($keyFile, trim($key));
        self::$apiKey = trim($key);
    }
    
    /**
     * Kiểm tra API key có sẵn không
     */
    public static function isAvailable() {
        return !empty(self::getApiKey());
    }
    
    /**
     * Gọi OpenAI Chat Completion API
     * @param array $messages [{role, content}, ...]
     * @param string $model Model name
     * @param float $temperature 0-2
     * @return array|null Response hoặc null nếu lỗi
     */
    public static function chatCompletion($messages, $model = 'gpt-3.5-turbo', $temperature = 0.7) {
        $apiKey = self::getApiKey();
        if (empty($apiKey)) return null;
        
        $data = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => 1000,
        ];
        
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) return null;
        
        $result = json_decode($response, true);
        return $result;
    }
    
    /**
     * AI chấm Speaking: phân tích transcript vs sample answer
     * @return array Scores + AI feedback
     */
    public static function scoreSpeaking($transcript, $sampleAnswer) {
        $prompt = "You are an English speaking evaluator. A student was asked to read the following text aloud. Compare their speech (transcript) with the original text and evaluate them.

ORIGINAL TEXT:
\"$sampleAnswer\"

STUDENT'S TRANSCRIPT (from speech recognition):
\"$transcript\"

Score the student on these criteria (0-100 each):
1. Accuracy: How many words match the original text
2. Fluency: Natural flow, appropriate length
3. Pronunciation: Based on how well speech recognition understood them (higher match = better pronunciation)

Also provide brief, encouraging feedback in Vietnamese (2-3 sentences).

Respond ONLY in this exact JSON format:
{
  \"accuracy_score\": <number>,
  \"fluency_score\": <number>,
  \"pronunciation_score\": <number>,
  \"overall_score\": <number>,
  \"feedback\": \"<Vietnamese feedback text>\"
}";

        $result = self::chatCompletion([
            ['role' => 'system', 'content' => 'You are an English teacher AI. Always respond with valid JSON only.'],
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.3);
        
        if (!$result || !isset($result['choices'][0]['message']['content'])) {
            return null; // Fallback to local scoring
        }
        
        $content = $result['choices'][0]['message']['content'];
        // Parse JSON from response
        $scores = json_decode($content, true);
        
        if (!$scores || !isset($scores['accuracy_score'])) {
            // Try to extract JSON from text
            if (preg_match('/\{[^}]+\}/', $content, $matches)) {
                $scores = json_decode($matches[0], true);
            }
        }
        
        if (!$scores || !isset($scores['accuracy_score'])) {
            return null;
        }
        
        // Clamp scores
        $scores['accuracy_score'] = max(0, min(100, intval($scores['accuracy_score'])));
        $scores['fluency_score'] = max(0, min(100, intval($scores['fluency_score'])));
        $scores['pronunciation_score'] = max(0, min(100, intval($scores['pronunciation_score'])));
        $scores['overall_score'] = round(
            $scores['accuracy_score'] * 0.4 +
            $scores['fluency_score'] * 0.3 +
            $scores['pronunciation_score'] * 0.3
        );
        
        return $scores;
    }
    
    /**
     * AI Chatbot: trả lời câu hỏi tiếng Anh
     * @param string $userMessage Tin nhắn từ user
     * @param array $history Lịch sử chat [{role, content}, ...]
     * @return string|null AI response hoặc null
     */
    public static function chatbot($userMessage, $history = []) {
        $systemPrompt = "You are English Learning AI Assistant - a friendly English learning chatbot. You help Vietnamese students learn English.

Rules:
- Answer in Vietnamese mixed with English examples when teaching
- If asked about grammar, explain clearly with examples
- If asked to translate, provide the translation with pronunciation guide
- If asked about vocabulary, give meaning, example sentence, and synonyms
- Keep responses concise (under 200 words)
- Use emoji to make responses engaging
- If the question is not related to English learning, politely redirect to English topics";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        // Add history (last 6 messages)
        $recentHistory = array_slice($history, -6);
        foreach ($recentHistory as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        
        $messages[] = ['role' => 'user', 'content' => $userMessage];
        
        $result = self::chatCompletion($messages, 'gpt-3.5-turbo', 0.7);
        
        if (!$result || !isset($result['choices'][0]['message']['content'])) {
            return null;
        }
        
        return $result['choices'][0]['message']['content'];
    }
}

