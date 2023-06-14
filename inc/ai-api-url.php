<?php
/**
 * AI Api Url.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Md_Ai_Content_Writer;

/**
 * AI Api Url
 *
 * @since x.x.x
 */
class AI_Api_Url extends Md_Ai_Content_Writer {

    use Get_Instance;

    /**
	 * API Origin URL.
	 *
	 * @var string
	 */
    public const ORIGIN = 'https://api.openai.com';

    /**
	 * The API Version.
	 *
	 * @var string
	 */
    public const API_VERSION = 'v1';

    /**
	 * Creating Full API URL.
	 *
	 * @var string
	 */
    public const OPEN_AI_URL = self::ORIGIN . "/" . self::API_VERSION;

    /**
     * Completion URL
     * 
     * @deprecated
     * @param string $engine
     * @return string
     */
    public static function completionURL(string $engine): string {
        return self::OPEN_AI_URL . "/engines/$engine/completions";
    }

    /**
     * Completions URL
     * 
     * @return string
     */
    public static function completionsURL(): string {
        return self::OPEN_AI_URL . "/completions";
    }

    /**
     * Edits Url
     * 
     * @return string
     */
    public static function editsUrl(): string {
        return self::OPEN_AI_URL . "/edits";
    }

    /**
     * Search URL
     * 
     * @param string $engine
     * @return string
     */
    public static function searchURL(string $engine): string {
        return self::OPEN_AI_URL . "/engines/$engine/search";
    }

    /**
     * Engines Url
     * 
     * @param
     * @return string
     */
    public static function enginesUrl(): string {
        return self::OPEN_AI_URL . "/engines";
    }

    /**
     * Engine Url
     * 
     * @param string $engine
     * @return string
     */
    public static function engineUrl(string $engine): string {
        return self::OPEN_AI_URL . "/engines/$engine";
    }

    /**
     * Classifications Url
     * 
     * @param
     * @return string
     */
    public static function classificationsUrl(): string {
        return self::OPEN_AI_URL . "/classifications";
    }

    /**
     * Moderation Url
     * 
     * @param
     * @return string
     */
    public static function moderationUrl(): string {
        return self::OPEN_AI_URL . "/moderations";
    }

    /**
     * Transcriptions Url
     * 
     * @param
     * @return string
     */
    public static function transcriptionsUrl(): string {
        return self::OPEN_AI_URL . "/audio/transcriptions";
    }

    /**
     * Translations Url
     * 
     * @param
     * @return string
     */
    public static function translationsUrl(): string {
        return self::OPEN_AI_URL . "/audio/translations";
    }

    /**
     * Files URL
     * 
     * @param
     * @return string
     */
    public static function filesUrl(): string {
        return self::OPEN_AI_URL . "/files";
    }

    /**
     * Fine Tune URL
     * 
     * @param
     * @return string
     */
    public static function fineTuneUrl(): string {
        return self::OPEN_AI_URL . "/fine-tunes";
    }

    /**
     * Fine Tune Model
     * 
     * @param
     * @return string
     */
    public static function fineTuneModel(): string {
        return self::OPEN_AI_URL . "/models";
    }

    /**
     * Answers Url
     * 
     * @param
     * @return string
     */
    public static function answersUrl(): string {
        return self::OPEN_AI_URL . "/answers";
    }

    /**
     * Image Url
     * 
     * @param
     * @return string
     */
    public static function imageUrl(): string {
        return self::OPEN_AI_URL . "/images";
    }

    /**
     * Embedding
     * 
     * @param
     * @return string
     */
    public static function embeddings(): string {
        return self::OPEN_AI_URL . "/embeddings";
    }

    /**
     * Chat Url
     * 
     * @param
     * @return string
     */
    public static function chatUrl(): string {
        return self::OPEN_AI_URL . "/chat/completions";
    }
}
