@component('mail::message')
    # Checkout Confirmed

    Dear {{ $name }},
    This is your cart summary:

    Total: {{ $total }}

    Products:
    @foreach ($products as $product)
        - Product: {{ $product['name'] }},
        ASIN: {{ $product['external_id'] }},
        Quantity: {{ $product['quantity'] }},
        Price: {{ $product['price'] }}
        ------------------------------------
    @endforeach

    Thanks,
    {{ config('app.name') }}
@endcomponent
