<?php

namespace App\Http\Controllers;

use App\Models\MailGroupList;
use App\Models\MailGroupMail;
use App\Models\MailGroupUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailGroupingController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod("GET")) {
            return view("mailgroup.login");
        } else {
            $request->validate([
                "email" => "required|email",
                "password" => "required|string|min:6"
            ]);

            if (Auth::guard('mail_group_web')->attempt($request->only($this->username(), 'password'), $request->boolean('remember'))) {
                return redirect()->route('email-grouping.index')->with('success', 'Login successfull');
            } else {
                return redirect()->back()->with('error', 'Login failed');
            }
        }
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username(): string
    {
        return 'email';
    }

    public function home()
    {
        return view('mailgroup.home');
    }
    public function logout()
    {
        Auth::guard('mail_group_web')->logout();
        return redirect()->route('email-grouping.index');
    }

    public function users()
    {
        $users = MailGroupUser::all();
        return view('mailgroup.users.index', ["users" => $users]);
    }

    public function saveUser(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email",
            "password" => "required|string|min:6|max:20",
            "role" => "required|in:admin,user",
        ]);
        $user = new MailGroupUser();
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        $user->password = Hash::make($request->input("password"));
        $user->role = $request->input("role");
        $user->save();

        return back()->with("success", "User added successfully");
    }
    public function updateUser(Request $request)
    {
        $request->validate([
            "id" => "required|exists:mail_group_users,id",
            "name" => "required|string",
            "email" => "required|email",
            "password" => "nullable|string|min:6|max:20",
            "role" => "required|in:admin,user",
        ]);
        $user = MailGroupUser::find($request->input("id"));
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        if ($request->has("password") && $request->input("password")) {
            $user->password = Hash::make($request->input("password"));
        }
        $user->role = $request->input("role");
        $user->save();

        return back()->with("success", "User updated successfully");
    }
    public function deleteUser(Request $request, $id)
    {
        $user = MailGroupUser::find($id);
        $user->delete();

        return back()->with("success", "User deleted successfully");
    }

    public function mailists()
    {
        $mailists = MailGroupList::with("mail_group_mails")->get();
        return view('mailgroup.maillists.index', ["mailists" => $mailists]);
    }

    public function saveMailist(Request $request)
    {
        $request->validate([
            "name" => "required|string"
        ]);
        $maillist = new MailGroupList();
        $maillist->name = $request->input("name");
        $maillist->save();

        return back()->with("success", "Mail list added successfully");
    }

    public function updateMailist(Request $request)
    {
        $request->validate([
            "id" => "required|exists:mail_group_lists,id",
            "name" => "required|string"
        ]);
        $maillist = MailGroupList::find($request->input("id"));
        $maillist->name = $request->input("name");
        $maillist->save();

        return back()->with("success", "Mail list updated successfully");
    }
    public function updateMailistMails(Request $request)
    {
        $request->validate([
            "id" => "required|exists:mail_group_lists,id",
            "emails" => "nullable|array",
            "emails.*" => "nullable|email"
        ]);
        $maillist = MailGroupList::find($request->input("id"));

        $maillist->mail_group_mails()->delete();

        $emails = $request->input("emails");
        if ($emails && sizeof($emails) > 0) {
            $maillistemails = [];
            foreach ($emails as $email) {
                $maillistemails[] = [
                    "mail" => $email,
                    "mail_group_list_id" => $maillist->id
                ];
            }
            MailGroupMail::insert($maillistemails);
        }

        return back()->with("success", "Mails updated successfully");
    }

    public function deleteMailist($id)
    {
        $maillist = MailGroupList::findOrFail($id);
        $maillist->mail_group_mails()->delete();
        $maillist->delete();
        return back()->with("success", "Maillist deleted successfully");
    }

    public function mailing()
    {
        $maillists = MailGroupList::all();

        return view('mailgroup.mailing', ["maillists" => $maillists]);
    }

    public function mailSend(Request $request)
    {
        $request->validate([
            "title" => "required|string|min:5",
            "list" => "required|exists:mail_group_lists,id",
            "message" => "required"
        ]);
        $message = $request->input("message");
        $htmlMessageBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Email Content</title>
            </head>
            <body>
                ' . $message . '
            </body>
            </html>
        ';
        $subject = $request->input("title");
        $mails = MailGroupMail::where("mail_group_list_id", $request->input("list"))->select("mail")->get()->pluck("mail")->toArray();
        if (sizeof($mails) == 0) {
            return back()->with("error", "No mail present in the selected list to sent emails");
        }
        $counter = 0;
        foreach ($mails as $mail) {
            try {
                $sent = Mail::html($htmlMessageBody, function (Message $message) use ($mail, $subject) {
                    $message->to($mail)->subject($subject);
                });
                if ($sent) {
                    $counter++;
                }
            } catch (Exception $e) {
                Log::error("Email sending error: " . $e->getMessage());
            }
        }
        return back()->with("success", $counter . " mails sent successfully. " . (sizeof($mails) - $counter) . " mails failed to sent. ");
    }
}
