<?php

namespace App\Mail\Transport;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphResponse;
use Microsoft\Graph\Model\Recipient;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class MicrosoftOutlookMailTransport extends AbstractTransport
{
    /**
     * Create a new outlook transport instance.
     */
    protected string $accessToken;
    private string $access_token_cache_key = 'outlook.transport.access_token_cache';

    public function __construct(
        protected string $client_id,
        protected string $client_secret,
        protected string $tenant_id,
        protected string $send_mail_post_url,
        protected int|null $timeout = null,
    ) {
        parent::__construct();
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {

        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $contentType = $email->getHtmlBody() ? 'html' : 'text';
        $r = new Recipient();
        /** @see End of File for $mailBody structure */
        $mailBody = [
            'message' => [
                'subject' => $email->getSubject(),
                'body' => [
                    'contentType' => $contentType,
                    'content' => $email->getHtmlBody() ? $email->getHtmlBody() : $email->getTextBody(),
                ],
                'from' => [
                    'emailAddress' => [
                        'address' => $email->getFrom()[0]->getAddress(),
                        'name' => $email->getFrom()[0]->getName(),
                    ],
                ],
                'sender' => [
                    'emailAddress' => [
                        'address' => $email->getFrom()[0]->getAddress(),
                        'name' => $email->getFrom()[0]->getName(),
                    ],
                ],
                'toRecipients' => [
                    [
                        'emailAddress' => [
                            'address' => $email->getTo()[0]->getAddress(),
                            'name' => $email->getTo()[0]->getName(),
                        ],
                    ],
                ],
            ],
        ];

        /** @var GraphResponse $sent */
        $sent = $graph->createRequest("POST", $this->send_mail_post_url)
            ->attachBody($mailBody)
            ->setTimeout($this->timeout ?? 60)
            ->execute();
        if ($sent->getStatus() === 202) {
            return;
        }
        $errorDetails = $sent->getBody();
        throw new TransportException("Failed to send email using Microsoft Graph API. Error: " . $errorDetails, $sent->getStatus());
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'outlook';
    }

    /**
     * Get the access token from the cache or obtain a new one.
     */
    protected function getAccessToken(): string
    {
        $accessToken = Cache::get($this->access_token_cache_key);
        if (!$accessToken || ($accessToken && $this->isTokenExpired($accessToken))) {
            $guzzle = new \GuzzleHttp\Client();
            $url = 'https://login.microsoftonline.com/' . $this->tenant_id . '/oauth2/v2.0/token';
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ],
            ])->getBody()->getContents());
            $accessToken = $token->access_token;
            Cache::set($this->access_token_cache_key, $accessToken);
        }
        return $accessToken;
    }

    protected function isTokenExpired(string $accessToken): bool
    {
        $tokenParts = explode('.', $accessToken);
        if (count($tokenParts) !== 3) {
            return true; // Invalid token format
        }
        $tokenPayload = base64_decode($tokenParts[1]);
        $payloadData = json_decode($tokenPayload);
        if (!isset($payloadData->exp)) {
            return true; // Expiration time not found in token
        }
        $expirationTime = $payloadData->exp;
        $currentTime = time();
        // Check if the token has expired (with a buffer of a few minutes)
        $buffer = 300; // 5 minutes
        if ($expirationTime <= ($currentTime + $buffer)) {
            return true; // Token has expired or is about to expire
        }
        return false; // Token is still valid
    }

    /*
        https://learn.microsoft.com/en-us/graph/api/resources/message?view=graph-rest-1.0
        {
        "bccRecipients": [{"@odata.type": "microsoft.graph.recipient"}],
        "body": {"@odata.type": "microsoft.graph.itemBody"},
        "bodyPreview": "string",
        "categories": ["string"],
        "ccRecipients": [{"@odata.type": "microsoft.graph.recipient"}],
        "changeKey": "string",
        "conversationId": "string",
        "conversationIndex": "String (binary)",
        "createdDateTime": "String (timestamp)",
        "flag": {"@odata.type": "microsoft.graph.followupFlag"},
        "from": {"@odata.type": "microsoft.graph.recipient"},
        "hasAttachments": true,
        "id": "string (identifier)",
        "importance": "String",
        "inferenceClassification": "String",
        "internetMessageHeaders": [{"@odata.type": "microsoft.graph.internetMessageHeader"}],
        "internetMessageId": "String",
        "isDeliveryReceiptRequested": true,
        "isDraft": true,
        "isRead": true,
        "isReadReceiptRequested": true,
        "lastModifiedDateTime": "String (timestamp)",
        "parentFolderId": "string",
        "receivedDateTime": "String (timestamp)",
        "replyTo": [{"@odata.type": "microsoft.graph.recipient"}],
        "sender": {"@odata.type": "microsoft.graph.recipient"},
        "sentDateTime": "String (timestamp)",
        "subject": "string",
        "toRecipients": [{"@odata.type": "microsoft.graph.recipient"}],
        "uniqueBody": {"@odata.type": "microsoft.graph.itemBody"},
        "webLink": "string",

        "attachments": [{"@odata.type": "microsoft.graph.attachment"}],
        "extensions": [{"@odata.type": "microsoft.graph.extension"}],
        "multiValueExtendedProperties": [{"@odata.type": "microsoft.graph.multiValueLegacyExtendedProperty"}],
        "singleValueExtendedProperties": [{"@odata.type": "microsoft.graph.singleValueLegacyExtendedProperty"}]
        }
    */
}
