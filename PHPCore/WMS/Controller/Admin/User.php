<?php
namespace WMS\Controller\Admin;

use Core\String;
use Core\Request;
use Core\UserManagement;
use Core\MVC\IController;
use WMS\Admin\Users\User as Usr;
use WMS\Viewer\Viewer;
use WMS\Viewer\AdminViewer;

class User implements IController
{
	public static function Initialize($url = null)
	{
		if (Request::Post('action') === 'loginCheck')
		{
			$isLoggedIn = Usr::IsLoggedIn();
			$data = ['isLoggedIn' => $isLoggedIn];
			if ($isLoggedIn) {
				$user = Usr::GetLoggedInUser();
				$data = array_merge($data, ['data' => $user->ToArray()], ['navbarItems' => Navigation::GetNavItems($user->userType)]);
			}
			Viewer::PrintJson($data);
		}

		if (!empty($url) && isset($url[0]))
		{
			if (Usr::IsLoggedIn())
			{
				switch ($url[0])
				{
					case 'home':
						self::UserHome();
						break;
				}
			}
			else
			{
				switch ($url[0])
				{
					case 'Login':
						if ($action = Request::Post('action'))
						{
							$json = [];
							switch ($action)
							{
								case 'login':
									try {
										if (($username = UserManagement\UserManagement::XSSFilter(Request::Post('username'))) &&
											$password = UserManagement\UserManagement::XSSFilter(Request::Post('password'))) {
											$userObj = Usr::Login($username, $password);
											$json = ['result' => 1, 'msg' => "<strong>Welcome " . $userObj->name . ",</strong> We hope you are in better health."];
										} else {
											$json = ['result' => 0, 'msg' => "You should enter username & password."];
										}
									}
									catch (UserManagement\UserLockedException $e) {
										$json = ['result' => 0, 'msg' => "Your account has been locked.<br>Please contact with the admin."];
									}
									catch (UserManagement\UserIpBlockedException $e) {
										$json = ['result' => 0, 'msg' => "Sorry! your ip is blocked."];
									}
									catch (UserManagement\InvalidUsernamePasswordException $e) {
										$json = ['result' => 0, 'msg' => "The username or password which you entered is incorrect! Please enter the correct data and then try again."];
									}
									catch (UserManagement\UserException $e) {
										$json = ['result' => 0, 'msg' => "Error! User login failed."];
									}
									catch (\Exception $e) {
										$json = ['result' => 0, 'msg' => "Error!"];
									}
									break;
							}
							Viewer::PrintJson($json);
						}
						else
						{
							AdminViewer::View('User/Login');
						}
						break;
					//case 'ActiveAccount':
					//    if ($action = Request::Request('action'))
					//    {
					//        $json = [];
					//        switch ($action)
					//        {
					//            case 'userValUsername':
					//                $username = String::XSSFilter(Request::Request('user')['username']);
					//                $exId = Request::Request('userId');
					//                if (is_numeric($exId))
					//                    $exId = intval($exId);
					//                else
					//                    $exId = null;
					//                if (!Usr::ValidateUsername($username, $exId))
					//                    http_response_code(400);
					//                else
					//                    http_response_code(200);
					//                break;
					//            case 'userValEmail':
					//                $userEmail = String::XSSFilter(Request::Request('user')['email']);
					//                $exId = Request::Request('userId');
					//                if (is_numeric($exId))
					//                    $exId = intval($exId);
					//                else
					//                    $exId = null;
					//                if(!Usr::ValidateUserEmail($userEmail, $exId))
					//                    http_response_code(400);
					//                else
					//                    http_response_code(200);
					//                break;
					//            //case 'activeCheck':
					//            //    try {
					//            //        if (($id = UserManagement\UserManagement::XSSFilter(Request::Post('id'))) &&
					//            //            $activeCode = UserManagement\UserManagement::XSSFilter(Request::Post('activeCode'))) {
					//            //            $userObj = Usr::GetActiveUser($id, $activeCode);
					//            //            $json = ['result' => 2, 'msg' => "<strong>Activision Code is correct,</strong> Please complete your data."];
					//            //        } else {
					//            //            $json = ['result' => 0, 'msg' => "You should enter ID & active code."];
					//            //        }
					//            //    }
					//            //    catch(UserManagement\UserNotExistsException $e){
					//            //        $json = ['result' => 0, 'msg' => "The Activision Code or ID which you entered is incorrect! Please enter the correct data and then try again."];
					//            //    }
					//            //    catch (UserManagement\UserIpBlockedException $e) {
					//            //        $json = ['result' => 0, 'msg' => "Sorry! your ip is blocked."];
					//            //    }
					//            //    catch (UserManagement\UserException $e) {
					//            //        $json = ['result' => 0, 'msg' => "Error!"];
					//            //    }
					//            //    catch (\Exception $e) {
					//            //        $json = ['result' => 0, 'msg' => "Error!"];
					//            //    }
					//            //    break;
					//            //case 'activeUser':
					//            //    try {
					//            //        if (($id = intval(Request::Post('id'))) &&
					//            //            $activeCode = String::XSSFilter(Request::Post('activeCode'))) {
					//            //            $data = self::initReqData();
					//            //            $activeUserData = $data['user'];
					//            //            $activeStdData = $data['student'];
					//            //            Usr::ActiveUser($id, $activeCode, $activeUserData);
					//            //            Student::Initialize();
					//            //            Student::Update($activeStdData, ['userId', $id]);
					//            //            $json = ['result' => 1, 'msg' => "<strong>Well Done " . htmlspecialchars($activeUserData['firstName']) . ",</strong> your account is now active, use your Username and Password to Login."];
					//            //        } else {
					//            //            $json = ['result' => 0, 'msg' => "You should enter valid data."];
					//            //        }
					//            //    }
					//            //    catch (UserManagement\UserNotExistsException $e) {
					//            //        $json = ['result' => 0, 'msg' => "The Activision Code or ID which you entered is incorrect! Please enter the correct data and then try again."];
					//            //    }
					//            //    catch (UserManagement\UserIpBlockedException $e) {
					//            //        $json = ['result' => 0, 'msg' => "Sorry! your ip is blocked."];
					//            //    }
					//            //    catch (UserManagement\UserException $e) {
					//            //        $json = ['result' => 0, 'msg' => "Error!"];
					//            //    }
					//            //    catch (\Exception $e) {
					//            //        $json = ['result' => 0, 'msg' => $e->getMessage()];
					//            //    }
					//            //    break;
					//        }
					//        Viewer::PrintJson($json);
					//    }
					//    else
					//    {
					//        Viewer::View('User' . DS . 'ActiveAccount');
					//    }
				}
			}
		}
		else
		{
			if (Usr::IsLoggedIn())
			{
				if ($action = Request::Post('action'))
				{
					$json = [];
					switch ($action)
					{
						case 'logout':
							try {
								Usr::Logout();
								$json = ['result' => 1, 'msg' => "You have signed out successfully."];
							}
							catch (UserManagement\UserException $e) {
								$json = ['result' => 0, 'msg' => "Error in sign out!"];
							}
							break;
					}

					Viewer::PrintJson($json);
				}
				else
				{
					self::UserHome();
				}
			}
			else
			{
				AdminViewer::View('User/Login');
			}
		}
	}

