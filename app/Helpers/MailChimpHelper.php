<?php

namespace App\Helpers;

class MailChimpHelper
{
    private string $api_key = config('app.mailchimp_api_key');
    private string $listid = config('app.mailchimp_listid');
    private string $server = config('app.mailchimp_server');

    public function listMembers(): array
    {
        if (class_exists("\MailchimpMarketing\ApiClient")) {
            $mailchimp = new \MailchimpMarketing\ApiClient();

            $mailchimp->setConfig([
                'apiKey' => $this->api_key,
                'server' => $this->server
            ]);

            $members = $mailchimp->lists->getListMembersInfo($this->listid)->members;

            if ($members && is_array($members) && sizeof($members) > 0) {
                $members_emails = array_map(function ($member) {
                    return $member->email_address;
                }, $members);

                return $members_emails;
            }
        }

        return [];
    }
}
