<?php

namespace App\Services;

use Telegram\Bot\Api;
use TelegramBot\Api\BotApi;

class TelegramService
{
    protected $bot;

    public function __construct()
    {
        $this->bot = new BotApi(config('telegram.token'));
    }

    /**
     * Отправляет сообщение по списку chat ID.
     *
     * @param array $chatIds Массив chat ID
     * @param string $message Текст сообщения
     * @return void
     */
    public function sendMessageToUsers(array $chatIds, string $message)
    {
        foreach ($chatIds as $chatId) {
            $this->sendMessage($chatId, $message);
        }
    }

    /**
     * Отправляет сообщение по chat ID.
     *
     * @param string $chatId chat ID
     * @param string $message Текст сообщения
     * @return void
     */
    public function sendMessage($chatId, $message)
    {
        try {
            $this->bot->sendMessage($chatId, $message);
        } catch (\Exception $e) {
            \Log::error("Ошибка отправки сообщения в Telegram: " . $e->getMessage());
        }
    }
}
