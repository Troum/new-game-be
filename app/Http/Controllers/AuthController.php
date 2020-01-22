<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return response()
                ->json(['success' => 'Вы успешно авторизовались'], Response::HTTP_OK)
                ->header('Authorization', $token);
        }
        return response()
            ->json(['error' => 'Ошибка авторизации'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()
            ->json(['success' => 'Успешно вышли'], Response::HTTP_OK);
    }

    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['success' => 'Токен обновлен'], Response::HTTP_OK)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'Ошибка обновления токена'], Response::HTTP_UNAUTHORIZED);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @return mixed
     */
    private function guard()
    {
        return Auth::guard();
    }

}
