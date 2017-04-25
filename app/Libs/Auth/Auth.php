<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 3/17/2016
 * Time: 12:08 PM
 */

namespace App\Libs\Auth;

use App\Repositories\UsersRepository;
use App\User;
use Illuminate\Support\Facades\Hash;
class Auth
{

    /**
     * @param array $credentials
     * @return bool
     */
    public static function attempt(array $credentials)
    {
        try{
            $user = (new UsersRepository())->findByEmail($credentials['email']);
        }
        catch (\Exception $e){
            return false;
        }

        if(!Hash::check($credentials['password'], $user->password))
            return false;

        return true;
    }

    /**
     * @param User $authenticatedUser
     * @return User
     */
    public static function login(User $authenticatedUser){
        $authenticatedUser->session_id = bcrypt($authenticatedUser->id);
        $authenticatedUser->save();
        return $authenticatedUser;
    }

    public static function logout(User $authenticatedUser = null)
    {
        $authenticatedUser->session_id = "";
        $authenticatedUser->save();
        return true;
    }

    public static function authenticateWithToken($token)
    {
        return ((new UsersRepository())->findByToken($token) == null)?false:true;
    }

    /**
     * @return User $user
     * */
    public static function user()
    {
        if(isset(getallheaders()['Authorization']) && getallheaders()['Authorization'] != ""){
            $user = (new UsersRepository())->findByToken(getallheaders()['Authorization']);
            if($user != null)
                return $user;
        }
        return null;
    }

    public static function check()
    {
        return (Auth::user() != null);
    }
}