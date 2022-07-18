<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseFactoryJson;
use App\Managers\UserManager;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;
use Psr\Log\LoggerInterface;

class AuthController extends Controller
{
    private const SIGNATURE_LOG   = 'checkout-svc:auth-controller';
    private const TOKEN_TYPE      = 'Bearer';
    private const EXPIRATION_TIME = '60 Minutes';

    public function __construct(
        private readonly ResponseFactoryJson $responseFactory,
        private readonly LoggerInterface     $logger,
        private readonly UserManager         $userManager,
    ) {
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string||min:8'
            ]);

            $data['password'] = Hash::make($data['password']);
            $user = new User($data);
            $authToken = $this->userManager->register($user);

            return $this->responseFactory->success([
                'access_token'    => $authToken,
                'token_type'      => self::TOKEN_TYPE,
                'expiration_time' => self::EXPIRATION_TIME
            ]);
        } catch (\Throwable $th) {
            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Unable to register a new user',
                [
                    'message'    => $th->getMessage(),
                    'exception'  => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new UnauthorizedException('Invalid credentials');
            }

            $authToken = $this->userManager->login($request->input('email'));

            return $this->responseFactory->success([
                'access_token'    => $authToken,
                'token_type'      => self::TOKEN_TYPE,
                'expiration_time' => self::EXPIRATION_TIME
            ]);
        } catch (\Throwable $th) {
            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Invalid login details',
                [
                    'message'   => $th->getMessage(),
                    'exception' => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }
}
