<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Massimiliano Palermo <maxx.palermo@gmail.com>
 * @copyright Since 2023 Massimiliano Palermo
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace MpSoft\MpWaCart\Api;

class WhatsAppBusinessApi
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $phoneNumberId;

    /**
     * @var string
     */
    private $baseUrl = 'https://graph.facebook.com/v17.0/';

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param string $phoneNumberId
     */
    public function __construct($apiKey, $phoneNumberId)
    {
        $this->apiKey = $apiKey;
        $this->phoneNumberId = $phoneNumberId;
    }

    /**
     * Send a text message
     *
     * @param string $to
     * @param string $message
     * @return array
     */
    public function sendTextMessage($to, $message)
    {
        $url = $this->baseUrl . $this->phoneNumberId . '/messages';

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        return $this->sendRequest($url, $data);
    }

    /**
     * Send a document message
     *
     * @param string $to
     * @param string $documentUrl
     * @param string $caption
     * @return array
     */
    public function sendDocumentMessage($to, $documentUrl, $caption)
    {
        $url = $this->baseUrl . $this->phoneNumberId . '/messages';

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'document',
            'document' => [
                'link' => $documentUrl,
                'caption' => $caption
            ]
        ];

        return $this->sendRequest($url, $data);
    }

    /**
     * Send a template message
     *
     * @param string $to
     * @param string $templateName
     * @param array $components
     * @return array
     */
    public function sendTemplateMessage($to, $templateName, $components = [])
    {
        $url = $this->baseUrl . $this->phoneNumberId . '/messages';

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => 'it'
                ]
            ]
        ];

        if (!empty($components)) {
            $data['template']['components'] = $components;
        }

        return $this->sendRequest($url, $data);
    }

    /**
     * Send a request to the WhatsApp Business API
     *
     * @param string $url
     * @param array $data
     * @return array
     */
    private function sendRequest($url, $data)
    {
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => $error
            ];
        }

        $responseData = json_decode($response, true);
        if ($httpCode >= 400) {
            return [
                'success' => false,
                'error' => $responseData['error'] ?? 'Unknown error'
            ];
        }

        return [
            'success' => true,
            'data' => $responseData
        ];
    }
}
