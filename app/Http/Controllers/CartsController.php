<?php

namespace App\Http\Controllers;


use App\Enums\IntegrationsEnum;
use App\Exceptions\InvalidCartException;
use App\Exceptions\ProductNotFoundException;
use App\Http\Responses\ResponseFactoryJson;
use App\Mail\CheckoutConfirmed;
use App\Managers\CartsManager;
use App\Managers\ProductsManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Psr\Log\LoggerInterface;

class CartsController extends Controller
{
    private const SIGNATURE_LOG = 'checkout-svc:products-controller';

    public function __construct(
        private readonly CartsManager        $cartManager,
        private readonly ProductsManager     $productManager,
        private readonly ResponseFactoryJson $responseFactory,
        private readonly LoggerInterface     $logger
    ) {
    }

    public function getProducts(Request $request): JsonResponse
    {
        try {

            /** @var User $user */
            $user = $request->user();
            $cart = $this->cartManager->getIncompleteCart($user);

            if ($cart === null) {
                throw new InvalidCartException('User has not active cart, please add products first');
            }

            $products = $this->cartManager->getProducts($cart);

            return $this->responseFactory->success($products->toArray());
        } catch (\Throwable $th) {
            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Unable to get the products',
                [
                    'message'    => $th->getMessage(),
                    'exception'  => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }

    public function checkout(Request $request): JsonResponse
    {
        try {

            /** @var User $user */
            $user = $request->user();
            $cart = $this->cartManager->getIncompleteCart($user);

            if ($cart === null) {
                throw new InvalidCartException('User has not active cart, please add products first');
            }

            $products = $this->cartManager->getProducts($cart);
            $total    = $this->cartManager->getTotal($cart);
            $totalFormatted = (new DecimalMoneyFormatter(new ISOCurrencies()))->format($total);

            $this->cartManager->checkout($cart, $products);

            $checkoutConfirmed = new CheckoutConfirmed($user->name, $totalFormatted, $products->toArray());
            Mail::to($cart->email)->send($checkoutConfirmed);

            return $this->responseFactory->success([
                'cart_id'  => $cart->id,
                'email'    => $cart->email,
                'total'    => $totalFormatted,
                'products' => $products->toArray()
            ]);
        } catch (\Throwable $th) {
            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Unable to get the products',
                [
                    'message'    => $th->getMessage(),
                    'exception'  => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }

    public function addProduct(Request $request): JsonResponse
    {
        try {

            $productData = $request->validate([
                'integration' => 'string|required|in:' . IntegrationsEnum::getString(),
                'externalId'  => 'string|max:32|required',
                'quantity'    => 'required|numeric|min:1|max:1000',
            ]);

            /** @var User $user */
            $user = $request->user();
            $cart       = $this->cartManager->getOrCreateCartByUser($user);
            $productDto = $this->productManager->getProduct(
                $productData['integration'],
                $productData['externalId']
            );

            $this->cartManager->addProduct($cart, $productDto, $productData['quantity']);

            return $this->responseFactory->success(['cart_id' => $cart->id, 'email' => $cart->email]);
        } catch (\Throwable $th) {
            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Unable to add the product to the cart',
                [
                    'message'    => $th->getMessage(),
                    'exception'  => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }

    public function deleteProduct(Request $request, string $externalId): JsonResponse
    {
        try {

            $rules      = ['externalId'  => 'string|max:32|required|exists:products,external_id'];
            $parameters = ['externalId'  => $externalId];
            $this->getValidationFactory()->make(
                $parameters,
                $rules
            )->validate();

            /** @var User $user */
            $user = $request->user();
            $cart = $this->cartManager->getIncompleteCart($user);

            if ($cart === null) {
                throw new InvalidCartException('User has not active cart, please add products first');
            }

            $productAdded = $this->productManager->getProductAdded($cart, $externalId);

            if ($productAdded === null) {
                throw new ProductNotFoundException('Product doesnt exist on the cart');
            }

            $this->cartManager->deleteProduct($productAdded);

            return $this->responseFactory->success(['cart_id' => $cart->id, 'email' => $cart->email]);
        } catch (\Throwable $th) {
            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Unable to add the product to the cart',
                [
                    'message'    => $th->getMessage(),
                    'exception'  => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }
}
