<?php

use Telegram\Bot\Laravel\Facades\Telegram;

// Send message to developer
function error_telegram($message){
    $error_text = env('APP_NAME', "");
    $error_text .= "\n".$message;
    $error_text = excerpt($error_text, 1000, "...");

    $chat_id = 738876893;

    $telegram_response = Telegram::sendMessage([
        'chat_id' => $chat_id,
        'text' => $error_text,
        //'parse_mode' => 'markdown'
    ]);
    $message_id = $telegram_response->getMessageId();
}