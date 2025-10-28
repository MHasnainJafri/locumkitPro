<?php

namespace App\Mail;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobNegotiateMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobPost $job;
    public User $employer;
    public User $freelancer;

    public float $job_expected_rate;
    public string $freelancer_message;

    public string $freelancer_questions_html;
    public string $accept_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(JobPost $job, User $freelancer, User $employer, float $job_expected_rate, string $freelancer_message)
    {
        $this->job = $job;
        $this->employer = $employer;
        $this->freelancer = $freelancer;
        $this->job_expected_rate = $job_expected_rate;
        $this->freelancer_message = $freelancer_message;

        $this->freelancer_questions_html = "";
        foreach ($freelancer->user_answers as $user_answer) {
            $answer_value = json_decode($user_answer->type_value) ? join(" / ", json_decode($user_answer->type_value)) : $user_answer->type_value;

            $this->freelancer_questions_html .= '
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->employer_question . '</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                </tr>
            ';
        }

        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($freelancer->id);
        $encrypted_job_expected_rate = encrypt($this->job_expected_rate);

        $this->accept_url = url("/negotiate/employer-accept-negotiation?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&job_expected_rate={$encrypted_job_expected_rate}");
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Job Negotiate Mail',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.job-negotiate-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}