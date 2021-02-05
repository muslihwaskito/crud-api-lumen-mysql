<?php 

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ResetPassword
 */
class ResetPasswordMail extends Mailable
{
	
	use Queueable, SerializesModels;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function build()
	{
		return $this->view('emails.reset_password')->with(['user' => $this->user]);
	}
}