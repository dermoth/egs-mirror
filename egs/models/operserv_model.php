<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * EGs Web Panel (Web Services for Atheme)
 *
 * author: J. Newing (synmuffin)
 * email: jnewing [at] gmail [dot] com
 * version: 3.0
 *
 */

/**
 * Operserv Model
 *
 */
class Operserv_model extends CI_Model {

	
	//========================================================
	// PRIVATE VARS
	//========================================================
	
	
	//========================================================
	// PUBLIC VARS
	//========================================================
	
	
	//========================================================
	// PUBLIC FUNCTIONS
	//========================================================
	
	
	/**
	 * Construct
	 * 
	 */
	public function __construct()
	{
		parent::__construct();
	}
	// --------------------------------------------------------
	
	
	/**
	 * check_access()
	 * function will check for access to Operserv and return a bool value if users has access
	 * or not
	 *
	 */
	public function check_access()
	{
		$ret_array = array();

		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"HELP"
			)
		);

		if ($cmd)
			return TRUE;

		return FALSE;
	}
	// --------------------------------------------------------


	/**
	 * akill_add()
	 * function allows service admins to add a specific akill to the network
	 *
	 * @param string $nick_host		- nickname, or hostname of the user to add the akill for
	 * @param string $type 			- type of akilll to add perma (!P) or timed (!T)
	 * @param string $duration 		- if the above param is !T then a time can be specified in format #d#m etc...
	 * @param string $reason 		- optional reason for the akill
	 */
	public function akill_add($nick_host, $akill_type, $duration = FALSE, $reason = FALSE)
	{
		$ret_array = array();

		if ($akill_type == "!T" && $duration)
		{
			$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
				array(
					"AKILL",
					"ADD",
					$nick_host,
					$akill_type . ' ' . ($duration) ? $duration : '1d' . ' ' . ($reason) ? $reason : 'No reason given.'
				)
			);	
		}
		else
		{
			$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
				array(
					"AKILL",
					"ADD",
					$nick_host,
					($reason) ? $reason : 'No reason given.',
				)
			);
		}

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	// --------------------------------------------------------


	/**
	 * akill_del()
	 * function deletes an akill for the akill list via id, now i know that bulk actions are supported bia #,#,# or #:# format
	 * however i've not yet added support for it in EGs... it's on the todo list.
	 *
	 * @param int $akill_id 	- the id of the akill to remove
	 */
	public function akill_del($akill_id)
	{
		$ret_array = array();

		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				'AKILL',
				'DEL',
				$akill_id
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	// --------------------------------------------------------


	/**
	 * akill_list()
	 * function will list all the current services akills
	 *
	 * @param bool $full 		- display the reason? default yes
	 */
	public function akill_list($full = TRUE)
	{
		$ret_array = array();

		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"AKILL",
				"LIST",
				(($full) ? 'FULL' : NULL)
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	// --------------------------------------------------------
	

	/**
	 * send_global()
	 * function will send out a global message to all users on the network. mind the spam!
	 *
	 * @param string $global_msg 	- the global message you wish to send.
	 */
	public function send_global($global_msg)
	{
		$ret_array = array();

		// build the global lines
		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"GLOBAL",
				$global_msg
			)
		);

		// send the global
		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"GLOBAL",
				"SEND"
			)
		);

		// clear the global
		$cmd1 = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"GLOBAL",
				"CLEAR"
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	// --------------------------------------------------------



	public function module_list()
	{
		$ret_array = array();

		// build the global lines
		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"MODLIST"
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	// --------------------------------------------------------


	
	public function clear_channel($clear_action, $clear_channel, $clear_reason = FALSE)
	{
		$ret_array = array();

		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"CLEARCHAN",
				$clear_action,
				$clear_channel,
				($clear_reason) ? $clear_reason : 'No reason given.'
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;

	}
	// --------------------------------------------------------


	public function rehash()
	{
		$ret_array = array();

		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"REHASH"
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	// --------------------------------------------------------


	/**
	 * specs()
	 * function will run a specs command on the current user
	 */
	public function specs()
	{
		$ret_array = array();

		$cmd = $this->atheme->atheme_command($this->session->userdata('nick'), $this->session->userdata('auth'), $this->config->item('atheme_operserv'),
			array(
				"SPECS"
			)
		);

		if ($cmd)
		{
			$ret_array['response'] = TRUE;
			$ret_array['data'] = $this->xmlrpc->display_response();
		}
		else
		{
			$ret_array['response'] = FALSE;
			$ret_array['data'] = $this->xmlrpc->display_error();
		}
		
		return $ret_array;
	}
	
	
	//========================================================
	// PRIVATE FUNCTIONS
	//========================================================
	
	
}
