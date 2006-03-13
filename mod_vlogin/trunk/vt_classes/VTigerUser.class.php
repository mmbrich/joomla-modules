<?php
/*
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; version 2 of the License.
     
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * (c) 2005 Matthew Brichacek <mmbrich@fosslabs.com>
 *
 */

require_once("VTigerConnection.class.php");
class VTigerUser {
	var $password;
	var $username;
	var $email;
	var $name;
	var $conn;
	var $data;

	function VTigerUser($servername)
	{
		$this->conn = new VTigerConnection($servername,"userserialize.php");
	}
	function authUser()
	{
		$this->data = array('user_name' => $this->username,
				      'user_password' => $this->password);
		$this->conn->setData($this->data);
		return $this->conn->execCommand('authenticate_user');
	}
	function addUser()
	{
		$this->data = array('username' 	=> $this->username,
				    'password' 	=> $this->password,
				    'email'	=> $this->email,
				    'name'	=> $this->name);
		$this->conn->setData($this->data);
		return $this->conn->execCommand('create_user');
	}
	function deleteUser()
	{
		$this->data = array('username' => $this->username);
		$this->conn->setData($this->data);
		return $this->conn->execCommand('delete_user');
	}
	function logOut()
	{
		setcookie("ck_login_id", '', time()-42000, '/');
        	setcookie("timezone", '', time()-42000, '/');
        	setcookie(session_name(), '', time()-42000, '/');
        	$_SESSION["authenticated_user_id"] = NULL;
		unset($_SESSION["authenticated_user_id"]);
	}
	function changePassword()
	{
		$this->data = array('user_name' => $this->username,
				   'user_password' => $this->password);
		$this->conn->setData($this->data);
		return $this->conn->execCommand('change_pass');
	}
}
?>