	private static function UserHome()
	{
		$user = Usr::GetLoggedInUser();
		AdminViewer::View('Home', ['userName' => $user->namePrefix . $user->name, 'userAvatar' => 'default-avatar.png']);
	}
	private static function initReqData()
	{
		$userId = intval(Request::Post('id'));
		$data = ['user' => [], 'student' => []];
		$reqUserData = Request::Post('user');
		$reqStudentData = Request::Post('student');

		if (!empty($reqUserData)) {
			//check first and last name is less than 50 and is not empty
			if ((isset($reqUserData['firstName']) && String::Check($reqUserData['firstName'], 2, 50, '/^[a-zA-Z][a-zA-Z ,.\'-]+$/i')) && ((isset($reqUserData['lastName']) && String::Check($reqUserData['lastName'], 2, 50, '/^[a-zA-Z][a-zA-Z ,.\'-]+$/i'))))
			{
				$data['user']['firstName'] = $reqUserData['firstName'];
				$data['user']['lastName'] = $reqUserData['lastName'];
			}
			else
				throw new \Exception("Error has been encountered! Kindly review the given first name or last name");
			//check userName is !empty and is valid and bigger than 5 and less than or equal to 20 and in pattern of ^[a-zA-Z][a-zA-Z0-9]*[._-]?[a-zA-Z0-9]+$
			if ((isset($reqUserData['username']) && String::Check($reqUserData['username'], 5, 20, '/^[a-zA-Z][a-zA-Z0-9]*[._-]?[a-zA-Z0-9]+$/i')) && Usr::ValidateUsername($reqUserData['username'], $userId))
				$data['user']['username'] = $reqUserData['username'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given user name");
			//check the email pattern and valid it and is not empty
			if ((isset($reqUserData['email']) && Usr::ValidateUserEmail($reqUserData['email'], $userId) && filter_var($reqUserData['email'], FILTER_VALIDATE_EMAIL)))
				$data['user']['email'] = $reqUserData['email'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given Email");
			//check password length and validation and match it with confirmed password
			if ((isset($reqUserData['password'])) && String::Check($reqUserData['password'], 6) && $reqUserData['password'] === $reqUserData['passwordConfirm'])
				$data['user']['password'] = $reqUserData['password'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given Password and be sure it match the confirmed");
		}
		if (!empty($reqStudentData)) {
			//check english to be bigger than 2 and contain just - and space and not bigger than 50 and not empty
			if ((isset($reqStudentData['englishName']) && String::Check($reqStudentData['englishName'], 2, 50, '/^[a-zA-Z][a-zA-Z ,.\'-]+$/i')))
				$data['student']['englishName'] = $reqStudentData['englishName'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given english name");
			//check phone number to be bigger than 11 number and just numbers
			if ((isset($reqStudentData['phoneNo'])) && is_numeric($reqStudentData['phoneNo']) && String::Check($reqStudentData['phoneNo'], 11, 11))
				$data['student']['phoneNo'] = $reqStudentData['phoneNo'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given phone number");
			//check rest of values to be not empty
			if((isset($reqStudentData['gender'])))
				$data['student']['gender'] = $reqStudentData['gender'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given gender");

			if ((isset($reqStudentData['birthDate'])))
				$data['student']['birthDate'] = $reqStudentData['birthDate'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given birth date");

			if (isset($reqStudentData['address']))
				$data['student']['address'] = $reqStudentData['address'];
			else
				throw new \Exception("Error has been encountered! Kindly review the given address");
		}
		//else
		//    throw new \Exception("Error! Kindly complete all the required information in order to proceed");
		return $data;
	}
}
